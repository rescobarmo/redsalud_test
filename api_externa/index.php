<?php
/**
 * API Externa RedSalud — documentación de endpoints.
 * GET /api_externa/
 */

require_once __DIR__ . '/bootstrap.php';

apiJson([
    'success' => true,
    'name'    => 'API Externa RedSalud',
    'version' => '1.0.0',
    'auth'    => 'Header X-API-Key (o ?api_key=...)',
    'endpoints' => [
        [
            'method'      => 'GET',
            'path'        => '/api_externa/redsalud.php',
            'description' => 'Devuelve todos los registros de la tabla redsalud',
            'auth'        => true,
        ],
        [
            'method'      => 'GET',
            'path'        => '/api_externa/redsalud.php?limit=100&offset=0',
            'description' => 'Mismos datos con paginación opcional',
            'auth'        => true,
        ],
    ],
]);
