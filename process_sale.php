<?php
/**
 * process_sale.php
 *
 * Simple controller to handle form submissions from the index.php.
 */

require 'config.php';
require 'SalesTransactionManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $manager = new SalesTransactionManager($pdo);

        $saleData = [
            'customer_id'  => (int)$_POST['customer_id'],
            'van_id'       => (int)$_POST['van_id'],
            'payment_mode' => $_POST['payment_mode'],
            'items'        => $_POST['items'] // Items come in as an array from the form
        ];

        $invoiceId = $manager->processSpotSale($saleData);

        echo "<h1>Success!</h1>";
        echo "<p>Spot Sale processed. Invoice ID: " . htmlspecialchars($invoiceId) . "</p>";
        echo '<a href="index.php" class="btn btn-primary">Go Back</a>';

    } catch (Exception $e) {
        echo "<h1>Error</h1>";
        echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo '<a href="index.php">Go Back</a>';
    }
} else {
    header("Location: index.php");
    exit();
}
