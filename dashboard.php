<?php
require_once 'config/header.php';

// Get statistics
$stats = [];
$stats['total_doctors'] = $conn->query("SELECT COUNT(*) as count FROM doctors WHERE status = 'active'")->fetch_assoc()['count'];
$stats['total_donors'] = $conn->query("SELECT COUNT(*) as count FROM blood_donors WHERE available = 1")->fetch_assoc()['count'];
$stats['total_medicines'] = $conn->query("SELECT COUNT(*) as count FROM medicines WHERE status = 'available'")->fetch_assoc()['count'];
$stats['total_appointments'] = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE user_id = {$_SESSION['user_id']}")->fetch_assoc()['count'];

// Get recent appointments
$recent_appointments = $conn->query("
    SELECT a.*, d.name as doctor_name, d.specialization 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    WHERE a.user_id = {$_SESSION['user_id']} 
    ORDER BY a.created_at DESC 
    LIMIT 5
");

// Get recent orders
$recent_orders = $conn->query("
    SELECT * FROM medicine_orders 
    WHERE user_id = {$_SESSION['user_id']} 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>

<h1>Dashboard</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Dashboard</span>
</div>
</div>

<div class="top-actions">
    <button class="notification-btn">
        <i class="fas fa-bell"></i>
        <span class="notification-badge">3</span>
    </button>
</div>
</div>

<div class="content-wrapper">
    <!-- Welcome Message -->
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; margin-bottom: 2rem;">
        <h2 style="color: white; margin-bottom: 0.5rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h2>
        <p style="color: rgba(255,255,255,0.9); margin: 0;">Here's your health dashboard overview</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?php echo $stats['total_doctors']; ?></div>
                    <div class="stat-label">Available Doctors</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 12% this month
                    </div>
                </div>
                <div class="stat-icon blue">
                    <i class="fas fa-user-md"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?php echo $stats['total_donors']; ?></div>
                    <div class="stat-label">Blood Donors</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 8% this month
                    </div>
                </div>
                <div class="stat-icon green">
                    <i class="fas fa-tint"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?php echo $stats['total_medicines']; ?></div>
                    <div class="stat-label">Medicines Available</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 15% this month
                    </div>
                </div>
                <div class="stat-icon orange">
                    <i class="fas fa-pills"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?php echo $stats['total_appointments']; ?></div>
                    <div class="stat-label">Your Appointments</div>
                    <div class="stat-change positive">
                        <i class="fas fa-check"></i> All time
                    </div>
                </div>
                <div class="stat-icon purple">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <h2 style="margin-bottom: 1.5rem;">Quick Access</h2>
    <div class="modules-grid">
        <a href="modules/consult.php" class="module-card">
            <div class="module-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <h3>Doctor Consultation</h3>
            <p>Book appointments with specialist doctors</p>
            <button class="btn btn-primary btn-sm">
                Explore Doctors <i class="fas fa-arrow-right"></i>
            </button>
        </a>
        
        <a href="modules/blood.php" class="module-card">
            <div class="module-icon">
                <i class="fas fa-tint"></i>
            </div>
            <h3>Blood Bank</h3>
            <p>Find blood donors in your area</p>
            <button class="btn btn-primary btn-sm">
                Find Donors <i class="fas fa-arrow-right"></i>
            </button>
        </a>
        
        <a href="modules/pharmacy.php" class="module-card">
            <div class="module-icon">
                <i class="fas fa-pills"></i>
            </div>
            <h3>Online Pharmacy</h3>
            <p>Order medicines with home delivery</p>
            <button class="btn btn-primary btn-sm">
                Browse Medicines <i class="fas fa-arrow-right"></i>
            </button>
        </a>
        
        <a href="modules/ambulance.php" class="module-card">
            <div class="module-icon">
                <i class="fas fa-ambulance"></i>
            </div>
            <h3>Ambulance Service</h3>
            <p>24/7 emergency ambulance service</p>
            <button class="btn btn-primary btn-sm">
                Request Now <i class="fas fa-arrow-right"></i>
            </button>
        </a>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <!-- Recent Appointments -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Appointments</h3>
                <a href="modules/consult.php" style="color: var(--primary-light); font-size: 0.9rem;">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_appointments->num_rows > 0): ?>
                            <?php while ($apt = $recent_appointments->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($apt['doctor_name']); ?></strong><br>
                                        <small style="color: var(--gray);"><?php echo htmlspecialchars($apt['specialization']); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($apt['appointment_date'])); ?></td>
                                    <td>
                                        <?php
                                        $badge_class = [
                                            'pending' => 'badge-warning',
                                            'confirmed' => 'badge-success',
                                            'completed' => 'badge-info',
                                            'cancelled' => 'badge-danger'
                                        ];
                                        ?>
                                        <span class="badge <?php echo $badge_class[$apt['status']]; ?>">
                                            <?php echo ucfirst($apt['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--gray);">
                                    No appointments yet
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
                <a href="modules/pharmacy.php" style="color: var(--primary-light); font-size: 0.9rem;">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_orders->num_rows > 0): ?>
                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['order_number']); ?></strong><br>
                                        <small style="color: var(--gray);"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></small>
                                    </td>
                                    <td><strong>৳<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    <td>
                                        <?php
                                        $badge_class = [
                                            'pending' => 'badge-warning',
                                            'processing' => 'badge-info',
                                            'shipped' => 'badge-primary',
                                            'delivered' => 'badge-success',
                                            'cancelled' => 'badge-danger'
                                        ];
                                        ?>
                                        <span class="badge <?php echo $badge_class[$order['status']]; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--gray);">
                                    No orders yet
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'config/footer.php'; ?>