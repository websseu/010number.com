<?php
session_start();
if (empty($_SESSION['admin_auth'])) {
    http_response_code(403);
    exit("권한이 없습니다.");
}

require_once __DIR__ . '/../config.php';

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0 || $action === '') {
    exit("잘못된 요청입니다.");
}

if ($action === 'delete') {
    $stmt = $mysqli->prepare("DELETE FROM phone_numbers WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) exit("✅ 삭제 완료");
    else exit("❌ 삭제 실패: " . $stmt->error);
}

if ($action === 'update') {
    $number = trim($_POST['number'] ?? '');
    $title  = trim($_POST['title'] ?? '');
    $category = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;

    $stmt = $mysqli->prepare("
        UPDATE phone_numbers 
        SET number=?, title=?, category_id=?
        WHERE id=?
    ");
    $stmt->bind_param("ssii", $number, $title, $category, $id);
    if ($stmt->execute()) exit("✅ 수정 완료");
    else exit("❌ 수정 실패: " . $stmt->error);
}
