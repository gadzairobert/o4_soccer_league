<?php
// admin/includes/functions.php

// Block direct access
if (!defined('IN_APP')) {
    exit('Direct access not allowed');
}

// ------------------------------------------------------------------
//  UPLOAD FILE (JPG, JPEG, PNG only)
// ------------------------------------------------------------------
if (!function_exists('uploadFile')) {
    function uploadFile($file, $targetDir)
    {
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return false;
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            return false;
        }

        $newName = uniqid('img_') . '.' . $ext;
        $targetPath = $targetDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $newName;
        }
        return false;
    }
}

// ------------------------------------------------------------------
//  DELETE FILE
// ------------------------------------------------------------------
if (!function_exists('deleteFile')) {
    function deleteFile($filepath)
    {
        return ($filepath && file_exists($filepath) && is_file($filepath)) ? unlink($filepath) : false;
    }
}
?>