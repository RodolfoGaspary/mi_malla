<?php
// Reemplaza prueba.php, que estaba vacío pero enlazado desde el menú Admin.
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/csrf.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar'])) {
    csrf_verify();

    $actual  = (string) ($_POST['actual'] ?? '');
    $nueva   = (string) ($_POST['nueva'] ?? '');
    $repetir = (string) ($_POST['repetir'] ?? '');

    if (strlen($nueva) < 8) {
        $message = '<div class="alert alert-danger">La nueva contraseña debe tener al menos 8 caracteres.</div>';
    } elseif (!hash_equals($nueva, $repetir)) {
        $message = '<div class="alert alert-danger">Las contraseñas nuevas no coinciden.</div>';
    } else {
        try {
            require_once __DIR__ . '/includes/db.php';

            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :id");
            $stmt->execute(['id' => (int) $_SESSION['id']]);
            $row = $stmt->fetch();

            if (!$row || !password_verify($actual, $row['password_hash'])) {
                $message = '<div class="alert alert-danger">La contraseña actual no es correcta.</div>';
            } else {
                $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id")
                    ->execute([
                        'hash' => password_hash($nueva, PASSWORD_BCRYPT),
                        'id'   => (int) $_SESSION['id'],
                    ]);

                session_regenerate_id(true);
                $message = '<div class="alert alert-success">Contraseña actualizada.</div>';
            }
        } catch (PDOException $e) {
            error_log('cambiar_password: ' . $e->getMessage());
            $message = '<div class="alert alert-danger">No se pudo actualizar la contraseña.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Cambiar contraseña · Mi Malla</title>
    <?php include 'includes/bootstrap_meta.php'; ?>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="padding-top: 5rem; max-width: 32rem;">
    <h2 class="mb-4">Cambiar contraseña</h2>
    <?= $message ?>
    <form method="POST" action="">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="actual" class="form-label">Contraseña actual</label>
            <input type="password" name="actual" id="actual" class="form-control" autocomplete="current-password" required>
        </div>
        <div class="mb-3">
            <label for="nueva" class="form-label">Nueva contraseña</label>
            <input type="password" name="nueva" id="nueva" class="form-control" minlength="8" autocomplete="new-password" required>
        </div>
        <div class="mb-3">
            <label for="repetir" class="form-label">Repetir nueva contraseña</label>
            <input type="password" name="repetir" id="repetir" class="form-control" minlength="8" autocomplete="new-password" required>
        </div>
        <button type="submit" name="cambiar" class="btn btn-primary">Actualizar</button>
    </form>
</div>
<?php include 'includes/bootstrap_end.php'; ?>
