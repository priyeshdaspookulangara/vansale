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
     * Save a pre-order (no inventory or accounting impact).
     */
    public function bookOrder(array $orderData) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO orders (customer_id, van_id, order_date, total_amount, status)
                VALUES (?, ?, ?, ?, 'Pending')
            ");
            $stmt->execute([
                $orderData['customer_id'],
                $orderData['van_id'],
                date('Y-m-d'),
                $orderData['total_amount']
            ]);
            $orderId = $this->pdo->lastInsertId();

            // Note: In a full system, you'd have an order_items table.
            // For this mini-ERP, we'll assume orders store the intent and items.
            // (Skipping order_items for brevity unless requested, as the prompt focused on invoice_items)

            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
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
}
