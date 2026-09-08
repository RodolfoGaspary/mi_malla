<?php
// Autocompletado de clientes. Endpoint AJAX — requiere sesión activa.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/db.php';

$query = trim($_GET['query'] ?? '');

// Evita barridos de la tabla completa con consultas de 1 carácter.
if (mb_strlen($query) < 2) {
    exit();
}

try {
    $sql = "SELECT id, nombres, apellidos
              FROM clientes
             WHERE nombres LIKE :query OR apellidos LIKE :query
             ORDER BY nombres, apellidos
             LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['query' => '%' . $query . '%']);
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('search_user: ' . $e->getMessage());
    http_response_code(500);
    exit('<p class="text-danger">No se pudo completar la búsqueda.</p>');
}

if (!$rows) {
    echo '<p class="text-muted mb-0">Sin resultados.</p>';
    exit();
}

echo '<ul class="list-group">';
foreach ($rows as $row) {
    printf(
        '<li class="result-item col-md-2 list-group-item" data-id="%d">%s %s</li>',
        (int) $row['id'],
        htmlspecialchars($row['nombres'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($row['apellidos'], ENT_QUOTES, 'UTF-8')
    );
}
echo '</ul>';
