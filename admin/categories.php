<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | Admin</title>
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
                <a href="categories.php" class="nav-link active">Categorías</a>
                <a href="orders.php" class="nav-link">Pedidos</a>
                <a href="settings.php" class="nav-link">Configuración</a>
                <a href="logout.php" class="nav-link" style="color: #ff4d4d;">Salir</a>
            </nav>
        </aside>

        <main class="main-content" style="padding: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Categorías</h1>
                <button class="btn-primary" style="width: auto;" onclick="openCatModal()">+ Nueva Categoría</button>
            </div>

            <table class="glass" style="width: 100%; border-collapse: collapse; border-radius: 20px; overflow: hidden;">
                <thead>
                    <tr style="text-align: left; background: rgba(255,255,255,0.05);">
                        <th style="padding: 1rem;">Prioridad</th>
                        <th style="padding: 1rem;">Nombre</th>
                        <th style="padding: 1rem;">Estado</th>
                        <th style="padding: 1rem;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cat-table-body"></tbody>
            </table>
        </main>
    </div>

    <!-- Category Modal -->
    <div id="cat-modal" class="modal glass">
        <div class="modal-content">
            <h2 id="cat-modal-title">Administrar Categoría</h2>
            <form id="cat-form" style="margin-top: 1.5rem;">
                <input type="hidden" id="cat-id">
                <input type="text" id="cat-name" placeholder="Nombre (Ej: Hamburguesas)" required class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                <input type="number" id="cat-priority" placeholder="Prioridad (0=Alta)" class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                <select id="cat-status" class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                    <option value="1">Activa</option>
                    <option value="0">Inactiva</option>
                </select>
                <button type="submit" class="btn-primary">Guardar</button>
                <button type="button" onclick="closeCatModal()" id="close-modal">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('admin_token');
        if (!token) window.location.href = 'index.php';

        async function loadCategories() {
            const resp = await fetch('../api/admin/categories.php', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const json = await resp.json();
            const body = document.getElementById('cat-table-body');
            body.innerHTML = '';
            
            json.data.forEach(c => {
                body.innerHTML += `
                    <tr>
                        <td style="padding: 1rem;">${c.priority}</td>
                        <td style="padding: 1rem;">${c.name}</td>
                        <td style="padding: 1rem;">${c.is_active ? '✅ Activa' : '❌ Inactiva'}</td>
                        <td style="padding: 1rem;">
                            <button class="btn-primary" style="width:auto; padding:0.4rem 0.8rem; background:var(--secondary);" onclick='editCat(${JSON.stringify(c)})'>Editar</button>
                            <button class="btn-primary" style="width:auto; padding:0.4rem 0.8rem; background:#ff4d4d;" onclick="deleteCat(${c.id})">Borrar</button>
                        </td>
                    </tr>
                `;
            });
        }

        window.openCatModal = () => {
            document.getElementById('cat-form').reset();
            document.getElementById('cat-id').value = '';
            document.getElementById('cat-modal').style.display = 'flex';
        };

        window.closeCatModal = () => document.getElementById('cat-modal').style.display = 'none';

        window.editCat = (c) => {
            document.getElementById('cat-id').value = c.id;
            document.getElementById('cat-name').value = c.name;
            document.getElementById('cat-priority').value = c.priority;
            document.getElementById('cat-status').value = c.is_active;
            document.getElementById('cat-modal').style.display = 'flex';
        };

        document.getElementById('cat-form').onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('cat-id').value;
            const data = {
                id: id,
                name: document.getElementById('cat-name').value,
                priority: document.getElementById('cat-priority').value,
                is_active: document.getElementById('cat-status').value
            };

            await fetch('../api/admin/categories.php', {
                method: id ? 'PUT' : 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(data)
            });
            closeCatModal();
            loadCategories();
        };

        window.deleteCat = async (id) => {
            if (!confirm('¿Borrar categoría? Se borrarán todos sus productos.')) return;
            await fetch(`../api/admin/categories.php?id=${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            loadCategories();
        };

        loadCategories();
    </script>
</body>
</html>
