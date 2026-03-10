<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Testing PHPMailer files...</h2>";

if (file_exists('includes/class.phpmailer.php')) {
    echo "class.phpmailer.php → FOUND<br>";
} else {
    echo "class.phpmailer.php → MISSING!<br>";
}

if (file_exists('includes/class.smtp.php')) {
    echo "class.smtp.php → FOUND<br>";
} else {
    echo "class.smtp.php → MISSING!<br>";
}

require_once 'includes/class.phpmailer.php';
require_once 'includes/class.smtp.php';

echo "<br><strong>PHPMailer loaded successfully!</strong><br>";
echo "Version: " . (defined('PHPMailer::VERSION') ? PHPMailer::VERSION : 'Old version');

require 'config.php';
echo "<br>Database connected (config.php loaded)";

?>