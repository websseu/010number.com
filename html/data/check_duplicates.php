<?php
$jsonPath = __DIR__ . '/number.json';

if (!file_exists($jsonPath)) {
    die("❌ number.json 파일을 찾을 수 없습니다.\n");
}

$json = file_get_contents($jsonPath);
$data = json_decode($json, true);

if (!is_array($data)) {
    die("❌ JSON 파싱 실패! 형식을 다시 확인하세요.\n");
}

$seen = [];
$duplicates = [];

foreach ($data as $row) {
    $number = trim($row['number'] ?? '');
    if ($number === '') continue;

    // 010-1234-5678 → 01012345678 로 통일
    $normalized = preg_replace('/[^0-9]/', '', $number);

    if (isset($seen[$normalized])) {
        $duplicates[$number] = true;
    } else {
        $seen[$normalized] = true;
    }
}

if (empty($duplicates)) {
    echo "✅ 중복된 번호 없음 (" . count($seen) . "건 확인)\n";
} else {
    echo "⚠️ 중복된 번호 (" . count($duplicates) . "건)\n";
    echo str_repeat("-", 30) . "\n";

    // 🔹 한 줄씩 줄바꿈 출력
    foreach (array_keys($duplicates) as $num) {
        echo $num . PHP_EOL;
    }
}
