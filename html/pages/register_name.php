<?php
    require_once __DIR__ . '/../config.php';

    $body_class = "site-register-name";

    $q = trim($_GET['q'] ?? '');

    $page_title = $q 
        ? "{$q} 번호 등록 요청 | 010number"
        : "번호 등록 요청 | 010number";

    $page_desc = $q
        ? "입력하신 {$q} 번호가 아직 등록되지 않았습니다. 아래 폼을 통해 제보해 주세요."
        : "아직 등록되지 않은 번호를 제보할 수 있습니다.";

    require_once __DIR__ . '/../includes/head.php';
    require_once __DIR__ . '/../includes/header.php';

    // ----------------------------
    // 1) DB에서 해당 번호가 이미 있는지 확인
    // ----------------------------
    $phone_info = null;

    if ($q !== '') {
        $stmt = $mysqli->prepare("SELECT * FROM phone_numbers WHERE REPLACE(number, '-', '') = ? LIMIT 1");
        $stmt->bind_param("s", $q);
        $stmt->execute();
        $result = $stmt->get_result();
        $phone_info = $result->fetch_assoc();
        $stmt->close();
    }
    // var_dump($phone_info);

    include __DIR__ . '/../includes/search.php';
?>

    <section class="search-result" aria-label="전화번호 검색 결과">
        <h2><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></h2>

        <?php if (!$q): ?>
            <p>번호를 검색하면 등록 상태를 확인하거나 제보할 수 있습니다.</p>
        <?php else: ?>
            <p class="mb10">입력하신 점포에 대한 <strong class="line">등록된 정보가 아직 없습니다.</strong></p>
        <?php endif; ?>
    </section>

    <section class="search-input" aria-label="번호 정보 제보 폼">
        <h3>점포 번호 제보하기</h3>

        <form action="/pages/register_save.php" method="post" class="register-form">
            <input type="hidden" name="store_name" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="number">번호</label>
                <input
                    type="text"
                    id="number"
                    name="number"
                    placeholder="예: 010-0123-0123"
                    required
                >
            </div>

            <div class="form-group">
                <label for="note">추가 설명</label>
                <textarea id="note" name="note" rows="4" placeholder="추가 설명이 필요하다면 적어주세요!"></textarea>
            </div>

            <div class="form-group">
            <button type="submit" class="btn-submit">제보하기</button>
            </div>
        </form>
    </section>

<?php
    require_once __DIR__ . '/../includes/footer.php';
?>