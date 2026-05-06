<?php
session_start();

$pageTitle = "Booth Request Form";

require_once '../helpers/checkLogin.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/formBuilder.php';
require      '../vendor/autoload.php';
require_once '../helpers/supabase.php';

$supabase = initializeSupabase();

//UNCOMMENT FOR PRODUCTION
//checkLogin();
//sessionTimer();

$showForm = true;

function sanitize($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

    $event_query = $supabase->from('event')
                           ->select('*')
                           ->execute();

    $events = parseQueryArray($event_query);

    $org_query = $supabase->from('exhibitor_organization')
                           ->select('*')
                           ->execute();

    $orgs = parseQueryArray($org_query);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $event = sanitize($_POST["event"]);
        $org = sanitize($_POST["org"]);
        $proposalBuilding = sanitize($_POST["building"]);
        $proposalDesc = sanitize($_POST["desc"]);
        
        if(empty($event) || empty($org) || empty($proposalBuilding) || empty($proposalDesc)) {
            $message = "All fields required";
        }

        else {

            try{
                
                $response = $supabase->from('booth')->insert([
                    'booth_building' => $proposalBuilding,
                    'organization_id' => $org,
                    'event_id' => $event,
                    'description' => $proposalDesc,
                    'user_id' => $_SESSION['user_id']
                ])->execute();

                if (isset($response->error) && $response->error != null) {
                    $message = "Database Error: " . $response->error->details . " - " . $response->error->message;
                } else {
                    $showForm = false;
                    $message = "Proposal submitted successfully!";
                }

            }
            catch(Exception $e) {
                $message = "Error submitting form: " . $e->getMessage();
            }

        }

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <!-- changed to request.css from main/styles-->
    
    <link href="../css/requests.css"
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

    <?php if ($showForm) { ?>
        <!-- Navigation / page intro -->
        <main class="main-content">
            <section class="hero-section">
                
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="dashboard-grid">

                <article class="dashboard-card">
                    <form method="post" action="boothRequest.php">
                        <div class="form-group">
                            <label for="event">Event: </label>
                            <select name="event">
                                <?php foreach($events as $event){?>
                                    <option value="<?= $event['event_id']?>"> <?=  htmlentities($event['event_name'])?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="org">Organization: </label>
                            <select name="org">
                                <?php foreach($orgs as $org){?>
                                    <option value="<?=$org['organization_id']?>"><?=  htmlentities($org['organization_name'])?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="building">Building:</label>
                            <input type="text" id="building" name="building" required>
                        </div>

                        <div class="form-group">
                            <label for="desc">Booth Description:</label>
                            <input type="text" id="desc" name="desc" required>
                        </div>
                        <button type="submit">Submit Form</button>
                    </form>
                </article>

            </section>

            <!-- Placeholder for announcements or future dynamic content -->
            <section class="info-section">
                
            </section>
        </main>
        <?php } else {  ?>
        <section class="success-container" style="text-align: center; padding: 50px;">
            <div class="success-card">
                <h2 style="color: #2ecc71;">✅ Success!</h2>
                <p><?= $message ?></p>
                <a href="exhibitorHome.php" class="btn">Return to Exhibitor Home</a>
            </div>
        </section>
        <?php   }   ?>

        <!-- Footer -->
        <footer class="site-footer">
            <?php
                include_once '../helpers/footer.html';
            ?>
            <h3><?= $message ?></h3>
        </footer>

    </div>

</body>
</html>