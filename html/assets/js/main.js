import { initPageLoader } from "./page-loader.js";
import { detectUserAgent } from "./user-agent.js";
import { initReportPad } from "./report-pad.js";
import { initPhoneNumberFormat } from "./phone-format.js";
import { initComments } from "./number-comments.js";

document.addEventListener("DOMContentLoaded", () => {
    // 디바이스 환경 감지
    detectUserAgent();

    // 페이지 로딩 애니메이션
    initPageLoader();

    // 제보하기 페이지 전화번호 패드
    initReportPad();

    // 전화번호 포맷팅
    initPhoneNumberFormat();

    // 댓글 기능 활성화
    initComments();
});