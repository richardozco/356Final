<?php

require '../vendor/autoload.php';
require_once '../helpers/supabase.php';
require_once '../helpers/header.php';

$supabase = initializeSupabase();

$database_user = 'program_user';

$username   = null;
$user_fname = null;
$user_lname = null;
$email      = null;
$password   = null;
$is_observer = null;
$message    = '';

function sanitize($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

// Pull any flash error set by a previous redirect
if (isset($_SESSION['error'])) {
    $message = $_SESSION['error'];
    unset($_SESSION['error']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username   = sanitize($_POST['username']   ?? '');
    $user_fname = sanitize($_POST['firstname']  ?? '');
    $user_lname = sanitize($_POST['lastname']   ?? '');
    $email      = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password   = $_POST['password']            ?? '';
    $confirm    = $_POST['confirm_password']     ?? '';
    $is_observer = true;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif ($username === '' || $user_fname === '' || $user_lname === '' || $email === '' || $password === '') {
        $message = "All fields are required.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $response = $supabase->from($database_user)->insert([
                'user_username' => $username,
                'user_fname'    => $user_fname,
                'user_lname'    => $user_lname,
                'user_email'    => $email,
                'user_password' => $hashed_password,
                'is_observer'   => $is_observer
            ])->execute();

            if (isset($response->data['code'])) {
                $message = match($response->data['code']) {
                    '23505' => 'That email is already registered.',
                    default => 'Something went wrong, please try again.'
                };
            } else {
                header('Location: welcome.php');
                exit();
            }

        } catch (Exception $e) {
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
    <title>Sign Up – Burvents</title>
    <link href="../css/styles.css"         rel="stylesheet">
    <link href="../css/create_account.css" rel="stylesheet">
    <link href="../css/signup.css"         rel="stylesheet">
</head>
<body>

<?= makeHeader("loggedOut") ?>

<div class="page-card">
    <div class="main-content">

        <!-- ── Left: Form panel ── -->
        <div class="form-panel">
            <h2>Create Account</h2>
            <p class="form-subtitle">Join Burvents — it only takes a minute.</p>

            <!-- Error / success message -->
            <?php if ($message !== ''): ?>
                <p class="form-message error"><?= htmlspecialchars($message) ?></p>
            <?php else: ?>
                <p class="form-message" aria-live="polite"></p>
            <?php endif; ?>

            <form class="account-form" method="POST" action="" id="signup-form" novalidate>

                <!-- Username -->
                <label for="username">
                    Username
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($username ?? '') ?>"
                           placeholder="e.g. jsmith42" autocomplete="username" required>
                </label>

                <!-- First + Last Name side-by-side -->
                <div class="name-row">
                    <label for="firstname">
                        First Name
                        <input type="text" id="firstname" name="firstname"
                               value="<?= htmlspecialchars($user_fname ?? '') ?>"
                               placeholder="Jane" autocomplete="given-name" required>
                    </label>
                    <label for="lastname">
                        Last Name
                        <input type="text" id="lastname" name="lastname"
                               value="<?= htmlspecialchars($user_lname ?? '') ?>"
                               placeholder="Smith" autocomplete="family-name" required>
                    </label>
                </div>

                <!-- Email -->
                <label for="email">
                    Email Address
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($email ?? '') ?>"
                           placeholder="jane@example.com" autocomplete="email" required>
                </label>

                <!-- Password with show/hide toggle -->
                <label for="password">
                    Password
                    <div class="pw-wrapper">
                        <input type="password" id="password" name="password"
                               placeholder="Min. 8 characters" autocomplete="new-password"
                               required minlength="8">
                        <button type="button" class="toggle-pw" data-target="password"
                                aria-label="Show password">👁</button>
                    </div>
                </label>

                <!-- Strength bar -->
                <div id="strength-bar-wrap"><div id="strength-bar"></div></div>

                <!-- Confirm Password with show/hide toggle -->
                <label for="confirm_password">
                    Confirm Password
                    <div class="pw-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password"
                               placeholder="Re-enter password" autocomplete="new-password"
                               required minlength="8">
                        <button type="button" class="toggle-pw" data-target="confirm_password"
                                aria-label="Show confirm password">👁</button>
                    </div>
                </label>

                <button type="submit">Sign Up</button>
            </form>

            <p class="alt-link">
                Already have an account? <a href="login.php">Log in</a>
            </p>
        </div>

        <!-- ── Right: Decorative panel ── -->
        <div class="image-panel">
            <div class="top-icon">🎟️</div>

            <!-- Org-chart / tree decoration (from create_account.css) -->
            <div class="tree">
                <div class="main-node node">🏛️</div>
                <div class="line vertical"></div>
                <div class="line horizontal"></div>
                <div class="node-row">
                    <div class="node">🎤</div>
                    <div class="node">🖼️</div>
                    <div class="node">👥</div>
                </div>
                <div class="bottom-row">
                    <div class="mini-node"></div>
                    <div class="mini-node"></div>
                    <div class="mini-node"></div>
                    <div class="mini-node"></div>
                </div>
            </div>
        </div>

    </div><!-- /.main-content -->
</div><!-- /.page-card -->

<?php include '../helpers/footer.html'; ?>

<script>
/* ── Show/hide password ── */
document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        if (!input) return;

        input.type = input.type === 'password' ? 'text' : 'password';
        btn.textContent = input.type === 'password' ? '👁' : '🙈';
        btn.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
    });
});

/* ── Password strength indicator ── */
const pwInput = document.getElementById('password');
const bar     = document.getElementById('strength-bar');

pwInput.addEventListener('input', () => {
    const val = pwInput.value;
    let score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors = ['#e74c3c','#e67e22','#f1c40f','#2ecc71'];
    bar.style.width      = (score * 25) + '%';
    bar.style.background = colors[score - 1] || 'transparent';
});

/* ── Client-side validation before submit ── */
document.getElementById('signup-form').addEventListener('submit', e => {
    const pw  = document.getElementById('password').value;
    const cpw = document.getElementById('confirm_password').value;
    const msg = document.querySelector('.form-message');

    if (pw !== cpw) {
        e.preventDefault();
        msg.textContent  = 'Passwords do not match.';
        msg.className    = 'form-message error';
    }
});
</script>

</body>
</html>