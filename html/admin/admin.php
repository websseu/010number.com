<?php
session_start();

// 로그인 세션 검사
if (empty($_SESSION['admin_auth'])) {
    header("Location: admin-login.php");
    exit;
}

// 세션 만료 
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > ($_SESSION['expire_time'] ?? 18000)) {
    session_unset();
    session_destroy();
    header("Location: admin-login.php?expired=1");
    exit;
}

require_once __DIR__ . '/../config.php';

$keyword = trim($_GET['q'] ?? '');

// 전화번호 목록 조회
$sql = "
    SELECT 
        p.id,
        p.number,
        p.title,
        p.category_id,
        c.name_ko AS category,
        p.view_count,
        p.created_at,
        COALESCE(com.comment_count, 0) AS comment_count
    FROM phone_numbers p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN (
        SELECT phone_id, COUNT(*) as comment_count 
        FROM comments 
        GROUP BY phone_id
    ) com ON p.id = com.phone_id
";
$params = [];

if ($keyword !== '') {
    $sql .= " WHERE p.number LIKE CONCAT('%', ?, '%') 
            OR p.title LIKE CONCAT('%', ?, '%')
            OR c.name_ko LIKE CONCAT('%', ?, '%')";
    $params = [$keyword, $keyword, $keyword];
}

$sql .= " ORDER BY p.id DESC";

$stmt = $mysqli->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// 카테고리 목록
$categories = [];
$res = $mysqli->query("SELECT id, name_ko FROM categories ORDER BY name_ko ASC");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}
$res->free();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 페이지 | 010number</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .admin-site table td a {
            color: #0D1938;
            text-decoration: none;
            font-weight: 500;
        }
        .admin-site table td a:hover {
            color: #1a2a4a;
            text-decoration: underline;
        }
    </style>
</head>
<body class="admin-site">
<header>
    <h1><a href="/">010number 관리자</a></h1>
</header>

<main>
    <section>
        <form class="search" method="get" action="">
            <input type="text" name="q" placeholder="번호 또는 제목으로 검색" value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit">검색</button>
        </form>
    </section>
    

    <div class="table-wrap">
        <table>
            <colgroup>
                <col style="width: 60px">
                <col style="width: 120px">
                <col style="width: 150px">
                <col style="width: 180px">
                <col style="width: 80px">
                <col style="width: 60px">
                <col style="width: 120px">
                <col style="width: 120px">
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>카테고리</th>
                <th>전화번호</th>
                <th>제목</th>
                <th>조회수</th>
                <th>댓글</th>
                <th>등록일</th>
                <th>관리</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="center"><?= $row['id'] ?></td>
                        <td class="center"><?= htmlspecialchars($row['category'] ?? '-') ?></td>
                        <td><a class="line" href="/number/<?= urlencode(preg_replace('/[^0-9]/', '', $row['number'])) ?>" target="_blank"><?= htmlspecialchars($row['number']) ?></a></td>
                        <td><?= htmlspecialchars($row['title'] ?? '-') ?></td>
                        <td class="center"><?= (int)$row['view_count'] ?></td>
                        <td class="center"><?= (int)$row['comment_count'] ?></td>
                        <td class="center"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td class="center">
                            <button 
                                type="button"
                                class="edit-btn"
                                data-id="<?= $row['id'] ?>"
                                data-number="<?= htmlspecialchars($row['number'], ENT_QUOTES) ?>"
                                data-title="<?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES) ?>"
                                data-category="<?= htmlspecialchars($row['category_id'] ?? '', ENT_QUOTES) ?>"
                            >수정</button>
                            <button type="button" class="delete-btn" data-id="<?= $row['id'] ?>">삭제</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center; padding:20px;">등록된 데이터가 없습니다.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- 수정 모달 -->
<div id="edit-modal">
    <div class="modal-content">
        <h3>데이터 수정</h3>
        <form id="edit-form">
            <input type="hidden" name="id" id="edit-id">
            <label for="edit-number">전화번호</label>
            <input type="text" name="number" id="edit-number" required>
            <label for="edit-title">제목</label>
            <input type="text" name="title" id="edit-title">
            <label for="edit-category">카테고리</label>
            <select name="category_id" id="edit-category">
                <option value="">선택 없음</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name_ko']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="modal-buttons">
                <button type="button" id="cancel-edit">취소</button>
                <button type="submit">저장</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("edit-modal");
    const form = document.getElementById("edit-form");
    const cancelBtn = document.getElementById("cancel-edit");

    // 수정 버튼 클릭 시 모달 열기
    document.querySelectorAll(".edit-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            form.reset();
            modal.style.display = "flex";
            document.getElementById("edit-id").value = btn.dataset.id;
            document.getElementById("edit-number").value  = btn.dataset.number  || "";
            document.getElementById("edit-title").value   = btn.dataset.title   || "";
            document.getElementById("edit-category").value= btn.dataset.category|| "";
        });
    });

    cancelBtn.addEventListener("click", () => modal.style.display = "none");

    // 저장 (AJAX)
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const res = await fetch("admin-update.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(new FormData(form)) + "&action=update"
        });
        const msg = await res.text();
        alert(msg.trim());
        if (msg.includes("완료")) location.reload();
    });

    // 삭제
    document.querySelectorAll(".delete-btn").forEach(btn => {
        btn.addEventListener("click", async () => {
            if (!confirm("정말 삭제하시겠습니까?")) return;
            const res = await fetch("admin-update.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ action: "delete", id: btn.dataset.id })
            });
            const msg = await res.text();
            alert(msg.trim());
            if (msg.includes("삭제")) location.reload();
        });
    });
});
</script>
</body>
</html>
