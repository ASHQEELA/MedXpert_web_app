<?php
require_once '../config/header.php';

if (!isAdmin()) {
    redirect('/medxpert/dashboard.php');
}

// Handle Add/Edit Donor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $name = sanitize($_POST['name']);
    $blood_type = sanitize($_POST['blood_type']);
    $age = (int)$_POST['age'];
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $location = sanitize($_POST['location']);
    $available = isset($_POST['available']) ? 1 : 0;
    $medical_conditions = sanitize($_POST['medical_conditions']);

    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO blood_donors (name, blood_type, age, phone, email, location, available, medical_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssis", $name, $blood_type, $age, $phone, $email, $location, $available, $medical_conditions);
        $stmt->execute();
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE blood_donors SET name=?, blood_type=?, age=?, phone=?, email=?, location=?, available=?, medical_conditions=? WHERE id=?");
        $stmt->bind_param("ssisssisi", $name, $blood_type, $age, $phone, $email, $location, $available, $medical_conditions, $id);
        $stmt->execute();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM blood_donors WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: donors.php");
    exit();
}

// Get all donors
$donors = $conn->query("SELECT * FROM blood_donors ORDER BY created_at DESC");
?>

<h1>Manage Donors</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Admin</span>
    <span>/</span>
    <span>Manage Donors</span>
</div>
</div>
<div class="top-actions">
    <button class="btn btn-primary" onclick="openModal('donorModal'); setDonorAction('add')">
        <i class="fas fa-plus"></i> Add New Donor
    </button>
</div>
</div>

<div class="content-wrapper">
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Blood Type</th>
                        <th>Age</th>
                        <th>Location</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($donor = $donors->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($donor['name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($donor['email']); ?></small>
                            </td>
                            <td><span class="badge badge-danger"><?php echo htmlspecialchars($donor['blood_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($donor['age']); ?></td>
                            <td><?php echo htmlspecialchars($donor['location']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $donor['available'] ? 'success' : 'danger'; ?>">
                                    <?php echo $donor['available'] ? 'Available' : 'Unavailable'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-secondary btn-sm" onclick='editDonor(<?php echo json_encode($donor); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $donor['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this donor?')">
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

<!-- Donor Modal -->
<div class="modal" id="donorModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Donor</h3>
            <button class="modal-close" onclick="closeModal('donorModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="action" id="donorAction" value="add">
            <input type="hidden" name="id" id="donorId">
            
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" id="donorName" class="form-input" required>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Blood Type</label>
                    <select name="blood_type" id="donorBloodType" class="form-input" required>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Age</label>
                    <input type="number" name="age" id="donorAge" class="form-input" required>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" id="donorPhone" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="donorEmail" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" name="location" id="donorLocation" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Medical Conditions (Optional)</label>
                <textarea name="medical_conditions" id="donorMedical" class="form-input" rows="2"></textarea>
            </div>
            
            <div class="form-group d-flex align-center gap-1">
                <input type="checkbox" name="available" id="donorAvailable" value="1" checked>
                <label class="form-label" style="margin-bottom: 0;">Currently Available for Donation</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Donor</button>
        </form>
    </div>
</div>

<script>
function setDonorAction(action) {
    document.getElementById('donorAction').value = action;
    document.getElementById('modalTitle').textContent = action === 'add' ? 'Add New Donor' : 'Edit Donor';
    if (action === 'add') {
        document.getElementById('donorId').value = '';
        document.querySelectorAll('#donorModal .form-input').forEach(input => input.value = '');
        document.getElementById('donorAvailable').checked = true;
    }
}

function editDonor(donor) {
    setDonorAction('edit');
    document.getElementById('donorId').value = donor.id;
    document.getElementById('donorName').value = donor.name;
    document.getElementById('donorBloodType').value = donor.blood_type;
    document.getElementById('donorAge').value = donor.age;
    document.getElementById('donorPhone').value = donor.phone;
    document.getElementById('donorEmail').value = donor.email;
    document.getElementById('donorLocation').value = donor.location;
    document.getElementById('donorMedical').value = donor.medical_conditions;
    document.getElementById('donorAvailable').checked = donor.available == 1;
    openModal('donorModal');
}
</script>

<?php require_once '../config/footer.php'; ?>
