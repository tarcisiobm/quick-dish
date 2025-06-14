-- ##############################
-- # 1. GRUPOS E PERMISSÕES
-- ##############################

-- 1.1 permission_groups (grupos de permissões)
CREATE TABLE permission_groups (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 1.2 profiles (perfis)
CREATE TABLE profiles (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 1.3 permissions (permissões)
CREATE TABLE permissions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    permission_group_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_permissions_permission_group FOREIGN KEY (permission_group_id) REFERENCES permission_groups(id)
);

-- 1.4 profile_permissions (permissões dos perfis)
CREATE TABLE profile_permissions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT NOT NULL,
    permission_id BIGINT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
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
    profile_id BIGINT NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    cpf CHAR(11) NULL UNIQUE,
    birth_date DATE NULL,
    image_path VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_users_profile FOREIGN KEY (profile_id) REFERENCES profiles(id)
);

-- 2.2 system_logs (logs do sistema)
CREATE TABLE system_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    action VARCHAR(50) NOT NULL,
    old_data JSON NULL,
    new_data JSON NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
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
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_addresses_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ##############################
-- # 3. MENU (CATEGORIAS, ITENS, ADICIONAIS)
-- ##############################

-- 3.1 categories (categorias do menu)
CREATE TABLE categories (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    image_path VARCHAR(255) NULL,
    display_order INT NOT NULL DEFAULT 0,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 3.2 items (itens do menu)
CREATE TABLE items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    promotional_price DECIMAL(10,2) NULL,
    image_path VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- 3.3 additionals (adicionais)
CREATE TABLE additionals (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 3.4 item_additionals (adicionais de itens)
CREATE TABLE item_additionals (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    item_id BIGINT NOT NULL,
    additional_id BIGINT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_item_additionals_item FOREIGN KEY (item_id) REFERENCES items(id),
    CONSTRAINT fk_item_additionals_additional FOREIGN KEY (additional_id) REFERENCES additionals(id)
);

-- ##############################
-- # 4. FORNECEDORES E INGREDIENTES
-- ##############################

-- 4.1 suppliers (fornecedores)
CREATE TABLE suppliers (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    cnpj VARCHAR(20) NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    street VARCHAR(255) NULL,
    number VARCHAR(20) NULL,
    complement VARCHAR(255) NULL,
    neighborhood VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(50) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 4.2 unit_measure (unidades de medida)
CREATE TABLE unit_measure (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    abbreviation VARCHAR(15) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 4.3 ingredients (ingredientes)
CREATE TABLE ingredients (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    supplier_id BIGINT NOT NULL,
    unit_measure_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255),
    unit_price DECIMAL(10,2) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    min_quantity DECIMAL(10,2) NOT NULL,
    max_quantity DECIMAL(10,2) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_ingredients_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    CONSTRAINT fk_ingredients_unit_measure FOREIGN KEY (unit_measure_id) REFERENCES unit_measure(id)
);

-- 4.4 item_ingredients (ingredientes dos itens)
CREATE TABLE item_ingredients (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    item_id BIGINT NOT NULL,
    ingredient_id BIGINT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL, 
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_item_ingredients_item FOREIGN KEY (item_id) REFERENCES items(id),
    CONSTRAINT fk_item_ingredients_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

-- 4.5 ingredients_movements (movimentações do estoque)
CREATE TABLE ingredients_movements (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    movement_type VARCHAR(50) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    previous_quantity DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(10,2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_ingredients_movements_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
    CONSTRAINT fk_ingredients_movements_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ##############################
-- # 5. MESAS, PEDIDOS E ITENS DE PEDIDOS
-- ##############################

-- 5.1 tables (mesas)
CREATE TABLE tables (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    number INT NOT NULL,
    capacity INT NOT NULL,
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
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_order_delivery_address FOREIGN KEY (address_id) REFERENCES addresses(id)
);

-- 5.3 orders (pedidos)
CREATE TABLE orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    order_delivery_id BIGINT NULL,
    table_id BIGINT NULL,
    order_type VARCHAR(50) NOT NULL,
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
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
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
    item_id BIGINT NOT NULL,
    quantity INT NOT NULL, -- quantidade de itens
    total_price DECIMAL(10,2) NOT NULL, -- preço unitario de um item x quantidade
    notes VARCHAR(255),
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_order_items_item FOREIGN KEY (item_id) REFERENCES items(id)
);

-- 5.5 order_item_additionals (adicionais dos itens dos pedidos)
CREATE TABLE order_item_additionals (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_item_id BIGINT NOT NULL,
    additional_id BIGINT NOT NULL,
    quantity INT NOT NULL, -- quantidade de adicionais
    total_price DECIMAL(10,2) NOT NULL, -- preço unitario de um adicional x quantidade
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_order_item_additionals_order_item FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    CONSTRAINT fk_order_item_additionals_additional FOREIGN KEY (additional_id) REFERENCES additionals(id)
);

-- ##############################
-- # 6. RESERVAS E PAGAMENTOS
-- ##############################

-- 6.1 reservations (reservas)
CREATE TABLE reservations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    table_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    guests_count INT NOT NULL,
    notes VARCHAR(255),
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_reservations_table FOREIGN KEY (table_id) REFERENCES tables(id),
    CONSTRAINT fk_reservations_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 6.2 payment_types (tipos de pagamento)
CREATE TABLE payment_types (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- 6.3 coupons (cupons)
CREATE TABLE coupons (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
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
    deleted_at DATETIME NULL
);

-- 6.4 payments (pagamentos)
CREATE TABLE payments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    payment_type_id BIGINT NOT NULL,
    coupon_id BIGINT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 0, -- status de pagamento 1 = Pago | 0 = Não pago
    paid_at DATETIME NULL, -- data de pagamento 
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_payments_payment_type FOREIGN KEY (payment_type_id) REFERENCES payment_types(id),
    CONSTRAINT fk_payments_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id)
);

-- ##############################
-- # 7. BANNERS (Destaques no site)
-- ##############################

CREATE TABLE banners (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    subtitle VARCHAR(255) NULL,
    description VARCHAR(255),
    image_path VARCHAR(255) NULL,
    `order` INT NOT NULL DEFAULT 0,
    start_date DATE NULL,
    end_date DATE NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL
);

-- ##############################
-- # Indíces para acesso
-- ##############################

CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_order_date ON orders(order_date);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_items_category_id ON items(category_id);
