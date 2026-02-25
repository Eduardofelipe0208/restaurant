let cart = JSON.parse(localStorage.getItem('cart')) || [];

function addToCart(productId, variants = []) {
    // We need to find the product name/price to update the UI
    const productCard = document.querySelector(`.product-card img[alt]`)?.closest('.product-card');
    // Simplified for now, in a real scenario we'd have a product lookup object

    const item = { id: productId, variants, qty: 1 };
    const existing = cart.find(i => i.id === productId &&
        JSON.stringify(i.variants) === JSON.stringify(variants));

    if (existing) {
        existing.qty++;
    } else {
        cart.push(item);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
}

function updateCartUI() {
    const counter = document.querySelector('.cart-count');
    if (counter) {
        const totalQty = cart.reduce((sum, i) => sum + i.qty, 0);
        counter.textContent = totalQty;
        // Simple bounce animation
        counter.parentElement.style.transform = 'scale(1.1)';
        setTimeout(() => counter.parentElement.style.transform = 'scale(1)', 200);
    }

    // Update Totals if modal is open
    const totalUsd = document.getElementById('cart-total-usd');
    const totalBs = document.getElementById('cart-total-bs');
    if (totalUsd && totalBs) {
        let sumUsd = 0;
        // In a full implementation, we'd lookup prices here. 
        // For now, let's keep the count/fab sync.
    }
}

function toggleCart() {
    const modal = document.getElementById('cart-modal');
    modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
}

function getItemPrice(item) {
    // This is simplified. In the real app, we'd fetch the price from the loaded products or API.
    // For now, we'll try to find it in the DOM or state if available.
    return 0; // Skeleton
}

function checkout() {
    if (cart.length === 0) return alert('Carrito vacío');

    const order = {
        table: new URLSearchParams(window.location.search).get('mesa') || 0,
        items: cart
    };

    fetch('./process_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(order)
    })
        .then(res => res.json())
        .then(data => {
            if (data.whatsapp_link) {
                window.open(data.whatsapp_link, '_blank');
                cart = [];
                localStorage.removeItem('cart');
                updateCartUI();
            } else {
                alert('Error al procesar el pedido');
            }
        })
        .catch(err => {
            console.error('Checkout error:', err);
            alert('Error conectando con el servidor');
        });
}

// Initial UI sync
updateCartUI();
