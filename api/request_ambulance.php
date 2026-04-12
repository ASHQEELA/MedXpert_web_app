<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to request an ambulance']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$name = sanitize($_POST['name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$pickup_location = sanitize($_POST['pickup_location'] ?? '');
$destination = sanitize($_POST['destination'] ?? '');
$emergency_type = sanitize($_POST['emergency_type'] ?? '');
$patient_condition = sanitize($_POST['patient_condition'] ?? '');
$request_type = sanitize($_POST['request_type'] ?? 'emergency');

// Validate input
if (empty($name) || empty($phone) || empty($pickup_location) || empty($destination)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit();
}

// Insert ambulance request
$stmt = $conn->prepare("INSERT INTO ambulance_requests (user_id, name, phone, pickup_location, destination, emergency_type, patient_condition, request_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("isssssss", $user_id, $name, $phone, $pickup_location, $destination, $emergency_type, $patient_condition, $request_type);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Ambulance request submitted successfully', 'request_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit ambulance request. Please try again.']);
}
?>
