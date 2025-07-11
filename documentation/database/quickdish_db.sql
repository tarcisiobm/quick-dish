CREATE DATABASE schema_quick_dish;
USE schema_quick_dish;

-- ##############################
-- # 1. GRUPOS E PERMISSÕES
-- ##############################

-- 1.1 permission_groups (grupos de permissões)
CREATE TABLE permission_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 1.2 profiles (perfis)
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 1.3 permissions (permissões)
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permission_group_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_permissions_permission_group FOREIGN KEY (permission_group_id) REFERENCES permission_groups(id)
);

-- 1.4 profile_permissions (permissões dos perfis)
CREATE TABLE profile_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_profile_permissions_profile FOREIGN KEY (profile_id) REFERENCES profiles(id),
    CONSTRAINT fk_profile_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id)
);

-- ##############################
-- # 2. USUÁRIOS E LOGS
-- ##############################

-- 2.1 users (usuários)
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    email_verified_at DATETIME NULL,
    profile_id INT NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    avatar VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_users_profile FOREIGN KEY (profile_id) REFERENCES profiles(id)
);

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    job_title VARCHAR(150) NOT NULL,
    salary DECIMAL(10, 2) NOT NULL,
    hire_date DATE NOT NULL,
    termination_date DATE NULL,
    work_schedule VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_employees_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 2.2 system_logs (logs do sistema)
CREATE TABLE system_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    action VARCHAR(50) NOT NULL,
    old_data JSON NULL,
    new_data JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_system_logs_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 2.3 addresses (endereços dos usuários)
CREATE TABLE addresses (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    zipcode VARCHAR(10) NULL,
    street VARCHAR(255) NOT NULL,
    number VARCHAR(20) NULL,
    complement VARCHAR(255) NULL,
    neighborhood VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(50) NULL,
    reference VARCHAR(255) NULL,
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_addresses_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ##############################
-- # 3. MENU (CATEGORIAS E PRODUTOS)
-- ##############################

-- 3.1 categories (categorias do menu)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    image_path VARCHAR(255) NULL,
    display_order INT NOT NULL DEFAULT 0,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 3.2 products (produtos do menu)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    promotional_price DECIMAL(10,2) NULL,
    image_path VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- ##############################
-- # 4. FORNECEDORES E INGREDIENTES
-- ##############################

-- 4.1 suppliers (fornecedores)
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    cnpj VARCHAR(20) NULL UNIQUE,
    phone VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 4.2 unit_measures (unidades de medida)
CREATE TABLE unit_measures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    abbreviation VARCHAR(15) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 4.3 supplier_purchases (compras dos fornecedores)
CREATE TABLE supplier_purchases (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    invoice_number VARCHAR(100) NULL,
    invoice_date DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_supplier_purchases_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- 4.4 ingredients (ingredientes)
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    unit_measure_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255),
    unit_price DECIMAL(10,2) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    min_quantity DECIMAL(10,2) NOT NULL,
    max_quantity DECIMAL(10,2) NOT NULL,
    is_additional BOOLEAN NOT NULL DEFAULT FALSE,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_ingredients_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    CONSTRAINT fk_ingredients_unit_measure FOREIGN KEY (unit_measure_id) REFERENCES unit_measures(id)
);

-- 4.5 product_ingredients (ingredientes dos produtos)
CREATE TABLE product_ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL, 
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_product_ingredients_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_product_ingredients_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

-- 4.6 product_additionals (adicionais disponíveis para cada produto)
CREATE TABLE product_additionals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_product_additionals_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_product_additionals_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

