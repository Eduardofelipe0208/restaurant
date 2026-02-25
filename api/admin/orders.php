<?php
/**
 * Admin Orders API
 * Digital Menu Restaurant PWA
 */

require_once '../../includes/db.php';
require_once '../../includes/auth.php';

header('Content-Type: application/json');

$authHeader = getallheaders()['Authorization'] ?? getallheaders()['authorization'] ?? '';
$userData = JWT::decode(str_replace('Bearer ', '', $authHeader));

if (!$userData) {
    http_response_code(401);
    exit;
}

try {
    $db = $pdo;
    $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 100");
    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
