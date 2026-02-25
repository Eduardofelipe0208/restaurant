<?php
require 'includes/db.php';
require 'includes/functions.php';

header('Content-Type: application/json');

// Fetch settings for exchange rate and whatsapp number
try {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['error' => 'DB Error Settings']));
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    die(json_encode(['error' => 'Invalid Input']));
}

$items = $input['items'] ?? [];
$table = $input['table'] ?? 0;

// Calcular totales
$total_usd = 0;
$items_text = '';
foreach($items as $item) {
    $price = getProductPrice($item['id']);
    $total_usd += $price * $item['qty'];
    
    // Fetch product name for the message
    $pStmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
    $pStmt->execute([$item['id']]);
    $pName = $pStmt->fetchColumn() ?: "Producto #".$item['id'];
    
    $variants = isset($item['variants']) && !empty($item['variants']) ? implode(', ', $item['variants']) : '';
    $items_text .= "{$item['qty']}x {$pName} {$variants} - ".formatCurrency($price, $settings['exchange_rate'])."\n";
}

$total_bs = $total_usd * $settings['exchange_rate'];

$message = "¡Hola! Nuevo pedido:\n\n{$items_text}\nTotal: ".formatCurrency($total_usd, $settings['exchange_rate'])."\nMesa: {$table}\nPago: Pendiente";

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (table_number, items, total_usd, total_bs, whatsapp_message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$table, json_encode($items), $total_usd, $total_bs, $message]);
    $order_id = $pdo->lastInsertId();

    // Insert into order_items (Normalization)
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price_usd, subtotal_usd, variants_json) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach($items as $item) {
        $price = getProductPrice($item['id']);
        
        // Fetch product name again for DB consistency
        $pStmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $pStmt->execute([$item['id']]);
        $pName = $pStmt->fetchColumn() ?: "Producto #".$item['id'];
        
        $itemStmt->execute([
            $order_id,
            $item['id'],
            $pName,
            $item['qty'],
            $price,
            $price * $item['qty'],
            json_encode($item['variants'] ?? [])
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'whatsapp_link' => generateWhatsAppLink([
            'whatsapp' => $settings['whatsapp_number'],
            'message' => $message
        ])
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
