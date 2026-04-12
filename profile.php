<?php
require_once 'config/header.php';

// Handle Profile Update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $user_id = $_SESSION['user_id'];
    
    // Handle Avatar Upload
    $avatar_sql = "";
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profiles/';
        $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
            $avatar_sql = ", avatar = '$file_name'";
            $_SESSION['avatar'] = $file_name;
        }
    }
    
    $query = "UPDATE users SET full_name = ?, phone = ?, address = ? $avatar_sql WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
    
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
        
        <form method="POST" class="card-body" enctype="multipart/form-data">
            <div class="form-group" style="text-align: center; margin-bottom: 2rem;">
                <div class="user-avatar" style="width: 100px; height: 100px; margin: 0 auto 1rem; font-size: 2.5rem;">
                    <?php if ($user['avatar'] && $user['avatar'] !== 'default-avatar.png'): ?>
                        <img src="uploads/profiles/<?php echo $user['avatar']; ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <label class="form-label">Profile Picture</label>
                <input type="file" name="avatar" class="form-input" accept="image/*">
            </div>

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
