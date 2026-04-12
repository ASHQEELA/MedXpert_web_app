<?php
require_once 'config/header.php';

// Handle Profile Update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $phone, $address, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $_SESSION['full_name'] = $full_name;
        $message = '<div class="alert alert-success">Profile updated successfully!</div>';
    } else {
        $message = '<div class="alert alert-error">Failed to update profile.</div>';
    }
}

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<h1>My Profile</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Profile</span>
</div>
</div>
</div>

<div class="content-wrapper">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3 class="card-title">Edit Personal Information</h3>
        </div>
        
        <?php echo $message; ?>
        
        <form method="POST" class="card-body">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-input" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address (Cannot be changed)</label>
                <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-input" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Member Since</label>
                <p style="color: var(--gray);"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>
</div>

<?php require_once 'config/footer.php'; ?>
