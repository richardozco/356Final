<?php

require '../vendor/autoload.php';
require_once '../helpers/supabase.php';
require_once '../helpers/header.php';

$supabase = initializeSupabase();

$database_user = 'program_user';

$username = null;
$user_fnam = null;
$user_lname = null;
$email = null;
$password = null;
$is_observer = null;


function sanitize($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}


if ($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $username = sanitize($_POST['username']);
    $user_fname = sanitize($_POST['firstname']);
    $user_lname = sanitize($_POST['lastname']);

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_SANITIZE_EMAIL)) {
        die('Invalid email address');
    }

    $password = $_POST['password'];
    $is_observer = true;


    if($username == null || $user_fname == null || $user_lname == null || $email == null || $password == null){
        $message = "All fields required";
    }
    else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $response = $supabase->from($database_user)->insert([
                'user_username'=> $username,
                'user_fname'=> $user_fname,
                'user_lname'=> $user_lname,
                'user_email'=> $email,
                'user_password'=> $hashed_password,
                'is_observer'=> $is_observer
            ])->execute();


            if (isset($response->data['code'])) {
                $message = match($response->data['code']) {
                '23505' => 'That email is already registered.',
                default => 'Something went wrong, please try again.'
                };
                $_SESSION['error'] = $message;
                header('Location: signup.php');
                exit();

            } else {
                header('Location: welcome.php');
                exit();
            }

        }
        catch(Exception $e){
            $message = "Error creating account: " . $e->getMessage();
        }
    }
}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="../css/main.css"
          type="text/css" rel="stylesheet" />
    <link href="../css/styles.css"
          type="text/css" rel="stylesheet" />

</head>
<body>
    <?=  $message = '' ?>
    <div class="signup-container">
        <p class="error">
            <?= $message ?>
        </p>

        <h1>Create Account</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>

            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            

            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>
