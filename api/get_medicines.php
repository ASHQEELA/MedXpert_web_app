<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';

$sql = "SELECT * FROM medicines WHERE status = 'available'";

if ($category !== 'all') {
    $sql .= " AND category = '$category'";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR category LIKE '%$search%' OR manufacturer LIKE '%$search%')";
}

$sql .= " ORDER BY name ASC";

$result = $conn->query($sql);

$medicines = [];
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}

echo json_encode([
    'success' => true,
    'medicines' => $medicines
]);
?>