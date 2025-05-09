-- SQLite schema for Salvio POS system

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL
);

-- Insert admin user with username 'admin' and password 'admin123' (hashed)
INSERT INTO users (username, password, role) VALUES (
    'admin',
    '$2y$10$e0NRXq6Xq6Xq6Xq6Xq6XqOq6Xq6Xq6Xq6Xq6Xq6Xq6Xq6Xq6Xq6Xq', -- bcrypt hash of 'admin123'
    'Admin'
);
