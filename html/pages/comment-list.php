<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

$phone_id = (int)($_GET['phone_id'] ?? 0);
if ($phone_id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $mysqli->prepare("SELECT id, content, created_at FROM comments WHERE phone_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $phone_id);
$stmt->execute();
$res = $stmt->get_result();

$comments = [];
while ($row = $res->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode($comments);
