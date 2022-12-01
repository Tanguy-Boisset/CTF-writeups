<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

setcookie('session', null, -1, '/');

// Redirect to login page
header("location: index.php");
exit;
?>