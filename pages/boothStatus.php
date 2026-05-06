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

    $proposals_query = $supabase
    ->from('booth')
    ->select('
        *,
        event(event_name),
        exhibitor_organization(organization_name)
    ')
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
    
    <!-- Changed to boothStatus.css from main/styles -->
    
    <link href="../css/status.css"
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
                            <article class="card-slate-<?=$form['booth_current_status']?>">
                            <h1>Status: <?php
                            if (htmlentities($form['booth_current_status']) == "p") {
                                echo "Proposed";
                            } else if (htmlentities($form['booth_current_status']) == "d") {
                                echo "Denied";
                            } else {
                                echo "Approved";
                            }
                            ?></h1>
                            <h2>Booth Building: <?= htmlentities($form['booth_building'])?></h2>
                            <h3>Event: <?= htmlentities($form['event']['event_name']) ?></h3>
                            <h4>Organization: <?= htmlentities($form['exhibitor_organization']['organization_name'])?></h4>
                            <p>Desc: <?= htmlentities($form['description'])?></p>
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

