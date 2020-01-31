<?php
session_start();
require_once "objects/class_connection.php";
require_once "objects/class_setting.php";
require_once "objects/class_version_update.php";
require_once "objects/class_login_check.php";
require_once 'page_initialization.php';
require_once 'header.php';
$filename = 'config.php';
unset($_SESSION['ct_login_user_id']);
unset($_SESSION['ct_user_email']);
unset($_SESSION['user_id']);
session_destroy(); //destroy the session
 header('Location:' . SITE_URL); //to redirect back to "index.php" after logging out
exit();

?>