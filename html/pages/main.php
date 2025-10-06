<?php
require_once __DIR__ . '/../config.php';

$body_class = "site-home";

$page_title = "모르는 번호 검색 · 스팸 전화 조회 서비스 | 010number";
$page_desc  = "모르는 번호, 스팸 전화, 광고 전화까지 쉽게 검색할 수 있는 서비스 010number";

$message = null;

if (isset($_GET['q'])) {
    $q = trim($_GET['q']);
    $q_number = normalize_phone($q);

    if ($q === '') {
        $message = "전화번호나 매장명을 입력해주세요.";
    } else {
        // 1) 먼저 번호 검색
        if ($q_number !== '' && preg_match('/^[0-9]+$/', $q_number)) {
            $stmt = $mysqli->prepare("SELECT id FROM phone_numbers WHERE REPLACE(number, '-', '') = ? LIMIT 1");
            $stmt->bind_param("s", $q_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $phone_info = $result->fetch_assoc();
            $stmt->close();

            if ($phone_info) {
                // 번호가 DB에 있음 → phone.php로 이동
                header("Location: /" . urlencode($q_number));
                exit;
            } else {
                // 번호가 DB에 없음 → register.php로 이동
                header("Location: /pages/register_number.php?q=" . urlencode($q_number));
                exit;
            }
        } else {
            // 2) 이름(매장명) 검색
            $stmt = $mysqli->prepare("SELECT id FROM phone_numbers WHERE store_name LIKE CONCAT('%', ?, '%') LIMIT 1");
            $stmt->bind_param("s", $q);
            $stmt->execute();
            $result = $stmt->get_result();
            $store_info = $result->fetch_assoc();
            $stmt->close();

            if ($store_info) {
                // 이름으로 찾은 결과가 있으면 → name.php로 이동
                header("Location: /pages/name.php?q=" . urlencode($q));
                exit;
            } else {
                // 이름도 DB에 없음 → 등록 페이지로 이동
                header("Location: /pages/register_name.php?q=" . urlencode($q));
                exit;
            }
        }
    }
}

// -----------------------------------
// 🔥 전체 데이터 불러오기
// 최근 등록된 순서로
// -----------------------------------
// ✅ 총 등록된 데이터 개수
$count_stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM phone_numbers");
$count_stmt->execute();
$count_result = $count_stmt->get_result()->fetch_assoc();
$total_count = $count_result['total'];
$count_stmt->close();

$stmt = $mysqli->prepare("
    SELECT id, store_name, number, note, created_at 
    FROM phone_numbers 
    ORDER BY created_at DESC 
");
$stmt->execute();
$result = $stmt->get_result();
$all_numbers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require_once __DIR__ . '/../includes/head.php';
require_once __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/search.php';
?>
    <section class="search-result" aria-label="전체 등록된 번호">

        <?php if (!empty($all_numbers)): ?>
            <p>
                오늘까지 등록된 총 <strong><?= number_format($total_count) ?></strong>개의 데이터가 있습니다.
            </p>
            <ul class="search-list">
                <?php foreach ($all_numbers as $row): ?>
                    <li>
                        <strong><?= htmlspecialchars($row['store_name'], ENT_QUOTES, 'UTF-8') ?></strong> :
                        <a href="/<?= urlencode($row['number']) ?>">
                            <?= htmlspecialchars($row['number'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                        <?php if (!empty($row['note'])): ?>
                            <span class="note">- <?= htmlspecialchars($row['note'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>아직 등록된 번호가 없습니다.</p>
        <?php endif; ?>
    </section>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>
