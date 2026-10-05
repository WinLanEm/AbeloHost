CREATE TABLE articles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    image_path VARCHAR(2048) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    view_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
    published_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    INDEX idx_articles_published_at (published_at),
    INDEX idx_articles_view_count (view_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
