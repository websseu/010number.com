<?php
    require_once __DIR__ . '/../config.php';
    header('Content-Type: text/html; charset=utf-8');

    // POST 데이터 받기
    $numberInput = trim($_POST['number'] ?? '');
    $title  = trim($_POST['title'] ?? '');

    // 저장용: 하이픈 포함된 원본 형식 유지
    $number = $numberInput;
    
    // 검색용: 하이픈 제거된 숫자만
    $cleanNumber = preg_replace('/[^0-9]/', '', $numberInput);

    // 유효성 검사
    if ($cleanNumber === '' || $title === '') {
        echo "<script>alert('전화번호와 제목은 필수 항목입니다.'); history.back();</script>";
        exit;
    }
    
    // 전화번호 길이 검증 (한국 전화번호는 7~11자리)
    // 1544-xxxx (7자리), 1588-xxxx (8자리), 02-xxx-xxxx (9-10자리), 010-xxxx-xxxx (11자리)
    $numLength = strlen($cleanNumber);
    if ($numLength < 7 || $numLength > 11) {
        echo "<script>alert('올바른 전화번호 형식이 아닙니다. (7~11자리)'); history.back();</script>";
        exit;
    }
    
    // 숫자만 있는지 확인
    if (!ctype_digit($cleanNumber)) {
        echo "<script>alert('전화번호는 숫자만 입력 가능합니다.'); history.back();</script>";
        exit;
    }

    // 중복 여부 확인
    $stmt = $mysqli->prepare("
        SELECT id 
        FROM phone_numbers 
        WHERE REPLACE(number, '-', '') = ? 
        LIMIT 1
    ");
    $stmt->bind_param("s", $cleanNumber);
    $stmt->execute();
    $res = $stmt->get_result();
    $exists = $res->fetch_assoc();

    // 중복 처리
    if ($exists) {
        echo "<script>
            alert('이미 등록된 번호입니다. 해당 번호 페이지로 이동합니다.');
            location.href = '/number/{$cleanNumber}';
        </script>";
        exit;
    }

    // 새 번호 등록
    $stmt = $mysqli->prepare("
        INSERT INTO phone_numbers (number, title, view_count, comment_count, created_at) 
        VALUES (?, ?, 0, 0, NOW())
    ");
    $stmt->bind_param("ss", $number, $title);

    if ($stmt->execute()) {
        $insertedId = $stmt->insert_id;
        echo "<script>
            alert('새 번호가 성공적으로 등록되었습니다. 감사합니다!');
            location.href='/number/{$cleanNumber}';
        </script>";
    } else {
        $error = $mysqli->error;
        echo "<script>
            alert('DB 저장 중 오류가 발생했습니다.\\n" . addslashes($error) . "');
            history.back();
        </script>";
    }
    $stmt->close();
?>
