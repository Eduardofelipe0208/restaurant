<?php
/** Admin Products CRUD */
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');
$authHeader = getallheaders()['Authorization'] ?? getallheaders()['authorization'] ?? '';
$userData = JWT::decode(str_replace('Bearer ', '', $authHeader));
if (!$userData) { http_response_code(401); exit; }
$method = $_SERVER['REQUEST_METHOD']; $db = $pdo;
try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id']) && isset($_GET['with_variants'])) {
                $pStmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                $pStmt->execute([$_GET['id']]);
                $product = $pStmt->fetch(PDO::FETCH_ASSOC);
                
                $vStmt = $db->prepare("SELECT * FROM product_variants WHERE product_id = ?");
                $vStmt->execute([$_GET['id']]);
                $product['variants'] = $vStmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode(['status' => 'success', 'data' => $product]);
            } else {
                $stmt = $db->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
                echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            }
            break;
            
        case 'POST':
            $db->beginTransaction();
            $i = sanitize(json_decode(file_get_contents('php://input'), true));
            $sql = "INSERT INTO products (category_id, name, description, price_usd, image_url, estimated_time, is_available) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $db->prepare($sql)->execute([$i['category_id'], $i['name'], $i['description'] ?? '', $i['price_usd'], $i['image_url'] ?? '', $i['estimated_time'] ?? 15, $i['is_available'] ?? 1]);
            $productId = $db->lastInsertId();
            
            if (isset($i['variants']) && is_array($i['variants'])) {
                $vSql = "INSERT INTO product_variants (product_id, name, additional_price_usd) VALUES (?, ?, ?)";
                $vStmt = $db->prepare($vSql);
                foreach ($i['variants'] as $v) {
                    $vStmt->execute([$productId, $v['name'], $v['price']]);
                }
            }
            $db->commit();
            echo json_encode(['status' => 'success', 'id' => $productId]); 
            break;
            
        case 'PUT':
            $db->beginTransaction();
            $i = sanitize(json_decode(file_get_contents('php://input'), true));
            $sql = "UPDATE products SET category_id = ?, name = ?, description = ?, price_usd = ?, image_url = ?, estimated_time = ?, is_available = ? WHERE id = ?";
            $db->prepare($sql)->execute([$i['category_id'], $i['name'], $i['description'], $i['price_usd'], $i['image_url'], $i['estimated_time'], $i['is_available'], $i['id']]);
            
            // Delete old variants and insert new ones
            $db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$i['id']]);
            if (isset($i['variants']) && is_array($i['variants'])) {
                $vSql = "INSERT INTO product_variants (product_id, name, additional_price_usd) VALUES (?, ?, ?)";
                $vStmt = $db->prepare($vSql);
                foreach ($i['variants'] as $v) {
                    $vStmt->execute([$i['id'], $v['name'], $v['price']]);
                }
            }
            $db->commit();
            echo json_encode(['status' => 'success']); 
            break;
            
        case 'DELETE':
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$_GET['id'] ?? 0]);
            echo json_encode(['status' => 'success']); 
            break;
    }
} catch (Exception $e) { http_response_code(500); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
