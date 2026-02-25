<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador QR | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-layout" style="display: grid; grid-template-columns: 250px 1fr; min-height: 100vh;">
        <aside class="sidebar glass" style="padding: 2rem; border-right: 1px solid rgba(255,255,255,0.1);">
            <h2>Admin</h2>
            <nav>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="products.php" class="nav-link">Productos</a>
                <a href="categories.php" class="nav-link">Categorías</a>
                <a href="orders.php" class="nav-link">Pedidos</a>
                <a href="qr_generator.php" class="nav-link active">Generador QR</a>
                <a href="settings.php" class="nav-link">Configuración</a>
                <a href="logout.php" class="nav-link">Salir</a>
            </nav>
        </aside>

        <main class="main-content" style="padding: 3rem;">
            <h1>Generador de Código QR</h1>
            
            <div class="glass" style="margin-top: 2rem; padding: 2.5rem; border-radius: 24px; max-width: 600px; text-align: center;">
                <p style="margin-bottom: 2rem; color: var(--text-muted);">Genera el código QR para que tus clientes escaneen y vean el menú.</p>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="table-number" style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem;">Número de Mesa (0 = Principal)</label>
                    <input type="number" id="table-number" value="0" min="0" class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: center; max-width: 150px;" onchange="generateQR()">
                </div>

                <div id="qr-container" style="background: white; padding: 1.5rem; border-radius: 20px; display: inline-block; margin-bottom: 2rem;">
                    <img id="qr-image" src="" alt="QR Code" style="width: 250px; height: 250px;">
                </div>

                <div style="margin-top: 1rem;">
                    <button class="btn-primary" onclick="downloadQR()" style="width: auto; padding: 1rem 3rem;">Descargar QR</button>
                    <p id="qr-url" style="margin-top: 1rem; font-size: 0.8rem; opacity: 0.5;"></p>
                </div>
            </div>
        </main>
    </div>

    <script>
        const token = localStorage.getItem('admin_token');
        if (!token) window.location.href = 'index.php';

        function generateQR() {
            const currentUrl = window.location.href.split('/admin/')[0];
            const mesa = document.getElementById('table-number').value;
            const fullUrl = mesa > 0 ? `${currentUrl}/mesa/${mesa}` : currentUrl;
            const qrSize = "300x300";
            const qrUrl = `https://chart.googleapis.com/chart?cht=qr&chs=${qrSize}&chl=${encodeURIComponent(fullUrl)}`;
            
            document.getElementById('qr-image').src = qrUrl;
            document.getElementById('qr-url').textContent = fullUrl;
        }

        function downloadQR() {
            const link = document.createElement('a');
            link.href = document.getElementById('qr-image').src;
            link.download = 'menu_qr_gochaburguer.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        generateQR();
    </script>
</body>
</html>
