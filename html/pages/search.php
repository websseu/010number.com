<?php
    require_once __DIR__ . '/../config.php';

    // 검색어 받기
    $searchQuery = trim($_GET['q'] ?? '');
    $searchType = $_GET['type'] ?? 'auto'; // number, title, auto

    // 검색 실행
    $searchResults = [];
    if ($searchQuery !== '') {
        // 자동 타입 감지: 숫자만 있으면 전화번호, 그 외는 제목으로 검색
        if ($searchType === 'auto') {
            $searchType = preg_match('/^[0-9\-\s]+$/', $searchQuery) ? 'number' : 'title';
        }
        
        if ($searchType === 'number') {
            // 전화번호로 검색 (하이픈 제거)
            $cleanQuery = preg_replace('/[^0-9]/', '', $searchQuery);
            $stmt = $mysqli->prepare("
                SELECT 
                    p.*, 
                    c.name_ko AS category_name, 
                    c.color AS category_color
                FROM phone_numbers p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE REPLACE(p.number, '-', '') LIKE ?
                ORDER BY p.view_count DESC, p.created_at DESC
                LIMIT 50
            ");
            $searchParam = "%{$cleanQuery}%";
        } else {
            // 제목으로 검색
            $stmt = $mysqli->prepare("
                SELECT 
                    p.*, 
                    c.name_ko AS category_name, 
                    c.color AS category_color
                FROM phone_numbers p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.title LIKE ?
                ORDER BY p.view_count DESC, p.created_at DESC
                LIMIT 50
            ");
            $searchParam = "%{$searchQuery}%";
        }
        
     $stmt->bind_param("s", $searchParam);
     $stmt->execute();
     $result = $stmt->get_result();
     $searchResults = $result->fetch_all(MYSQLI_ASSOC);
     
     // 검색 결과가 정확히 1개면 해당 번호 페이지로 자동 이동
     if (count($searchResults) === 1) {
         $phone = $searchResults[0];
         $cleanNumber = preg_replace('/[^0-9]/', '', $phone['number']);
         header("Location: /number/{$cleanNumber}");
         exit;
     }
 }

    // SEO 메타데이터
    $page_title = "전화번호 검색 | 010number";
    $page_desc = "전화번호, 제목으로 스팸번호를 검색하세요. 010number에서 안전한 전화번호 정보를 확인하세요.";
    $page_keyword = "전화번호 검색, 스팸번호 검색, 010number";
    $body_class = "site-search";

    require_once __DIR__ . '/../includes/site-head.php';
    require_once __DIR__ . '/../includes/site-header.php';
    require_once __DIR__ . '/../includes/search-area.php';
?>

        <!-- 검색 페이지 -->
        <section id="search-page">
            <h2>검색 결과</h2>
            <p>"<?= htmlspecialchars($searchQuery) ?>" 검색 결과 (<?= $searchType === 'number' ? '전화번호' : '제목' ?> 검색)</p>

            <div class="search-result">
                <?php if ($searchQuery !== ''): ?>
                <div>
                    <?php if (count($searchResults) > 0): ?>
                        <div class="result-count">총 <?= count($searchResults) ?>개의 결과를 찾았습니다.</div>
                        <div class="category-list">
                            <ul>
                                <?php $i = 1; foreach ($searchResults as $phone): ?>
                                <li>
                                    <a href="/number/<?= urlencode(preg_replace('/[^0-9]/', '', $phone['number'])) ?>">
                                        <span class="index"><?= $i ?>.</span> 
                                        <span class="num"><?= htmlspecialchars($phone['number']) ?></span>
                                        <?php if (!empty($phone['category_name'])): ?>
                                            <span class="badge" style="background-color: <?= htmlspecialchars($phone['category_color']) ?>;">
                                                <?= htmlspecialchars($phone['category_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <strong class="title"><?= htmlspecialchars($phone['title'] ?: '이름 미등록') ?></strong>
                                        <span class="meta">
                                            <i class="view"></i> <?= number_format((int)$phone['view_count']) ?>
                                            <i class="date"></i> <?= substr($phone['created_at'], 0, 10) ?>
                                        </span>
                                    </a>
                                </li>
                                <?php $i++; endforeach; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <p>검색 결과가 없습니다.</p>
                        </div>
                        
                        <!-- 제보하기 영역 -->
                        <section id="report-area">
                            <h3>전화번호 제보하기</h3>
                            <p>여러분의 제보가 모두에게 큰 도움이 됩니다</p>

                            <form class="report-group" action="/report-save" method="post">
                                <input class="report-number" type="text" name="number" id="number" maxlength="14" 
                                       value="<?= htmlspecialchars($searchQuery) ?>" placeholder="--_--" />
                                <input class="report-title" type="text" name="title" id="title" placeholder="예: 사기 스미싱 문자" />
                                <div class="report-pad">
                                    <button type="button">1</button>
                                    <button type="button">2</button>
                                    <button type="button">3</button>
                                    <button type="button">4</button>
                                    <button type="button">5</button>
                                    <button type="button">6</button>
                                    <button type="button">7</button>
                                    <button type="button">8</button>
                                    <button type="button">9</button>
                                    <button type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eraser-icon lucide-eraser"><path d="M21 21H8a2 2 0 0 1-1.42-.587l-3.994-3.999a2 2 0 0 1 0-2.828l10-10a2 2 0 0 1 2.829 0l5.999 6a2 2 0 0 1 0 2.828L12.834 21"/><path d="m5.082 11.09 8.828 8.828"/></svg>
                                    </button>
                                    <button type="button">0</button>
                                    <button type="submit">등록</button>
                                </div>
                            </form>
                        </section>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <!-- 검색어가 없을 때 -->
                <div class="no-search">
                    <h3>검색어를 입력해주세요</h3>
                    <p>상단 검색창에서 전화번호나 제목을 입력하여 검색하세요.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

<?php 
require_once __DIR__ . '/../includes/site-footer.php'; 
?>
