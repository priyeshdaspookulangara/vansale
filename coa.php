<?php require_once "auth_check.php"; ?>
requireRole(["Super Admin", "Accountant"]);
<?php
/**
 * coa.php
 *
 * Chart of Accounts (COA) Management Interface.
 */

require 'includes/config.php';
require 'includes/COAManager.php';

$manager = new COAManager($pdo);
$groupsTree = $manager->getAccountGroupsTree();
$flatGroups = $manager->getAllGroupsFlat();

function renderGroup($group, $depth = 0) {
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
    $output = "
        <div class='group-node py-2 px-3 border-start border-primary mb-1' style='margin-left: " . ($depth * 20) . "px; background: #f0f7ff;'>
            <strong>{$group['name']}</strong> <span class='badge bg-info text-dark small'>{$group['type']}</span>
        </div>";

    // Heads in this group
    foreach ($group['heads'] as $head) {
        $output .= "
            <div class='head-node d-flex justify-content-between align-items-center py-2 px-3 border-bottom ms-4' style='margin-left: " . (($depth + 1) * 20) . "px;'>
                <div>
                    <span class='text-muted small'>[{$head['code']}]</span> <strong>{$head['name']}</strong>
                </div>
                <div>
                    <button class='btn btn-sm btn-outline-secondary edit-head-btn' data-id='{$head['id']}'>Edit</button>
                    <button class='btn btn-sm btn-outline-danger delete-head-btn' data-id='{$head['id']}'>Delete</button>
                </div>
            </div>";
    }

    // Recurse children groups
    foreach ($group['children'] as $child) {
        $output .= renderGroup($child, $depth + 1);
    }

    return $output;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart of Accounts - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .group-node { border-left-width: 4px !important; }
        .head-node { background-color: #fff; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Van Sales ERP</a>
    </div>
</nav>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Chart of Accounts</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHeadModal">+ Add Account Head</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php foreach ($groupsTree as $rootGroup): ?>
                <?= renderGroup($rootGroup) ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addHeadModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="headForm" method="POST" action="coa_action.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Account Head</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create" id="formAction">
                    <input type="hidden" name="id" id="headId">

                    <div class="mb-3">
                        <label class="form-label">Parent Group</label>
                        <select class="form-select" name="group_id" id="groupId" required>
                            <?php foreach ($flatGroups as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= $g['name'] ?> (<?= $g['type'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Name</label>
                        <input type="text" class="form-control" name="name" id="headName" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Code</label>
                        <input type="text" class="form-control" name="code" id="headCode" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="headDesc"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Handle Edit Button Click
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

    // Handle Delete Button Click
    document.querySelectorAll('.delete-head-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this account?')) {
                const id = this.dataset.id;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'coa_action.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
</script>

</body>
</html>
