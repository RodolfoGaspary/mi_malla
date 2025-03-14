<?php
// Check if the user clicked the logout button
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    // Unset all session variables and destroy the session
    session_start();
    session_unset();
    session_destroy();

    // Redirect to the login page (adjust the path as needed)
    header("Location: login.php");
    exit();
}
?>

<!-- Logout Button -->
<ul class="navbar-nav navbar-nav-scroll ms-auto" style="--bs-scroll-height: 1000px;">
<form method="POST" action="">
    <button type="submit" name="logout" class="btn btn-outline-light">Logout</button>
</form>
</ul>