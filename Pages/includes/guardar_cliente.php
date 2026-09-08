<?php
// Alta o actualización de cliente. Endpoint AJAX — requiere sesión y CSRF.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

csrf_verify();

$campos = [
    'nombres'   => trim($_POST['nombresProforma']   ?? ''),
    'apellidos' => trim($_POST['apellidosProforma'] ?? ''),
    'telf'      => trim($_POST['celularProforma']   ?? ''),
    'email'     => trim($_POST['emailProforma']     ?? ''),
    'direccion' => trim($_POST['direccionProforma'] ?? ''),
    'info'      => trim($_POST['infoProforma']      ?? ''),
];

$faltantes = [];
foreach (['nombres', 'apellidos', 'telf', 'email', 'direccion'] as $obligatorio) {
    if ($campos[$obligatorio] === '') {
        $faltantes[] = $obligatorio;
    }
}

if ($faltantes) {
    http_response_code(422);
    echo json_encode(['error' => 'Faltan campos obligatorios', 'campos' => $faltantes]);
    exit();
}

if (!filter_var($campos['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['error' => 'Correo electrónico inválido', 'campos' => ['email']]);
    exit();
}

try {
    // Mismo criterio de identidad que la versión original:
    // coincide por nombre completo o por teléfono.
    $stmt = $pdo->prepare(
        "SELECT id FROM clientes
          WHERE (nombres = :nombres AND apellidos = :apellidos)
             OR telf = :telf
          LIMIT 1"
    );
    $stmt->execute([
        'nombres'   => $campos['nombres'],
        'apellidos' => $campos['apellidos'],
        'telf'      => $campos['telf'],
    ]);
    $existente = $stmt->fetch();

    if ($existente) {
        $campos['id'] = (int) $existente['id'];
        $pdo->prepare(
            "UPDATE clientes
                SET nombres = :nombres, apellidos = :apellidos, telf = :telf,
                    email = :email, direccion = :direccion, info = :info
              WHERE id = :id"
        )->execute($campos);

        echo json_encode(['ok' => true, 'id' => $campos['id'], 'accion' => 'actualizado']);
        exit();
    }

    $pdo->prepare(
        "INSERT INTO clientes (nombres, apellidos, telf, email, direccion, info)
         VALUES (:nombres, :apellidos, :telf, :email, :direccion, :info)"
    )->execute($campos);

    echo json_encode([
        'ok'     => true,
        'id'     => (int) $pdo->lastInsertId(),
        'accion' => 'registrado',
    ]);
} catch (PDOException $e) {
    error_log('guardar_cliente: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo guardar el cliente']);
}
