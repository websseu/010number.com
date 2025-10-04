<?php
    require_once __DIR__ . '/config.php';

    $page_title = "모르는 번호 검색 · 스팸 전화 조회 서비스 | 010number";
    $page_desc  = "모르는 번호·스팸 전화·광고 전화까지 쉽게 검색할 수 있는 서비스 010number";
    $body_class  = "site-home"; 

    require_once __DIR__ . '/includes/head.php'; 
?>


<body class="<?= $body_class ?? '' ?>">
    <header class="site-header vh">
        <h1>
            <a href="<?= BASE_URL ?>">
                <img class="site-logo" src="assets/images/logo.png" alt="010number">
            </a>
        </h1>
        <p>모르는 번호 검색 · 스팸 전화 조회 서비스</p>
    </header>

    <main class="site-main">
        <section class="container center">
            <h2 class="vh">모르는 번호, 스팸 전화 걱정 끝!</h2>
            <a href="<?= BASE_URL ?>">
                <img class="site-logo" src="assets/images/logo.png" alt="010number">
            </a>
            <form class="search-form" action="/search.php" method="get" role="search" aria-label="전화번호 검색">
                <input type="text" name="q" placeholder="모르는 번호를 검색해보세요!" required>
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        role="img" class="icon ">
                        <path
                            d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                    </svg>
                </button>
            </form>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p><a href="mailto:webstoryboy@naver.com">© 2025 010number.com</a></p>
        </div>
    </footer>
</body>
</html>
