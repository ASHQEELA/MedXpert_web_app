<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to book an appointment']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$doctor_id = (int)($_POST['doctor_id'] ?? 0);
$appointment_date = sanitize($_POST['appointment_date'] ?? '');
$appointment_time = sanitize($_POST['appointment_time'] ?? '');
$consultation_type = sanitize($_POST['consultation_type'] ?? 'in-person');
$symptoms = sanitize($_POST['symptoms'] ?? '');

// Validate input
if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit();
}

// Basic date validation
if (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
    echo json_encode(['success' => false, 'message' => 'Appointment date cannot be in the past']);
    exit();
}

// Check if doctor exists and is active
$stmt = $conn->prepare("SELECT id FROM doctors WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Selected doctor is not available']);
    exit();
}

// Insert appointment
$stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, consultation_type, symptoms, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("iissss", $user_id, $doctor_id, $appointment_date, $appointment_time, $consultation_type, $symptoms);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully', 'appointment_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to book appointment. Please try again.']);
}
?>
