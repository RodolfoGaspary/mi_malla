<?php
include("../../Queries/db_connect.php");

// Get the user ID
$id = $_GET['id'];

// Fetch user details from the database
$sql = "SELECT nombres, apellidos, telf, email, direccion, info FROM clientes WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
} else {
    echo "User not found.";
}
?>