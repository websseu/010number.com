<?php
// ==============================
// config.php (env 기반 설정)
// ==============================

// 1. .env 파일 불러오기 함수
function loadEnv($path)
{
    if (!file_exists($path)) {
        die("❌ .env 파일을 찾을 수 없습니다: {$path}");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue; // 주석 무시
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }
}

// 2. .env 불러오기
$envPath = __DIR__ . '/.env';
loadEnv($envPath);

// 3. 환경변수 상수 정의
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

define('BASE_URL', getenv('BASE_URL'));
define('SITE_NAME', getenv('SITE_NAME'));

define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));

// 4. DB 연결
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 5. 연결 오류 처리
if ($mysqli->connect_error) {
    die("❌ DB 연결 실패: " . $mysqli->connect_error);
}

// 6. 문자셋 설정
if (!$mysqli->set_charset(DB_CHARSET)) {
    die("❌ 문자셋 설정 실패: " . $mysqli->error);
}

// 7. 개발용 에러 표시 설정
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
