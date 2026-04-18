<?php
/**
 * includes/OrderManager.php
 *
 * Handles pre-orders and their lifecycle (Booking -> Fulfillment).
 */

class OrderManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Save a pre-order with line items.
     */
    public function bookOrder(array $orderData) {
        try {
            $this->pdo->beginTransaction();

            // 1. Calculate Total Amount and fetch item details
            $totalAmount = 0;
            $itemsToSave = [];

            $prodStmt = $this->pdo->prepare("SELECT base_price FROM products WHERE id = ?");

            foreach ($orderData['items'] as $item) {
                $prodStmt->execute([$item['product_id']]);
                $price = $prodStmt->fetchColumn();
                $qty = (float)$item['quantity'];
                $lineTotal = $price * $qty;

                $totalAmount += $lineTotal;
                $itemsToSave[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $lineTotal
                ];
            }

            // 2. Insert Order Header
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (customer_id, van_id, order_date, total_amount, status)
                VALUES (?, ?, ?, ?, 'Pending')
            ");
            $stmt->execute([
                $orderData['customer_id'],
                $orderData['van_id'],
                date('Y-m-d'),
                $totalAmount
            ]);
            $orderId = $this->pdo->lastInsertId();

            // 3. Insert Order Items
            $itemInsertStmt = $this->pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($itemsToSave as $item) {
                $itemInsertStmt->execute([
                    $orderId, $item['product_id'], $item['quantity'], $item['unit_price'], $item['total_price']
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Fetch pending orders for a van.
     */
    public function getPendingOrders($vanId) {
        $stmt = $this->pdo->prepare("
            SELECT o.*, c.name as customer_name
            FROM orders o
            JOIN customers c ON o.customer_id = c.id
            WHERE o.van_id = ? AND o.status = 'Pending'
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$vanId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order details including items.
     */
    public function getOrderDetails($orderId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $itemStmt = $this->pdo->prepare("
                SELECT oi.*, p.name as product_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $itemStmt->execute([$orderId]);
            $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $order;
    }
}
