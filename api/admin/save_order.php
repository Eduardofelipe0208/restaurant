<?php
/**
 * Save Product Order API
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
    $input = json_decode(file_get_contents('php://input'), true);
    $orders = $input['orders'] ?? []; // Array of {id, position}

    if (empty($orders)) {
        throw new Exception("No data provided");
    }

    $db = $pdo;
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE products SET position = ? WHERE id = ?");
    foreach ($orders as $item) {
        $stmt->execute([$item['position'], $item['id']]);
    }

    $db->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
