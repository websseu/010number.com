<?php
    require_once __DIR__ . '/../config.php';

    $body_class = "site-name";

    $q = trim($_GET['q'] ?? '');

    $page_title = $q ? "'{$q}' 검색 결과 | 010number" : "매장명 검색 | 010number";
    $page_desc  = $q ? "‘{$q}’ 관련 전화번호 검색 결과를 확인하세요." : "매장명으로 전화번호를 검색하세요.";

    require_once __DIR__ . '/../includes/head.php';
    require_once __DIR__ . '/../includes/header.php';

    // ----------------------------
    // DB 검색
    // ----------------------------
    $results = [];
    if ($q !== '') {
        $stmt = $mysqli->prepare("SELECT * FROM phone_numbers WHERE store_name LIKE CONCAT('%', ?, '%') ORDER BY store_name ASC");
        $stmt->bind_param("s", $q);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
    include __DIR__ . '/../includes/search.php';
?>

    <section class="search-result" aria-label="점포명 검색 결과">
        <?php if ($q): ?>
            <h2><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></h2>
            
            <?php if (count($results) === 1): ?>
                <!-- 결과가 하나일 때 -->
                <?php $row = $results[0]; ?>
                <p>
                    <strong><?= htmlspecialchars($row['store_name'], ENT_QUOTES, 'UTF-8') ?></strong> 의 전화번호는
                    <a href="/<?= htmlspecialchars(normalize_phone($row['number']), ENT_QUOTES, 'UTF-8') ?>" class="line">
                        <?= htmlspecialchars($row['number'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    입니다.
                </p>

            <?php elseif (count($results) > 1): ?>
                <p class="desc">총 <strong><?= count($results) ?></strong>개의 관련 번호가 검색되었습니다.</p>
                <!-- 결과가 여러 개일 때 -->
                <ul class="search-list">
                    <?php foreach ($results as $row): ?>
                        <li>
                            <strong><?= htmlspecialchars($row['store_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <a href="/<?= htmlspecialchars(normalize_phone($row['number']), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($row['number'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

            <?php else: ?>
                <!-- 결과가 없는 경우 -->
                <p>검색하신 키워드에 해당하는 등록된 번호가 없습니다.</p>
            <?php endif; ?>

        <?php endif; ?>
    </section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
