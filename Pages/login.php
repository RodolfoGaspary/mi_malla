<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/csrf.php';

$message = '';

if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    try {
        require_once __DIR__ . '/includes/db.php';

        // Una sola consulta trae todo lo que la sesión necesita.
        $stmt = $pdo->prepare(
            "SELECT id, username, nombre, role, password_hash
               FROM users
              WHERE username = :username
              LIMIT 1"
        );
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['id']       = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre']   = $user['nombre'] ?? $user['username'];
            $_SESSION['role']     = $user['role'] ?? 'user';

            // Token nuevo para la sesión recién creada.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header('Location: dashboard.php');
            exit();
        }

        // Mismo mensaje para usuario inexistente y clave incorrecta:
        // no revela qué usuarios existen.
        $message = '<div class="alert alert-danger">Usuario o contraseña incorrectos.</div>';
    } catch (PDOException $e) {
        error_log('login: ' . $e->getMessage());
        $message = '<div class="alert alert-danger">No se pudo iniciar sesión. Inténtalo más tarde.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Iniciar sesión · Mi Malla</title>
    <?php include 'includes/bootstrap_meta.php'; ?>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh; background-color: #5B7C99;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body" style="background-color: #F5F0E6; border-radius: 10px;">
                    <h2 class="text-center mb-4">Iniciar Sesión</h2>
                    <?= $message ?>
                    <form method="POST" action="">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" name="username" id="username" class="form-control" autocomplete="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="login">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/bootstrap_end.php'; ?>
