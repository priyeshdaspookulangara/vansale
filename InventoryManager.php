<?php
/**
 * InventoryManager.php
 *
 * Handles stock movements:
 * - Warehouse to Van Stock Transfer.
 * - Inventory tracking.
 */

class InventoryManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Transfer stock from a warehouse to a van.
     */
    public function transferStockToVan(array $transferData) {
        try {
            $this->pdo->beginTransaction();

            $refNo = 'ST-' . time() . rand(100, 999);
            $transferDate = date('Y-m-d');

            // 1. Insert Stock Transfer Header
            $stmt = $this->pdo->prepare("
                INSERT INTO stock_transfers (from_warehouse_id, to_van_id, transfer_date, reference_no, remarks)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $transferData['warehouse_id'],
                $transferData['van_id'],
                $transferDate,
                $refNo,
                $transferData['remarks'] ?? null
            ]);
            $transferId = $this->pdo->lastInsertId();

            // 2. Prepare statements for items and inventory updates
            $itemStmt = $this->pdo->prepare("INSERT INTO stock_transfer_items (transfer_id, product_id, quantity) VALUES (?, ?, ?)");

            $deductWarehouseStmt = $this->pdo->prepare("
                UPDATE warehouse_inventory
                SET quantity = quantity - ?
                WHERE warehouse_id = ? AND product_id = ? AND quantity >= ?
            ");

            $addVanStmt = $this->pdo->prepare("
                INSERT INTO van_inventory (van_id, product_id, quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
            ");

            // 3. Process each item
            foreach ($transferData['items'] as $item) {
                $qty = (float)$item['quantity'];
                $productId = (int)$item['product_id'];

                // Record transfer item
                $itemStmt->execute([$transferId, $productId, $qty]);

                // Deduct from warehouse
                $deductWarehouseStmt->execute([$qty, $transferData['warehouse_id'], $productId, $qty]);
                if ($deductWarehouseStmt->rowCount() == 0) {
                    throw new Exception("Insufficient stock in warehouse for product ID $productId.");
                }

                // Add to van
                $addVanStmt->execute([$transferData['van_id'], $productId, $qty]);
            }

            $this->pdo->commit();
            return $transferId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Fetch a transfer voucher and its items.
     */
    public function getTransferVoucher($id) {
        $stmt = $this->pdo->prepare("
            SELECT st.*, w.name as warehouse_name, v.van_number
            FROM stock_transfers st
            JOIN warehouses w ON st.from_warehouse_id = w.id
            JOIN vans v ON st.to_van_id = v.id
            WHERE st.id = ?
        ");
        $stmt->execute([$id]);
        $header = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$header) return null;

        $itemStmt = $this->pdo->prepare("
            SELECT sti.*, p.name as product_name
            FROM stock_transfer_items sti
            JOIN products p ON sti.product_id = p.id
            WHERE sti.transfer_id = ?
        ");
        $itemStmt->execute([$id]);
        $header['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        return $header;
    }
}
