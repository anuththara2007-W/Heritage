<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['count' => 0]);
    exit();
}

$conn = getDBConnection();
$userId = getCurrentUserId();

$stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode(['count' => intval($data['count'] ?? 0)]);

$stmt->close();
closeDBConnection($conn);
?>
