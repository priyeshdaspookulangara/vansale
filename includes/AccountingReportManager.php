<?php
/**
 * AccountingReportManager.php
 *
 * Handles generation of financial reports:
 * - Trial Balance.
 * - Balance Sheet (future).
 * - P&L (future).
 */

class AccountingReportManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Generate Trial Balance as of a specific date.
     * Aggregates debits and credits from journal_items for each account_head.
     */
    public function getTrialBalance($asOfDate) {
        $sql = "
            SELECT
                ah.name as account_name,
                ah.code as account_code,
                ag.type as account_type,
                SUM(ji.debit) as total_debit,
                SUM(ji.credit) as total_credit
            FROM account_heads ah
            JOIN account_groups ag ON ah.group_id = ag.id
            LEFT JOIN journal_items ji ON ah.id = ji.account_id
            LEFT JOIN journal_entries je ON ji.journal_entry_id = je.id
            WHERE je.entry_date <= ? OR je.id IS NULL
            GROUP BY ah.id, ah.name, ah.code, ag.type
            HAVING total_debit != 0 OR total_credit != 0
            ORDER BY ag.type, ah.name
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$asOfDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate Day Book for a specific date.
     * Shows all transactions in chronological order.
     */
    public function getDayBook($date) {
        $sql = "
            SELECT
                je.id as entry_id,
                je.entry_date,
                je.reference_no,
                je.description,
                je.source_type,
                ji.debit,
                ji.credit,
                ah.name as account_name,
                ah.code as account_code
            FROM journal_entries je
            JOIN journal_items ji ON je.id = ji.journal_entry_id
            JOIN account_heads ah ON ji.account_id = ah.id
            WHERE je.entry_date = ?
            ORDER BY je.created_at ASC, ji.debit DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
