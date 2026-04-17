<?php
/**
 * COAManager.php
 *
 * Handles Chart of Accounts (COA) operations:
 * - Fetching hierarchical groups and heads.
 * - CRUD operations for Account Heads.
 */

class COAManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all account groups organized hierarchically.
     */
    public function getAccountGroupsTree() {
        $stmt = $this->pdo->query("SELECT * FROM account_groups ORDER BY parent_id ASC, name ASC");
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tree = [];
        $groupMap = [];

        foreach ($groups as $group) {
            $group['children'] = [];
            $group['heads'] = $this->getHeadsByGroup($group['id']);
            $groupMap[$group['id']] = $group;
        }

        foreach ($groupMap as $id => &$group) {
            if ($group['parent_id'] === null) {
                $tree[] = &$group;
            } else {
                $groupMap[$group['parent_id']]['children'][] = &$group;
            }
        }

        return $tree;
    }

    /**
     * Get account heads for a specific group.
     */
    public function getHeadsByGroup($groupId) {
        $stmt = $this->pdo->prepare("SELECT * FROM account_heads WHERE group_id = ? ORDER BY name ASC");
        $stmt->execute([$groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all account groups as a flat list for select inputs.
     */
    public function getAllGroupsFlat() {
        $stmt = $this->pdo->query("SELECT id, name, type FROM account_groups ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * CRUD: Create Account Head
     */
    public function createAccountHead($data) {
        $stmt = $this->pdo->prepare("INSERT INTO account_heads (group_id, name, code, description) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['group_id'],
            $data['name'],
            $data['code'],
            $data['description'] ?? null
        ]);
    }

    /**
     * CRUD: Update Account Head
     */
    public function updateAccountHead($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE account_heads SET group_id = ?, name = ?, code = ?, description = ? WHERE id = ?");
        return $stmt->execute([
            $data['group_id'],
            $data['name'],
            $data['code'],
            $data['description'] ?? null,
            $id
        ]);
    }

    /**
     * CRUD: Delete Account Head
     */
    public function deleteAccountHead($id) {
        // Check if there are journal entries associated with this account
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM journal_items WHERE account_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cannot delete account head as it has existing transactions.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM account_heads WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get single account head by ID.
     */
    public function getAccountHead($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM account_heads WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
