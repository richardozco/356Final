<?php
session_start();

$pageTitle = "Admin Home";

require_once '../helpers/checkLogin.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/formBuilder.php';
require '../vendor/autoload.php';
require_once '../helpers/supabase.php';

$supabase = initializeSupabase();

checkLogin();
sessionTimer();

$database_user = 'program_user';

$org_name = null;
$org_industry = null;
$error = null;
$data = null;

if (array_key_exists('user_id', $_SESSION)) {
    $user_id = $_SESSION['user_id'];

    $query = $supabase->query
        ->from($database_user)
        ->select('*')
        ->eq('user_id', $user_id)
        ->execute();

    $data = parseQuery($query);

    if (!$data['is_admin']) {
        header('welcome.php');
        exit();
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
    <link href="../css/admin-home.css"
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

        <!-- Navigation / page intro -->
        <main class="main-content">

        <p class="error">
            <?=$error?>
        </p>

            <section class="hero-section">
                <h1>Admin Home</h1>
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="dashboard-grid">

                <article class="dashboard-card">
                    <h2> Event Review </h2>
                    <p> Review event requests. </p>
                    <a href="eventReview.php" class="btn">Go to Review</a>
                </article>

                <article class="dashboard-card">
                    <h2> Booth Review </h2>
                    <p> Review booth requests for a given event. </p>
                    <a href="boothReview.php" class="btn">Go to Review</a>
                </article>

                <article class="dashboard-card">
                    <h2> Speaker Proposal </h2>
                    <p> Review of speaker event porposal requests. </p>
                    <a href="speakerReview.php" class="btn">Go to Review</a>
                </article>

            </section>
        </main>

        <!-- Footer -->
        <footer class="site-footer">
            <?php
                include_once '../helpers/footer.html';
            ?>
        </footer>

    </div>

</body>
</html>