<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Fetch settings
try {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$settings) {
        die("Settings not found");
    }
} catch (PDOException $e) {
    die("Error fetching settings: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['restaurant_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="theme-color" content="<?php echo $settings['primary_color']; ?>">
</head>
<body>
    <header class="header glass">
        <div class="logo">
            <?php if($settings['logo_url']): ?>
                <img src="<?php echo $settings['logo_url']; ?>" alt="Logo" style="width: 100%; border-radius: 50%;">
            <?php else: ?>
                <?= substr($settings['restaurant_name'], 0, 1) ?>
            <?php endif; ?>
        </div>
        <h1><?php echo $settings['restaurant_name']; ?></h1>
        <div class="exchange-rate">1 USD = <?=number_format($settings['exchange_rate'],2)?> Bs.</div>
    </header>

    <main id="menu-container">
        <!-- Contenido dinámico cargado por app.js -->
        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
            Cargando el menú...
        </div>
    </main>

    <div id="cart-fab" class="cart-fab" onclick="toggleCart()">
        <span class="cart-count">0</span>
        🛒
    </div>

    <!-- Modal del Carrito -->
    <div id="cart-modal" class="modal glass">
        <div class="modal-content">
            <h2 style="margin-bottom: 1.5rem;">Tu Pedido</h2>
            <div id="cart-items">
                <!-- Items del carrito -->
            </div>
            
            <div class="cart-total">
                <p>Total: <span id="cart-total-usd">$0.00</span></p>
                <p style="font-size: 0.9rem; color: var(--text-muted); font-weight: normal;">
                    Total Bs: <span id="cart-total-bs">0.00 Bs.</span>
                </p>
            </div>

            <button class="btn-primary" onclick="checkout()" style="margin-top: 1rem;">Confirmar Pedido</button>
            <button id="close-modal" onclick="toggleCart()">Continuar Viendo</button>
        </div>
    </div>

    <!-- Script contexts -->
    <script>
        const SETTINGS = <?php 
            $settings['mesa'] = $_GET['mesa'] ?? 0;
            echo json_encode($settings); 
        ?>;
    </script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>
