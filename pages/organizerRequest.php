<?php
session_start();

$pageTitle = "Event Request Form";

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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $eventName = sanitize($_POST["eventName"]);
        $eventCap = sanitize($_POST["eventCap"]);

        $eventStart = sanitize($_POST["eventStart"]);
        $eventEnd = sanitize($_POST["eventEnd"]);

        $Starttemp = new DateTime($eventStart);
        $Endtemp = new DateTime($eventEnd);
        $eventStartStamp = $Starttemp->format('c');
        $eventEndStamp = $Endtemp->format('c');

        $eventLoc = sanitize($_POST["eventLoc"]);
        $eventDesc = sanitize($_POST["eventDesc"]);

        $query = $supabase->from('user_organizer')
                    ->select('*')
                    ->eq('organizer_id', $_SESSION['user_id'])
                    ->execute();

        $data = parseQuery($query);
        
        if(empty($eventName) || empty($eventCap) || empty($eventStart) || empty($eventEnd) || empty($eventLoc) || empty($eventDesc)) {
            $message = "All fields required";
        }

        else {

            try{
                // TEST AFTER DB UPDATES WITH EVENT
                $response = $supabase->from('event')->insert([
                    'event_name' => $eventName,
                    'event_capacity' => $eventCap,
                    'event_start_time' => $eventStartStamp,
                    'event_end_time' => $eventEndStamp,
                    'event_location' => $eventLoc,
                    'event_description' => $eventDesc,
                    'user_id' => $data['organizer_id']
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

    <?php if ($showForm) { ?>
        <!-- Navigation / page intro -->
        <main class="main-content">
            <section class="hero-section">
                
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="dashboard-grid">

                <article class="dashboard-card">
                    <form method="post" action="organizerRequest.php">

                        <div class="form-group">
                            <label for="name">Event Name:</label>
                            <input type="text" id="name" name="eventName" required="required">
                        </div>

                        <div class="form-group">
                            <label for="cap">capacity:</label>
                            <input type="number" min="1" max="9999" id="cap" name="eventCap" required="required">
                        </div>

                        <div class="form-group">
                            <label for="start">Start Date:</label>
                            <input type="datetime-local" id="start" name="eventStart" min="<?= date('Y-m-d');?>" required="required">
                        </div>

                        <div class="form-group">
                            <label for="end">End Date:</label>
                            <input type="datetime-local" id="end" name="eventEnd" min="<?= date('Y-m-d'); ?>" required="required">
                        </div>

                        <div class="form-group">
                            <label for="loc">Location:</label>
                            <input type="text" id="loc" name="eventLoc" required="required">
                        </div>

                        <div class="form-group">
                            <label for="desc">Description:</label>
                            <input type="text" id="desc" name="eventDesc" required="required">
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
                <a href="organizerHome.php" class="btn">Return to Organizer Home</a>
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