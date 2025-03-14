<?php
include("../../Queries/db_connect.php");

// Get the search query
$query = $_GET['query'];

// Search for matching names in the database
$sql = "SELECT id, nombres, apellidos FROM clientes WHERE nombres LIKE :query OR apellidos LIKE :query";
$stmt = $pdo->prepare($sql);
$stmt->execute(['query' => "%$query%"]);

if ($stmt->rowCount() > 0) {
    echo "<ul class='list-group'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li class='result-item col-md-2 list-group-item' data-id='{$row['id']}'>{$row['nombres']} {$row['apellidos']}</li>";
    };
    echo"</ul>";
} else {
    echo "No results found.";
}
?>