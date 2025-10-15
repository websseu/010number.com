<?php
    require_once __DIR__ . '/../config.php';

    // slug 기반으로 카테고리 가져오기
    $slug = trim($_GET['slug'] ?? '');

    $stmt = $mysqli->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $category = $stmt->get_result()->fetch_assoc();

    if (!$category) {
        http_response_code(404);
        die("<h1>존재하지 않는 카테고리입니다.</h1>");
    }

    // 해당 카테고리의 전화번호 목록 불러오기
    $stmt = $mysqli->prepare("
        SELECT id, title, number, view_count, created_at, comment_count
        FROM phone_numbers
        WHERE category_id = ?
        ORDER BY id DESC
    ");
    $stmt->bind_param("i", $category['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // 메타데이터
    $page_title   = "{$category['name_ko']} 전화번호 목록 | 010number";
    $page_desc    = "010number에서 {$category['name_ko']} 관련 전화번호 정보를 확인하세요. 스팸 차단, 피싱 예방, 유용한 정보까지 모두 제공합니다.";
    $page_keyword = "{$category['name_ko']}, 전화번호, 스팸, 피싱, 010number";
    $body_class   = "site-category";

    require_once __DIR__ . '/../includes/site-head.php';
    require_once __DIR__ . '/../includes/site-header.php';
    require_once __DIR__ . '/../includes/search-area.php';
?>

        <!-- 카테고리 디테일 -->
        <section id="category-detail">
            <h2><?= htmlspecialchars($category['name_ko']) ?></h2>
            <p><?= htmlspecialchars($category['description'] ?? '') ?><br>등록된 번호: <strong><?= number_format($result->num_rows) ?></strong>개</p>
        
            <div class="category-list">
                <?php if ($result->num_rows > 0): ?>
                    <ul>
                        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                            <li>
                                <a href="/number/<?= urlencode($row['number']) ?>">
                                    <span class="index"><?= $i ?>.</span> 
                                    <span class="num"><?= htmlspecialchars($row['number']) ?></span>
                                    <strong class="title">
                                        <?= htmlspecialchars($row['title'] ?: '이름 미등록') ?>
                                        <?php if ((int)$row['comment_count'] > 0): ?>
                                            <span class="red">[<?= $row['comment_count'] ?>]</span>
                                        <?php endif; ?>
                                    </strong>
                                    <span class="meta">
                                        <i class="view"></i> <?= (int)$row['view_count'] ?>
                                        <i class="date"></i> <?= substr($row['created_at'], 0, 10) ?>
                                    </span>
                                </a>
                            </li>
                        <?php $i++; endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-category">등록된 번호가 없습니다.</p>
                <?php endif; ?>
            </div>
        </section>


<?php 
require_once __DIR__ . '/../includes/site-footer.php'; 
?>