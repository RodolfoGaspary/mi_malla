<?php require_once __DIR__ . '/includes/sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Dashboard · Mi Malla</title>
    <?php include 'includes/bootstrap_meta.php'; ?>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container-fluid" style="padding-top: 5rem;">
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') ?>.</h2>
    <div id="btn-nuevaProforma" class="d-grid gap-2 col-6 mx-auto mt-4">
        <a href="proforma.php" class="btn btn-primary btn-lg">Nueva Proforma</a>
    </div>
</div>
<?php include 'includes/bootstrap_end.php'; ?>
