<?php 
/**
 * Admin Panel Dashboard
 * Digital Menu Restaurant PWA
 */
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Auth check already handled by auth.php and functions. 
// But we need a client-side check for the token for API calls.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .stat-card { padding: 2.5rem; border-radius: 24px; text-align: center; }
        .stat-card h3 { font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 1px; }
        .stat-card span { font-size: 3rem; font-weight: 600; display: block; margin: 0.5rem 0; }
        .quick-actions { margin-top: 3rem; display: flex; gap: 1rem; flex-wrap: wrap; }
        .quick-actions .btn-primary { width: auto; padding: 1.2rem 2.5rem; text-decoration: none; border-radius: 16px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="admin-layout" style="display: grid; grid-template-columns: 250px 1fr; min-height: 100vh;">
        <aside class="sidebar glass" style="padding: 2.5rem; border-right: 1px solid rgba(255,255,255,0.1);">
            <h2 style="margin-bottom: 2.5rem; color: var(--primary);">GMenu Admin</h2>
            <nav>
                <a href="index.php" class="nav-link active">Dashboard</a>
                <a href="products.php" class="nav-link">Productos</a>
                <a href="categories.php" class="nav-link">Categorías</a>
                <a href="orders.php" class="nav-link">Pedidos</a>
                <a href="qr_generator.php" class="nav-link">Generador QR</a>
                <a href="settings.php" class="nav-link">Configuración</a>
                <a href="logout.php" class="nav-link" style="color: #ff4d4d; margin-top: 2rem;">Salir</a>
            </nav>
        </aside>

        <main class="main-content" style="padding: 4rem;">
            <h1 style="font-size: 2.5rem;">Panel de Control</h1>
            <p style="color: var(--text-muted);">Métricas clave de hoy</p>
            
            <div class="stats-grid">
                <div class="stat-card glass">
                    <h3>Pedidos Hoy</h3>
                    <span><?=countOrdersToday()?></span>
                    <small style="color: var(--secondary);">Nuevas órdenes</small>
                </div>
                <div class="stat-card glass">
                    <h3>Ventas USD</h3>
                    <span>$<?=todaySales()?></span>
                    <small style="color: var(--accent);">Ingresos totales</small>
                </div>
            </div>

            <h2 style="margin-top: 4rem; margin-bottom: 1.5rem;">Acciones Rápidas</h2>
            <div class="quick-actions">
                <a href="settings.php" class="btn-primary">Actualizar Tasa / Branding</a>
                <a href="qr_generator.php" class="btn-primary" style="background: var(--secondary);">Generar QR de Mesas</a>
                <a href="products.php" class="btn-primary" style="background: var(--glass); border: 1px solid rgba(255,255,255,0.1);">Gestionar Menú</a>
            </div>
        </main>
    </div>

    <script>
        const token = localStorage.getItem('admin_token');
        if (!token) window.location.href = 'login.php';
    </script>
</body>
</html>
