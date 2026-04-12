<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$sql = "SELECT * FROM doctors WHERE status = 'active'";

if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR specialization LIKE '%$search%' OR hospital LIKE '%$search%' OR location LIKE '%$search%')";
}

$sql .= " ORDER BY rating DESC, name ASC";

$result = $conn->query($sql);

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode([
    'success' => true,
    'doctors' => $doctors
]);
?>