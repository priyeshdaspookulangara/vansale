<?php
/**
 * salesman/save_order.php
 *
 * Processes pre-order booking form.
 */

require_once '../auth_check.php';
requireRole(['Van Salesman', 'Super Admin']);
require_once '../includes/config.php';
require_once '../includes/OrderManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $manager = new OrderManager($pdo);
        $orderData = [
            'customer_id' => $_POST['customer_id'],
            'van_id'      => $_POST['van_id'],
            'items'       => $_POST['items']
        ];

        $orderId = $manager->bookOrder($orderData);
        header("Location: order_list.php?msg=booked&id=$orderId");
        exit;
    } catch (Exception $e) {
        die("Error saving order: " . $e->getMessage());
    }
}
