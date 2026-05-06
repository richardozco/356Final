<?php
session_start();


require_once '../helpers/checkLogin.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/supabase.php';

$pageTitle = 'Event Register';

$supabase = initializeSupabase();

checkLogin();
sessionTimer();

if(array_key_exists('user_id', $_SESSION)){
$enroll_query = $supabase
                    ->from('event_registration')
                    ->select('*')
                    ->eq('user_id', $_SESSION['user_id'])
                    ->execute();
    
    $all_enrolled_events = parseQueryArray(($enroll_query));
}

if($_SERVER["REQUEST_METHOD"] == "GET"){

    $event_id = trim(strip_tags($_GET["event_id"]));

    $tempTime = new DateTime();
    $currTime = $tempTime->format('c');

    $check = false;
    foreach ($all_enrolled_events as $enrolled) { 
    if ($enrolled['event_id'] == $event_id) {
        $check = true;
        }
    } 

    if(!$check) {
    $response = $supabase->from('event_registration')->insert([
                    'event_id' => $event_id,
                    'user_id' => $_SESSION['user_id'],
                    'submitted_date' => $currTime
                ])->execute();
    }
}

if(array_key_exists('user_id', $_SESSION)){

    $current_event_query = $supabase
                    ->from('event')
                    ->select('*')
                    ->order('event_start_time', ['ascending' => false])
                    ->eq('event_current_status', "a")
                    ->execute();

    $data = parseQueryArray($current_event_query);

    $enroll_query = $supabase
                    ->from('event_registration')
                    ->select('*')
                    ->eq('user_id', $_SESSION['user_id'])
                    ->execute();
    
    $all_enrolled_events = parseQueryArray(($enroll_query));
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
                        foreach($data as $event)
                        {
                            ?>
                            <article class="card-slate">
                                <h2><?= htmlentities($event['event_name']) ?></h2>
                                <p>Event Capacity: <?=  htmlentities($event['event_capacity'])?></p>
                                <p>Start Date: <?=  htmlentities(formatTime($event['event_start_time']))?></p>
                                <p>End Date: <?=  htmlentities(formatTime($event['event_end_time']))?></p>
                                <p>Location: <?=  htmlentities($event['event_location'])?></p>
                                <p><?=  htmlentities($event['event_description'])?></p>

                                <?php 
                                $check = false;
                                foreach ($all_enrolled_events as $enrolled) { 
                                    if ($enrolled['event_id'] == $event['event_id']) {
                                        $check = true;
                                    }
                                 } 

                                 if(!$check) { ?>
                                    <a href="observerRegister.php?event_id=<?= urlencode($event['event_id']) ?>" class="btn">Register for this event</a>
                                 <?php } else { ?>
                                    <a class="btn" style="background-color:#91d775"> Already Registered!</a>
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