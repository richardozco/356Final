<?php
// main landing page for the website
session_start();

require_once 'helpers/sessionTimer.php';
require_once 'helpers/header.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once 'helpers/supabase.php';

sessionTimer();

$supabase = initializeSupabase();

$query = $supabase->query
    ->from('event')
    ->select('*')
    ->execute();

$data = parseQueryArray($query);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>landing page</title>

  <link rel="stylesheet" href="../css/styles.css" />
  <link rel="stylesheet" href="../css/main.css" />
</head>
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
<body>
    <main class="main-content">
            <section class="hero-section">
                <h2>Welcome Page</h2>
                <p>
                    Welcome to Burvents. Select how you would like to use the platform.
                </p>
            </section>

            <section class="dashboard-grid">

                <article class="dashboard-card">
                    <h3>Observe</h3>
                    <p>Access attendee-facing event views and updates.</p>
                    <a href="pages/observerHome.php" class="btn">Go to Observe</a>
                </article>

                <article class="dashboard-card">
                    <h3>Speakers</h3>
                    <p>Register as a speaker and manage proposal information.</p>
                    <a href="pages/speakerHome.php" class="btn">Go to Speakers</a>
                </article>

                <article class="dashboard-card">
                    <h3>Exhibitors</h3>
                    <p>Create and manage exhibit-related information.</p>
                    <a href="pages/exhibitorHome.php" class="btn">Go to Exhibitors</a>
                </article>

            </section>

            <section class="info-section">
                <h3>Upcoming Events:</h3>
                <section class="layout-stack">
                <?php
                    foreach($data as $event)
                    {
                        ?>
                        <section class="card-slate">
                        <h4><?= htmlentities($event['event_name']) ?></h4>
                        <p>Event Capacity: <?=  htmlentities($event['event_capacity'])?></p>
                        <p>Start Date: <?=  htmlentities(formatTime($event['event_start_time']))?></p>
                        <p>End Date: <?=  htmlentities(formatTime($event['event_end_time']))?></p>
                        <p>Location: <?=  htmlentities($event['event_location'])?></p>
                        <p><?=  htmlentities($event['event_description'])?></p>
                        </section>
                        <?php
                    }
                ?>
                </section>

            </section>
        </main>
</body>
<?php
include_once 'helpers/footer.html';
?>
</html>