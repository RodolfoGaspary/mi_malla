<?php 
    include 'includes/sesion.php';
?>
<nav class="navbar navbar-expand-md fixed-top" style="background-color: #CC6B49;">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php" style="color:#F5F0E6;">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse vw-100" id="navbarScroll">
            <ul class="navbar-nav navbar-nav-scroll ms-auto" style="--bs-scroll-height: 1000px;">
                <li class="nav-item">
                <a class="nav-link" aria-current="page" href="proforma.php" style="color:#F5F0E6;">Proformar</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#" style="color:#F5F0E6;">Proformas</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#" style="color:#F5F0E6;">Instalaciones</a>
                </li>
                <?php 
                    include("admin_check.php");
                    if (isAdmin()) { // Check if the user is an admin
                        echo '
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:#F5F0E6; font-weight: bold;">
                                Admin
                            </a>
                            <ul class="dropdown-menu" style="background-color:#CC6B49;">
                                <li><a class="dropdown-item" href="register.php">Registrar Usuario</a></li>
                                <li><a class="dropdown-item" href="prueba.php">Cambiar Contraseña</a></li>
                            </ul>
                        </li>
                        ';
                    }
                ?>                
            </ul>
            <?php include 'includes/logout.php'; ?>
        </div>
    </div>
</nav>
