<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$blood_type = isset($_GET['blood_type']) ? sanitize($_GET['blood_type']) : 'all';

$sql = "SELECT * FROM blood_donors WHERE available = 1";

if ($blood_type !== 'all') {
    $sql .= " AND blood_type = '$blood_type'";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR location LIKE '%$search%')";
}

$sql .= " ORDER BY name ASC";

$result = $conn->query($sql);

$donors = [];
while ($row = $result->fetch_assoc()) {
    $donors[] = $row;
}

echo json_encode([
    'success' => true,
    'donors' => $donors
]);
?>