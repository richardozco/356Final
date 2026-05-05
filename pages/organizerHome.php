<?php
session_start();

$pageTitle = "Organizer Home";

require_once '../helpers/checkLogin.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/formBuilder.php';
require '../vendor/autoload.php';
require_once '../helpers/supabase.php';

$supabase = initializeSupabase();

//UNCOMMENT FOR PRODUCTION
//checkLogin();
//sessionTimer();

$database_user = 'program_user';

$org_name = null;
$org_industry = null;
$error = null;
$data = null;

function sanitize($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $response = $supabase->from('user_organizer')->insert([
                'organizer_id'=> $_SESSION['user_id']
            ])->execute();
}

if (array_key_exists('user_id', $_SESSION)) {
    $user_id = $_SESSION['user_id'];

    $query = $supabase->query
        ->from('user_organizer')
        ->select('*')
        ->eq('organizer_id', $user_id)
        ->execute();

    $data = parseQuery($query);

    if (!$data['organizer_id']) {
        $form = registerOrganizerForm('organizerHome.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="../css/main.css"
          type="text/css" rel="stylesheet" />
    <link href="../css/styles.css"
          type="text/css" rel="stylesheet" />
</head>
<body>
<!-- Header -->
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

    <!-- Main page wrapper -->
    <div class="page-container">

    <?php
        if ($form) {
            ?>
                <?= $form ?>
            <?php
        }
        else
        {
    ?>

        <!-- Navigation / page intro -->
        <main class="main-content">

        <p class="error">
            <?=$error?>
        </p>

            <section class="hero-section">
                <h1>Organizer Home</h1>
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="dashboard-grid">

                <article class="dashboard-card">
                    <h2> Event Request </h2>
                    <p> Request an event space. </p>
                    <a href="organizerRequest.php" class="btn">Go to Request form</a>
                </article>

                <article class="dashboard-card">
                    <h2> Event Status </h2>
                    <p> View status of previous event requests. </p>
                    <a href="organizerStatus.php" class="btn">Go to Event Status</a>
                </article>

            </section>
        </main>
        <?php
        }
        ?>

        <!-- Footer -->
        <footer class="site-footer">
            <?php
                include_once '../helpers/footer.html';
            ?>
        </footer>

    </div>

</body>
</html>