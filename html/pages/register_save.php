<?php
require_once __DIR__ . '/../config.php';

$number     = normalize_phone($_POST['number'] ?? '');
$store_name = trim($_POST['store_name'] ?? '');
$note       = trim($_POST['note'] ?? '');

// ----------------------
// 2) 중복 확인 (번호 또는 점포명)
// ----------------------
$stmt = $mysqli->prepare("
    SELECT id, store_name, number 
      FROM phone_numbers 
     WHERE REPLACE(number, '-', '') = ? 
        OR store_name = ?
     LIMIT 1
");
$stmt->bind_param("ss", $number, $store_name);
$stmt->execute();
$result   = $stmt->get_result();
$existing = $result->fetch_assoc();
$stmt->close();

if ($existing) {
    // 중복 안내
    $existNum  = htmlspecialchars($existing['number']);
    $existName = htmlspecialchars($existing['store_name']);
    echo "<script>
            alert('⚠️ 이미 등록된 정보입니다.\\n점포명: {$existName}\\n번호: {$existNum}');
            window.location.href = '/" . urlencode($existing['number']) . "';
          </script>";
    exit;
}

// ----------------------
// 3) 신규 등록
// ----------------------
$stmt = $mysqli->prepare("
    INSERT INTO phone_numbers (store_name, number, note, views)
    VALUES (?, ?, ?, 0)
");
$stmt->bind_param("sss", $store_name, $number, $note);

if ($stmt->execute()) {
    // 성공
    echo "<script>
            alert('✅ 성공적으로 등록되었습니다.');
            window.location.href = '/';
          </script>";
} else {
    // 실패
    $error = addslashes($stmt->error);
    echo "<script>
            alert('❌ 등록 중 오류가 발생했습니다: {$error}');
            history.back();
          </script>";
}

$stmt->close();
exit;
