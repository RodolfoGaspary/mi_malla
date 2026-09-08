<?php
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/admin_check.php';
?>
<nav class="navbar navbar-expand-md fixed-top" style="background-color: #CC6B49;">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php" style="color:#F5F0E6;">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Abrir navegación">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse vw-100" id="navbarScroll">
            <ul class="navbar-nav navbar-nav-scroll ms-auto" style="--bs-scroll-height: 1000px;">
                <li class="nav-item">
                    <a class="nav-link" href="proforma.php" style="color:#F5F0E6;">Proformar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="proformas.php" style="color:#F5F0E6;">Proformas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="instalaciones.php" style="color:#F5F0E6;">Instalaciones</a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:#F5F0E6; font-weight: bold;">
                        Admin
                    </a>
                    <ul class="dropdown-menu" style="background-color:#CC6B49;">
                        <li><a class="dropdown-item" href="register.php">Registrar Usuario</a></li>
                        <li><a class="dropdown-item" href="cambiar_password.php">Cambiar Contraseña</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <?php require __DIR__ . '/logout.php'; ?>
        </div>
    </div>
</nav>
