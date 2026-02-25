<?php
/**
 * Admin Login API
 */
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

try {
    $db = $pdo;
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $token = JWT::encode([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'exp' => time() + (60 * 60 * 24)
        ]);
        echo json_encode(['status' => 'success', 'token' => $token]);
    } else {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e.getMessage()]);
}
