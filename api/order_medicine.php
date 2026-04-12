<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to place an order']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$medicine_id = (int)($_POST['medicine_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);
$delivery_address = sanitize($_POST['delivery_address'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$payment_method = sanitize($_POST['payment_method'] ?? 'cash');
// Note: price field from form is not used; we fetch it from database for security

// Validate input
if (empty($medicine_id) || empty($delivery_address) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Check medicine availability and price
    $stmt = $conn->prepare("SELECT id, name, price, stock, status FROM medicines WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $medicine_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Selected medicine not found");
    }
    
    $medicine = $result->fetch_assoc();
    
    if ($medicine['status'] !== 'available' || $medicine['stock'] < $quantity) {
        throw new Exception("Medicine is out of stock or insufficient quantity");
    }
    
    $total_amount = $medicine['price'] * $quantity;
    $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    
    // Insert order
    $stmt = $conn->prepare("INSERT INTO medicine_orders (user_id, order_number, total_amount, delivery_address, phone, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("isdsss", $user_id, $order_number, $total_amount, $delivery_address, $phone, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    
    // Insert order item
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $medicine_id, $quantity, $medicine['price']);
    $stmt->execute();
    
    // Update medicine stock
    $new_stock = $medicine['stock'] - $quantity;
    $new_status = $new_stock > 0 ? 'available' : 'out_of_stock';
    $stmt = $conn->prepare("UPDATE medicines SET stock = ?, status = ? WHERE id = ?");
    $stmt->bind_param("isi", $new_stock, $new_status, $medicine_id);
    $stmt->execute();
    
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully', 
        'order_number' => $order_number,
        'order_id' => $order_id
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
