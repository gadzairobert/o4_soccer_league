<?php
require '../config.php';
if (!isset($_SESSION['admin_id'])) header('Location: login.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $stadium = $_POST['stadium'];
    $logo = NULL;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo = uploadFile($_FILES['logo'], '../uploads/clubs/');
    }
    $stmt = $pdo->prepare("INSERT INTO clubs (name, logo, description, stadium) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $logo, $description, $stadium]);
    header('Location: dashboard.php?success=club_added');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Add Club</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-5">
    <form method="POST" enctype="multipart/form-data" class="card p-4">
        <h2>Add Club</h2>
        <input type="text" name="name" class="form-control mb-2" placeholder="Club Name" required>
        <input type="file" name="logo" class="form-control mb-2" accept="image/*">
        <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
        <input type="text" name="stadium" class="form-control mb-2" placeholder="Stadium">
        <button type="submit" class="btn btn-primary">Add Club</button>
    </form>
</body>
</html>