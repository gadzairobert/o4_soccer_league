<?php
require '../config.php';
if (isset($_SESSION['admin_id'])) {
    $_SESSION['last_activity'] = time();
}
http_response_code(204);
exit;
?>