<?php
session_start();

$pageTitle = "Request Form";

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

$proposalTopic = null;
$proposalTitle = null;
$proposalDesc = null;

function sanitize($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

    $current_event_query = $supabase->from('event')
                           ->select('event_id')
                           ->execute();

    $current_event = parseQuery($current_event_query);
    $event_id = $current_event['event_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $proposalTopic = sanitize($_POST["topic"]);
        $proposalTitle = sanitize($_POST["title"]);
        $proposalDesc = sanitize($_POST["desc"]);
        
        if(empty($proposalTopic) || empty($proposalTitle) || empty($proposalDesc)) {
            $message = "All fields required";
        }

        else {

        if (!$event_id) {
            $message = "Error: Could not find a valid Event ID.";
        }

            try{
                
                $response = $supabase->from('proposal')->insert([
                    'speaker_id'=> $_SESSION['user_id'],
                    'event_id' => $event_id,
                    'proposal_status' => 'Submitted',
                    'is_approved' => false,
                    'proposal_topic'=> $proposalTopic,
                    'proposal_name'=> $proposalTitle,
                    'proposal_description'=> $proposalDesc
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
                    <form method="post" action="speakerRequest.php">
                        <div class="form-group">
                            <label for="title">Title: </label>
                            <input type="text" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="topic">Topic: </label>
                            <input type="text" id="topic" name="topic" required>
                        </div>

                        <div class="form-group">
                            <label for="desc">Description:</label>
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
                <a href="speakerHome.php" class="btn">Return to Speaker Home</a>
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