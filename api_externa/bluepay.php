<?php
/**
 * GET /api_externa/redsalud.php
 * Devuelve registros de redsalud unidos con clientesredsalud por numero.
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

    /*$join = "FROM redsalud r
             LEFT JOIN clientesredsalud c
               ON r.numero COLLATE utf8mb4_unicode_ci = c.numero";*/
    $tabla = "FROM bluepay";

    $total = (int)$pdo->query("SELECT COUNT(*) {$tabla}")->fetchColumn();

    $sql = "SELECT
                r.id,
                r.nombre,
                r.numero,
                r.conversacion,
                r.categoria_cliente,
                r.horario,
                r.presupuesto,
                r.obs,
                r.monto,
                r.fecha_creacion,
                r.fecha_actualizacion,
            {$tabla}
            ORDER BY r.fecha_creacion {$order}";

    if ($limit !== null) {
        $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    $data = $pdo->query($sql)->fetchAll();

    apiJson([
        'success' => true,
        'tables'  => ['redsalud', 'clientesredsalud'],
        'join'    => 'r.numero = c.numero',
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
        'error'   => 'Error al consultar Tabla: ' . $e->getMessage(),
    ], 500);
}
