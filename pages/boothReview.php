<?php
session_start();

require_once '../helpers/checkLogin.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/supabase.php';

$pageTitle = 'Booth Review';

$supabase = initializeSupabase();

checkLogin();
sessionTimer();

if(array_key_exists('user_id', $_SESSION)){
$query = $supabase
                    ->from('booth')
                    ->select('*')
                    ->execute();
    
    $all_booths = parseQueryArray(($query));
}

if($_SERVER["REQUEST_METHOD"] == "GET"){

    $booth_id = trim(strip_tags($_GET["booth_id"]));
    $type = trim(strip_tags($_GET["type"]));

    $response = $supabase->from('booth')
                ->update(['booth_current_status' => $type])
                ->eq('booth_id', $booth_id)
                ->execute();
}

if(array_key_exists('user_id', $_SESSION)){

    $query = $supabase
                    ->from('booth')
                    ->select('*')
                    ->order('booth_current_status', ['ascending' => false])
                    ->execute();
    
    $all_booths = parseQueryArray(($query));

        $query = $supabase
                    ->from('event')
                    ->select('*')
                    ->execute();

    $all_events = parseQueryArray(($query));

    $query = $supabase
                    ->from('exhibitor_organization')
                    ->select('*')
                    ->execute();

    $all_orgs = parseQueryArray(($query));
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
                        foreach($all_booths as $booth)
                        {
                            ?>
                            <article class="card-slate-<?=$booth['booth_current_status']?>">
                                <p>Booth Building: <?=  htmlentities($booth['booth_building'])?></p>
                                <p>Number: <?=  htmlentities($booth['booth_number'])?></p>
                                <p>Event Name: <?php  
                                    foreach($all_events as $event) {
                                        if($booth['event_id'] == $event['event_id']) {
                                            ?> <?= htmlentities($event['event_name']) ?> <?php
                                        }
                                    }
                                ?></p>
                                 <p>Organization Name: <?php  
                                    foreach($all_orgs as $org) {
                                        if($booth['organization_id'] == $org['event_id']) {
                                            ?> <?= htmlentities($event['organization_name']) ?> <?php
                                        }
                                    }
                                ?></p>
                                <p>Description:<?=  htmlentities($booth['description'])?></p>
                                <?php if ($booth['booth_current_status'] == "p") { ?>
                                        <a class="btn" href="boothReview.php?booth_id=<?=$booth['booth_id']?>&type=a"> Approve ✓ </a>
                                        <a class="btn" href="boothReview.php?booth_id=<?=$booth['booth_id']?>&type=d"> Deny X </a>
                                <?php } else if ($booth['booth_current_status'] == "a") { ?>
                                        <a class="btn"> Approved ✓ </a>
                                        <a class="btn" href="boothReview.php?booth_id=<?=$booth['booth_id']?>&type=p"> Revert to proposal </a>
                                <?php } else if ($booth['booth_current_status'] == "d") { ?>
                                        <a class="btn"> Denied X </a>
                                        <a class="btn" href="boothReview.php?booth_id=<?=$booth['booth_id']?>&type=p"> Revert to proposal </a>
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