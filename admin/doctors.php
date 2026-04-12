<?php
require_once '../config/header.php';

if (!isAdmin()) {
    redirect('/medxpert/dashboard.php');
}

// Handle Add/Edit Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $name = sanitize($_POST['name']);
    $specialization = sanitize($_POST['specialization']);
    $experience = (int)$_POST['experience'];
    $qualification = sanitize($_POST['qualification']);
    $hospital = sanitize($_POST['hospital']);
    $location = sanitize($_POST['location']);
    $fee = (float)$_POST['fee'];
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $available_days = sanitize($_POST['available_days']);
    $available_time = sanitize($_POST['available_time']);
    $bio = sanitize($_POST['bio']);
    $status = sanitize($_POST['status']);
    $image = 'default-doctor.png';

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/doctors/';
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = 'doctor_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $file_name;
        }
    }

    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, experience, qualification, hospital, location, fee, phone, email, available_days, available_time, bio, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssdsssssss", $name, $specialization, $experience, $qualification, $hospital, $location, $fee, $phone, $email, $available_days, $available_time, $bio, $status, $image);
        $stmt->execute();
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        // If no new image uploaded, keep the existing one
        if ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $stmt_get = $conn->prepare("SELECT image FROM doctors WHERE id=?");
            $stmt_get->bind_param("i", $id);
            $stmt_get->execute();
            $result = $stmt_get->get_result()->fetch_assoc();
            $image = $result['image'];
        }
        $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, experience=?, qualification=?, hospital=?, location=?, fee=?, phone=?, email=?, available_days=?, available_time=?, bio=?, status=?, image=? WHERE id=?");
        $stmt->bind_param("ssisssdsssssssi", $name, $specialization, $experience, $qualification, $hospital, $location, $fee, $phone, $email, $available_days, $available_time, $bio, $status, $image, $id);
        $stmt->execute();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: doctors.php");
    exit();
}

// Get all doctors
$doctors = $conn->query("SELECT * FROM doctors ORDER BY created_at DESC");
?>

<h1>Manage Doctors</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Admin</span>
    <span>/</span>
    <span>Manage Doctors</span>
</div>
</div>
<div class="top-actions">
    <button class="btn btn-primary" onclick="openModal('doctorModal'); setDoctorAction('add')">
        <i class="fas fa-plus"></i> Add New Doctor
    </button>
</div>
</div>

<div class="content-wrapper">
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Hospital</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($doctor = $doctors->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; background: #f0f0f0;">
                                        <?php if ($doctor['image'] && $doctor['image'] !== 'default-doctor.png'): ?>
                                            <img src="../uploads/doctors/<?php echo $doctor['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-user-md"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($doctor['name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($doctor['email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                            <td><?php echo htmlspecialchars($doctor['hospital']); ?></td>
                            <td>৳<?php echo number_format($doctor['fee'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $doctor['status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($doctor['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-secondary btn-sm" onclick='editDoctor(<?php echo json_encode($doctor); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $doctor['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this doctor?')">
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

<!-- Doctor Modal -->
<div class="modal" id="doctorModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Doctor</h3>
            <button class="modal-close" onclick="closeModal('doctorModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" class="modal-body" enctype="multipart/form-data">
            <input type="hidden" name="action" id="doctorAction" value="add">
            <input type="hidden" name="id" id="doctorId">
            
            <div class="form-group">
                <label class="form-label">Doctor Image</label>
                <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; background: #f0f0f0; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                    <img id="doctorImagePreview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <i id="doctorImagePlaceholder" class="fas fa-user-md" style="font-size: 2rem; color: #ccc;"></i>
                </div>
                <input type="file" name="image" id="doctorImage" class="form-input" accept="image/*" onchange="previewDoctorImage(event)">
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" id="doctorName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="specialization" id="doctorSpecialization" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Experience (Years)</label>
                    <input type="number" name="experience" id="doctorExperience" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Qualification</label>
                    <input type="text" name="qualification" id="doctorQualification" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hospital</label>
                    <input type="text" name="hospital" id="doctorHospital" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" id="doctorLocation" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Consultation Fee</label>
                    <input type="number" step="0.01" name="fee" id="doctorFee" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" id="doctorPhone" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="doctorEmail" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="doctorStatus" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Available Days (e.g., Mon-Fri)</label>
                <input type="text" name="available_days" id="doctorDays" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Available Time (e.g., 9:00 AM - 5:00 PM)</label>
                <input type="text" name="available_time" id="doctorTime" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea name="bio" id="doctorBio" class="form-input" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Doctor</button>
        </form>
    </div>
</div>

<script>
function previewDoctorImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('doctorImagePreview');
    const placeholder = document.getElementById('doctorImagePlaceholder');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
}

function setDoctorAction(action) {
    document.getElementById('doctorAction').value = action;
    document.getElementById('modalTitle').textContent = action === 'add' ? 'Add New Doctor' : 'Edit Doctor';
    if (action === 'add') {
        document.getElementById('doctorId').value = '';
        document.querySelectorAll('#doctorModal .form-input').forEach(input => input.value = '');
        document.getElementById('doctorStatus').value = 'active';
        document.getElementById('doctorImagePreview').style.display = 'none';
        document.getElementById('doctorImagePlaceholder').style.display = 'block';
    }
}

function editDoctor(doctor) {
    setDoctorAction('edit');
    document.getElementById('doctorId').value = doctor.id;
    document.getElementById('doctorName').value = doctor.name;
    document.getElementById('doctorSpecialization').value = doctor.specialization;
    document.getElementById('doctorExperience').value = doctor.experience;
    document.getElementById('doctorQualification').value = doctor.qualification;
    document.getElementById('doctorHospital').value = doctor.hospital;
    document.getElementById('doctorLocation').value = doctor.location;
    document.getElementById('doctorFee').value = doctor.fee;
    document.getElementById('doctorPhone').value = doctor.phone;
    document.getElementById('doctorEmail').value = doctor.email;
    document.getElementById('doctorDays').value = doctor.available_days;
    document.getElementById('doctorTime').value = doctor.available_time;
    document.getElementById('doctorBio').value = doctor.bio;
    document.getElementById('doctorStatus').value = doctor.status;
    
    // Show doctor image if exists
    if (doctor.image && doctor.image !== 'default-doctor.png') {
        document.getElementById('doctorImagePreview').src = '../uploads/doctors/' + doctor.image;
        document.getElementById('doctorImagePreview').style.display = 'block';
        document.getElementById('doctorImagePlaceholder').style.display = 'none';
    } else {
        document.getElementById('doctorImagePreview').style.display = 'none';
        document.getElementById('doctorImagePlaceholder').style.display = 'block';
    }
    
    openModal('doctorModal');
}
</script>

<?php require_once '../config/footer.php'; ?>
