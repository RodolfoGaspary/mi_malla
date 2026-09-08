<?php
// Devuelve los datos de un cliente. Endpoint AJAX — requiere sesión activa.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Identificador inválido']);
    exit();
}

try {
    $sql = "SELECT nombres, apellidos, telf, email, direccion, info
              FROM clientes
             WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
} catch (PDOException $e) {
    error_log('get_user: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar el cliente']);
    exit();
}

if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'Cliente no encontrado']);
    exit();
}

echo json_encode($row, JSON_UNESCAPED_UNICODE);
