<?php
require_once __DIR__ . '/../config.php';

// 전체 등록 번호 수 조회 (카테고리 무관)
$totalResult = $mysqli->query("SELECT COUNT(*) as total FROM phone_numbers");
$grandTotal = $totalResult->fetch_assoc()['total'];

// 카테고리별 아이콘 매핑 (원하는 순서대로)
$icons = [
    '사기/피싱'   => '⚠️',
    '스팸/광고'   => '🚫',
    '교육/학원'   => '🎓',
    '금융/은행'   => '🏦',
    '보험/대출'   => '💳',
    '택배/배달'   => '📦',
    '인증/보안'   => '🔐',
    '광고/마케팅' => '📢',
    '영업/상담'   => '☎️',
    '운송/항공'   => '✈️',
    '플랫폼/앱'   => '📱',
    '공공기관'    => '🏛️',
    '쇼핑/관광'   => '🛍️',
    '상점/매장'   => '🏬',
    '병원/의료'   => '🏥',
    '유용/정보'   => '👑',
    '기타'       => '📞'
];

// 카테고리별 번호 개수 조회
$stmt = $mysqli->prepare("
    SELECT c.id, c.slug, c.name_ko, c.color, COUNT(p.id) AS total
    FROM categories c
    LEFT JOIN phone_numbers p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.id ASC
");
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

// 카드 데이터 구성 (아이콘 배열 순서대로 정렬)
$categoryCards = [];

// 아이콘 배열의 순서대로 카테고리 정렬
foreach ($icons as $categoryName => $icon) {
    // 해당 카테고리 찾기
    $found = false;
    foreach ($rows as $row) {
        if ($row['name_ko'] === $categoryName) {
            $categoryCards[] = [
                'id'    => $row['id'], 
                'slug'  => $row['slug'], 
                'color' => $row['color'],
                'icon'  => $icon,
                'name'  => $row['name_ko'],
                'count' => number_format($row['total'])
            ];
            $found = true;
            break;
        }
    }
    
    // 데이터베이스에 없는 카테고리도 표시 (0개로)
    if (!$found) {
        $categoryCards[] = [
            'id'    => 0, 
            'slug'  => strtolower(str_replace(['/', ' '], ['-', '-'], $categoryName)), 
            'color' => '#999999',
            'icon'  => $icon,
            'name'  => $categoryName,
            'count' => '0'
        ];
    }
}

// 메타데이터
$page_title = "010number | 모르는 번호 · 스팸 번호 · 유용한 번호 · 상점 번호 조회 서비스";
$page_desc  = "모르는 번호부터 스팸, 금융, 기관, 배달기사 번호까지 — 010number에서 한 번에 확인하세요. 스미싱·피싱 예방부터 상점·택배 유용번호까지 모두 모은 전화번호 조회 서비스입니다.";
$page_keyword = "전화번호 조회, 스팸 전화, 모르는 번호 검색, 금융기관 전화번호, 상점 전화, 택배, 유용한 번호, 스미싱 피싱, 010number";
$body_class = "site-main";

require_once __DIR__ . '/../includes/site-head.php';
require_once __DIR__ . '/../includes/site-header.php';
require_once __DIR__ . '/../includes/search-area.php';
?>
        <!-- 카테고리별 리스트 -->
        <section id="category-summary">
            <h2>모르는 번호가 걱정될 땐, 010number</h2>
            <p>
                스팸 전화부터 유용한 상점, 기관 연락처까지, 현재 <em class="line"><?= number_format($grandTotal) ?></em>개의 전화번호 정보를 제공합니다.<br>
                <em class="line">사용자 제보와 최신 데이터를 바탕으로 매일 업데이트되어</em> 더 안전하고 똑똑한 통화 생활을 도와드립니다.
            </p>

            <div class="category-group">
                <ul>
                    <?php foreach ($categoryCards as $card): ?>
                        <li style="--color: <?= htmlspecialchars($card['color']) ?>">
                            <a href="/category/<?= htmlspecialchars($card['slug']) ?>" class="category-link">
                                <div class="category-icon"><?= $card['icon'] ?></div>
                                <div class="category-info">
                                    <span><?= htmlspecialchars($card['name']) ?></span>
                                    <span><?= $card['count'] ?>건</span>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

<?php 
require_once __DIR__ . '/../includes/site-footer.php'; 
?>