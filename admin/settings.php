<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración | Admin</title>
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
                <a href="settings.php" class="nav-link active">Configuración</a>
                <a href="index.php" class="nav-link">Salir</a>
            </nav>
        </aside>

        <main class="main-content" style="padding: 3rem;">
            <h1>Configuración del Sistema</h1>
            
            <form id="settings-form" class="glass" style="margin-top: 2rem; padding: 2.5rem; border-radius: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h3 style="margin-bottom: 1.5rem;">Información Básica</h3>
                        <label style="display:block; margin-bottom:0.5rem;">Nombre del Restaurante</label>
                        <input type="text" id="set-name" class="btn-primary" style="background:rgba(255,255,255,0.05); text-align:left; margin-bottom:1rem;">
                        
                        <label style="display:block; margin-bottom:0.5rem;">WhatsApp (Ej: 58412...)</label>
                        <input type="text" id="set-wa" class="btn-primary" style="background:rgba(255,255,255,0.05); text-align:left; margin-bottom:1rem;">
                        
                        <label style="display:block; margin-bottom:0.5rem;">Tasa de Cambio (USD -> Bs.)</label>
                        <input type="number" step="0.01" id="set-rate" class="btn-primary" style="background:rgba(255,255,255,0.05); text-align:left; margin-bottom:1rem; border: 2px solid var(--primary);">
                    </div>
                    <div>
                        <h3 style="margin-bottom: 1.5rem;">Apariencia</h3>
                        <label style="display:block; margin-bottom:0.5rem;">Color Principal (HEX)</label>
                        <input type="color" id="set-color" style="width:100%; height:45px; margin-bottom:1rem;">
                        
                        <label style="display:block; margin-bottom:0.5rem;">URL del Logo</label>
                        <input type="text" id="set-logo" class="btn-primary" style="background:rgba(255,255,255,0.05); text-align:left; margin-bottom:1rem;">
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" style="margin-top: 2rem; width: auto; padding: 1rem 3rem;">Guardar Cambios</button>
            </form>
        </main>
    </div>

    <script>
        const token = localStorage.getItem('admin_token');
        if (!token) window.location.href = 'index.php';

        async function loadSettings() {
            const resp = await fetch('../api/settings.php');
            const json = await resp.json();
            const s = json.data;
            document.getElementById('set-name').value = s.restaurant_name;
            document.getElementById('set-wa').value = s.whatsapp_number;
            document.getElementById('set-rate').value = s.exchange_rate;
            document.getElementById('set-color').value = s.primary_color || '#FF6B35';
            document.getElementById('set-logo').value = s.logo_url || '';
        }

        document.getElementById('settings-form').onsubmit = async (e) => {
            e.preventDefault();
            const data = {
                restaurant_name: document.getElementById('set-name').value,
                whatsapp_number: document.getElementById('set-wa').value,
                exchange_rate: document.getElementById('set-rate').value,
                primary_color: document.getElementById('set-color').value,
                logo_url: document.getElementById('set-logo').value,
                pago_movil_data: {} // Simplified for now
            };

            const resp = await fetch('../api/admin/settings.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(data)
            });

            if (resp.ok) alert('Configuración actualizada');
        };

        loadSettings();
    </script>
</body>
</html>
