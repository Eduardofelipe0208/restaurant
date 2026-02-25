<?php
/** Admin Settings Update */
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
header('Content-Type: application/json');
$userData = JWT::decode(str_replace('Bearer ', '', getallheaders()['Authorization'] ?? ''));
if (!$userData) { http_response_code(401); exit; }
try {
    $i = $_POST;
    $logoUrl = null;

    // Handle Logo Upload
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $tmpName = $_FILES['logo_file']['tmp_name'];
        $newName = 'logo_' . time() . '.webp';
        $destPath = $uploadDir . $newName;

        // Simple WebP conversion if GD is available
        $img = null;
        $info = getimagesize($tmpName);
        if ($info['mime'] == 'image/jpeg') $img = imagecreatefromjpeg($tmpName);
        elseif ($info['mime'] == 'image/png') $img = imagecreatefrompng($tmpName);
        
        if ($img) {
            imagewebp($img, $destPath, 80);
            imagedestroy($img);
            $logoUrl = 'assets/uploads/' . $newName;
        } else {
            // Fallback: move as is if not jpeg/png
            move_uploaded_file($tmpName, $destPath);
            $logoUrl = 'assets/uploads/' . $newName;
        }
    }

    $db = $pdo;
    $sql = "UPDATE settings SET restaurant_name = ?, whatsapp_number = ?, exchange_rate = ?, primary_color = ?, pago_movil_data = ?";
    $params = [$i['restaurant_name'], $i['whatsapp_number'], $i['exchange_rate'], $i['primary_color'], $i['pago_movil_data']];
    
    if ($logoUrl) {
        $sql .= ", logo_url = ?";
        $params[] = $logoUrl;
    }
    
    $sql .= " WHERE id = 1";
    $db->prepare($sql)->execute($params);
    
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { 
    http_response_code(500); 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
}
