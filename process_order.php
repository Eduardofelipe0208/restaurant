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
$payment_method = $input['payment_method'] ?? 'Pago Móvil';

// Calcular totales
$total_usd = 0;
$items_text = "";
foreach($items as $item) {
    $price = getProductPrice($item['id']);
    
    // Fetch product name for the message
    $pStmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
    $pStmt->execute([$item['id']]);
    $pName = $pStmt->fetchColumn() ?: "Producto #".$item['id'];
    
    // Add variants weight to price and text
    $variant_text = "";
    if (isset($item['variants']) && is_array($item['variants'])) {
        foreach ($item['variants'] as $v) {
            // In a real app we'd verify price in DB, but for now we follow the simple structure
            $variant_text .= " (Extra: {$v})";
        }
    }
    
    $total_usd += $price * $item['qty'];
    $items_text .= "{$item['qty']}x {$pName}{$variant_text}\n";
}

$total_bs = $total_usd * $settings['exchange_rate'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (table_number, total_usd, total_bs, payment_method, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$table, $total_usd, $total_bs, $payment_method]);
    $order_id = $pdo->lastInsertId();

    // Insert into order_items (Normalization)
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price_usd, subtotal_usd, variants_json) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach($items as $item) {
        $price = getProductPrice($item['id']);
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
            'table' => $table,
            'items_text' => $items_text,
            'total_usd' => $total_usd,
            'total_bs' => $total_bs,
            'payment_method' => $payment_method
        ])
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
