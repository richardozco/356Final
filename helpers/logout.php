<?php
// send user to this page whenever needing to redirect to logging out

session_start();

$_SESSION = array();

session_destroy();

header("Location: /pages/login.php");
exit;
?>