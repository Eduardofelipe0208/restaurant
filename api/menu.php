<?php
/** Menu API */
require_once '../includes/db.php';
header('Content-Type: application/json');
try {
    $db = $pdo;
    $catStmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY priority ASC");
    $categories = $catStmt->fetchAll();
    
    $prodStmt = $db->query("SELECT p.*, GROUP_CONCAT(JSON_OBJECT('name', v.name, 'price', v.additional_price_usd)) as variants FROM products p LEFT JOIN product_variants v ON p.id = v.product_id WHERE p.is_available = 1 GROUP BY p.id ORDER BY p.category_id, p.position ASC");
    $products = $prodStmt->fetchAll();
    foreach ($products as &$p) { $p['variants'] = $p['variants'] ? json_decode('[' . $p['variants'] . ']', true) : []; }
    
    echo json_encode(['status' => 'success', 'data' => ['categories' => $categories, 'products' => $products]]);
} catch (Exception $e) { http_response_code(500); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
