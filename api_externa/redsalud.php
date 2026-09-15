<?php
/**
 * GET /api_externa/redsalud.php
 * Devuelve todos los registros de la tabla redsalud.
 *
 * Query opcionales:
 *   - limit  (int)  máximo de filas
 *   - offset (int)  desplazamiento
 *   - order  (asc|desc) orden por fecha_creacion (default: desc)
 */

require_once __DIR__ . '/bootstrap.php';

apiRequireKey();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiJson(['success' => false, 'error' => 'Método no permitido. Usa GET.'], 405);
}

try {
    $pdo = getDB();

    $limit  = isset($_GET['limit'])  ? max(1, (int)$_GET['limit'])  : null;
    $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
    $order  = strtolower($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

    $total = (int)$pdo->query('SELECT COUNT(*) FROM redsalud')->fetchColumn();

    $sql = "SELECT
                id,
                nombre,
                numero,
                conversacion,
                categoria_cliente,
                horario,
                presupuesto,
                obs,
                agente_id,
                fecha_creacion,
                fecha_actualizacion
            FROM redsalud
            ORDER BY fecha_creacion {$order}";

    if ($limit !== null) {
        $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    $data = $pdo->query($sql)->fetchAll();

    apiJson([
        'success' => true,
        'table'   => 'redsalud',
        'total'   => $total,
        'count'   => count($data),
        'limit'   => $limit,
        'offset'  => $offset,
        'order'   => strtolower($order),
        'data'    => $data,
    ]);
} catch (Exception $e) {
    apiJson([
        'success' => false,
        'error'   => 'Error al consultar redsalud: ' . $e->getMessage(),
    ], 500);
}
