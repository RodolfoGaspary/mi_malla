<?php
session_start();
$message = ""; // Message to display after login attempt

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    // Redirect to the dashboard page
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <?php include 'includes/bootstrap_meta.php';?>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh; background-color: #5B7C99;">
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body" style="background-color: #F5F0E6; border-radius: 10px;">
                        <img src="..." class="rounded mx-auto d-block" alt="Mallas de seguridad" title="Mi Malla">
                        <h2 class="text-center mb-4">Iniciar Sesion</h2>
                        <?php echo $message; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <input type="text" name="username" id="username" class="form-control" placeholder="Usuario" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
                        </form>
                        <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
                                $username = $_POST['username'];
                                $password = $_POST['password'];

                                try {
                                    // Database connection
                                    include'../Queries/db_connect.php';

                                    // Fetch user's hashed password
                                    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE username = ?");
                                    $stmt->execute([$username]);
                                    $user = $stmt->fetch();

                                    if ($user && password_verify($password, $user['password_hash'])) {
                                        // Login successful
                                        // Fetch the user's role (assuming it's stored in your database)
                                        $stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
                                        $stmt->execute([$username]);
                                        $role = $stmt->fetchColumn();

                                        // Fetch the user's nombre (assuming it's stored in your database)
                                        $stmt = $pdo->prepare("SELECT nombre FROM users WHERE username = ?");
                                        $stmt->execute([$username]);
                                        $nombre = $stmt->fetchColumn();

                                        // Fetch the user's id (assuming it's stored in your database)
                                        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                                        $stmt->execute([$username]);
                                        $id = $stmt->fetchColumn();

                                        // Start session and store user information
                                        session_start();
                                        session_regenerate_id(true); // Secure the session
                                        $_SESSION['username'] = $username;
                                        $_SESSION['role'] = $role; // Store the user's role
                                        $_SESSION['nombre'] = $nombre; // Store the user's name
                                        $_SESSION['id'] = $id; // Store the user's id

                                        // Redirect to the dashboard
                                        session_regenerate_id(true);
                                        header("Location: dashboard.php");
                                        exit();
                                    } else {
                                        // Invalid credentials
                                        $message = "<p style='color:red;'>Invalid username or password.</p>";
                                    }
                                } catch (PDOException $e) {
                                    $message = "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    
    <?php include 'includes/bootstrap_end.php';?>