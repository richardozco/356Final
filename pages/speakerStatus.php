<?php
session_start();



require_once '../helpers/checkLogin.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/supabase.php';

$pageTitle = "Your Submissions";

$supabase = initializeSupabase();

//checkLogin();
//sessionTimer();
   
    $current_event_query = $supabase->from('event')
                           ->select('event_id')
                           ->execute();

    $current_event = parseQuery($current_event_query);
    $event_id = $current_event['event_id'];

    $proposals_query = $supabase->query
        ->from('proposal')
        ->select('*')
        ->eq('speaker_id', $_SESSION['user_id'])
        ->execute();

    $proposal = parseQueryArray($proposals_query);

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

        <!-- Navigation / page intro -->
        <main class="main-content">
            <section class="hero-section">
                <h1>Submitted Proposals </h1> 
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="layout-stack"> 

                <?php
                        foreach($proposal as $form)
                        {
                            ?>
                            <article class="card-slate">
                            <h1>Status: <?= htmlentities($form['proposal_status'])?></h1>
                            <h2>Title: <?= htmlentities($form['proposal_name']) ?></h2>
                            <h3>Topic: <?= htmlentities($form['proposal_topic'])?></h3>
                            <p>Desc: <?= htmlentities($form['proposal_description'])?></p>
                            </article>
                            <?php
                        }
                    ?>
                
            </section>

            <!-- Placeholder for announcements or future dynamic content -->
            <section class="info-section">
                
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

