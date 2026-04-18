<?php
require_once '../includes/header.php';
requireRole(['Super Admin']);
require_once '../includes/config.php';
require_once '../includes/ProductManager.php';

$manager = new ProductManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manager->saveProduct($_POST);
    header("Location: products.php?msg=success");
    exit;
}

$products = $manager->getProducts();
$categories = $manager->getCategories();
?>

<div class="page-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Products Master</h1>
            <p class="text-muted">Manage your inventory items and base pricing.</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#prodModal">+ New Product</button>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>HSN</th>
                            <th class="text-end">Base Price</th>
                            <th class="text-end">GST %</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($p['sku']) ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name']) ?></span></td>
                            <td><?= htmlspecialchars($p['hsn_code']) ?></td>
                            <td class="text-end">₹<?= number_format($p['base_price'], 2) ?></td>
                            <td class="text-end text-success"><?= number_format($p['gst_rate'], 2) ?>%</td>
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
</div>

<!-- Product Modal -->
<div class="modal fade" id="prodModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">SKU</label>
                        <input type="text" name="sku" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">HSN Code</label>
                        <input type="text" name="hsn_code" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">GST %</label>
                        <input type="number" step="0.01" name="gst_rate" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Base Selling Price (Taxable)</label>
                        <input type="number" step="0.01" name="base_price" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
