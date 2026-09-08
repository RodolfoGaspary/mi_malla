<?php
// Guarda una proforma con sus áreas. Reemplaza el archivo anterior,
// que contenía por error una copia de la lógica de login.
//
// Se incluye desde proforma.php; deja $proformaMensaje y $proformaId
// para que la página los muestre.

require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';

$proformaMensaje = '';
$proformaId      = null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['save'])) {
    return;
}

csrf_verify();

$cliente = [
    'nombres'   => trim($_POST['nombresProforma']   ?? ''),
    'apellidos' => trim($_POST['apellidosProforma'] ?? ''),
    'telf'      => trim($_POST['celularProforma']   ?? ''),
    'email'     => trim($_POST['emailProforma']     ?? ''),
    'direccion' => trim($_POST['direccionProforma'] ?? ''),
    'info'      => trim($_POST['infoProforma']      ?? ''),
];

$tipos      = (array) ($_POST['Tipo']      ?? []);
$ubicaciones= (array) ($_POST['Ubicacion'] ?? []);
$anchos     = (array) ($_POST['Ancho']     ?? []);
$alturas    = (array) ($_POST['Altura']    ?? []);

// Todas las filas de área deben venir completas y alineadas.
$total = count($tipos);
if ($total === 0
    || count($ubicaciones) !== $total
    || count($anchos) !== $total
    || count($alturas) !== $total) {
    $proformaMensaje = '<div class="alert alert-danger">Revisa las áreas: faltan datos.</div>';
    return;
}

$areas = [];
foreach (range(0, $total - 1) as $i) {
    $ancho  = str_replace(',', '.', trim((string) $anchos[$i]));
    $altura = str_replace(',', '.', trim((string) $alturas[$i]));

    if (!is_numeric($ancho) || !is_numeric($altura)
        || (float) $ancho <= 0 || (float) $altura <= 0) {
        $proformaMensaje = '<div class="alert alert-danger">'
            . 'Ancho y altura deben ser números mayores que cero (fila ' . ($i + 1) . ').'
            . '</div>';
        return;
    }

    $areas[] = [
        'tipo'      => trim((string) $tipos[$i]),
        'ubicacion' => trim((string) $ubicaciones[$i]),
        'ancho'     => (float) $ancho,
        'altura'    => (float) $altura,
        'metros2'   => round((float) $ancho * (float) $altura, 4),
    ];
}

try {
    $pdo->beginTransaction();

    // 1. Cliente: se reutiliza si ya existe, se actualiza con los datos del formulario.
    $stmt = $pdo->prepare(
        "SELECT id FROM clientes
          WHERE (nombres = :nombres AND apellidos = :apellidos) OR telf = :telf
          LIMIT 1"
    );
    $stmt->execute([
        'nombres'   => $cliente['nombres'],
        'apellidos' => $cliente['apellidos'],
        'telf'      => $cliente['telf'],
    ]);
    $fila = $stmt->fetch();

    if ($fila) {
        $clienteId = (int) $fila['id'];
        $cliente['id'] = $clienteId;
        $pdo->prepare(
            "UPDATE clientes
                SET nombres = :nombres, apellidos = :apellidos, telf = :telf,
                    email = :email, direccion = :direccion, info = :info
              WHERE id = :id"
        )->execute($cliente);
    } else {
        $pdo->prepare(
            "INSERT INTO clientes (nombres, apellidos, telf, email, direccion, info)
             VALUES (:nombres, :apellidos, :telf, :email, :direccion, :info)"
        )->execute($cliente);
        $clienteId = (int) $pdo->lastInsertId();
    }

    // 2. Cabecera de la proforma.
    $pdo->prepare(
        "INSERT INTO proformas (cliente_id, user_id, total_m2, creado_en)
         VALUES (:cliente_id, :user_id, :total_m2, NOW())"
    )->execute([
        'cliente_id' => $clienteId,
        'user_id'    => (int) $_SESSION['id'],
        'total_m2'   => round(array_sum(array_column($areas, 'metros2')), 4),
    ]);
    $proformaId = (int) $pdo->lastInsertId();

    // 3. Detalle de áreas.
    $insertArea = $pdo->prepare(
        "INSERT INTO proforma_areas (proforma_id, tipo, ubicacion, ancho, altura, metros2)
         VALUES (:proforma_id, :tipo, :ubicacion, :ancho, :altura, :metros2)"
    );
    foreach ($areas as $area) {
        $area['proforma_id'] = $proformaId;
        $insertArea->execute($area);
    }

    $pdo->commit();

    $proformaMensaje = '<div class="alert alert-success">'
        . 'Proforma #' . $proformaId . ' generada con '
        . count($areas) . ' área(s).</div>';
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('guardar_proforma: ' . $e->getMessage());
    $proformaMensaje = '<div class="alert alert-danger">No se pudo generar la proforma.</div>';
}
