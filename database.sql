-- ============================================
-- Anime & Waifu Vault - Database Script
-- ============================================

CREATE DATABASE IF NOT EXISTS anime_waifu_vault CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE anime_waifu_vault;

-- Tabel Animes
CREATE TABLE IF NOT EXISTS animes (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
