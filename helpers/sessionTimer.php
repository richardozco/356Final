<?php
// keeps track of the last interaction from the user
// if longer than the specified timeout, session is destroyed
// user will be sent to the login page

function sessionTimer() {

    $session_timeout = 1800; // 1800 = 30 minutes

    if (isset($_SESSION["last_activity"])) {
        $elapsed_time = time() - $_SESSION["last_activity"];
        if ($elapsed_time > $session_timeout) {
            session_unset();
            session_destroy();
            header("Location: /index.php");
            exit();
        }
    }

    $_SESSION['last_activity'] = time();
}
?>