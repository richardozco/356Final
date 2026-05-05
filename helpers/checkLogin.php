<?php
// makes sure that the user has logged in through the login.php page

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /pages/login.php');
        exit();
    }
}

?>