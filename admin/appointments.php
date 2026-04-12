<?php
require_once '../config/header.php';

if (!isAdmin()) {
    redirect('/medxpert/dashboard.php');
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $status = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();
}

// Get all appointments
$appointments = $conn->query("
    SELECT a.*, d.name as doctor_name, d.specialization, u.full_name as user_name, u.phone as user_phone 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
?>

<h1>Manage Appointments</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Admin</span>
    <span>/</span>
    <span>Manage Appointments</span>
</div>
</div>
</div>

<div class="content-wrapper">
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments->num_rows > 0): ?>
                        <?php while ($apt = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo date('M d, Y', strtotime($apt['appointment_date'])); ?></strong><br>
                                    <small><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($apt['user_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($apt['user_phone']); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($apt['doctor_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($apt['specialization']); ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?php echo ucfirst($apt['consultation_type']); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $badge_class = [
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge badge-<?php echo $badge_class[$apt['status']]; ?>">
                                        <?php echo ucfirst($apt['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display: flex; gap: 0.5rem;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $apt['id']; ?>">
                                        <select name="status" class="form-input" style="padding: 0.25rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $apt['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $apt['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="completed" <?php echo $apt['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $apt['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--gray);">No appointments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../config/footer.php'; ?>
