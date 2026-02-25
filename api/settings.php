<?php
/** Settings API */
require_once '../includes/db.php';
header('Content-Type: application/json');
try {
    $db = $pdo;
    $stmt = $db->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch();
    if ($settings) {
        unset($settings['id']);
        if ($settings['pago_movil_data']) $settings['pago_movil_data'] = json_decode($settings['pago_movil_data'], true);
        echo json_encode(['status' => 'success', 'data' => $settings]);
    } else { echo json_encode(['status' => 'error', 'message' => 'Settings not found']); }
} catch (Exception $e) { http_response_code(500); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
