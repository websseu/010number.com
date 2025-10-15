<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$phone_id = (int)($input['phone_id'] ?? 0);
$content  = trim($input['content'] ?? '');
$password = trim($input['password'] ?? '');

if ($phone_id <= 0 || $content === '') {
    echo json_encode(['success' => false, 'message' => '내용을 입력해주세요.']);
    exit;
}

$hash = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

$stmt = $mysqli->prepare("INSERT INTO comments (phone_id, content, password) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $phone_id, $content, $hash);

// 댓글 수 업데이트
$mysqli->query("UPDATE phone_numbers SET comment_count = comment_count + 1 WHERE id = {$phone_id}");

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '댓글이 등록되었습니다.']);
} else {
    echo json_encode(['success' => false, 'message' => '댓글 등록 실패: ' . $stmt->error]);
}
$stmt->close();
