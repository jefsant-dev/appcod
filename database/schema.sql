CREATE TABLE IF NOT EXISTS visitors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NULL,
    forwarded_for VARCHAR(255) NULL,
    user_agent VARCHAR(1000) NULL,
    referer_url VARCHAR(2048) NULL,
    landing_url VARCHAR(2048) NULL,
    query_string VARCHAR(1024) NULL,
    utm_source VARCHAR(255) NULL,
    utm_medium VARCHAR(255) NULL,
    utm_campaign VARCHAR(255) NULL,
    utm_term VARCHAR(255) NULL,
    utm_content VARCHAR(255) NULL,
    accept_language VARCHAR(255) NULL,
    is_bot TINYINT(1) NOT NULL DEFAULT 0,
    bot_reason VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visitors_created_at (created_at),
    INDEX idx_visitors_ip_created (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visitor_id BIGINT UNSIGNED NULL,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    assunto VARCHAR(120) NOT NULL,
    mensagem TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(1000) NULL,
    email_sent TINYINT(1) NOT NULL DEFAULT 0,
    email_sent_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contact_messages_visitor
        FOREIGN KEY (visitor_id) REFERENCES visitors(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    INDEX idx_messages_created_at (created_at),
    INDEX idx_messages_email_created (email, created_at),
    INDEX idx_messages_visitor_id (visitor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;