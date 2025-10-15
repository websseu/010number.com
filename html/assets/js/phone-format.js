/**
 * 한국 전화번호 형식으로 변환 (010-2222-4444)
 * @param {string} number - 변환할 전화번호
 * @returns {string} - 변환된 전화번호
 */
export function formatKoreanPhone(number) {
    let num = number.replace(/[^0-9]/g, "");

    // 1xx 번호 (114, 1588-2188 등)
    if (/^(1\d{2,3})$/.test(num)) return num;
    if (/^(15|16|18)\d{2}\d{4}$/.test(num)) return num.replace(/^(\d{4})(\d{4})$/, "$1-$2");

    // 02 지역번호 (서울)
    if (/^02/.test(num)) {
        if (num.length <= 5) return num;
        else if (num.length <= 9)
            return num.replace(/^(\d{2})(\d{3,4})(\d{0,4})$/, "$1-$2-$3").replace(/-$/, "");
        else return num.slice(0, 10).replace(/^(\d{2})(\d{4})(\d{4})$/, "$1-$2-$3");
    }

    // 0xx 지역번호 (기타 지역)
    if (/^0\d{2}/.test(num)) {
        if (num.length <= 6) return num;
        else if (num.length === 10)
            return num.replace(/^(\d{3})(\d{3})(\d{4})$/, "$1-$2-$3");
        else if (num.length === 11)
            return num.replace(/^(\d{3})(\d{3})(\d{4})$/, "$1-$2-$3");
        else if (num.length <= 10)
            return num.replace(/^(\d{3})(\d{3,4})(\d{0,4})$/, "$1-$2-$3").replace(/-$/, "");
    }

    // 060, 070, 080 번호
    if (/^0(60|70|80)/.test(num)) {
        return num.slice(0, 10)
            .replace(/^(\d{3})(\d{3})(\d{0,4})$/, "$1-$2-$3")
            .replace(/-$/, "");
    }

    // 010, 011, 016, 017, 018, 019 휴대폰 번호
    if (/^01[016789]/.test(num)) {
        if (num.length <= 7) return num;
        else if (num.length === 11)
            return num.replace(/^(\d{3})(\d{4})(\d{4})$/, "$1-$2-$3");
        else return num.replace(/^(\d{3})(\d{3,4})(\d{0,4})$/, "$1-$2-$3").replace(/-$/, "");
    }

    return num;
}

/**
 * 국제 전화번호 형식으로 변환 (82 10-2222-3333)
 * @param {string} number - 변환할 전화번호
 * @returns {string} - 변환된 전화번호
 */
export function formatIntlPhone(number) {
    let num = number.replace(/[^0-9]/g, "");

    // 1xx 번호는 +82를 붙여서 반환
    if (/^(1\d{2,3})$/.test(num)) return `+82 ${num}`;
    if (/^(15|16|18)\d{2}\d{4}$/.test(num)) return num.replace(/^(\d{4})(\d{4})$/, "+82 $1-$2");

    // 02 지역번호 (서울) - +82 2-xxxx-xxxx
    if (/^02/.test(num)) {
        if (num.length <= 5) return num;
        else if (num.length <= 9)
            return num.replace(/^(\d{2})(\d{3,4})(\d{0,4})$/, "+82 $2-$3").replace(/-$/, "");
        else return num.slice(0, 10).replace(/^(\d{2})(\d{4})(\d{4})$/, "+82 $2-$3");
    }

    // 0xx 지역번호 (기타 지역) - +82 xx-xxx-xxxx (앞의 0 제거)
    if (/^0\d{2}/.test(num)) {
        if (num.length <= 6) return num;
        else if (num.length <= 10)
            return num.replace(/^(\d{3})(\d{3,4})(\d{0,4})$/, "+82 $1-$2-$3").replace(/^\+82 0/, "+82 ").replace(/-$/, "");
    }

    // 060, 070, 080 번호 - +82 60-xxx-xxxx (앞의 0 제거)
    if (/^0(60|70|80)/.test(num)) {
        return num.slice(0, 10)
            .replace(/^(\d{3})(\d{3})(\d{0,4})$/, "+82 $1-$2-$3")
            .replace(/^\+82 0/, "+82 ")
            .replace(/-$/, "");
    }

    // 010, 011, 016, 017, 018, 019 휴대폰 번호 - +82 10-xxxx-xxxx (앞의 0 제거)
    if (/^01[016789]/.test(num)) {
        if (num.length <= 7) return num;
        else return num.replace(/^(\d{3})(\d{3,4})(\d{0,4})$/, "+82 $1-$2-$3").replace(/^\+82 0/, "+82 ").replace(/-$/, "");
    }

    return num;
}

/**
 * sub-number 요소에 변환된 전화번호 출력
 */
export function initPhoneNumberFormat() {
    const subNumberElement = document.querySelector('.sub-number');
    const numberElement = document.querySelector('.number');

    if (subNumberElement && numberElement) {
        const originalNumber = numberElement.textContent.trim();
        const koreanFormat = formatKoreanPhone(originalNumber);
        const intlFormat = formatIntlPhone(originalNumber);

        subNumberElement.innerHTML = `${koreanFormat} / ${intlFormat}`;
    }
}
