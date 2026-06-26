<?php
session_start();

// Unset the admin session variable
unset($_SESSION['admin']);

// Destroy the session
session_destroy();

// Redirect to admin index.php
header("Location: index.php");
exit();
?>