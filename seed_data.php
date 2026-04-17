<?php
/**
 * seed_data.php
 *
 * Populates sample data for demonstration.
 */

require 'includes/config.php';

try {
    $pdo->beginTransaction();

    // 1. Account Groups
    $pdo->exec("INSERT INTO account_groups (id, name, type) VALUES (1, 'Current Assets', 'Asset')");
    $pdo->exec("INSERT INTO account_groups (id, name, type) VALUES (2, 'Current Liabilities', 'Liability')");
    $pdo->exec("INSERT INTO account_groups (id, name, type) VALUES (3, 'Direct Income', 'Income')");

    // 2. Account Heads (Standard COA)
    $pdo->exec("INSERT INTO account_heads (id, group_id, name, code) VALUES (1001, 1, 'Cash in Hand', 'CASH01')");
    $pdo->exec("INSERT INTO account_heads (id, group_id, name, code) VALUES (1002, 1, 'Accounts Receivable', 'AR01')");
    $pdo->exec("INSERT INTO account_heads (id, group_id, name, code) VALUES (2001, 2, 'CGST Payable', 'CGST01')");
    $pdo->exec("INSERT INTO account_heads (id, group_id, name, code) VALUES (2002, 2, 'SGST Payable', 'SGST01')");
    $pdo->exec("INSERT INTO account_heads (id, group_id, name, code) VALUES (2003, 2, 'IGST Payable', 'IGST01')");
    $pdo->exec("INSERT INTO account_heads (id, group_id, name, code) VALUES (4001, 3, 'Sales Revenue', 'REV01')");

    // 3. States
    $pdo->exec("INSERT INTO states (state_code, state_name) VALUES ('27', 'Maharashtra'), ('33', 'Tamil Nadu')");

    // 4. Customers
    $pdo->exec("INSERT INTO customers (name, gstin, state_code) VALUES ('Local B2B Customer', '27ABCDE1234F1Z5', '27')");
    $pdo->exec("INSERT INTO customers (name, gstin, state_code) VALUES ('Inter-state B2B Customer', '33FGHIJ5678K1Z5', '33')");

    // 5. Product Categories
    $pdo->exec("INSERT INTO product_categories (id, name, default_gst_rate) VALUES (1, 'FMCG Goods', 18.00)");

    // 6. Products
    $pdo->exec("INSERT INTO products (id, category_id, name, sku, hsn_code, base_price, gst_rate) VALUES (1, 1, 'Wheat Flour 5kg', 'WF5KG', '1101', 250.00, 18.00)");

    // 7. Warehouse and Vans
    $pdo->exec("INSERT INTO warehouses (id, name) VALUES (1, 'Central Warehouse')");
    $pdo->exec("INSERT INTO vans (id, van_number, warehouse_id) VALUES (1, 'MH-12-AB-1234', 1)");

    // 8. Van Inventory (Initial Stock)
    $pdo->exec("INSERT INTO van_inventory (van_id, product_id, quantity) VALUES (1, 1, 100)");

    // 9. Users (Hashed Passwords)
    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $accountantHash = password_hash('acc123', PASSWORD_DEFAULT);
    $salesmanHash = password_hash('sales123', PASSWORD_DEFAULT);

    $userStmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, assigned_van_id) VALUES (?, ?, ?, ?, ?)");
    $userStmt->execute(['admin', 'admin@erp.com', $adminHash, 'Super Admin', null]);
    $userStmt->execute(['accountant', 'acc@erp.com', $accountantHash, 'Accountant', null]);
    $userStmt->execute(['salesman1', 'sales@erp.com', $salesmanHash, 'Van Salesman', 1]);

    $pdo->commit();
    echo "Sample data seeded successfully.";

} catch (Exception $e) {
    $pdo->rollBack();
    die("Seeding failed: " . $e->getMessage());
}
