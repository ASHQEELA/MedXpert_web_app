<?php
require_once 'config/header.php';

// Get user's orders
$orders = $conn->query("
    SELECT o.*, 
    (SELECT GROUP_CONCAT(CONCAT(m.name, ' (', oi.quantity, ')') SEPARATOR ', ') 
     FROM order_items oi 
     JOIN medicines m ON oi.medicine_id = m.id 
     WHERE oi.order_id = o.id) as items
    FROM medicine_orders o 
    WHERE o.user_id = {$_SESSION['user_id']} 
    ORDER BY o.created_at DESC
");
?>

<h1>My Medicine Orders</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Orders</span>
</div>
</div>
</div>

<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Order History</h3>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order # & Date</th>
                        <th>Medicines</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Delivery Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['order_number']); ?></strong><br>
                                    <small><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></small>
                                </td>
                                <td>
                                    <small style="color: var(--gray); display: block; max-width: 300px;"><?php echo htmlspecialchars($order['items']); ?></small>
                                </td>
                                <td><strong>৳<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                <td>
                                    <?php
                                    $badge_class = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge badge-<?php echo $badge_class[$order['status']]; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <small style="color: var(--gray); display: block; max-width: 200px;"><?php echo htmlspecialchars($order['delivery_address']); ?></small>
                                    <small style="color: var(--gray);"><?php echo htmlspecialchars($order['phone']); ?></small>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--gray);">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'config/footer.php'; ?>
