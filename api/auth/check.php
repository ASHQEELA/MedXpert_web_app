<?php
// This file is often used to check login status via AJAX
require_once '../../config/db.php';

header('Content-Type: application/json');

if (isLoggedIn()) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['full_name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
}
?>
