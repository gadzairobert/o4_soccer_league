<?php
define('IN_APP', true);

session_start();

// Database connection
$host = 'localhost';
$dbname = 'b04slom6h9u2_04sl';
$username = 'b04slom6h9u2_04sl';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$functionsPath = __DIR__ . '/admin/includes/functions.php';
if (file_exists($functionsPath) && !function_exists('uploadFile')) {
    require_once $functionsPath;
}
?>