export function initReportPad() {
    const numberInput = document.querySelector("#number");
    const titleInput = document.querySelector("#title");
    const buttons = document.querySelectorAll(".report-pad button");
    const form = numberInput?.closest("form");

    if (!numberInput || !buttons.length) return;

    // ===============================
    // 🔊 1️⃣ 효과음 리스트 준비
    // ===============================
    const soundPaths = [
        "/assets/sound/button-push.wav",
        "/assets/sound/button-radio-ping.wav",
        "/assets/sound/light-button.wav",
        "/assets/sound/technology-button.wav"
    ];

    // 미리 로드한 오디오 객체들
    const sounds = soundPaths.map(path => {
        const audio = new Audio(path);
        audio.preload = "auto";
        audio.volume = 0.5;
        return audio;
    });

    // 등록용 음악
    const musicSound = new Audio("/assets/sound/game-level-music.wav");
    musicSound.preload = "auto";
    musicSound.volume = 0.6;

    // 랜덤 재생 함수
    function playRandomSound() {
        const sound = sounds[Math.floor(Math.random() * sounds.length)];
        try {
            sound.currentTime = 0; // 처음부터 재생
            sound.play();
        } catch (err) {
            console.warn("사운드 재생 실패:", err);
        }
    }

    // 음악 재생
    function playMusic() {
        try {
            musicSound.currentTime = 0;
            musicSound.play();
        } catch (err) {
            console.warn("음악 재생 실패:", err);
        }
    }

    // ===============================
    // 2️⃣ 버튼 클릭 처리
    // ===============================
    let eraseClickTime = 0;
    let eraseTimer = null;

    buttons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            if (btn.type !== "submit") e.preventDefault();

            const isEraser = btn.querySelector(".lucide-eraser");
            const text = btn.textContent.trim();
            let current = numberInput.value.replace(/[^0-9]/g, "");

            // 지우기 버튼 처리
            if (isEraser) {
                current = current.slice(0, -1);
                numberInput.value = formatKoreanPhone(current);

                // 지우개 더블클릭 감지
                eraseClickTime++;
                clearTimeout(eraseTimer);
                eraseTimer = setTimeout(() => {
                    eraseClickTime = 0;
                }, 400); // 0.4초 이내 두 번 클릭 시

                if (eraseClickTime >= 2) {
                    eraseClickTime = 0;
                    playMusic(); // 🎵 음악 재생
                }
                return;
            }

            // 숫자 버튼일 경우 효과음
            if (/^[0-9]$/.test(text)) {
                playRandomSound();
                current += text;
                if (current.length > 11) current = current.slice(0, 11);
                numberInput.value = formatKoreanPhone(current);
            }
        });
    });

    // ===============================
    // 3️⃣ 폼 제출 시 유효성 검사
    // ===============================
    form.addEventListener("submit", (e) => {
        const number = numberInput.value.trim();
        const title = titleInput.value.trim();

        if (number === "") {
            e.preventDefault();
            alert("전화번호를 입력해주세요.");
            numberInput.focus();
            return;
        }

        if (title === "") {
            e.preventDefault();
            alert("제목(설명)을 입력해주세요.");
            titleInput.focus();
            return;
        }
    });

    // ===============================
    // 4️⃣ 키보드 입력 처리
    // ===============================
    document.addEventListener("keydown", (e) => {
        const target = e.target;
        const isEditable =
            target.isContentEditable ||
            target.tagName === "TEXTAREA" ||
            (target.tagName === "INPUT" &&
                target.type !== "submit" &&
                target.type !== "button");

        if (isEditable && target !== numberInput) return;

        let current = numberInput.value.replace(/[^0-9]/g, "");

        // 숫자 키
        if (/^[0-9]$/.test(e.key)) {
            e.preventDefault();
            playRandomSound();
            current += e.key;
            if (current.length > 11) current = current.slice(0, 11);
            numberInput.value = formatKoreanPhone(current);
        }

        // Backspace
        if (e.key === "Backspace") {
            e.preventDefault();
            current = current.slice(0, -1);
            numberInput.value = formatKoreanPhone(current);
        }

        // Enter → 폼 제출
        if (e.key === "Enter") {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    // ===============================
    // 5️⃣ 입력창 직접 입력 시 자동 포맷
    // ===============================
    numberInput.addEventListener("input", () => {
        let raw = numberInput.value.replace(/[^0-9]/g, "");
        numberInput.value = formatKoreanPhone(raw);
    });

    // ===============================
    // 6️⃣ 한국 전화번호 포맷 함수
    // ===============================
    function formatKoreanPhone(num) {
        num = num.replace(/[^0-9]/g, "");

        if (/^(1\d{2,3})$/.test(num)) return num;
        if (/^(15|16|18)\d{2}\d{4}$/.test(num)) return num.replace(/^(\d{4})(\d{4})$/, "$1-$2");

        if (/^02/.test(num)) {
            if (num.length <= 5) return num;
            else if (num.length <= 9)
                return num.replace(/^(\d{2})(\d{3,4})(\d{0,4})$/, "$1-$2-$3").replace(/-$/, "");
            else return num.slice(0, 10).replace(/^(\d{2})(\d{4})(\d{4})$/, "$1-$2-$3");
        }

        if (/^0\d{2}/.test(num)) {
            if (num.length <= 6) return num;
            else if (num.length === 10)
                return num.replace(/^(\d{3})(\d{3})(\d{4})$/, "$1-$2-$3");
            else if (num.length === 11)
                return num.replace(/^(\d{3})(\d{3})(\d{4})$/, "$1-$2-$3");
            else if (num.length <= 10)
                return num.replace(/^(\d{3})(\d{3,4})(\d{0,4})$/, "$1-$2-$3").replace(/-$/, "");
        }

        if (/^0(60|70|80)/.test(num)) {
            return num.slice(0, 10)
                .replace(/^(\d{3})(\d{3})(\d{0,4})$/, "$1-$2-$3")
                .replace(/-$/, "");
        }

        if (/^01[016789]/.test(num)) {
            if (num.length <= 7) return num;
            else if (num.length === 11)
                return num.replace(/^(\d{3})(\d{4})(\d{4})$/, "$1-$2-$3");
            else return num.replace(/^(\d{3})(\d{3,4})(\d{0,4})$/, "$1-$2-$3").replace(/-$/, "");
        }

        return num;
    }
}
