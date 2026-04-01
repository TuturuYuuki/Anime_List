-- ============================================
-- Anime & Waifu Vault - Database Script
-- ============================================

CREATE DATABASE IF NOT EXISTS anime_waifu_vault CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE anime_waifu_vault;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    profile_pict VARCHAR(500) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- Tabel Kredensial WebAuthn (Fingerprint/Face Unlock)
CREATE TABLE IF NOT EXISTS user_credentials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    credential_id VARCHAR(255) NOT NULL,
    public_key TEXT DEFAULT NULL,
    sign_count BIGINT NOT NULL DEFAULT 0,
    transports VARCHAR(255) DEFAULT NULL,
    aaguid VARCHAR(64) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_user_credentials_credential_id (credential_id),
    INDEX idx_user_credentials_user_id (user_id),
    CONSTRAINT fk_user_credentials_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel Token Reset Password
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(128) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_password_reset_token (token),
    INDEX idx_password_reset_user_id (user_id),
    CONSTRAINT fk_password_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel Animes
CREATE TABLE IF NOT EXISTS animes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    mal_id INT DEFAULT NULL,
    judul VARCHAR(255) NOT NULL,
    eps_nonton INT DEFAULT 0,
    eps_total INT DEFAULT 0,
    gambar_path VARCHAR(500) DEFAULT NULL,
    status ENUM('watching', 'completed', 'on_hold', 'dropped', 'plan_to_watch') DEFAULT 'plan_to_watch',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Waifus
CREATE TABLE IF NOT EXISTS waifus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    nama VARCHAR(255) NOT NULL,
    anime_asal VARCHAR(255) DEFAULT NULL,
    umur VARCHAR(50) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    is_fav TINYINT(1) DEFAULT 0,
    pict_path VARCHAR(500) DEFAULT NULL,
    art_path VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample data
INSERT INTO animes (mal_id, judul, eps_nonton, eps_total, status) VALUES
(21, 'One Piece', 1000, 0, 'watching'),
(5114, 'Fullmetal Alchemist: Brotherhood', 64, 64, 'completed'),
(1535, 'Death Note', 37, 37, 'completed');

INSERT INTO waifus (nama, anime_asal, umur, bio, is_fav) VALUES
('Rem', 'Re:Zero', '17', 'Oni biru yang setia dan penuh kasih sayang dari mansion Roswaal.', 1),
('Zero Two', 'Darling in the FranXX', '17', 'Hybrid manusia-klaxosaur yang liar dan penuh semangat.', 0),
('Asuna Yuuki', 'Sword Art Online', '17', 'Flash dari SAO, petarung handal dan penuh perhatian.', 0);
