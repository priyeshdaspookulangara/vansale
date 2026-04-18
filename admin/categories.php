<?php
require_once '../includes/header.php';
requireRole(['Super Admin']);
require_once '../includes/config.php';
require_once '../includes/ProductManager.php';

$manager = new ProductManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manager->saveCategory($_POST);
    header("Location: categories.php?msg=success");
    exit;
}

$categories = $manager->getCategories();
?>

<div class="page-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Product Categories</h1>
            <p class="text-muted">Manage tax-level product groups.</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#catModal">+ New Category</button>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th class="text-end">Default GST %</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="text-end"><?= number_format($c['default_gst_rate'], 2) ?>%</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-link text-secondary p-0">Edit</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="catModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title">Category Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Default GST %</label>
                    <input type="number" step="0.01" name="gst_rate" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
