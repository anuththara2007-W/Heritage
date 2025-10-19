<?php
require_once __DIR__ . '/../config/session.php';

logoutUser();

header("Location: /index.php");
exit();
?>
