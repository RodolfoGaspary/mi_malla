<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/admin_check.php';

requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $nombre   = trim($_POST['nombre'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $role     = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

    if ($username === '' || $nombre === '') {
        $message = '<div class="alert alert-danger">Usuario y nombre son obligatorios.</div>';
    } elseif (strlen($password) < 8) {
        $message = '<div class="alert alert-danger">La contraseña debe tener al menos 8 caracteres.</div>';
    } else {
        try {
            require_once __DIR__ . '/includes/db.php';

            // Se guardan nombre y role: login.php los espera en la sesión.
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, nombre, role, password_hash)
                 VALUES (:username, :nombre, :role, :hash)"
            );
            $stmt->execute([
                'username' => $username,
                'nombre'   => $nombre,
                'role'     => $role,
                'hash'     => password_hash($password, PASSWORD_BCRYPT),
            ]);

            $message = '<div class="alert alert-success">Usuario registrado correctamente.</div>';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $message = '<div class="alert alert-danger">Ese usuario ya existe.</div>';
            } else {
                error_log('register: ' . $e->getMessage());
                $message = '<div class="alert alert-danger">No se pudo registrar el usuario.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Registrar usuario · Mi Malla</title>
    <?php include 'includes/bootstrap_meta.php'; ?>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body style="background-color: #E6B85C;">
<?php include 'includes/header.php'; ?>
<div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
    <div class="col-sm-10 col-md-5 col-lg-4">
        <div class="card shadow">
            <div class="card-body" style="background-color: #F5F0E6; border-radius: 10px;">
                <h2 class="text-center mb-4">Registrar Usuario</h2>
                <?= $message ?>
                <form method="POST" action="">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre para mostrar</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" minlength="8" autocomplete="new-password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select name="role" id="role" class="form-select">
                            <option value="user">Usuario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="register">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/bootstrap_end.php'; ?>
