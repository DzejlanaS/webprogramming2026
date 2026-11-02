SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS Petly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Petly;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(8,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(30) NOT NULL DEFAULT 'cash_on_delivery',
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL,
    street VARCHAR(150) NOT NULL,
    city VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    notes VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(8,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Petly', 'Admin', 'admin@petly.ba', '$2y$12$EPUtt.B2T38l/o.A58TQKuRmf1D7zwhtQKge0zH.HMsamE4iQd.fy', 'admin'),
('Amina', 'Karić', 'amina.karic@example.com', '$2y$12$JnpPYEmgoZdABwjkBCfTFuwWJrVnlbjvcHJA9QgJTcrFpAbAqeu1S', 'customer'),
('Emir', 'Kovač', 'emir.kovac@example.com', '$2y$12$JnpPYEmgoZdABwjkBCfTFuwWJrVnlbjvcHJA9QgJTcrFpAbAqeu1S', 'customer');

INSERT INTO categories (name, description) VALUES
('Food', 'Dry and wet food for dogs and cats'),
('Toys', 'Chew toys, teasers and other toys for play'),
('Accessories', 'Leashes, beds, collars and everyday gear'),
('Grooming', 'Shampoos, brushes and coat care'),
('Health', 'Vitamins and supplements for healthy pets');

INSERT INTO products (category_id, name, description, price, stock_quantity, image_url, is_featured) VALUES
(1, 'Premium Dog Food 3kg', 'Grain-free, chicken & rice recipe for adult dogs.', 24.90, 24, 'https://images.unsplash.com/photo-1714068691210-073dc52c6c1d', 1),
(1, 'Tasty Cat Food 2kg', 'Salmon & tuna recipe, no added sugar.', 19.90, 31, 'https://images.unsplash.com/photo-1558993457-4bc6ec2c3734', 1),
(2, 'Squeaky Bone Toy', 'Durable rubber chew toy for dogs.', 6.50, 58, 'https://images.unsplash.com/photo-1535294435445-d7249524ef2e', 0),
(2, 'Feather Teaser Wand', 'Interactive wand toy to keep cats active.', 8.00, 40, 'https://images.unsplash.com/photo-1638826595775-e2eae86cda8e', 0),
(3, 'Adjustable Leash', 'Padded handle, fits small to large dogs.', 12.00, 22, 'https://images.unsplash.com/photo-1555265626-8ed04bde01a1', 0),
(3, 'Cozy Pet Bed', 'Soft fleece bed, machine washable cover.', 35.00, 15, 'https://images.unsplash.com/photo-1581888227599-779811939961', 1),
(4, 'Oatmeal Shampoo', 'Gentle formula for sensitive skin.', 9.90, 27, 'https://images.unsplash.com/photo-1611173622933-91942d394b04', 0),
(4, 'Slicker Brush', 'Reduces shedding, gentle on skin.', 7.50, 33, 'https://images.unsplash.com/photo-1727510190155-51abda425a82', 1),
(5, 'Multivitamin Chews', 'Daily support for coat, joints and immunity.', 15.00, 19, 'https://images.unsplash.com/photo-1568640347023-a616a30bc3bd', 0);

INSERT INTO orders (user_id, order_number, order_date, total_amount, status, full_name, phone, email, street, city, postal_code, notes) VALUES
(2, 'PET-104822', '2026-10-12 10:24:00', 32.40, 'delivered', 'Amina Karić', '+387 61 234 567', 'amina.karic@example.com', 'Zmaja od Bosne 12', 'Sarajevo', '71000', NULL),
(2, 'PET-109273', '2026-10-03 16:05:00', 35.00, 'pending', 'Amina Karić', '+387 61 234 567', 'amina.karic@example.com', 'Zmaja od Bosne 12', 'Sarajevo', '71000', 'Please call before delivery'),
(3, 'PET-117745', '2026-10-18 09:40:00', 19.90, 'pending', 'Emir Kovač', '+387 62 345 678', 'emir.kovac@example.com', 'Maršala Tita 45', 'Mostar', '88000', NULL);

INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES
(1, 1, 1, 24.90, 24.90),
(1, 8, 1, 7.50, 7.50),
(2, 6, 1, 35.00, 35.00),
(3, 2, 1, 19.90, 19.90);

INSERT INTO contacts (user_id, name, email, subject, message) VALUES
(2, 'Amina Karić', 'amina.karic@example.com', 'Delivery time', 'Hi, how long does delivery to Sarajevo usually take?'),
(NULL, 'Lejla Begić', 'lejla.begic@example.com', 'Product question', 'Is the Cozy Pet Bed suitable for a large dog?');
