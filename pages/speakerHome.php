<?php
session_start();

$pageTitle = "Speaker Home";

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
$form = null;

function sanitize($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $response = $supabase->from('user_speaker')->insert([
                'speaker_id'=> $_SESSION['user_id']
            ])->execute();

    $response = $supabase->from($database_user)
                ->update(['is_speaker' => true])
                ->eq('user_id', $_SESSION['user_id'])
                ->execute();
}

if (array_key_exists('user_id', $_SESSION)) {
    $user_id = $_SESSION['user_id'];

    $query = $supabase->query
        ->from('user_speaker')
        ->select('*')
        ->eq('speaker_id', $user_id)
        ->execute();

    $data = parseQuery($query);

    if (!$data['speaker_id']) {
        $form = registerSpeakerForm('speakerHome.php');
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
    <link href="../css/speaker-home.css"
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
                    <h2> Stage Request </h2>
                    <p> Request a stage space for a given event. </p>
                    <a href="speakerRequest.php" class="btn">Go to Stage Requests</a>
                </article>

                <article class="dashboard-card">
                    <h2> Proposal Status </h2>
                    <p> View status of previous stage requests. </p>
                    <a href="speakerStatus.php" class="btn">Go to Proposals</a>
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