<?php
/**
 * Unified API Router
 * Digital Menu Restaurant PWA
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_menu':
            // Get Categories
            $catStmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY priority ASC");
            $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get Products
            $prodStmt = $pdo->query("
                SELECT p.*, GROUP_CONCAT(
                    JSON_OBJECT('name', v.name, 'price', v.additional_price_usd)
                ) as variants
                FROM products p
                LEFT JOIN product_variants v ON p.id = v.product_id
                WHERE p.is_available = 1
                GROUP BY p.id
                ORDER BY p.category_id, p.position ASC
            ");
            $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process variants JSON
            foreach ($products as &$product) {
                if ($product['variants']) {
                    $product['variants'] = json_decode('[' . $product['variants'] . ']', true);
                } else {
                    $product['variants'] = [];
                }
            }
            
            echo json_encode([
                'status' => 'success',
                'categories' => $categories,
                'products' => $products
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
