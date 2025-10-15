# 010number 프로젝트

모르는 번호, 스팸 전화, 광고 전화를 검색하고 사용자들이 확인할 수 있는 웹 서비스입니다.

## 📂 디렉토리 구조

html/
├── assets/
│   ├── css/
│   │   ├── _commons.css
│   │   ├── _fonts.css
│   │   ├── _resets.css
│   │   └── style.css
│   ├── fonts/
│   ├── img/
│   └── js/
│       ├── main.js
│       ├── page-loader.js
│       ├── report-pad.js     
│       └── user-agent.js
│
├── data/
│   ├── emart_stores.json
│   └── import_emart.php
│
├── includes/
│   ├── info-meta.php
│   ├── search-area.php
│   ├── site-footer.php
│   └── site-header.php
│
├── pages/
│   ├── delete.php
│   ├── main.php
│   ├── report-save.php       
│   └── report.php            
│
├── phpMyAdmin/
├── config.php
├── index.php
├── phpinfo.php
└── README.md

## 테이블
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) UNIQUE NOT NULL COMMENT '영문 식별자 (예: spam, ad, store)',
    name_ko VARCHAR(50) NOT NULL COMMENT '한글명 (예: 스팸, 광고, 상점)',
    color VARCHAR(10) DEFAULT '#999' COMMENT '뱃지 색상 HEX',
    description VARCHAR(255) DEFAULT NULL COMMENT '카테고리 설명'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (id, slug, name_ko, color, description) VALUES
(1, 'fraud', '사기/피싱', '#B71C1C', '스미싱, 피싱, 보이스피싱 관련 사기 전화'),
(2, 'spam', '스팸/광고', '#D32F2F', '불필요 문자, 광고문자, 홍보, 대리운전'),
(3, 'edu', '교육/학원', '#0288D1', '대학교, 교육원, 학원, 개발원 등 교육 관련'),
(4, 'finance', '금융/은행', '#512DA8', '은행, 카드, 증권, 페이, 캐피탈 등 금융 관련'),
(5, 'delivery', '택배/배달', '#388E3C', '택배, 운전, 대리기사, 기사, 배송 관련'),
(6, 'security', '인증/보안', '#00796B', '인증번호, 인증문자, 모바일인증 관련'),
(7, 'marketing', '광고/마케팅', '#F57C00', '광고, 프로모션, 이벤트, 혜택 안내'),
(8, 'sales', '영업/상담', '#5D4037', '영업사원, 상담원, 정치문자 등 영업 목적'),
(9, 'transport', '운송/항공', '#1976D2', '항공사, 운송업, 아시아나항공 등'),
(10, 'platform', '플랫폼/앱', '#0097A7', '배달의민족, 앱 기반 서비스, 온라인 플랫폼'),
(11, 'public', '공공기관', '#7B1FA2', '공단, 국립, 정부기관, 공공기관 등'),
(12, 'shop', '쇼핑', '#FBC02D', '홈쇼핑, G마켓, 온라인 쇼핑 관련'),
(13, 'store', '상점', '#303F9F', '토즈, 야놀자, 오프라인 매장, 소상공인'),
(14, 'hospital', '병원', '#C2185B', '병원, 의원, 의료기관 관련'),
(15, 'etc', '기타', '#9E9E9E', '기타 분류 불명확 번호');

CREATE TABLE phone_numbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(20) NOT NULL UNIQUE COMMENT '전화번호',
    title VARCHAR(100) DEFAULT NULL COMMENT '상점명 또는 발신자명',
    category_id INT DEFAULT NULL COMMENT '카테고리 ID',
    view_count INT DEFAULT 0 COMMENT '조회수',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '등록일',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




	