-- 4.7 ingredients_movements (movimentações do estoque)
CREATE TABLE ingredients_movements (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT NOT NULL,
    user_id BIGINT NOT NULL,
    movement_type ENUM('entry', 'exit', 'manual_adjustment') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    previous_quantity DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(10,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_ingredients_movements_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
    CONSTRAINT fk_ingredients_movements_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ##############################
-- # 5. MESAS, PEDIDOS E ITENS DE PEDIDOS
-- ##############################

-- 5.1 tables (mesas)
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number INT NOT NULL,
    capacity TINYINT NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 5.2 order_delivery (entregas dos pedidos)
CREATE TABLE order_delivery (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    address_id BIGINT NOT NULL,
    delivery_price DECIMAL(10,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_order_delivery_address FOREIGN KEY (address_id) REFERENCES addresses(id)
);

-- 5.3 orders (pedidos)
CREATE TABLE orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    order_delivery_id BIGINT NULL,
    table_id INT NULL,
    order_type ENUM('dine-in', 'delivery', 'takeout') NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending' NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    notes VARCHAR(255),
    prep_time_minutes INT NULL,
    order_date DATETIME NOT NULL, -- data do pedido
    confirmed_at DATETIME NULL, -- pagamento confirmado
    ready_at DATETIME NULL, -- pedido preparado
    delivered_at DATETIME NULL, -- pedido saiu para entrega
    completed_at DATETIME NULL, -- pedido finalizado
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_orders_employee FOREIGN KEY (employee_id) REFERENCES users(id),
    CONSTRAINT fk_order_order_delivery FOREIGN KEY (order_delivery_id) REFERENCES order_delivery(id),
    CONSTRAINT fk_orders_table FOREIGN KEY (table_id) REFERENCES tables(id)
);

-- 5.4 order_items (itens dos pedidos)
CREATE TABLE order_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    product_id INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL, -- historico do valor da unidade do item quando realizou o pedido
    quantity INT NOT NULL, -- quantidade de itens
    total_price DECIMAL(10,2) NOT NULL, -- preço unitario de um item x quantidade
    notes VARCHAR(255),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 5.5 additionals (adicionais dos itens do pedido)
CREATE TABLE additionals (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_item_id BIGINT NOT NULL,
    ingredient_id INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL, -- historico do valor da unidade do adicional quando realizou o pedido
    quantity INT NOT NULL, -- quantidade de adicionais
    total_price DECIMAL(10,2) NOT NULL, -- preço unitario x quantidade
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_additionals_order_item FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    CONSTRAINT fk_additionals_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

-- ##############################
-- # 6. RESERVAS E PAGAMENTOS
-- ##############################

-- 6.1 reservations (reservas)
CREATE TABLE reservations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    table_id INT NOT NULL,
    user_id BIGINT NOT NULL,
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    guests_count INT NOT NULL,
    notes VARCHAR(255),
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_reservations_table FOREIGN KEY (table_id) REFERENCES tables(id),
    CONSTRAINT fk_reservations_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 6.2 payment_types (tipos de pagamento)
CREATE TABLE payment_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 6.3 coupons (cupons)
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    discount_type ENUM('percentage', 'fixed') NOT NULL, -- desconto fixo ou percentual
    discount_value DECIMAL(10,2) NOT NULL, -- valor do desconto (fixo ou percentual)
    min_order_value DECIMAL(10,2) NOT NULL, -- preço minimo do pedido
    usage_limit INT NULL, -- máximo de usos (para quando é limitado a X usos)
    used_count INT NOT NULL DEFAULT 0, -- contagem de uso até o máximo para expirar
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT chk_coupons_dates CHECK (end_date >= start_date),
    CONSTRAINT chk_coupons_discount_value CHECK (discount_value >= 0),
    CONSTRAINT chk_coupons_min_order_value CHECK (min_order_value >= 0)
);

-- 6.4 payments (pagamentos)
CREATE TABLE payments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    payment_type_id INT NOT NULL,
    coupon_id INT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 0, -- status de pagamento 1 = Pago | 0 = Não pago
    paid_at DATETIME NULL, -- data de pagamento 
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_payments_payment_type FOREIGN KEY (payment_type_id) REFERENCES payment_types(id),
    CONSTRAINT fk_payments_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id),
    CONSTRAINT chk_payments_amount CHECK (amount >= 0)
);

-- ##############################
-- # 7. REVIEWS (Avaliações)
-- ##############################

