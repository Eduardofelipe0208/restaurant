<?php
/** Admin Settings Update */
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
header('Content-Type: application/json');
$userData = JWT::decode(str_replace('Bearer ', '', getallheaders()['Authorization'] ?? ''));
if (!$userData) { http_response_code(401); exit; }
try {
    $i = json_decode(file_get_contents('php://input'), true);
    $db = $pdo;
    $sql = "UPDATE settings SET restaurant_name = ?, whatsapp_number = ?, exchange_rate = ?, primary_color = ?, logo_url = ?, pago_movil_data = ? WHERE id = 1";
    $db->prepare($sql)->execute([$i['restaurant_name'], $i['whatsapp_number'], $i['exchange_rate'], $i['primary_color'], $i['logo_url'], json_encode($i['pago_movil_data'])]);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { http_response_code(500); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
