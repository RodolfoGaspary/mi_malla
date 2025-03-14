<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
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