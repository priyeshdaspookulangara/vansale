<?php
/**
 * example_usage.php
 *
 * Demonstrates how to use the SalesTransactionManager to process a spot sale.
 */

require 'includes/config.php';
require 'includes/SalesTransactionManager.php';

try {
    $manager = new SalesTransactionManager($pdo);

    // Sample sale data:
    // - Local Customer (ID 1)
    // - Van (ID 1)
    // - Product (ID 1) - 5 units at 260.00 each
    $saleData = [
        'customer_id' => 1,
        'van_id' => 1,
        'payment_mode' => 'Cash',
        'items' => [
            [
                'product_id' => 1,
                'quantity' => 5,
                'unit_price' => 260.00
            ]
        ]
    ];

    echo "Processing spot sale...\n";
    $invoiceId = $manager->processSpotSale($saleData);
    echo "Sale processed successfully. Invoice ID: " . $invoiceId . "\n";

} catch (Exception $e) {
    echo "Error processing sale: " . $e->getMessage() . "\n";
}
