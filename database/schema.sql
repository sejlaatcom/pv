-- =====================================================================
--  منصة نقاطي — مخطط قاعدة البيانات (MySQL / MariaDB)
--  الترميز: utf8mb4 لدعم العربية والرموز التعبيرية
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS quiz_attempts;
DROP TABLE IF EXISTS quiz_questions;
DROP TABLE IF EXISTS quizzes;
DROP TABLE IF EXISTS task_submissions;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS redemptions;
DROP TABLE IF EXISTS rewards;
DROP TABLE IF EXISTS student_badges;
DROP TABLE IF EXISTS badges;
DROP TABLE IF EXISTS point_transactions;
DROP TABLE IF EXISTS point_items;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS student_groups;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS entities;

SET FOREIGN_KEY_CHECKS = 1;

-- ===== الجهات المشتركة (مدرسة، حلقة تحفيظ، أكاديمية...) =====
CREATE TABLE entities (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(200) NOT NULL,
    slug                VARCHAR(100) NOT NULL UNIQUE,
    type                ENUM('school','quran','women','sports','club','other') NOT NULL DEFAULT 'school',
    city                VARCHAR(100) DEFAULT NULL,
    phone               VARCHAR(20)  DEFAULT NULL,
    email               VARCHAR(190) DEFAULT NULL,
    logo_url            VARCHAR(255) DEFAULT NULL,
    primary_color       VARCHAR(20)  DEFAULT '#0d9488',
    subscription_status ENUM('trial','active','expired') NOT NULL DEFAULT 'trial',
    subscription_ends_at DATE        DEFAULT NULL,
    -- تفعيل رابط التسجيل الذاتي للطلاب: /join/{slug}
    allow_self_register TINYINT(1)   NOT NULL DEFAULT 0,
    -- إظهار لوحة شرف عامة للجهة: /e/{slug}
    public_board        TINYINT(1)   NOT NULL DEFAULT 1,
    created_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== مستخدمو لوحة التحكم (مدير الجهة والمشرفون) =====
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    entity_id     INT NOT NULL,
    name          VARCHAR(150) NOT NULL,
    email         VARCHAR(190) NOT NULL UNIQUE,
    phone         VARCHAR(20)  DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('admin','supervisor') NOT NULL DEFAULT 'supervisor',
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    last_login_at DATETIME     DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    INDEX idx_users_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== المجموعات / الفصول / الحلقات =====
CREATE TABLE student_groups (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    entity_id     INT NOT NULL,
    name          VARCHAR(150) NOT NULL,
    description   VARCHAR(300) DEFAULT NULL,
    level         VARCHAR(100) DEFAULT NULL,
    color         VARCHAR(20)  DEFAULT '#0d9488',
    supervisor_id INT          DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_groups_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_groups_supervisor FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_groups_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== الطلاب / المشاركون =====
CREATE TABLE students (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    entity_id      INT NOT NULL,
    group_id       INT DEFAULT NULL,
    name           VARCHAR(150) NOT NULL,
    code           VARCHAR(50)  DEFAULT NULL,
    gender         ENUM('male','female') NOT NULL DEFAULT 'male',
    guardian_name  VARCHAR(150) DEFAULT NULL,
    guardian_phone VARCHAR(20)  DEFAULT NULL,
    access_token   VARCHAR(40)  NOT NULL UNIQUE,
    total_points   INT          NOT NULL DEFAULT 0,
    is_active      TINYINT(1)   NOT NULL DEFAULT 1,
    joined_at      DATE         DEFAULT NULL,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_students_group FOREIGN KEY (group_id) REFERENCES student_groups(id) ON DELETE SET NULL,
    INDEX idx_students_entity (entity_id),
    INDEX idx_students_group (group_id),
    INDEX idx_students_points (entity_id, total_points)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== بنود النقاط (سلوكيات إيجابية وسلبية) =====
CREATE TABLE point_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    entity_id  INT NOT NULL,
    title      VARCHAR(150) NOT NULL,
    points     INT NOT NULL,
    category   ENUM('behavior','attendance','memorization','homework','participation','penalty','other') NOT NULL DEFAULT 'behavior',
    color      VARCHAR(20) DEFAULT '#0d9488',
    is_active  TINYINT(1)  NOT NULL DEFAULT 1,
    created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_items_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    INDEX idx_items_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== حركات النقاط =====
CREATE TABLE point_transactions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    entity_id   INT NOT NULL,
    student_id  INT NOT NULL,
    item_id     INT DEFAULT NULL,
    points      INT NOT NULL,
    type        ENUM('earn','deduct','redeem','adjust') NOT NULL DEFAULT 'earn',
    reason      VARCHAR(300) DEFAULT NULL,
    awarded_by  INT DEFAULT NULL,
    awarded_by_name VARCHAR(150) DEFAULT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tx_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_tx_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_tx_item FOREIGN KEY (item_id) REFERENCES point_items(id) ON DELETE SET NULL,
    CONSTRAINT fk_tx_user FOREIGN KEY (awarded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_tx_entity_date (entity_id, created_at),
    INDEX idx_tx_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== الأوسمة =====
CREATE TABLE badges (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    entity_id       INT NOT NULL,
    title           VARCHAR(150) NOT NULL,
    description     VARCHAR(300) DEFAULT NULL,
    icon            VARCHAR(20)  DEFAULT '🏅',
    color           VARCHAR(20)  DEFAULT '#f59e0b',
    required_points INT          DEFAULT NULL,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_badges_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    INDEX idx_badges_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE student_badges (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    badge_id   INT NOT NULL,
    awarded_by INT DEFAULT NULL,
    awarded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sb_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_sb_badge FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    UNIQUE KEY uq_student_badge (student_id, badge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== متجر الجوائز =====
CREATE TABLE rewards (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    entity_id   INT NOT NULL,
    title       VARCHAR(150) NOT NULL,
    description VARCHAR(300) DEFAULT NULL,
    cost        INT NOT NULL,
    stock       INT NOT NULL DEFAULT 0,
    icon        VARCHAR(20) DEFAULT '🎁',
    is_active   TINYINT(1)  NOT NULL DEFAULT 1,
    created_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rewards_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    INDEX idx_rewards_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE redemptions (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    entity_id  INT NOT NULL,
    student_id INT NOT NULL,
    reward_id  INT NOT NULL,
    cost       INT NOT NULL,
    status     ENUM('pending','approved','delivered','rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    handled_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_red_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_red_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_red_reward FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE CASCADE,
    INDEX idx_red_entity (entity_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== الحضور والانصراف =====
CREATE TABLE attendance (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    entity_id   INT NOT NULL,
    group_id    INT DEFAULT NULL,
    student_id  INT NOT NULL,
    day         DATE NOT NULL,
    status      ENUM('present','absent','late','excused') NOT NULL DEFAULT 'present',
    check_in    VARCHAR(10) DEFAULT NULL,
    check_out   VARCHAR(10) DEFAULT NULL,
    notes       VARCHAR(300) DEFAULT NULL,
    recorded_by INT DEFAULT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_att_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY uq_att_student_day (student_id, day),
    INDEX idx_att_entity_day (entity_id, day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== المهام والدرجات =====
CREATE TABLE tasks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    entity_id   INT NOT NULL,
    group_id    INT DEFAULT NULL,
    title       VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    due_date    DATE DEFAULT NULL,
    max_score   INT NOT NULL DEFAULT 10,
    points      INT NOT NULL DEFAULT 10,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_by  INT DEFAULT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_group FOREIGN KEY (group_id) REFERENCES student_groups(id) ON DELETE SET NULL,
    INDEX idx_tasks_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE task_submissions (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    task_id      INT NOT NULL,
    student_id   INT NOT NULL,
    score        INT DEFAULT NULL,
    status       ENUM('pending','submitted','graded','late') NOT NULL DEFAULT 'pending',
    notes        VARCHAR(300) DEFAULT NULL,
    submitted_at DATETIME DEFAULT NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sub_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    CONSTRAINT fk_sub_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY uq_task_student (task_id, student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== المسابقات والاستبانات =====
CREATE TABLE quizzes (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    entity_id         INT NOT NULL,
    title             VARCHAR(200) NOT NULL,
    description       TEXT DEFAULT NULL,
    type              ENUM('quiz','survey') NOT NULL DEFAULT 'quiz',
    category          VARCHAR(100) DEFAULT NULL,
    points_per_correct INT NOT NULL DEFAULT 5,
    time_limit_minutes INT DEFAULT NULL,
    is_published      TINYINT(1) NOT NULL DEFAULT 0,
    created_by        INT DEFAULT NULL,
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_quiz_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    INDEX idx_quiz_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_questions (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id        INT NOT NULL,
    question       TEXT NOT NULL,
    options_json   TEXT DEFAULT NULL,
    correct_answer VARCHAR(500) DEFAULT NULL,
    points         INT NOT NULL DEFAULT 1,
    sort_order     INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_qq_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_qq_quiz (quiz_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_attempts (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id         INT NOT NULL,
    student_id      INT NOT NULL,
    correct_count   INT NOT NULL DEFAULT 0,
    total_questions INT NOT NULL DEFAULT 0,
    points_awarded  INT NOT NULL DEFAULT 0,
    answers_json    TEXT DEFAULT NULL,
    completed_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_qa_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    CONSTRAINT fk_qa_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY uq_quiz_student (quiz_id, student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== سجل التنبيهات المرسلة لأولياء الأمور =====
CREATE TABLE notifications (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    entity_id  INT NOT NULL,
    student_id INT DEFAULT NULL,
    title      VARCHAR(200) NOT NULL,
    body       TEXT DEFAULT NULL,
    channel    ENUM('app','whatsapp') NOT NULL DEFAULT 'app',
    created_by INT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_notif_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_notif_entity (entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== رسائل نموذج التواصل في الموقع التعريفي =====
CREATE TABLE contact_messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    email       VARCHAR(190) NOT NULL,
    phone       VARCHAR(20) DEFAULT NULL,
    entity_type VARCHAR(50) DEFAULT NULL,
    subject     VARCHAR(200) DEFAULT NULL,
    message     TEXT NOT NULL,
    status      ENUM('new','read','replied') NOT NULL DEFAULT 'new',
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
