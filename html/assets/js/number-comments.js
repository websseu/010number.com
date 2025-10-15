export function initComments() {
  const group = document.querySelector(".comment-group");
  if (!group) return;

  const phoneId = group.dataset.phoneId;
  const listEl = group.querySelector(".comment-list");
  const countEl = document.getElementById("comment-count");
  const textarea = document.getElementById("comment-content");
  const passwordInput = document.getElementById("comment-password");
  const btn = document.getElementById("comment-btn");

  const avatars = [
    "Angry-with-Fang", "Awe", "Blank", "Calm", "Cheeky", "Concerned-Fear",
    "Concerned", "Contempt", "Cute", "Cyclops", "Driven", "Eating-Happy",
    "Explaining", "Eyes-Closed", "Fear", "Hectic", "Loving-Grin-1",
    "Loving-Grin2", "Monster", "Old", "Rage", "Serious", "Smile-Big",
    "Smile-LOL", "Smile-Teeth-Gap", "Smile", "Solemn", "Suspicious",
    "Tired", "Very-Angry"
  ];

  const lastNames = ["김", "이", "박", "최", "정", "강", "조", "윤", "장", "임"];
  const firstNames = [
    "민수", "영희", "지훈", "서연", "현우", "지민", "수빈", "도윤", "하늘", "예린",
    "지원", "하은", "태현", "서준", "예진", "지아", "민재", "하람", "시우", "수아"
  ];

  function randomName() {
    const ln = lastNames[Math.floor(Math.random() * lastNames.length)];
    const fn = firstNames[Math.floor(Math.random() * firstNames.length)];
    return ln + fn;
  }

  // 댓글 불러오기
  async function loadComments() {
    listEl.innerHTML = `<p class="loading">댓글 불러오는 중...</p>`;
    const res = await fetch(`/comment-list?phone_id=${phoneId}`);
    const comments = await res.json();

    if (countEl) countEl.textContent = comments.length;

    // 아바타 & 이름 랜덤 함수
    const avatar = avatars[Math.floor(Math.random() * avatars.length)];
    const author = randomName();

    // 댓글이 없을 때
    if (!comments.length) {
      listEl.innerHTML = `
        <div class="comment-item first-comment">
          <div class="comment-avatar">
            <img src="/assets/img/face/${avatar}.svg" alt="${author}">
          </div>
          <div class="comment-content">
            <div class="comment-header">
              <span class="author">${author}</span>
              <span class="date">${new Date().toISOString().slice(0, 10)}</span>
            </div>
            <div class="comment-text">
              아직 댓글이 없습니다.<br>
              <strong>첫 번째 댓글</strong>을 남겨보세요! 😊
            </div>
          </div>
        </div>
      `;
      return;
    }

    // 댓글이 있을 때
    listEl.innerHTML = comments.map(c => {
      const avatar = avatars[Math.floor(Math.random() * avatars.length)];
      const author = randomName();
      return `
        <div class="comment-item">
          <div class="comment-avatar">
            <img src="/assets/img/face/${avatar}.svg" alt="${author}">
          </div>
          <div class="comment-content">
            <div class="comment-header">
              <span class="author">${author}</span>
              <span class="date">${c.created_at}</span>
            </div>
            <div class="comment-text">${c.content}</div>
          </div>
        </div>
      `;
    }).join("");
  }

  // 댓글 작성
  btn.addEventListener("click", async () => {
    const content = textarea.value.trim();
    const password = passwordInput.value.trim();

    if (!content) {
      alert("댓글 내용을 입력하세요.");
      return;
    }

    const res = await fetch("/comment-save", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        phone_id: phoneId,
        content,
        password
      })
    });

    const result = await res.json();
    alert(result.message);

    if (result.success) {
      textarea.value = "";
      passwordInput.value = "";
      loadComments();
    }
  });

  loadComments();
}
