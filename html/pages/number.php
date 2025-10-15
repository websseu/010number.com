<?php
    require_once __DIR__ . '/../config.php';

    // URL 파라미터 확인
    $numberParam = trim($_GET['num'] ?? '');
    
    if ($numberParam === '') {
        http_response_code(400);
        echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
        exit;
    }
    
    // 숫자만 추출 (하이픈 제거)
    $cleanNumber = preg_replace('/[^0-9]/', '', $numberParam);

    // 번호 정보 조회
    $stmt = $mysqli->prepare("
        SELECT 
            p.*, 
            c.name_ko AS category_name, 
            c.color AS category_color
        FROM phone_numbers p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE REPLACE(p.number, '-', '') = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $cleanNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $phone = $result->fetch_assoc();

    if (!$phone) {
        http_response_code(404);
        die("<h1>등록되지 않은 번호입니다.</h1><p>검색한 번호: {$cleanNumber}</p>");
    }

    // 조회수 증가
    $update = $mysqli->prepare("UPDATE phone_numbers SET view_count = view_count + 1 WHERE id = ?");
    $update->bind_param("i", $phone['id']);
    $update->execute();
    $update->close();

    // SEO 메타데이터
    $page_title   = "{$phone['number']} - {$phone['title']}  | 010number";
    $page_desc    = "{$phone['title']} 관련 전화번호 정보입니다. 스팸, 광고, 보이스피싱, 기관 등 위험도와 설명을 확인하세요.";
    $page_keyword = "{$phone['number']}, {$phone['title']}, 전화번호 조회, 스팸번호, 010number";
    $body_class   = "site-number";

    require_once __DIR__ . '/../includes/site-head.php';
    require_once __DIR__ . '/../includes/site-header.php';
    require_once __DIR__ . '/../includes/search-area.php';
?>
        <!-- 넘버 페이지 -->
        <section id="number-detail">
            <div class="number-group">
                <h2 class="number"><?= htmlspecialchars($phone['number']) ?></h2>
                <p class="sub-number">
                    
                </p>
                <p class="title"><?= htmlspecialchars($phone['title'] ?: '등록된 이름 없음') ?></p>
                <?php if (!empty($phone['category_name'])): ?>
                <span class="category-badge" style="background-color: <?= htmlspecialchars($phone['category_color']) ?>;">
                    <?= htmlspecialchars($phone['category_name']) ?>
                </span>
                <?php endif; ?>
            
                <div class="number-info">
                    <div class="info-box">
                        <i class="view"></i>
                        <span>조회수</span>
                        <strong><?= number_format((int)$phone['view_count']) ?></strong>
                    </div>
                    <div class="info-box">
                        <i class="date"></i>
                        <span>등록일</span>
                        <strong><?= substr($phone['created_at'], 0, 10) ?></strong>
                    </div>
                </div>

                <div class="number-desc">
                    <p><strong><?= htmlspecialchars($phone['title'] ?: '이 번호') ?></strong> 관련 전화번호 정보입니다.<br>
                    스팸, 광고, 보이스피싱, 기관 연락처 등<br>사용자 제보를 기반으로 매일 업데이트됩니다.
                    </p>
                </div>
            </div>
        </section>

        <!-- 댓글 영역 -->
        <section id="comment-section">
            <div class="comment-group" data-phone-id="<?= (int)$phone['id'] ?>">
                <h3 class="comment-title">커뮤니티 댓글(<span id="comment-count">0</span>)</h3>
                
                <div class="comment-list"></div>

                <!-- 기존 댓글들 -->
                <!-- <div class="comment-item">
                    <div class="comment-avatar">
                        <img src="/assets/img/face/Hectic.svg" alt="아바타">
                    </div>
                    <div class="comment-content">
                        <div class="comment-header">
                            <span class="author">김철수</span>
                            <span class="date">2024-01-15</span>
                        </div>
                        <div class="comment-text">
                            이 번호로 계속 스팸 전화가 와서 신고했습니다. 정말 짜증나네요. 다른 분들도 조심하세요!
                            이 번호로 계속 스팸 전화가 와서 신고했습니다. 정말 짜증나네요. 다른 분들도 조심하세요!
                        </div>
                    </div>
                </div> -->
                
                <!-- 댓글 작성 영역 -->
                <div class="comment-add">
                    <h4>댓글 쓰기</h4>
                    <div class="comment-form">
                        <textarea id="comment-content" placeholder="이 번호에 대한 경험을 공유해주세요. 사진과 이름은 랜덤으로 적용됩니다."></textarea>
                        <div>
                            <input id="comment-password" type="password" placeholder="비밀번호를 입력하면 삭제할 수 있습니다." hidden>
                            <button id="comment-btn" class="comment-btn">댓글 쓰기</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<?php 
require_once __DIR__ . '/../includes/site-footer.php'; 
?>