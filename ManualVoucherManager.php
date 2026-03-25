<?php
/**
 * ManualVoucherManager.php
 *
 * Handles manual accounting vouchers:
 * - Receipt, Payment, Journal, Contra.
 */

class ManualVoucherManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a manual voucher.
     *
     * @param array $voucherData {
     *      'type': 'Receipt'|'Payment'|'Journal'|'Contra',
     *      'date': 'YYYY-MM-DD',
     *      'reference_no': string,
     *      'description': string,
     *      'items': [
     *          ['account_id': int, 'debit': decimal, 'credit': decimal, 'memo': string],
     *          ...
     *      ]
     * }
     */
    public function createVoucher(array $voucherData) {
        try {
            $this->pdo->beginTransaction();

            // 1. Validate Balance
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($voucherData['items'] as $item) {
                $totalDebit += (float)($item['debit'] ?? 0);
                $totalCredit += (float)($item['credit'] ?? 0);
            }

            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new Exception("Voucher is out of balance. Debits ($totalDebit) must equal Credits ($totalCredit).");
            }

            // 2. Insert Journal Entry Header
            $stmt = $this->pdo->prepare("
                INSERT INTO journal_entries (entry_date, reference_no, description, source_type)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $voucherData['date'],
                $voucherData['reference_no'],
                $voucherData['description'],
                'manual_' . strtolower($voucherData['type'])
            ]);
            $jeId = $this->pdo->lastInsertId();

            // 3. Insert Journal Items
            $itemStmt = $this->pdo->prepare("
                INSERT INTO journal_items (journal_entry_id, account_id, debit, credit, memo)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($voucherData['items'] as $item) {
                $itemStmt->execute([
                    $jeId,
                    $item['account_id'],
                    $item['debit'] ?? 0,
                    $item['credit'] ?? 0,
                    $item['memo'] ?? null
                ]);
            }

            $this->pdo->commit();
            return $jeId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
