<?php
/**
 * SalesTransactionManager.php
 *
 * Core logic for handling Spot Sales, Inventory updates,
 * GST calculations, and Double-Entry Accounting postings.
 *
 * Refined version following code review.
 */

class SalesTransactionManager {
    private $pdo;
    private $businessStateCode;

    // Account Code Mappings
    private $accountCodes = [
        'sales' => 'REV01',
        'cgst'  => 'CGST01',
        'sgst'  => 'SGST01',
        'igst'  => 'IGST01',
        'cash'  => 'CASH01',
        'ar'    => 'AR01'
    ];
    private $accountIds = [];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->loadBusinessSettings();
        $this->resolveAccountIds();
    }

    /**
     * Load core business settings like state code for GST calculation.
     */
    private function loadBusinessSettings() {
        $stmt = $this->pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'business_state_code'");
        $this->businessStateCode = $stmt->fetchColumn() ?: '27'; // Default if not set
    }

    /**
     * Resolve account IDs from their codes to avoid hardcoding.
     */
    private function resolveAccountIds() {
        $stmt = $this->pdo->prepare("SELECT id, code FROM account_heads WHERE code IN (" .
            implode(',', array_fill(0, count($this->accountCodes), '?')) . ")");
        $stmt->execute(array_values($this->accountCodes));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $codeToId = [];
        foreach ($results as $row) {
            $codeToId[$row['code']] = $row['id'];
        }

        foreach ($this->accountCodes as $key => $code) {
            if (!isset($codeToId[$code])) {
                throw new Exception("Account code '$code' ($key) not found in Chart of Accounts.");
            }
            $this->accountIds[$key] = $codeToId[$code];
        }
    }

    /**
     * Process a Spot Sale:
     * 1. Calculate Taxes (GST)
     * 2. Insert Invoice and Items
     * 3. Deduct Van Inventory (Strictly)
     * 4. Post Double-Entry Journal Records
     */
    public function processSpotSale(array $saleData) {
        try {
            $this->pdo->beginTransaction();

            // 1. Fetch Customer Info
            $customerStmt = $this->pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $customerStmt->execute([$saleData['customer_id']]);
            $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                throw new Exception("Customer not found.");
            }

            $isInterState = ($customer['state_code'] !== $this->businessStateCode);
            $invoiceNo = 'INV-' . time() . rand(100, 999);
            $invoiceDate = date('Y-m-d');

            // Prepare statements outside the loop
            $productStmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
            $updateInventoryStmt = $this->pdo->prepare("
                UPDATE van_inventory
                SET quantity = quantity - ?
                WHERE van_id = ? AND product_id = ? AND quantity >= ?
            ");

            // 2. Prepare Totals
            $totalTaxable = 0;
            $totalCGST = 0;
            $totalSGST = 0;
            $totalIGST = 0;
            $totalAmount = 0;

            $itemsToInsert = [];

            // 3. Process Line Items
            foreach ($saleData['items'] as $item) {
                $productStmt->execute([$item['product_id']]);
                $product = $productStmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception("Product ID {$item['product_id']} not found.");
                }

                $qty = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $taxableValue = $qty * $unitPrice;
                $gstRate = $product['gst_rate'];

                $cgstRate = 0; $cgstAmount = 0;
                $sgstRate = 0; $sgstAmount = 0;
                $igstRate = 0; $igstAmount = 0;

                if ($isInterState) {
                    $igstRate = $gstRate;
                    $igstAmount = round(($taxableValue * $igstRate) / 100, 2);
                } else {
                    $cgstRate = $gstRate / 2;
                    $sgstRate = $gstRate / 2;
                    $cgstAmount = round(($taxableValue * $cgstRate) / 100, 2);
                    $sgstAmount = round(($taxableValue * $sgstRate) / 100, 2);
                }

                $lineTotal = $taxableValue + $cgstAmount + $sgstAmount + $igstAmount;

                $totalTaxable += $taxableValue;
                $totalCGST += $cgstAmount;
                $totalSGST += $sgstAmount;
                $totalIGST += $igstAmount;
                $totalAmount += $lineTotal;

                $itemsToInsert[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'taxable_value' => $taxableValue,
                    'gst_rate' => $gstRate,
                    'cgst_rate' => $cgstRate,
                    'cgst_amount' => $cgstAmount,
                    'sgst_rate' => $sgstRate,
                    'sgst_amount' => $sgstAmount,
                    'igst_rate' => $igstRate,
                    'igst_amount' => $igstAmount,
                    'total_amount' => $lineTotal
                ];

                // 4. Update Van Inventory (Strict Deduction)
                $updateInventoryStmt->execute([$qty, $saleData['van_id'], $product['id'], $qty]);

                if ($updateInventoryStmt->rowCount() == 0) {
                    throw new Exception("Insufficient stock or product not found in van: {$product['name']}.");
                }
            }

            // 5. Insert Invoice
            $invoiceStmt = $this->pdo->prepare("
                INSERT INTO invoices (
                    customer_id, van_id, invoice_no, invoice_date,
                    total_taxable_value, total_cgst, total_sgst, total_igst,
                    total_amount, payment_mode, payment_status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $paymentStatus = ($saleData['payment_mode'] === 'Credit') ? 'Unpaid' : 'Paid';
            $invoiceStmt->execute([
                $saleData['customer_id'], $saleData['van_id'], $invoiceNo, $invoiceDate,
                $totalTaxable, $totalCGST, $totalSGST, $totalIGST,
                $totalAmount, $saleData['payment_mode'], $paymentStatus
            ]);
            $invoiceId = $this->pdo->lastInsertId();

            // 6. Insert Invoice Items
            $itemInsertStmt = $this->pdo->prepare("
                INSERT INTO invoice_items (
                    invoice_id, product_id, quantity, unit_price, taxable_value,
                    gst_rate, cgst_rate, cgst_amount, sgst_rate, sgst_amount,
                    igst_rate, igst_amount, total_amount
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            foreach ($itemsToInsert as $item) {
                $itemInsertStmt->execute([
                    $invoiceId, $item['product_id'], $item['quantity'], $item['unit_price'], $item['taxable_value'],
                    $item['gst_rate'], $item['cgst_rate'], $item['cgst_amount'], $item['sgst_rate'], $item['sgst_amount'],
                    $item['igst_rate'], $item['igst_amount'], $item['total_amount']
                ]);
            }

            // 7. DOUBLE-ENTRY ACCOUNTING POSTING
            $this->postJournalEntry($invoiceId, $invoiceNo, $invoiceDate, $saleData, $totalTaxable, $totalCGST, $totalSGST, $totalIGST, $totalAmount);

            $this->pdo->commit();
            return $invoiceId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Internal method to post automated accounting entries.
     */
    private function postJournalEntry($invoiceId, $invoiceNo, $invoiceDate, $saleData, $taxable, $cgst, $sgst, $igst, $total) {
        $jeStmt = $this->pdo->prepare("
            INSERT INTO journal_entries (entry_date, reference_no, description, source_type, source_id)
            VALUES (?, ?, ?, 'invoice', ?)
        ");
        $jeStmt->execute([$invoiceDate, $invoiceNo, "Sales Invoice #$invoiceNo", $invoiceId]);
        $jeId = $this->pdo->lastInsertId();

        $debitAccount = ($saleData['payment_mode'] === 'Credit') ? $this->accountIds['ar'] : $this->accountIds['cash'];

        $jiStmt = $this->pdo->prepare("
            INSERT INTO journal_items (journal_entry_id, account_id, debit, credit, memo)
            VALUES (?, ?, ?, ?, ?)
        ");

        // DEBIT: Cash or Accounts Receivable
        $jiStmt->execute([$jeId, $debitAccount, $total, 0, "Invoice total"]);

        // CREDIT: Sales Revenue
        $jiStmt->execute([$jeId, $this->accountIds['sales'], 0, $taxable, "Taxable value of goods"]);

        // CREDIT: Taxes
        if ($cgst > 0) $jiStmt->execute([$jeId, $this->accountIds['cgst'], 0, $cgst, "CGST Component"]);
        if ($sgst > 0) $jiStmt->execute([$jeId, $this->accountIds['sgst'], 0, $sgst, "SGST Component"]);
        if ($igst > 0) $jiStmt->execute([$jeId, $this->accountIds['igst'], 0, $igst, "IGST Component"]);
    }
}
