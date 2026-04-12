<?php
require_once '../config/header.php';

if (!isAdmin()) {
    redirect('/medxpert/dashboard.php');
}

// Handle Add/Edit Medicine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $name = sanitize($_POST['name']);
    $category = sanitize($_POST['category']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $description = sanitize($_POST['description']);
    $manufacturer = sanitize($_POST['manufacturer']);
    $dosage = sanitize($_POST['dosage']);
    $prescription_required = isset($_POST['prescription_required']) ? 1 : 0;
    $status = sanitize($_POST['status']);

    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO medicines (name, category, price, stock, description, manufacturer, dosage, prescription_required, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssssis", $name, $category, $price, $stock, $description, $manufacturer, $dosage, $prescription_required, $status);
        $stmt->execute();
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE medicines SET name=?, category=?, price=?, stock=?, description=?, manufacturer=?, dosage=?, prescription_required=?, status=? WHERE id=?");
        $stmt->bind_param("ssdssssisi", $name, $category, $price, $stock, $description, $manufacturer, $dosage, $prescription_required, $status, $id);
        $stmt->execute();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: medicines.php");
    exit();
}

// Get all medicines
$medicines = $conn->query("SELECT * FROM medicines ORDER BY created_at DESC");
?>

<h1>Manage Medicines</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Admin</span>
    <span>/</span>
    <span>Manage Medicines</span>
</div>
</div>
<div class="top-actions">
    <button class="btn btn-primary" onclick="openModal('medicineModal'); setMedicineAction('add')">
        <i class="fas fa-plus"></i> Add New Medicine
    </button>
</div>
</div>

<div class="content-wrapper">
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($med = $medicines->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($med['name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($med['manufacturer']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($med['category']); ?></td>
                            <td>৳<?php echo number_format($med['price'], 2); ?></td>
                            <td><?php echo $med['stock']; ?></td>
                            <td>
                                <span class="badge badge-<?php echo $med['status'] === 'available' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($med['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-secondary btn-sm" onclick='editMedicine(<?php echo json_encode($med); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $med['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this medicine?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Medicine Modal -->
<div class="modal" id="medicineModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Medicine</h3>
            <button class="modal-close" onclick="closeModal('medicineModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="action" id="medicineAction" value="add">
            <input type="hidden" name="id" id="medicineId">
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Medicine Name</label>
                    <input type="text" name="name" id="medicineName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" id="medicineCategory" class="form-input" required>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Price (৳)</label>
                    <input type="number" step="0.01" name="price" id="medicinePrice" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock" id="medicineStock" class="form-input" required>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" name="manufacturer" id="medicineManufacturer" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Dosage (e.g., 500mg)</label>
                    <input type="text" name="dosage" id="medicineDosage" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="medicineDescription" class="form-input" rows="3"></textarea>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="medicineStatus" class="form-input">
                        <option value="available">Available</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
                <div class="form-group d-flex align-center gap-1" style="margin-top: 1.5rem;">
                    <input type="checkbox" name="prescription_required" id="medicinePrescription" value="1">
                    <label class="form-label" style="margin-bottom: 0;">Prescription Required</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Medicine</button>
        </form>
    </div>
</div>

<script>
function setMedicineAction(action) {
    document.getElementById('medicineAction').value = action;
    document.getElementById('modalTitle').textContent = action === 'add' ? 'Add New Medicine' : 'Edit Medicine';
    if (action === 'add') {
        document.getElementById('medicineId').value = '';
        document.querySelectorAll('#medicineModal .form-input').forEach(input => input.value = '');
        document.getElementById('medicineStatus').value = 'available';
        document.getElementById('medicinePrescription').checked = false;
    }
}

function editMedicine(med) {
    setMedicineAction('edit');
    document.getElementById('medicineId').value = med.id;
    document.getElementById('medicineName').value = med.name;
    document.getElementById('medicineCategory').value = med.category;
    document.getElementById('medicinePrice').value = med.price;
    document.getElementById('medicineStock').value = med.stock;
    document.getElementById('medicineManufacturer').value = med.manufacturer;
    document.getElementById('medicineDosage').value = med.dosage;
    document.getElementById('medicineDescription').value = med.description;
    document.getElementById('medicineStatus').value = med.status;
    document.getElementById('medicinePrescription').checked = med.prescription_required == 1;
    openModal('medicineModal');
}
</script>

<?php require_once '../config/footer.php'; ?>
