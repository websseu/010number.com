<?php
require_once __DIR__ . '/../config.php'; // 상위 폴더의 config.php 로 연결

// ✅ JSON 파일 경로 (같은 폴더 안에 있으니까 __DIR__ 기준)
$jsonPath = __DIR__ . '/emart_stores.json';

// JSON 파일 읽기
$json = file_get_contents($jsonPath);
$data = json_decode($json, true);

if (!$data) {
    die("❌ JSON 데이터를 불러올 수 없습니다: {$jsonPath}\n");
}

// DB 연결 확인
if (!$mysqli) {
    die("❌ DB 연결 실패.\n");
}

// 카테고리 ID (store)
$catStmt = $mysqli->prepare("SELECT id FROM categories WHERE slug = 'store' LIMIT 1");
$catStmt->execute();
$catResult = $catStmt->get_result()->fetch_assoc();
$category_id = $catResult['id'] ?? 7;

// INSERT 준비
$stmt = $mysqli->prepare("
    INSERT INTO phone_numbers (store_name, number, category_id, note)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE store_name = VALUES(store_name)
");

$count = 0;
foreach ($data as $store) {
    $store_name = trim($store['store_name'] ?? '');
    $number = trim($store['number'] ?? '');
    if ($store_name === '' || $number === '') continue;

    // 브랜드 자동 분류
    if (str_contains($store_name, '노브랜드')) $note = '노브랜드 매장';
    elseif (str_contains($store_name, '트레이더스')) $note = '트레이더스 매장';
    elseif (str_contains($store_name, '에브리데이')) $note = '에브리데이 매장';
    elseif (str_contains($store_name, '몰리스')) $note = '몰리스 매장';
    elseif (str_contains($store_name, '토이킹덤')) $note = '토이킹덤 매장';
    elseif (str_contains($store_name, '스타필드')) $note = '스타필드 매장';
    else $note = '이마트 매장';

    $stmt->bind_param("ssis", $store_name, $number, $category_id, $note);
    $stmt->execute();
    $count++;
}

echo "✅ 총 {$count}개의 매장 정보가 DB에 입력되었습니다.\n";
?>
