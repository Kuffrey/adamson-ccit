
CREATE TABLE IF NOT EXISTS announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    excerpt VARCHAR(255) DEFAULT NULL,
    body TEXT NOT NULL,
    category VARCHAR(64) DEFAULT 'general',
    image_url VARCHAR(255) DEFAULT NULL,
    date DATE DEFAULT NULL,
    author VARCHAR(128) DEFAULT NULL,
    status VARCHAR(32) DEFAULT 'published',
    published_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample announcement
INSERT INTO announcements (title, excerpt, body, category, image_url, date, author, status, published_at, created_at, updated_at)
VALUES (
    'Mid-Year Inset 2025: Responsible AI in Education',
    'Adamson University launches its first-ever 2025 Mid-Year Inset with a focus on responsible AI.',
    'Adamson University''s Center for Innovative Learning (CIL) held a full-day training on June 2 at the Co Po Ty Hall, Carlos Tiu Building, gathering faculty from all colleges for an intensive program on integrating AI into the academic landscape, guided by ethics, inclusion, and responsibility.',
    'general',
    '/adamson-ccit/public/assets/images/news/sample3.jpg',
    '2025-06-02',
    'CCIT Communications',
    'published',
    NOW(),
    NOW(),
    NOW()
);