-- 7.1 reviews (avaliações)
CREATE TABLE reviews (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ##############################
-- # 8. Accounts payable (Contas a pagar)
-- ##############################

-- 8.1 accounts_payable (contas a pagar)
CREATE TABLE accounts_payable (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NULL,
    purchase_id BIGINT NULL,
    payable_id BIGINT NOT NULL, -- id da origem (ex: employee_id ou purchase_id),
    payable_type VARCHAR(255) NOT NULL, -- tipo de pagamento (ex: 'employee', 'purchase')
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    due_date DATE NOT NULL,
    paid_at DATETIME NULL,
    status ENUM('pending', 'paid', 'overdue') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_accounts_payable_employee FOREIGN KEY (employee_id) REFERENCES employees(id),
    CONSTRAINT fk_accounts_payable_purchase FOREIGN KEY (purchase_id) REFERENCES supplier_purchases(id),
    CONSTRAINT chk_accounts_payable_amount CHECK (amount >= 0)
);

-- ##############################
-- # Indexes (Índices)
-- ##############################

-- ## Groups and Permissions
CREATE INDEX idx_permissions_permission_group_id ON permissions(permission_group_id);
CREATE INDEX idx_profile_permissions_profile_id ON profile_permissions(profile_id);
CREATE INDEX idx_profile_permissions_permission_id ON profile_permissions(permission_id);

-- ## Users and Logs
CREATE INDEX idx_users_profile_id ON users(profile_id);
CREATE INDEX idx_system_logs_user_id ON system_logs(user_id);

-- ## Menu
CREATE INDEX idx_products_category_id ON products(category_id);

-- ## Suppliers and Ingredients
CREATE INDEX idx_ingredients_supplier_id ON ingredients(supplier_id);
CREATE INDEX idx_ingredients_unit_measure_id ON ingredients(unit_measure_id);
CREATE INDEX idx_ingredients_is_additional ON ingredients(is_additional);
CREATE INDEX idx_product_ingredients_product_id ON product_ingredients(product_id);
CREATE INDEX idx_product_ingredients_ingredient_id ON product_ingredients(ingredient_id);
CREATE INDEX idx_product_additionals_product_id ON product_additionals(product_id);
CREATE INDEX idx_product_additionals_ingredient_id ON product_additionals(ingredient_id);
CREATE INDEX idx_ingredients_movements_ingredient_id ON ingredients_movements(ingredient_id);
CREATE INDEX idx_ingredients_movements_user_id ON ingredients_movements(user_id);

-- ## Orders and Tables
CREATE INDEX idx_order_delivery_address_id ON order_delivery(address_id);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_employee_id ON orders(employee_id);
CREATE INDEX idx_orders_order_delivery_id ON orders(order_delivery_id);
CREATE INDEX idx_orders_table_id ON orders(table_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_order_date ON orders(order_date);

CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_order_items_product_id ON order_items(product_id);
CREATE INDEX idx_additionals_order_item_id ON additionals(order_item_id);
CREATE INDEX idx_additionals_ingredient_id ON additionals(ingredient_id);

-- ## Reservations and Payments
CREATE INDEX idx_reservations_table_id ON reservations(table_id);
CREATE INDEX idx_reservations_user_id ON reservations(user_id);
CREATE INDEX idx_reservations_date ON reservations(reservation_date);
CREATE INDEX idx_payments_order_id ON payments(order_id);
CREATE INDEX idx_payments_payment_type_id ON payments(payment_type_id);
CREATE INDEX idx_payments_coupon_id ON payments(coupon_id);

-- ## Reviews
CREATE INDEX idx_reviews_user_id ON reviews(user_id);

-- ## Accounts Payable
CREATE INDEX idx_accounts_payable_payable ON accounts_payable(payable_id, payable_type);
CREATE INDEX idx_accounts_payable_employee_id ON accounts_payable(employee_id);
CREATE INDEX idx_accounts_payable_purchase_id ON accounts_payable(purchase_id);
CREATE INDEX idx_accounts_payable_status ON accounts_payable(status);
CREATE INDEX idx_accounts_payable_due_date ON accounts_payable(due_date);