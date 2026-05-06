<?php
session_start();

require_once '../helpers/checkLogin.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/supabase.php';

$pageTitle = 'Speaker Review';

$supabase = initializeSupabase();

checkLogin();
sessionTimer();

if(array_key_exists('user_id', $_SESSION)){
    $query = $supabase
                    ->from('proposal')
                    ->select('*')
                    ->execute();
    
    $all_proposals = parseQueryArray(($query));
}

if($_SERVER["REQUEST_METHOD"] == "GET"){

    $proposal_id = trim(strip_tags($_GET["proposal_id"]));
    $type = trim(strip_tags($_GET["type"]));

    $response = $supabase->from('proposal')
                ->update(['proposal_status' => $type])
                ->eq('proposal_id', $proposal_id)
                ->execute();
}

if(array_key_exists('user_id', $_SESSION)){

    $query = $supabase
                    ->from('proposal')
                    ->select('*')
                    ->execute();
    
    $all_proposals = parseQueryArray(($query));

    $query = $supabase
                    ->from('event')
                    ->select('*')
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
                        foreach($all_proposals as $proposal)
                        {
                            ?>
                            <article class="card-slate-<?=$proposal['proposal_status']?>">
                                <h2><?= htmlentities($proposal['proposal_name']) ?></h2>
                                <p>Proposal Name: <?=  htmlentities($proposal['proposal_name'])?></p>
                                <p>Topic: <?=  htmlentities($proposal['proposal_topic'])?></p>
                                <p>Event Name: <?php  
                                    foreach($all_events as $event) {
                                        if($proposal['event_id'] == $event['event_id']) {
                                            ?> <?= htmlentities($event['event_name']) ?> <?php
                                        }
                                    }
                                ?></p>
                                <p>Description: <?=  htmlentities($proposal['proposal_description'])?></p>
                                <?php if ($proposal['proposal_status'] == "p") { ?>
                                        <a class="btn" href="speakerReview.php?proposal_id=<?=$proposal['proposal_id']?>&type=a"> Approve ✓ </a>
                                        <a class="btn" href="speakerReview.php?proposal_id=<?=$proposal['proposal_id']?>&type=d"> Deny X </a>
                                <?php } else if ($proposal['proposal_status'] == "a") { ?>
                                        <a class="btn"> Approved ✓ </a>
                                        <a class="btn" href="speakerReview.php?proposal_id=<?=$proposal['proposal_id']?>&type=p"> Revert to proposal </a>
                                <?php } else if ($proposal['proposal_status'] == "d") { ?>
                                        <a class="btn"> Denied X </a>
                                        <a class="btn" href="speakerReview.php?proposal_id=<?=$proposal['proposal_id']?>&type=p"> Revert to proposal </a>
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