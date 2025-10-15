<?php
    require_once __DIR__ . '/../config.php';   

    // 메타데이터
    $page_title = "전화번호 제보하기 | 010number";
    $page_desc  = "모르는 번호나 스팸 전화번호를 제보해주세요. 여러분의 제보로 더 정확한 전화번호 데이터가 만들어집니다.";
    $page_keyword = "전화번호 제보, 스팸 전화 제보, 광고 전화 신고, 모르는 번호 등록, 010number 제보";
    $body_class = "site-report";

    require_once __DIR__ . '/../includes/site-head.php';
    require_once __DIR__ . '/../includes/site-header.php';
    require_once __DIR__ . '/../includes/search-area.php';
?>
        <!-- 제보하기 영역 -->
        <section id="report-area">
            <h2>전화번호 제보하기</h2>
            <p>여러분의 제보가 모두에게 큰 도움이 됩니다</p>

            <form class="report-group" action="/report-save" method="post">
                <input class="report-number" type="text" name="number" id="number" maxlength="14" placeholder="--_--" />
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

<?php
    require_once __DIR__ . '/../includes/site-footer.php'; 
?>