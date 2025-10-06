<?php
require_once __DIR__ . '/config.php';

$route = $_GET['route'] ?? 'main'; 

switch ($route) {
    case 'main':
        include __DIR__ . '/pages/main.php';
        break;

    case 'register':
        include __DIR__ . '/pages/register.php';
        break;

    default:
        if (preg_match('/^[0-9\-]+$/', $route)) {
            $_GET['number'] = $route;    
            include __DIR__ . '/pages/phone.php';
        } else {
            http_response_code(404);
            echo "페이지를 찾을 수 없습니다.";
        }
        break;
}