<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
        .sidebar { padding: 2rem; border-right: 1px solid rgba(255,255,255,0.1); }
        .nav-link { display: block; padding: 1rem; color: var(--text-muted); text-decoration: none; border-radius: 12px; margin-bottom: 0.5rem; }
        .nav-link.active { background: var(--glass); color: white; }
        .main-content { padding: 3rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        th, td { text-align: left; padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .action-btn { padding: 0.5rem 1rem; border-radius: 8px; border: none; cursor: pointer; margin-right: 0.5rem; }
        .edit-btn { background: var(--secondary); color: white; }
        .delete-btn { background: #ff4d4d; color: white; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar glass">
            <h2>Admin</h2>
            <nav>
                <a href="index.php" class="nav-link active">Dashboard</a>
                <a href="products.php" class="nav-link active">Productos</a>
                <a href="categories.php" class="nav-link">Categorías</a>
                <a href="settings.php" class="nav-link">Configuración</a>
                <a href="index.php" class="nav-link" style="color: #ff4d4d;">Salir</a>
            </nav>
        </aside>

        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>Gestión de Productos</h1>
                <button class="btn-primary" style="width: auto;" onclick="openModal()">+ Nuevo Producto</button>
            </div>

            <table class="glass" style="border-radius: 20px; overflow: hidden;">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio (USD)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <!-- Products will be loaded here -->
                </tbody>
            </table>
        </main>
    </div>

    <!-- Modal for Create/Edit -->
    <div id="product-modal" class="modal glass">
        <div class="modal-content">
            <h2 id="modal-title">Nuevo Producto</h2>
            <form id="product-form" style="margin-top: 1.5rem;">
                <input type="hidden" id="prod-id">
                <input type="text" id="prod-name" placeholder="Nombre" required class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                <select id="prod-category" required class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;"></select>
                <input type="number" step="0.01" id="prod-price" placeholder="Precio USD" required class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                <textarea id="prod-desc" placeholder="Descripción" class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem; height: 100px;"></textarea>
                <input type="text" id="prod-img" placeholder="URL Imagen" class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                
                <div style="margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                    <h3>Variantes / Extras</h3>
                    <div id="variant-list" style="margin-top: 0.5rem;"></div>
                    <button type="button" class="btn-primary" style="width: auto; padding: 0.5rem 1rem; margin-top: 0.5rem;" onclick="addVariantRow()">+ Añadir Extra</button>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 2rem;">Guardar Producto</button>
                <button type="button" onclick="closeModal()" id="close-modal" style="background:transparent; border:none; color:var(--text-muted); width:100%; margin-top:0.5rem;">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('admin_token');
        if (!token) window.location.href = 'index.php';

        async function loadProducts() {
            const resp = await fetch('../api/admin/products.php', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const json = await resp.json();
            const body = document.getElementById('product-table-body');
            body.innerHTML = '';
            
            // Sort by position before rendering
            const sortedProducts = json.data.sort((a, b) => a.position - b.position);

            sortedProducts.forEach(p => {
                const tr = document.createElement('tr');
                tr.draggable = true;
                tr.setAttribute('data-id', p.id);
                tr.innerHTML = `
                    <td><img src="${p.image_url}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"></td>
                    <td>${p.name}</td>
                    <td>${p.category_name}</td>
                    <td>$${p.price_usd}</td>
                    <td>${p.is_available ? '✅' : '❌'}</td>
                    <td>
                        <button class="action-btn edit-btn" onclick='editProduct(${JSON.stringify(p)})'>Editar</button>
                        <button class="action-btn delete-btn" onclick="deleteProduct(${p.id})">Borrar</button>
                    </td>
                `;
                setupDragEvents(tr);
                body.appendChild(tr);
            });
        }

        let dragSrcEl = null;

        function setupDragEvents(el) {
            el.addEventListener('dragstart', function(e) {
                this.style.opacity = '0.4';
                dragSrcEl = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.innerHTML);
            });

            el.addEventListener('dragover', function(e) {
                if (e.preventDefault) e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                return false;
            });

            el.addEventListener('drop', function(e) {
                if (e.stopPropagation) e.stopPropagation();
                if (dragSrcEl !== this) {
                    const allRows = Array.from(document.querySelectorAll('#product-table-body tr'));
                    const srcIdx = allRows.indexOf(dragSrcEl);
                    const targetIdx = allRows.indexOf(this);
                    
                    if (srcIdx < targetIdx) {
                        this.parentNode.insertBefore(dragSrcEl, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(dragSrcEl, this);
                    }
                    saveOrder();
                }
                return false;
            });

            el.addEventListener('dragend', function() {
                this.style.opacity = '1';
                document.querySelectorAll('#product-table-body tr').forEach(row => {
                    row.classList.remove('over');
                });
            });
        }

        async function saveOrder() {
            const rows = Array.from(document.querySelectorAll('#product-table-body tr'));
            const orders = rows.map((row, index) => ({
                id: row.getAttribute('data-id'),
                position: index
            }));

            await fetch('../api/admin/save_order.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({ orders })
            });
        }

        async function loadCategories() {
            const resp = await fetch('../api/menu.php');
            const json = await resp.json();
            const select = document.getElementById('prod-category');
            select.innerHTML = json.data.categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        }

        window.openModal = () => {
            document.getElementById('product-form').reset();
            document.getElementById('prod-id').value = '';
            document.getElementById('variant-list').innerHTML = '';
            document.getElementById('modal-title').textContent = 'Nuevo Producto';
            document.getElementById('product-modal').style.display = 'flex';
        };

        window.addVariantRow = (name = '', price = 0) => {
            const div = document.createElement('div');
            div.className = 'variant-row';
            div.style.display = 'flex';
            div.style.gap = '0.5rem';
            div.style.marginBottom = '0.5rem';
            div.innerHTML = `
                <input type="text" placeholder="Ej: Extra Queso" value="${name}" class="btn-primary v-name" style="background: rgba(255,255,255,0.05); text-align: left;">
                <input type="number" step="0.01" placeholder="Precio" value="${price}" class="btn-primary v-price" style="background: rgba(255,255,255,0.05); text-align: left; width: 100px;">
                <button type="button" onclick="this.parentElement.remove()" style="background:red; color:white; border:none; border-radius:8px; padding:0 10px;">X</button>
            `;
            document.getElementById('variant-list').appendChild(div);
        };

        window.closeModal = () => document.getElementById('product-modal').style.display = 'none';

        window.editProduct = async (p) => {
            document.getElementById('prod-id').value = p.id;
            document.getElementById('prod-name').value = p.name;
            document.getElementById('prod-category').value = p.category_id;
            document.getElementById('prod-price').value = p.price_usd;
            document.getElementById('prod-desc').value = p.description;
            document.getElementById('prod-img').value = p.image_url;
            document.getElementById('variant-list').innerHTML = '';
            
            // Fetch variants for this product
            const resp = await fetch(`../api/admin/products.php?id=${p.id}&with_variants=1`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const json = await resp.json();
            if (json.data && json.data.variants) {
                json.data.variants.forEach(v => addVariantRow(v.name, v.additional_price_usd));
            }

            document.getElementById('modal-title').textContent = 'Editar Producto';
            document.getElementById('product-modal').style.display = 'flex';
        };

        document.getElementById('product-form').onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('prod-id').value;
            const method = id ? 'PUT' : 'POST';
            
            const variants = Array.from(document.querySelectorAll('.variant-row')).map(row => ({
                name: row.querySelector('.v-name').value,
                price: row.querySelector('.v-price').value
            }));

            const data = {
                id: id,
                name: document.getElementById('prod-name').value,
                category_id: document.getElementById('prod-category').value,
                price_usd: document.getElementById('prod-price').value,
                description: document.getElementById('prod-desc').value,
                image_url: document.getElementById('prod-img').value,
                is_available: 1,
                variants: variants
            };

            await fetch('../api/admin/products.php', {
                method: method,
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(data)
            });
            closeModal();
            loadProducts();
        };

        window.deleteProduct = async (id) => {
            if (!confirm('¿Borrar producto?')) return;
            await fetch(`../api/admin/products.php?id=${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            loadProducts();
        };

        loadProducts();
        loadCategories();
    </script>
</body>
</html>
