// settings is now provided by index.php via the SETTINGS global
const settings = window.SETTINGS || {};
const API_BASE = './';

// Service Worker PWA
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('./sw.js')
        .then(() => console.log('SW Registered'))
        .catch(err => console.error('SW Error', err));
}

// Cargar menú
async function loadMenu() {
    try {
        const res = await fetch(API_BASE + 'api.php?action=get_menu');
        const data = await res.json();
        renderCategories(data.categories);
        renderProducts(data.products);
    } catch (e) {
        console.error('Error loading menu:', e);
    }
}

// Render categorías y productos con filtros
function renderCategories(categories) {
    const container = document.getElementById('menu-container');
    const categoryNav = document.createElement('div');
    categoryNav.className = 'categories-nav';
    categoryNav.innerHTML = `<button class="category-btn active" data-id="all">Todos</button>`;

    categories.forEach(cat => {
        categoryNav.innerHTML += `<button class="category-btn" data-id="${cat.id}">${cat.name}</button>`;
    });

    container.prepend(categoryNav);
}

function renderProducts(products) {
    const container = document.getElementById('menu-container');
    let productsDiv = document.getElementById('products-list');

    if (!productsDiv) {
        productsDiv = document.createElement('div');
        productsDiv.id = 'products-list';
        productsDiv.className = 'product-grid';
        container.appendChild(productsDiv);
    }

    productsDiv.innerHTML = '';

    products.forEach(p => {
        const productUI = document.createElement('div');
        productUI.className = 'product-card glass';
        productUI.setAttribute('data-category', p.category_id);

        const priceBs = (p.price_usd * settings.exchange_rate).toFixed(2);

        productUI.innerHTML = `
            <img src="${p.image_url || 'assets/images/placeholder.jpg'}" alt="${p.name}" class="product-img">
            <div class="product-info">
                <h3>${p.name}</h3>
                <p>${p.description || ''}</p>
                <div class="price">
                    <span class="usd">$${p.price_usd}</span>
                    <span class="bs">${priceBs} Bs.</span>
                </div>
                <button class="add-to-cart" onclick="addToCart(${p.id})">Añadir al Carrito</button>
            </div>
        `;
        productsDiv.appendChild(productUI);
    });
}

function filterByCategory(id) {
    const cards = document.querySelectorAll('.product-card');
    const btns = document.querySelectorAll('.category-btn');

    btns.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.id === id);
    });

    cards.forEach(card => {
        if (id === 'all' || card.dataset.category === id) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Filtros dinámicos
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('category-btn')) {
        filterByCategory(e.target.dataset.id);
    }
});

// Initial load
loadMenu();
