<?php
function sanitize($data) { return htmlspecialchars(strip_tags(trim($data))); }
function formatCurrency($usd, $rate) { 
    $bs = $usd * $rate; 
    return number_format($usd,2).' USD (Bs. '.number_format($bs,2).')'; 
}
function generateWhatsAppLink($data) {
    // Expected components: table, items_text, total_usd, total_bs, payment_method
    $header = "*Resumen del Pedido - Mesa {$data['table']}*\n";
    $separator = "-------------------------\n";
    $body = $data['items_text'];
    $footer = "Total: $".number_format($data['total_usd'], 2)." (Bs. ".number_format($data['total_bs'], 2).")\n";
    $footer .= "Método: {$data['payment_method']}";
    
    $fullMessage = $header . $separator . $body . $separator . $footer;
    return "https://wa.me/{$data['whatsapp']}/?text=" . urlencode($fullMessage);
}

function getProductPrice($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT price_usd FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product ? $product['price_usd'] : 0;
}

function countOrdersToday() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
    return $stmt->fetchColumn();
}

function todaySales() {
    global $pdo;
    $stmt = $pdo->query("SELECT SUM(total_usd) FROM orders WHERE DATE(created_at) = CURDATE()");
    return number_format($stmt->fetchColumn() ?: 0, 2);
}
?>
