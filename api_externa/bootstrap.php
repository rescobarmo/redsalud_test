<?php
/**
 * Bootstrap compartido para la API externa.
 * Headers CORS + validación de API Key + respuesta JSON.
 */

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function apiJson(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function apiRequireKey(): void
{
    $expected = getenv('EXTERNAL_API_KEY') ?: '';

    if ($expected === '') {
        apiJson([
            'success' => false,
            'error'   => 'EXTERNAL_API_KEY no está configurada en el servidor (.env)',
        ], 500);
    }

    $provided = $_SERVER['HTTP_X_API_KEY']
        ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? null)
        ?? ($_GET['api_key'] ?? '');

    if (is_string($provided) && str_starts_with($provided, 'Bearer ')) {
        $provided = substr($provided, 7);
    }

    if (!is_string($provided) || !hash_equals($expected, $provided)) {
        apiJson([
            'success' => false,
            'error'   => 'API Key inválida o ausente. Envía el header X-API-Key.',
        ], 401);
    }
}
