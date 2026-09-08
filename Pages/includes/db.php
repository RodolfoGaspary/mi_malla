<?php
// Punto único de acceso a la conexión PDO.
// Mantiene la ruta a Queries/db_connect.php en un solo lugar.

require_once __DIR__ . '/../../Queries/db_connect.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('db_connect.php no definió $pdo');
    http_response_code(500);
    exit('Error de configuración.');
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
