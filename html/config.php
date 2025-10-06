<?php
/**
 * ==========================================
 * config.php  (010number 사이트 설정)
 * ==========================================
 */

// ---------------------------------------------------
// 1) .env 파일 로드
// ---------------------------------------------------
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    die('❌ 환경 설정 파일(.env)이 없습니다.');
}

$env = parse_ini_file($envPath, false, INI_SCANNER_TYPED);
if ($env === false) {
    die('❌ .env 파일을 읽을 수 없습니다. 구문을 확인하세요.');
}

// ---------------------------------------------------
// 2) 환경 설정
// ---------------------------------------------------
define('APP_DEBUG', $env['APP_DEBUG'] ?? false);

// 기본 시간대
date_default_timezone_set('Asia/Seoul');

// 에러 표시 및 로그
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php_errors.log');
}

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------
// 3) 사이트 기본 정보
// ---------------------------------------------------
define('SITE_NAME', $env['SITE_NAME'] ?? '010number');
define('BASE_URL', rtrim($env['BASE_URL'] ?? 'http://localhost', '/'));

// ---------------------------------------------------
// 4) 데이터베이스 설정
// ---------------------------------------------------
define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('DB_NAME', $env['DB_NAME'] ?? '');

// DB 연결
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    if (APP_DEBUG) {
        die('❌ 데이터베이스 연결 실패: ' . $mysqli->connect_error);
    } else {
        error_log('DB 연결 실패: ' . $mysqli->connect_error);
        die('⚠️ 잠시 후 다시 시도해주세요.');
    }
}

$mysqli->set_charset('utf8mb4');

// ---------------------------------------------------
// 5) 공통 유틸리티 함수
// ---------------------------------------------------
if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void {
        header('Location: ' . $url);
        exit();
    }
}

if (!function_exists('normalize_phone')) {
    function normalize_phone(string $number): string {
        return preg_replace('/[^0-9]/', '', $number);
    }
}
