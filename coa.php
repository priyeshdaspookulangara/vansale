<?php
require_once 'includes/header.php';
require_once 'includes/config.php';
requireRole(['Super Admin', 'Accountant']);
require_once 'includes/COAManager.php';

$manager = new COAManager($pdo);
$groupsTree = $manager->getAccountGroupsTree();
$flatGroups = $manager->getAllGroupsFlat();

function renderGroup($group, $depth = 0) {
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
    $output = "
        <div class='group-node py-2 px-3 border-start border-primary mb-1' style='margin-left: " . ($depth * 20) . "px; background: #f9f9f9;'>
            <strong>{$group['name']}</strong> <span class='badge bg-info text-dark small'>{$group['type']}</span>
        </div>";

    foreach ($group['heads'] as $head) {
        $output .= "
            <div class='head-node d-flex justify-content-between align-items-center py-2 px-3 border-bottom ms-4' style='margin-left: " . (($depth + 1) * 20) . "px;'>
                <div>
                    <span class='text-muted small'>[{$head['code']}]</span> <strong>{$head['name']}</strong>
                </div>
                <div>
                    <button class='btn btn-sm btn-link text-secondary edit-head-btn' data-id='{$head['id']}'>Edit</button>
                    <button class='btn btn-sm btn-link text-danger delete-head-btn' data-id='{$head['id']}'>Delete</button>
                </div>
            </div>";
    }

    foreach ($group['children'] as $child) {
        $output .= renderGroup($child, $depth + 1);
    }

    return $output;
}
?>

<div class="page-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Chart of Accounts</h1>
            <p class="text-muted">Maintain your financial structure and ledger heads.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHeadModal">+ Add Account Head</button>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php foreach ($groupsTree as $rootGroup): ?>
                <?= renderGroup($rootGroup) ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Modal (Same as before but with modern styling) -->
<div class="modal fade" id="addHeadModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="headForm" method="POST" action="coa_action.php">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalTitle">Account Head Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create" id="formAction">
                    <input type="hidden" name="id" id="headId">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Parent Group</label>
                        <select class="form-select" name="group_id" id="groupId" required>
                            <?php foreach ($flatGroups as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= $g['name'] ?> (<?= $g['type'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Account Name</label>
                        <input type="text" class="form-control" name="name" id="headName" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Account Code</label>
                        <input type="text" class="form-control" name="code" id="headCode" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea class="form-control" name="description" id="headDesc"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Integration for edit buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-head-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch('coa_action.php?action=get&id=' + id)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('modalTitle').innerText = 'Edit Account Head';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('headId').value = data.id;
                        document.getElementById('groupId').value = data.group_id;
                        document.getElementById('headName').value = data.name;
                        document.getElementById('headCode').value = data.code;
                        document.getElementById('headDesc').value = data.description;
                        new bootstrap.Modal(document.getElementById('addHeadModal')).show();
                    });
            });
        });

        document.querySelectorAll('.delete-head-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this account?')) {
                    const id = this.dataset.id;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'coa_action.php';
                    form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="${id}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>
