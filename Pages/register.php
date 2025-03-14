<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
    <?php include 'includes/bootstrap_meta.php';?>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body style="background-color: #E6B85C; padding-top: 0px;">
    <div>
    <?php 
                include 'includes/header.php'; 
                if (!isAdmin()) {
                    // If the user is not an admin, redirect or display an error
                    header("Location: dashboard.php");
                    exit();
                }
    ?>
    </div>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="col-sm-10 col-md-4 col-lg-3">
            <div class="card shadow">
                <div class="card-body" style="background-color: #F5F0E6; border-radius: 10px;">
                    <img src="..." class="rounded mx-auto d-block" alt="Mallas de seguridad" title="Mi Malla">
                    <h2 class="text-center mb-4">Registrar Usuario</h2>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <input type="text" name="username" id="username" class="form-control" placeholder="Usuario" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                        </div>
                        <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
                                $username = $_POST['username'];
                                $password = $_POST['password'];

                                // Hash the password securely
                                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                                try {
                                    // Database connection
                                    include'../Queries/db_connect.php';

                                    // Insert the new user into the database
                                    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
                                    $stmt->execute([$username, $passwordHash]);

                                    echo "<p style='color:green;'>Usuario registrado correctamente!</p>";

                                } catch (PDOException $e) {
                                    // Handle errors (e.g., duplicate username)
                                    if ($e->getCode() == 23000) { // Integrity constraint violation (e.g., unique key conflict)
                                        echo "<p style='color:red;'>Error: Usuario ya registrado.</p>";
                                    } else {
                                        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                                    }
                                }
                            }
                        ?>
                        <button type="submit" class="btn btn-primary w-100" name="register">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    <?php include 'includes/bootstrap_end.php';?>