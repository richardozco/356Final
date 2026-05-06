<?php
session_start();

require_once '../helpers/checkLogin.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/supabase.php';

$pageTitle = 'Event Review';

$supabase = initializeSupabase();

checkLogin();
sessionTimer();

if(array_key_exists('user_id', $_SESSION)){
$query = $supabase
                    ->from('event')
                    ->select('*')
                    ->execute();
    
    $all_events = parseQueryArray(($query));
}

if($_SERVER["REQUEST_METHOD"] == "GET"){

    $event_id = trim(strip_tags($_GET["event_id"]));
    $type = trim(strip_tags($_GET["type"]));

    $response = $supabase->from('event')
                ->update(['event_current_status' => $type])
                ->eq('event_id', $event_id)
                ->execute();
}

if(array_key_exists('user_id', $_SESSION)){

    $query = $supabase
                    ->from('event')
                    ->select('*')
                    ->order('event_current_status', ['ascending' => false])
                    ->execute();
    
    $all_events = parseQueryArray(($query));
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

        <!-- Navigation / page intro -->
        <main class="main-content">
            <section class="hero-section">
                
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="layout-stack">

                <?php
                        foreach($all_events as $event)
                        {
                            ?>
                            <article class="card-slate-<?=$event['event_current_status']?>">
                                <h2><?= htmlentities($event['event_name']) ?></h2>
                                <p>Event Capacity: <?=  htmlentities($event['event_capacity'])?></p>
                                <p>Start Date: <?=  htmlentities(formatTime($event['event_start_time']))?></p>
                                <p>End Date: <?=  htmlentities(formatTime($event['event_end_time']))?></p>
                                <p>Location: <?=  htmlentities($event['event_location'])?></p>
                                <p><?=  htmlentities($event['event_description'])?></p>
                                <?php if ($event['event_current_status'] == "p") { ?>
                                        <a class="btn" href="eventReview.php?event_id=<?=$event['event_id']?>&type=a"> Approve ✓ </a>
                                        <a class="btn" href="eventReview.php?event_id=<?=$event['event_id']?>&type=d"> Deny X </a>
                                <?php } else if ($event['event_current_status'] == "a") { ?>
                                        <a class="btn"> Approved ✓ </a>
                                        <a class="btn" href="eventReview.php?event_id=<?=$event['event_id']?>&type=p"> Revert to proposal </a>
                                <?php } else if ($event['event_current_status'] == "d") { ?>
                                        <a class="btn"> Denied X </a>
                                        <a class="btn" href="eventReview.php?event_id=<?=$event['event_id']?>&type=p"> Revert to proposal </a>
                                <?php } ?>
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