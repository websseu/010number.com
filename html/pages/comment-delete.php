<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);
$password = trim($_POST['password'] ?? '');

if ($id <= 0 || $password === '') {
    echo json_encode(['success' => false, 'message' => '비밀번호를 입력해주세요.']);
    exit;
}

$stmt = $mysqli->prepare("SELECT password FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row || !$row['password']) {
    echo json_encode(['success' => false, 'message' => '비밀번호가 설정되지 않았습니다.']);
    exit;
}

if (!password_verify($password, $row['password'])) {
    echo json_encode(['success' => false, 'message' => '비밀번호가 일치하지 않습니다.']);
    exit;
}

$delete = $mysqli->prepare("DELETE FROM comments WHERE id = ?");
$delete->bind_param("i", $id);
$delete->execute();

// 댓글 수 -1
$mysqli->query("UPDATE phone_numbers SET comment_count = GREATEST(comment_count - 1, 0)
                WHERE id = (SELECT phone_id FROM comments WHERE id = {$id} LIMIT 1)");

echo json_encode(['success' => true, 'message' => '댓글이 삭제되었습니다.']);
