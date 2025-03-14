<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <?php include 'includes/bootstrap_meta.php';?>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<?php include 'includes/header.php';?>
<div class="container-fluid">
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
    <div id="btn-nuevaProforma" class="d-grid gap-2 col-6 mx-auto">
        <a href="proforma.php" class="btn btn-primary btn-lg">Nueva Proforma</a>
    </div>
</div>
    <?php include 'includes/bootstrap_end.php';?>

