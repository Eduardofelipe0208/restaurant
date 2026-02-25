<?php
/**
 * Admin Categories API (CRUD)
 * Digital Menu Restaurant PWA
 */

require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

// Verify JWT
$authHeader = getallheaders()['Authorization'] ?? getallheaders()['authorization'] ?? '';
$userData = JWT::decode(str_replace('Bearer ', '', $authHeader));

if (!$userData) {
    http_response_code(401);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$db = $pdo;

try {
    switch ($method) {
        case 'GET':
            $stmt = $db->query("SELECT * FROM categories ORDER BY priority ASC");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'POST':
            $input = sanitize(json_decode(file_get_contents('php://input'), true));
            $sql = "INSERT INTO categories (name, priority, icon, is_active) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$input['name'], $input['priority'] ?? 0, $input['icon'] ?? '', $input['is_active'] ?? 1]);
            echo json_encode(['status' => 'success', 'id' => $db->lastInsertId()]);
            break;

        case 'PUT':
            $input = sanitize(json_decode(file_get_contents('php://input'), true));
            $sql = "UPDATE categories SET name = ?, priority = ?, icon = ?, is_active = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$input['name'], $input['priority'], $input['icon'], $input['is_active'], $input['id']]);
            echo json_encode(['status' => 'success']);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
