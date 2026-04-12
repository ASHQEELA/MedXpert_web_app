<?php
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    redirect('/medxpert/index.php');
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedXpert - Modern Medical Platform</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/medxpert/assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <h2><i class="fas fa-heartbeat"></i> MedXpert</h2>
                    <p>Your Health, Our Priority</p>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-title">Main</div>
                    <a href="/medxpert/dashboard.php" class="menu-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/medxpert/profile.php" class="menu-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="/medxpert/my_appointments.php" class="menu-item <?php echo $current_page === 'my_appointments' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Appointments</span>
                    </a>
                    <a href="/medxpert/my_orders.php" class="menu-item <?php echo $current_page === 'my_orders' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-bag"></i>
                        <span>My Orders</span>
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-title">Medical Services</div>
                    <a href="/medxpert/modules/consult.php" class="menu-item <?php echo $current_page === 'consult' ? 'active' : ''; ?>">
                        <i class="fas fa-user-md"></i>
                        <span>Doctor Consultation</span>
                    </a>
                    <a href="/medxpert/modules/blood.php" class="menu-item <?php echo $current_page === 'blood' ? 'active' : ''; ?>">
                        <i class="fas fa-tint"></i>
                        <span>Blood Bank</span>
                    </a>
                    <a href="/medxpert/modules/pharmacy.php" class="menu-item <?php echo $current_page === 'pharmacy' ? 'active' : ''; ?>">
                        <i class="fas fa-pills"></i>
                        <span>Online Pharmacy</span>
                    </a>
                    <a href="/medxpert/modules/ambulance.php" class="menu-item <?php echo $current_page === 'ambulance' ? 'active' : ''; ?>">
                        <i class="fas fa-ambulance"></i>
                        <span>Ambulance Service</span>
                    </a>
                </div>
                
                <?php if (isAdmin()): ?>
                <div class="menu-section">
                    <div class="menu-title">Administration</div>
                    <a href="/medxpert/admin/index.php" class="menu-item <?php echo $current_page === 'index' && strpos($_SERVER['PHP_SELF'], 'admin') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Admin Dashboard</span>
                    </a>
                    <a href="/medxpert/admin/doctors.php" class="menu-item">
                        <i class="fas fa-stethoscope"></i>
                        <span>Manage Doctors</span>
                    </a>
                    <a href="/medxpert/admin/donors.php" class="menu-item">
                        <i class="fas fa-hand-holding-medical"></i>
                        <span>Manage Donors</span>
                    </a>
                    <a href="/medxpert/admin/medicines.php" class="menu-item">
                        <i class="fas fa-capsules"></i>
                        <span>Manage Medicines</span>
                    </a>
                    <a href="/medxpert/admin/orders.php" class="menu-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Orders</span>
                    </a>
                    <a href="/medxpert/admin/appointments.php" class="menu-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Appointments</span>
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="menu-section">
                    <div class="menu-title">Account</div>
                    <a href="/medxpert/api/logout.php" class="menu-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <h4><?php echo htmlspecialchars($_SESSION['full_name']); ?></h4>
                        <p><?php echo ucfirst($_SESSION['role']); ?></p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">