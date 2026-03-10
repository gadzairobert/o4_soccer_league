<?php
require '../config.php';
if (!isset($_SESSION['admin_id'])) header('Location: login.php');

// Fetch clubs for dropdown
$clubs = $pdo->query("SELECT id, name FROM clubs")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $club_id = $_POST['club_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $jersey_number = $_POST['jersey_number'];
    $dob = $_POST['dob'];
    $nationality = $_POST['nationality'];
    $photo = NULL;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = uploadFile($_FILES['photo'], '../uploads/players/');
    }
    $stmt = $pdo->prepare("INSERT INTO players (club_id, name, photo, position, jersey_number, date_of_birth, nationality) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$club_id, $name, $photo, $position, $jersey_number, $dob, $nationality]);
    header('Location: dashboard.php?success=player_added');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Add Player</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-5">
    <form method="POST" enctype="multipart/form-data" class="card p-4">
        <h2>Add Player</h2>
        <select name="club_id" class="form-control mb-2" required>
            <option value="">Select Club</option>
            <?php foreach ($clubs as $club): ?>
                <option value="<?= $club['id'] ?>"><?= $club['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="name" class="form-control mb-2" placeholder="Player Name" required>
        <input type="file" name="photo" class="form-control mb-2" accept="image/*">
        <select name="position" class="form-control mb-2" required>
            <option value="GK">Goalkeeper</option>
            <option value="DF">Defender</option>
            <option value="MF">Midfielder</option>
            <option value="FW">Forward</option>
        </select>
        <input type="number" name="jersey_number" class="form-control mb-2" placeholder="Jersey Number" required>
        <input type="date" name="dob" class="form-control mb-2">
        <input type="text" name="nationality" class="form-control mb-2" placeholder="Nationality">
        <button type="submit" class="btn btn-primary">Add Player</button>
    </form>
</body>
</html>