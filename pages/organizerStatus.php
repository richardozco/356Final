<?php
session_start();

require_once '../helpers/checkLogin.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/supabase.php';

$pageTitle = "Your Submissions";

$supabase = initializeSupabase();

checkLogin();
sessionTimer();

    $proposals_query = $supabase
    ->from('event')
    ->select('*')
    ->eq('user_id', $_SESSION['user_id'])
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
                        foreach($proposal as $event)
                        {
                            ?>
                            <article class="card-slate-<?=$event['event_current_status']?>">
                                <h1>Status: <?php
                                if (htmlentities($event['event_current_status']) == "p") {
                                    echo "Proposed";
                                } else if (htmlentities($event['event_current_status']) == "d") {
                                    echo "Denied";
                                } else {
                                    echo "Approved";
                                } ?>
                                <h2><?= htmlentities($event['event_name']) ?></h2>
                                <p>Event Capacity: <?=  htmlentities($event['event_capacity'])?></p>
                                <p>Start Date: <?=  htmlentities(formatTime($event['event_start_time']))?></p>
                                <p>End Date: <?=  htmlentities(formatTime($event['event_end_time']))?></p>
                                <p>Location: <?=  htmlentities($event['event_location'])?></p>
                                <p><?=  htmlentities($event['event_description'])?></p>
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

