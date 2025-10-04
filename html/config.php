<?php
/**
 * ==========================================
 * config.php  (010number 사이트 설정)
 * ==========================================
 */

// ------------------------------------------
// 1) 환경 설정
// ------------------------------------------

// 개발 단계: true / 운영 단계: false
define('APP_DEBUG', true);

// 기본 시간대 설정
date_default_timezone_set('Asia/Seoul');

// 에러 표시 및 로그
if (APP_DEBUG) {
    ini_set('display_errors', 1);
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

// ------------------------------------------
// 2) 사이트 기본 정보
// ------------------------------------------
define('SITE_NAME', '010number');
define('BASE_URL', 'https://010number.com');

// ------------------------------------------
// 3) 데이터베이스 설정
// ------------------------------------------
define('DB_HOST', 'localhost');            
define('DB_USER', 'root');
define('DB_PASS', 'qwer1234!@#$'); 
define('DB_NAME', '010number');

// MySQLi 연결
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die('❌ 데이터베이스 연결 실패: ' . $mysqli->connect_error);
}

// ------------------------------------------
// 4) 공통 유틸리티 함수
// ------------------------------------------
function base_url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}
