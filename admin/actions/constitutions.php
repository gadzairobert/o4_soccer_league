<?php
// admin/actions/constitutions.php
if (!defined('IN_APP')) exit('Direct access not allowed');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ——————————————————————— HELPERS ———————————————————————
define('CONSTITUTION_UPLOAD_DIR', __DIR__ . '/../../uploads/constitutions/');
define('CONSTITUTION_MAX_SIZE',   5 * 1024 * 1024); // 5 MB

/**
 * Handle the PDF upload and return ['filename' => ..., 'path' => ..., 'size' => ...]
 * or throw a RuntimeException on failure.
 */
function handleConstitutionUpload(array $file): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by a PHP extension.',
        ];
        throw new RuntimeException($messages[$file['error']] ?? 'Unknown upload error.');
    }

    // Validate MIME type
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if ($mimeType !== 'application/pdf') {
        throw new RuntimeException('Only PDF files are allowed.');
    }

    // Validate size
    if ($file['size'] > CONSTITUTION_MAX_SIZE) {
        throw new RuntimeException('PDF file must not exceed 5 MB.');
    }

    // Validate extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        throw new RuntimeException('File must have a .pdf extension.');
    }

    // Ensure upload directory exists
    if (!is_dir(CONSTITUTION_UPLOAD_DIR) && !mkdir(CONSTITUTION_UPLOAD_DIR, 0755, true)) {
        throw new RuntimeException('Could not create upload directory.');
    }

    // Build a unique filename: timestamp_randomhex_originalname.pdf
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name'], '.pdf'));
    $filename  = time() . '_' . bin2hex(random_bytes(4)) . '_' . $safeName . '.pdf';
    $destPath  = CONSTITUTION_UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Failed to save the uploaded PDF.');
    }

    return [
        'filename' => $filename,
        'path'     => 'uploads/constitutions/' . $filename,
        'size'     => $file['size'],
    ];
}

// ——————————————————————— ADD CONSTITUTION ———————————————————————
if ($action === 'add_constitution') {
    $title          = trim($_POST['title']          ?? '');
    $description    = trim($_POST['description']    ?? '');
    $version        = trim($_POST['version']        ?? '');
    $effective_date = trim($_POST['effective_date'] ?? '');
    $uploaded_by    = trim($_POST['uploaded_by']    ?? '');
    $is_active      = isset($_POST['is_active']) ? 1 : 0;

    if ($title && $version && $effective_date && !empty($_FILES['pdf_file']['name'])) {
        try {
            $upload = handleConstitutionUpload($_FILES['pdf_file']);

            // If this one is active, unset all others first
            if ($is_active) {
                $pdo->prepare("UPDATE club_constitutions SET is_active = 0")->execute();
            }

            $stmt = $pdo->prepare("
                INSERT INTO club_constitutions 
                    (title, description, version, effective_date, pdf_filename, pdf_path, pdf_size, uploaded_by, is_active)
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title,
                $description,
                $version,
                $effective_date,
                $upload['filename'],
                $upload['path'],
                $upload['size'],
                $uploaded_by,
                $is_active,
            ]);

            $success = 'Constitution added successfully!';
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            // Clean up uploaded file if DB insert failed
            if (!empty($upload['path']) && file_exists(CONSTITUTION_UPLOAD_DIR . $upload['filename'])) {
                unlink(CONSTITUTION_UPLOAD_DIR . $upload['filename']);
            }
            $error = 'Database error: This version may already exist.';
        }
    } else {
        $error = 'Please fill all required fields and select a PDF file.';
    }

// ——————————————————————— EDIT CONSTITUTION ———————————————————————
} elseif ($action === 'edit_constitution') {
    $id             = (int)($_POST['id']             ?? 0);
    $title          = trim($_POST['title']          ?? '');
    $description    = trim($_POST['description']    ?? '');
    $version        = trim($_POST['version']        ?? '');
    $effective_date = trim($_POST['effective_date'] ?? '');
    $uploaded_by    = trim($_POST['uploaded_by']    ?? '');
    $is_active      = isset($_POST['is_active']) ? 1 : 0;

    if ($id && $title && $version && $effective_date) {
        try {
            // Fetch existing record
            $existing = $pdo->prepare("SELECT * FROM club_constitutions WHERE id = ?");
            $existing->execute([$id]);
            $record = $existing->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                throw new RuntimeException('Constitution record not found.');
            }

            $pdfFilename = $record['pdf_filename'];
            $pdfPath     = $record['pdf_path'];
            $pdfSize     = $record['pdf_size'];
            $oldFile     = null;

            // Handle optional new PDF upload
            if (!empty($_FILES['pdf_file']['name'])) {
                $upload      = handleConstitutionUpload($_FILES['pdf_file']);
                $oldFile     = CONSTITUTION_UPLOAD_DIR . $pdfFilename; // mark for deletion
                $pdfFilename = $upload['filename'];
                $pdfPath     = $upload['path'];
                $pdfSize     = $upload['size'];
            }

            if ($is_active) {
                $pdo->prepare("UPDATE club_constitutions SET is_active = 0 WHERE id != ?")->execute([$id]);
            }

            $stmt = $pdo->prepare("
                UPDATE club_constitutions 
                SET title = ?, description = ?, version = ?, effective_date = ?,
                    pdf_filename = ?, pdf_path = ?, pdf_size = ?, uploaded_by = ?, is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $title,
                $description,
                $version,
                $effective_date,
                $pdfFilename,
                $pdfPath,
                $pdfSize,
                $uploaded_by,
                $is_active,
                $id,
            ]);

            // Remove old file only after successful DB update
            if ($oldFile && file_exists($oldFile)) {
                unlink($oldFile);
            }

            $success = 'Constitution updated successfully!';
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'Update failed. The version number may already be in use.';
        }
    } else {
        $error = 'Invalid data submitted.';
    }

// ——————————————————————— DELETE CONSTITUTION ———————————————————————
} elseif ($action === 'delete_constitution' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT pdf_filename FROM club_constitutions WHERE id = ?");
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            $error = 'Record not found.';
        } else {
            $pdo->prepare("DELETE FROM club_constitutions WHERE id = ?")->execute([$id]);

            // Delete the physical PDF file
            $filePath = CONSTITUTION_UPLOAD_DIR . $record['pdf_filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $success = 'Constitution deleted successfully.';
        }
    } catch (Exception $e) {
        $error = 'Delete failed.';
    }
}

// ——————————————————————— REDIRECT ———————————————————————
$redirect = $_SERVER['HTTP_REFERER'] ?? '?page=constitutions';
if (isset($success)) {
    header("Location: $redirect&success=" . urlencode($success));
} elseif (isset($error)) {
    header("Location: $redirect&error=" . urlencode($error));
} else {
    header("Location: $redirect");
}
exit;