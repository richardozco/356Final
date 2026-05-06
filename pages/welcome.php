<?php
//welcome page after logging in
session_start();

require '../helpers/checkLogin.php';
require '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../vendor/autoload.php';
require_once '../helpers/supabase.php';

checkLogin();
sessionTimer();

$supabase = initializeSupabase();

$database_user = 'program_user';
$username = $_SESSION['username'];

$query = $supabase->query
    ->from($database_user)
    ->select('is_exhibitor, is_observer, is_organizer, is_speaker, is_admin')
    ->eq('user_username', $username)
    ->execute();

$data = parseQuery($query);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Welcome page</title>

  <link rel="stylesheet" href="../css/main.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <link rel="stylesheet" href="../css/welcome.css" />
</head>
<main>
<?php
    if (array_key_exists('username', $_SESSION)) {
        ?>
            <?=makeHeader("loggedIn");?>
        <?php
    } else {
    ?>
            <?=makeHeader("loggedOut");?>
    <?php   
    }
?>
    <h1>
        Welcome, <?= $username ?>
    </h1>

    <div id="welcome-section">
        <h2>
            What would you like to do?
        </h2>

        <ul id="welcome-options" class="account-actions">
            <?php if ($data['is_observer']){?>
            <li> <a href="observerHome.php"> Get Tickets </a> </li>
            <?php } ?>
            <?php if ($data['is_exhibitor']){?>
            <li> <a href="exhibitorHome.php"> Exhibitor Home </a> </li>
            <?php } ?>
            <?php if ($data['is_speaker']){?>
            <li> <a href="speakerHome.php"> Speaker Home </a> </li>
            <?php } ?>
            <?php if ($data['is_organizer']){?>
            <li> <a href="organizerHome.php"> Organizer Home </a> </li>
            <?php } ?>
            <?php if ($data['is_admin']){?>
            <li> <a href="adminHome.php"> Admin Home </a> </li>
            <?php } ?>
        </ul>
    </div>
</main>
<?php
include_once '../helpers/footer.html';
?>
</html>