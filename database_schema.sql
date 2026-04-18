-- Mini-ERP Database Schema (MySQL/MariaDB)

-- 1. ACCOUNTING MODULE
CREATE TABLE account_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    type ENUM('Asset', 'Liability', 'Equity', 'Income', 'Expense') NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES account_groups(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE account_heads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(20) UNIQUE,
    description TEXT,
    is_reconcilable BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (group_id) REFERENCES account_groups(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE journal_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entry_date DATE NOT NULL,
    reference_no VARCHAR(50),
    description TEXT,
    source_type VARCHAR(50), -- e.g., 'invoice', 'payment', 'manual'
    source_id INT,           -- ID of the source document
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (entry_date),
    INDEX (reference_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE journal_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_entry_id INT NOT NULL,
    account_id INT NOT NULL,
    debit DECIMAL(15,2) DEFAULT 0.00,
    credit DECIMAL(15,2) DEFAULT 0.00,
    memo TEXT,
    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES account_heads(id),
    INDEX (account_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. MASTERS MODULE
CREATE TABLE states (
    state_code VARCHAR(2) PRIMARY KEY,
    state_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    gstin VARCHAR(15),
    address TEXT,
    state_code VARCHAR(2) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    credit_limit DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (state_code) REFERENCES states(state_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    default_gst_rate DECIMAL(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) UNIQUE,
    hsn_code VARCHAR(20),
    base_price DECIMAL(15,2) NOT NULL,
    gst_rate DECIMAL(5,2) NOT NULL,
    unit_of_measure VARCHAR(20),
    FOREIGN KEY (category_id) REFERENCES product_categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. INVENTORY MODULE
CREATE TABLE warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE vans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    van_number VARCHAR(20) NOT NULL UNIQUE,
    driver_name VARCHAR(100),
    warehouse_id INT NOT NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE van_inventory (
    van_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(15,3) DEFAULT 0.000,
    PRIMARY KEY (van_id, product_id),
    FOREIGN KEY (van_id) REFERENCES vans(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE warehouse_inventory (
    warehouse_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(15,3) DEFAULT 0.000,
    PRIMARY KEY (warehouse_id, product_id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. SALES & ORDER MODULE
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    van_id INT NOT NULL,
    order_date DATE NOT NULL,
    status ENUM('Pending', 'Fulfilled', 'Cancelled') DEFAULT 'Pending',
    total_amount DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (van_id) REFERENCES vans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(15,3) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    total_price DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NULL, -- Optional, if converted from order
    customer_id INT NOT NULL,
    van_id INT NOT NULL,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    invoice_date DATE NOT NULL,
    total_taxable_value DECIMAL(15,2) NOT NULL,
    total_cgst DECIMAL(15,2) DEFAULT 0.00,
    total_sgst DECIMAL(15,2) DEFAULT 0.00,
    total_igst DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL,
    payment_status ENUM('Unpaid', 'Partially Paid', 'Paid') DEFAULT 'Unpaid',
    payment_mode ENUM('Cash', 'Bank Transfer', 'UPI', 'Credit') DEFAULT 'Cash',
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (van_id) REFERENCES vans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(15,3) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL, -- Price per unit (taxable value)
    taxable_value DECIMAL(15,2) NOT NULL, -- qty * unit_price
    gst_rate DECIMAL(5,2) NOT NULL,
    cgst_rate DECIMAL(5,2) DEFAULT 0.00,
    cgst_amount DECIMAL(15,2) DEFAULT 0.00,
    sgst_rate DECIMAL(5,2) DEFAULT 0.00,
    sgst_amount DECIMAL(15,2) DEFAULT 0.00,
    igst_rate DECIMAL(5,2) DEFAULT 0.00,
    igst_amount DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. SETTINGS
-- 5. STOCK TRANSFER MODULE
CREATE TABLE stock_transfers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_warehouse_id INT NOT NULL,
    to_van_id INT NOT NULL,
    transfer_date DATE NOT NULL,
    reference_no VARCHAR(50) UNIQUE,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (to_van_id) REFERENCES vans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE stock_transfer_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transfer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(15,3) NOT NULL,
    FOREIGN KEY (transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. SETTINGS
-- 6. AUTHENTICATION & RBAC MODULE
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Super Admin', 'Accountant', 'Van Salesman') NOT NULL,
    assigned_van_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_van_id) REFERENCES vans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. SETTINGS
CREATE TABLE settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Settings for GST
INSERT INTO settings (setting_key, setting_value) VALUES ('business_state_code', '27'); -- e.g., 27 for Maharashtra
INSERT INTO settings (setting_key, setting_value) VALUES ('business_name', 'My Van Sales ERP');
