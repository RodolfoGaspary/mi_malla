<?php
// Botón de cierre de sesión. Se incluye desde header.php,
// donde la sesión ya está iniciada por sesion.php.
require_once __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    csrf_verify();

    $_SESSION = [];

    // Elimina también la cookie de sesión.
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
                  $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<form method="POST" action="" class="d-flex ms-auto">
    <?= csrf_field() ?>
    <button type="submit" name="logout" class="btn btn-outline-light">Logout</button>
</form>
