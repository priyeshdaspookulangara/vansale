<?php
/**
 * includes/ProductManager.php
 *
 * Handles Master Data for Products and Categories.
 */

class ProductManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // --- CATEGORY CRUD ---
    public function getCategories() {
        return $this->pdo->query("SELECT * FROM product_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveCategory($data) {
        if (isset($data['id']) && !empty($data['id'])) {
            $stmt = $this->pdo->prepare("UPDATE product_categories SET name = ?, default_gst_rate = ? WHERE id = ?");
            return $stmt->execute([$data['name'], $data['gst_rate'], $data['id']]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO product_categories (name, default_gst_rate) VALUES (?, ?)");
            return $stmt->execute([$data['name'], $data['gst_rate']]);
        }
    }

    // --- PRODUCT CRUD ---
    public function getProducts() {
        return $this->pdo->query("
            SELECT p.*, pc.name as category_name
            FROM products p
            JOIN product_categories pc ON p.category_id = pc.id
            ORDER BY p.name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveProduct($data) {
        if (isset($data['id']) && !empty($data['id'])) {
            $stmt = $this->pdo->prepare("
                UPDATE products
                SET category_id = ?, name = ?, sku = ?, hsn_code = ?, base_price = ?, gst_rate = ?
                WHERE id = ?
            ");
            return $stmt->execute([$data['category_id'], $data['name'], $data['sku'], $data['hsn_code'], $data['base_price'], $data['gst_rate'], $data['id']]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO products (category_id, name, sku, hsn_code, base_price, gst_rate)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$data['category_id'], $data['name'], $data['sku'], $data['hsn_code'], $data['base_price'], $data['gst_rate']]);
        }
    }
}
