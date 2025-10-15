<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: text/plain; charset=utf-8');

// ==========================
// 1️⃣ JSON 파일 불러오기
// ==========================
$jsonPath = __DIR__ . '/data_number.json';

if (!file_exists($jsonPath)) {
    die("❌ number.json 파일을 찾을 수 없습니다.\n");
}

$json = file_get_contents($jsonPath);
$data = json_decode($json, true);

if (!is_array($data)) {
    die("❌ JSON 파싱 실패! 형식을 다시 확인하세요.\n");
}

// ==========================
// 2️⃣ SQL 준비
// ==========================
$sql = "INSERT INTO phone_numbers (title, number, category_id) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("❌ SQL 준비 실패: " . $mysqli->error . "\n");
}

// ==========================
// 3️⃣ 카테고리 매핑 캐시
// ==========================
$catCache = [];
$res = $mysqli->query("SELECT id, name_ko FROM categories");
while ($row = $res->fetch_assoc()) {
    $catCache[$row['name_ko']] = (int)$row['id']; // 예: '금융/은행' => 4
}
$res->free();

// ==========================
// 4️⃣ 데이터 삽입
// ==========================
$success = 0;
$fail = 0;

foreach ($data as $row) {
    $title    = trim($row['title'] ?? '') ?: null;
    $number   = trim($row['number'] ?? '');
    $category = trim($row['category'] ?? '') ?: null;

    if ($number === '') {
        echo "⚠️ 번호 누락 → 건너뜀: {$title}\n";
        $fail++;
        continue;
    }

    // category_id 매핑
    $category_id = $catCache[$category] ?? null;

    $stmt->bind_param("ssi", $title, $number, $category_id);

    if ($stmt->execute()) {
        echo "✅ 등록 완료: {$title} ({$number})\n";
        $success++;
    } else {
        echo "❌ 등록 실패: {$title} ({$number}) → {$stmt->error}\n";
        $fail++;
    }
}

// ==========================
// 5️⃣ 결과 출력
// ==========================
$stmt->close();
$mysqli->close();

echo "\n==============================\n";
echo "✅ 성공: {$success}건\n";
echo "❌ 실패: {$fail}건\n";
echo "==============================\n";
