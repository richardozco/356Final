<?php
// generic header for all pages
// has logout or login button depending on the string passed in

function makeHeader($type) {
    if ($type == "loggedIn") {
$header = '
<header class="site-header">
    <div class="logo-area">
        <h1 class="site-logo">Burvents</h1>
    </div>
    <nav class="account-actions" id="nav-header">
        <li> <a href="../index.php"> Home </a> </li>
        <li> <a href="../pages/welcome.php"> Account </a> </li>
        <li> <a href="../helpers/logout.php"> Logout </a> </li>
    </nav>
</header>
';
}
else if ($type == "loggedOut") {
    $header = '
    <header class="site-header">
    <div class="logo-area">
        <h1 class="site-logo">Burvents</h1>
    </div>
    <nav class="account-actions" id="nav-header">
        <li> <a href="../index.php"> Home </a> </li>
        <li> <a href="../pages/welcome.php"> Account </a> </li>
        <li> <a href="../pages/login.php"> Log In </a> </li>
    </nav>
</header>
';
}
return $header;
}
?>