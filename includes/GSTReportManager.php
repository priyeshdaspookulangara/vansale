<?php
/**
 * GSTReportManager.php
 *
 * Handles generation of GST-related reports:
 * - GSTR-1 (B2B Sales).
 * - GSTR-1 (B2C Sales - future).
 * - GSTR-2 (Purchases - future).
 */

class GSTReportManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Generate GSTR-1 B2B Sales Report (Invoices for customers with GSTIN).
     */
    public function getB2BSalesReport($startDate, $endDate) {
        $sql = "
            SELECT
                i.invoice_no,
                i.invoice_date,
                c.name as customer_name,
                c.gstin as customer_gstin,
                c.state_code as customer_state_code,
                i.total_taxable_value,
                i.total_cgst,
                i.total_sgst,
                i.total_igst,
                i.total_amount
            FROM invoices i
            JOIN customers c ON i.customer_id = c.id
            WHERE i.invoice_date BETWEEN ? AND ?
            AND c.gstin IS NOT NULL AND c.gstin != ''
            ORDER BY i.invoice_date ASC, i.invoice_no ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
