<?php
session_start();

$pageTitle = "Exhibitor Home";

require_once '../helpers/checkLogin.php';
require_once '../helpers/sessionTimer.php';
require_once '../helpers/header.php';
require_once '../helpers/formBuilder.php';
require '../vendor/autoload.php';
require_once '../helpers/supabase.php';

$supabase = initializeSupabase();

//UNCOMMENT FOR PRODUCTION
//checkLogin();
//sessionTimer();

$database_user = 'program_user';

$org_name = null;
$org_industry = null;
$error = null;
$data = null;

function sanitize($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $org_name = sanitize($_POST['org_name']);
    $org_industry = sanitize($_POST['org_industry']);

    if($org_name == null || $org_industry == null){
        $message = "All fields required";
    }

    $response = $supabase->from('exhibitor_organization')->insert([
                'organization_name'=> $org_name,
                'organization_industry'=> $org_industry
            ])->execute();

    $query = $supabase->query
      ->from('exhibitor_organization')
      ->select('*')
      ->eq('organization_name', $org_name)
      ->execute();

    $data = parseQuery($query);

    if ($data['organization_id']) {

    $response = $supabase->from('user_exhibitor')->insert([
                'exhibitor_id'=> $_SESSION['user_id'],
                'organization_id'=> $data['organization_id']
            ])->execute();

    $response = $supabase->from($database_user)
                ->update(['is_exhibitor' => true])
                ->eq('user_id', $_SESSION['user_id'])
                ->execute();
    }
    else
    {
        $error = 'could not get organization id';
    }
    
}

if (array_key_exists('user_id', $_SESSION)) {
    $user_id = $_SESSION['user_id'];

    $query = $supabase->query
        ->from('user_exhibitor')
        ->select('*')
        ->eq('exhibitor_id', $user_id)
        ->execute();

    $data = parseQuery($query);

    if (!$data['exhibitor_id']) {
        $form = registerExhibitorForm('exhibitorHome.php');
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

    <?php
        if ($form) {
            ?>
                <?= $form ?>
            <?php
        }
        else
        {
    ?>

        <!-- Navigation / page intro -->
        <main class="main-content">

        <p class="error">
            <?=$error?>
        </p>

            <section class="hero-section">
                <h1>Exhibitor Home</h1>
            </section>

            <!-- Main role / feature navigation based on your wireframe -->
            <section class="dashboard-grid">

                <article class="dashboard-card">
                    <h2> Booth Request </h2>
                    <p> Request a booth space for a given event. </p>
                    <a href="boothRequest.php" class="btn">Go to Booths</a>
                </article>

                <article class="dashboard-card">
                    <h2> Booth Status </h2>
                    <p> View status of previous booth requests. </p>
                    <a href="boothStatus.php" class="btn">Go to Booth Status</a>
                </article>

            </section>
        </main>
        <?php
        }
        ?>

        <!-- Footer -->
        <footer class="site-footer">
            <?php
                include_once '../helpers/footer.html';
            ?>
        </footer>

    </div>

</body>
</html>