<?php
    require_once __DIR__ . '/../config.php';

    $body_class = "site-phone";

    $number = normalize_phone($_GET['number'] ?? '');

    $page_title = $number
        ? "{$number} 번호 조회 · 스팸 전화 검색 | 010number"
        : "번호 조회 | 010number";

    $page_desc  = $number
        ? "{$number} 번호의 발신자 정보와 스팸 여부를 확인하세요."
        : "전화번호 조회 서비스 - 스팸 및 광고 전화번호를 쉽게 검색할 수 있습니다.";

    require_once __DIR__ . '/../includes/head.php';
    require_once __DIR__ . '/../includes/header.php';

    // ----------------------------
    // 2) DB에서 번호 정보 조회
    // ----------------------------
    $phone_info = null;
    if ($number) {
        $stmt = $mysqli->prepare("SELECT * FROM phone_numbers WHERE REPLACE(number, '-', '') = ? LIMIT 1");
        $stmt->bind_param("s", $number);
        $stmt->execute();
        $result = $stmt->get_result();
        $phone_info = $result->fetch_assoc();
        $stmt->close(); 
    }

    include __DIR__ . '/../includes/search.php';
?>

    <section class="search-result" aria-label="전화번호 검색 결과">
        <?php if ($number): ?>
            <h2>
                <a href="tel:<?= htmlspecialchars($number, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($number, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h2>

            <?php if ($phone_info): ?>
                <p class="desc">
                    이 번호는 <strong class="line"><?= htmlspecialchars($phone_info['store_name']) ?> </strong> 번호입니다.
                </p>
            <?php else: ?>
                <p>해당 번호에 대한 정보가 없습니다.</p>
            <?php endif; ?>

        <?php else: ?>
            <h2>조회할 번호를 입력하세요</h2>
            <p>검색창에 확인하고 싶은 전화번호를 입력하세요.</p>
        <?php endif; ?>
    </section>

<?php
    require_once __DIR__ . '/../includes/footer.php'; 
?>