CREATE DATABASE IF NOT EXISTS restaurant_menu;
USE restaurant_menu;

CREATE TABLE settings (
    id INT PRIMARY KEY DEFAULT 1,
    restaurant_name VARCHAR(100),
    whatsapp_number VARCHAR(20),
    exchange_rate DECIMAL(10,4) DEFAULT 50.00,
    pago_movil_data JSON,
    logo_url VARCHAR(255),
    primary_color VARCHAR(7) DEFAULT '#FF6B35',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),
    priority INT DEFAULT 0,
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100),
    description TEXT,
    price_usd DECIMAL(10,2),
    image_url VARCHAR(255),
    estimated_time INT, -- minutos
    is_available BOOLEAN DEFAULT TRUE,
    position INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE product_variants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    name VARCHAR(50),
    additional_price_usd DECIMAL(10,2),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_number INT,
    customer_name VARCHAR(100),
    items JSON,
    total_usd DECIMAL(10,2),
    total_bs DECIMAL(10,2),
    payment_method ENUM('pago_movil','zelle','efectivo','transferencia'),
    status ENUM('pending','confirmed','preparing','ready','delivered') DEFAULT 'pending',
    whatsapp_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DATOS INICIALES
INSERT INTO settings (restaurant_name, whatsapp_number) VALUES ('Mi Restaurante', '584124567890');
INSERT INTO users (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password
INSERT INTO categories (name, priority) VALUES 
('Entradas',1),('Hamburguesas',2),('Acompañamientos',3),('Bebidas',4),('Postres',5);
