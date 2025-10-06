<!-- number.json 파일 DB에 올리기 -->

<?php
require_once __DIR__ . '/../config.php'; 

// JSON 파일 경로
$jsonFile = __DIR__ . '/number.json';

if (!file_exists($jsonFile)) {
    die("❌ JSON 파일을 찾을 수 없습니다.");
}

// JSON 파일 읽기
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

if (empty($data)) {
    die("❌ JSON 데이터가 비어있습니다.");
}

$inserted = 0;
$updated  = 0;
 
foreach ($data as $row) {
    $store_name = trim($row['store_name'] ?? '');
    $number     = trim($row['number'] ?? '');

    if ($store_name === '' || $number === '') {
        continue; // 필수 데이터가 없으면 건너뛰기
    }

    // 이미 존재하는 번호인지 확인
    $stmt = $mysqli->prepare("SELECT id FROM phone_numbers WHERE number = ? LIMIT 1");
    $stmt->bind_param("s", $number);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing = $result->fetch_assoc();
    $stmt->close();

    if ($existing) {
        // 이미 있으면 업데이트
        $stmt = $mysqli->prepare("UPDATE phone_numbers SET store_name = ? WHERE id = ?");
        $stmt->bind_param("si", $store_name, $existing['id']);
        $stmt->execute();
        $stmt->close();
        $updated++;
    } else {
        // 새 데이터면 삽입
        $stmt = $mysqli->prepare("INSERT INTO phone_numbers (store_name, number) VALUES (?, ?)");
        $stmt->bind_param("ss", $store_name, $number);
        $stmt->execute();
        $stmt->close();
        $inserted++;
    }
}

echo "✅ 등록 완료: 추가 {$inserted}건, 업데이트 {$updated}건\n";
