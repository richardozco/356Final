<?php
session_start();

$pageTitle = "Observer Home";

require_once '../helpers/checkLogin.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/formBuilder.php';
require_once '../vendor/autoload.php';
require_once '../helpers/supabase.php';

$supabase = initializeSupabase();

checkLogin();
sessionTimer();

$query = $supabase->query
         ->from('event')
         ->select('*')
         ->order('event_start_time', ['ascending' => true])
         ->execute();

$data = parseQuery($query);


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
                <h1>Speaker Home</h1>
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="dashboard-grid">

                <article class="dashboard-card">
                    <h2> Register For Event </h2>
                    <p> Register for an upcoming event.</p>
                    <a href="observerRegister.php?event_id=<?= urlencode($data['event_id']) ?>" class="btn">Go to Register</a>
                </article>

                <article class="dashboard-card">
                    <h2> Explore Booths </h2>
                    <p> Browse booths in the upcoming Event </p>
                    <a href="boothExplore.php?event_id=<?= urlencode($data['event_id']) ?>" class="btn">Go to Booths</a>
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