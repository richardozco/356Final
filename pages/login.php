<?php
// main page for logging users into the website
// if user is already logged in, redirects them to the index (main) page

session_start();

require '../vendor/autoload.php';
require_once '../helpers/supabase.php';

$supabase = initializeSupabase();

$database_user = 'program_user';

$username = null;
$password = null;
$error = null;
$data = null;


if ($_SERVER["REQUEST_METHOD"] == "POST"){

  if (array_key_exists('user_id', $_SESSION)) {
    header('Location: welcome.php');
    exit();
  }

  $username = trim(strip_tags($_POST["username"] ?? ''));
  
  if ($username === '') {
    $error = 'Please enter username';

  }
  else {
  $query = $supabase->query
      ->from($database_user)
      ->select('*')
      ->eq('user_username', $username)
      ->execute();

  $data = parseQuery($query);
  
  $password = $_POST['password'] ?? '';

  if ($data == null) {
    $error = 'User not found';
  }
  else {
    $stored_pass = $data['user_password'];

    if($stored_pass !== '' and password_verify($password, $stored_pass))
    {
      $password_confirmed = true;
      $_SESSION['user_id'] = $data['user_id'];
      $_SESSION['username'] = $data['user_username'];
      $_SESSION['is_speaker'] = $data['is_speaker'];
      $_SESSION['is_exhibitor'] = $data['is_exhibitor'];
      $_SESSION['is_organizer'] = $data['is_organizer'];
      header('Location: welcome.php');
      exit();
    }
    elseif($password == $stored_pass)
    {
      $password_confirmed = true;
      $_SESSION['user_id'] = $data['user_id'];
      $_SESSION['username'] = $data['user_username'];
      $_SESSION['is_speaker'] = $data['is_speaker'];
      $_SESSION['is_exhibitor'] = $data['is_exhibitor'];
      $_SESSION['is_organizer'] = $data['is_organizer'];
      header('Location: welcome.php');
      exit();
    }
    else {
      $error = 'wrong password';
    }

  }

  }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Simple Login</title>

  <link rel="stylesheet" href="../css/main.css" />
</head>
<body>
  <div class="card" role="main">
    <h1>Sign In</h1>

    <p class="error">
    <?=$error?>
    </p>

    <form method = "post" id="loginForm" action="<?= htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) ?>">
      <div class="field">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required="required"/>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required />
      </div>
      <button class="btn" type="submit">Log in</button>
    </form>
</body>
<footer>
  <div class="container">
    <a href="signup.php" class="button">Create Account</a> 
  </div>
</footer>

</html>