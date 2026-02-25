<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-layout" style="display: grid; grid-template-columns: 250px 1fr; min-height: 100vh;">
        <aside class="sidebar glass" style="padding: 2rem; border-right: 1px solid rgba(255,255,255,0.1);">
            <h2>Admin</h2>
            <nav>
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="products.php" class="nav-link">Productos</a>
                <a href="categories.php" class="nav-link">Categorías</a>
                <a href="orders.php" class="nav-link active">Pedidos</a>
                <a href="settings.php" class="nav-link">Configuración</a>
                <a href="logout.php" class="nav-link" style="color: #ff4d4d;">Salir</a>
            </nav>
        </aside>

        <main class="main-content" style="padding: 3rem;">
            <h1>Historial de Pedidos</h1>
            
            <table class="glass" style="width: 100%; border-collapse: collapse; border-radius: 20px; overflow: hidden; margin-top: 2rem;">
                <thead>
                    <tr style="text-align: left; background: rgba(255,255,255,0.05);">
                        <th style="padding: 1rem;">ID</th>
                        <th style="padding: 1rem;">Cliente</th>
                        <th style="padding: 1rem;">Total USD</th>
                        <th style="padding: 1rem;">Total Bs.</th>
                        <th style="padding: 1rem;">Fecha</th>
                        <th style="padding: 1rem;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="order-table-body"></tbody>
            </table>
        </main>
    </div>

    <!-- Order Detail Modal -->
    <div id="order-modal" class="modal glass">
        <div class="modal-content">
            <h2>Detalle del Pedido</h2>
            <div id="order-detail-content" style="margin-top: 1.5rem;"></div>
            <button class="btn-primary" onclick="closeOrderModal()" style="margin-top: 2rem;">Cerrar</button>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('admin_token');
        if (!token) window.location.href = 'index.php';

        async function loadOrders() {
            const resp = await fetch('../api/admin/orders.php', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const json = await resp.json();
            const body = document.getElementById('order-table-body');
            body.innerHTML = '';
            
            json.data.forEach(o => {
                body.innerHTML += `
                    <tr>
                        <td style="padding: 1rem;">#${o.id}</td>
                        <td style="padding: 1rem;">${o.customer_name}</td>
                        <td style="padding: 1rem;">$${o.total_usd}</td>
                        <td style="padding: 1rem;">${o.total_bs} Bs.</td>
                        <td style="padding: 1rem;">${new Date(o.created_at).toLocaleString()}</td>
                        <td style="padding: 1rem;">
                            <button class="btn-primary" style="width:auto; padding:0.4rem 0.8rem;" onclick='viewOrder(${JSON.stringify(o)})'>Ver</button>
                        </td>
                    </tr>
                `;
            });
        }

        window.viewOrder = (o) => {
            const content = document.getElementById('order-detail-content');
            const items = JSON.parse(o.items);
            let itemsHtml = '<ul>';
            items.forEach(i => {
                itemsHtml += `<li>${i.name} x${i.quantity}</li>`;
            });
            itemsHtml += '</ul>';

            content.innerHTML = `
                <p><strong>Cliente:</strong> ${o.customer_name}</p>
                <p><strong>Total USD:</strong> $${o.total_usd}</p>
                <p><strong>Total Bs.:</strong> ${o.total_bs} Bs.</p>
                <hr style="margin: 1rem 0; border: 0.5px solid rgba(255,255,255,0.1);">
                <p><strong>Productos:</strong></p>
                ${itemsHtml}
                <hr style="margin: 1rem 0; border: 0.5px solid rgba(255,255,255,0.1);">
                <p><strong>Mensaje:</strong></p>
                <pre style="white-space: pre-wrap; font-family: inherit; font-size: 0.8rem; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 12px;">${o.whatsapp_message}</pre>
            `;
            document.getElementById('order-modal').style.display = 'flex';
        };

        window.closeOrderModal = () => document.getElementById('order-modal').style.display = 'none';

        loadOrders();
    </script>
</body>
</html>
