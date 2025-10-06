    <section class="search-section" role="search" aria-label="전화번호 검색">
        <form class="search-form" action="/" method="get">
            <label for="search-input" class="visually-hidden">전화번호 검색</label>
            <input
                type="text"
                id="search-input"
                name="q"
                placeholder="모르는 번호를 검색해보세요!"
                aria-label="전화번호 입력"
                value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required
            >
            <button
                type="submit"
                class="search-button"
                aria-label="검색"
            ></button>
        </form>
    </section> 