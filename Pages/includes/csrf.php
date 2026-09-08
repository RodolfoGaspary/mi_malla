<?php
// Token CSRF por sesión. Incluir después de sesion.php (o de session_start()).

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'];
}

/** Campo oculto listo para insertar dentro de un <form>. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
         . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/** Valida el token recibido; corta la petición si no coincide. */
function csrf_verify(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf_token'] ?? '', $sent)) {
        http_response_code(419);
        exit('Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
    }
}
