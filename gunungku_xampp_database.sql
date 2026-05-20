-- Gunungku Database Schema for XAMPP / phpMyAdmin
-- MySQL 8+
-- Generated from uploaded interface files

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP DATABASE IF EXISTS gunungku;
CREATE DATABASE gunungku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gunungku;

-- =========================
-- MASTER USERS & AUTH
-- =========================
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL DEFAULT 2,
    nama_lengkap VARCHAR(120) NOT NULL,
    username VARCHAR(50) DEFAULT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    foto_profil VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    level_pendaki VARCHAR(50) DEFAULT 'Explorer',
    status_akun ENUM('aktif','nonaktif','diblokir') NOT NULL DEFAULT 'aktif',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE user_stats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    total_summit INT UNSIGNED NOT NULL DEFAULT 0,
    total_km DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_elevation_gain INT UNSIGNED NOT NULL DEFAULT 0,
    total_jam_trail INT UNSIGNED NOT NULL DEFAULT 0,
    avg_pace DECIMAL(5,2) NOT NULL DEFAULT 0,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_stats_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_stats_user (user_id)
) ENGINE=InnoDB;

-- =========================
-- MOUNTAINS / DISCOVERY / MAP
-- =========================
CREATE TABLE mountains (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_gunung VARCHAR(120) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    lokasi VARCHAR(150) NOT NULL,
    provinsi VARCHAR(100) DEFAULT NULL,
    ketinggian_mdpl INT UNSIGNED NOT NULL,
    tingkat_kesulitan ENUM('mudah','sedang','sulit','ekstrem') NOT NULL DEFAULT 'sedang',
    durasi_hari TINYINT UNSIGNED DEFAULT NULL,
    deskripsi TEXT,
    status_gunung ENUM('buka','tutup','terbatas') NOT NULL DEFAULT 'buka',
    cuaca_ringkas VARCHAR(120) DEFAULT NULL,
    suhu_min DECIMAL(4,1) DEFAULT NULL,
    suhu_max DECIMAL(4,1) DEFAULT NULL,
    koordinat_lat DECIMAL(10,7) DEFAULT NULL,
    koordinat_lng DECIMAL(10,7) DEFAULT NULL,
    gambar_utama VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE trails (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mountain_id BIGINT UNSIGNED NOT NULL,
    nama_jalur VARCHAR(120) NOT NULL,
    titik_awal VARCHAR(120) DEFAULT NULL,
    jarak_km DECIMAL(6,2) DEFAULT NULL,
    estimasi_jam DECIMAL(5,2) DEFAULT NULL,
    tingkat_kesulitan ENUM('mudah','sedang','sulit','ekstrem') NOT NULL DEFAULT 'sedang',
    status_jalur ENUM('aktif','tutup','perbaikan') NOT NULL DEFAULT 'aktif',
    deskripsi TEXT DEFAULT NULL,
    peta_offline_file VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_trails_mountain FOREIGN KEY (mountain_id) REFERENCES mountains(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE trail_waypoints (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trail_id BIGINT UNSIGNED NOT NULL,
    nama_waypoint VARCHAR(120) NOT NULL,
    urutan INT UNSIGNED NOT NULL,
    ketinggian_mdpl INT UNSIGNED DEFAULT NULL,
    suhu_c DECIMAL(4,1) DEFAULT NULL,
    jarak_dari_start_km DECIMAL(6,2) DEFAULT NULL,
    estimasi_menit_dari_start INT UNSIGNED DEFAULT NULL,
    lat DECIMAL(10,7) DEFAULT NULL,
    lng DECIMAL(10,7) DEFAULT NULL,
    keterangan TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_waypoints_trail FOREIGN KEY (trail_id) REFERENCES trails(id) ON DELETE CASCADE,
    UNIQUE KEY uk_waypoint_order (trail_id, urutan)
) ENGINE=InnoDB;

CREATE TABLE mountain_weather (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mountain_id BIGINT UNSIGNED NOT NULL,
    waypoint_id BIGINT UNSIGNED DEFAULT NULL,
    waktu_pencatatan DATETIME NOT NULL,
    kondisi VARCHAR(100) NOT NULL,
    suhu_c DECIMAL(4,1) DEFAULT NULL,
    kelembapan TINYINT UNSIGNED DEFAULT NULL,
    kecepatan_angin DECIMAL(5,2) DEFAULT NULL,
    catatan VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_weather_mountain FOREIGN KEY (mountain_id) REFERENCES mountains(id) ON DELETE CASCADE,
    CONSTRAINT fk_weather_waypoint FOREIGN KEY (waypoint_id) REFERENCES trail_waypoints(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================
-- SIMAKSI
-- =========================
CREATE TABLE simaksi_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_simaksi VARCHAR(30) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    mountain_id BIGINT UNSIGNED NOT NULL,
    trail_id BIGINT UNSIGNED DEFAULT NULL,
    nama_pengaju VARCHAR(120) NOT NULL,
    nik VARCHAR(30) NOT NULL,
    tanggal_naik DATE NOT NULL,
    tanggal_turun DATE NOT NULL,
    jumlah_peserta INT UNSIGNED NOT NULL DEFAULT 1,
    status_pengajuan ENUM('draft','diajukan','diverifikasi','ditolak','selesai') NOT NULL DEFAULT 'draft',
    setuju_peraturan TINYINT(1) NOT NULL DEFAULT 0,
    catatan_admin TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_simaksi_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_simaksi_mountain FOREIGN KEY (mountain_id) REFERENCES mountains(id) ON DELETE RESTRICT,
    CONSTRAINT fk_simaksi_trail FOREIGN KEY (trail_id) REFERENCES trails(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE simaksi_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    simaksi_id BIGINT UNSIGNED NOT NULL,
    jenis_dokumen ENUM('ktp','paspor','surat_sehat','lainnya') NOT NULL DEFAULT 'ktp',
    nama_file VARCHAR(255) NOT NULL,
    path_file VARCHAR(255) NOT NULL,
    ukuran_kb INT UNSIGNED DEFAULT NULL,
    mime_type VARCHAR(100) DEFAULT NULL,
    uploaded_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_simaksi_doc_simaksi FOREIGN KEY (simaksi_id) REFERENCES simaksi_applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================
-- CHECKLIST PERLENGKAPAN
-- =========================
CREATE TABLE equipment_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    icon_name VARCHAR(50) DEFAULT NULL,
    urutan INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE equipment_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    nama_item VARCHAR(120) NOT NULL,
    deskripsi_singkat VARCHAR(255) DEFAULT NULL,
    berat_kg DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    prioritas ENUM('essential','comfort','optional') NOT NULL DEFAULT 'essential',
    is_default TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipment_category FOREIGN KEY (category_id) REFERENCES equipment_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_checklists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    mountain_id BIGINT UNSIGNED DEFAULT NULL,
    trail_id BIGINT UNSIGNED DEFAULT NULL,
    nama_checklist VARCHAR(120) NOT NULL,
    total_weight_kg DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    total_items INT UNSIGNED NOT NULL DEFAULT 0,
    packed_items INT UNSIGNED NOT NULL DEFAULT 0,
    status_checklist ENUM('draft','aktif','selesai') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_checklist_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_checklist_mountain FOREIGN KEY (mountain_id) REFERENCES mountains(id) ON DELETE SET NULL,
    CONSTRAINT fk_checklist_trail FOREIGN KEY (trail_id) REFERENCES trails(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE user_checklist_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    checklist_id BIGINT UNSIGNED NOT NULL,
    equipment_item_id BIGINT UNSIGNED DEFAULT NULL,
    nama_item_custom VARCHAR(120) DEFAULT NULL,
    deskripsi_item VARCHAR(255) DEFAULT NULL,
    berat_kg DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    prioritas ENUM('essential','comfort','optional') NOT NULL DEFAULT 'essential',
    is_packed TINYINT(1) NOT NULL DEFAULT 0,
    urutan INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_checklist_item_checklist FOREIGN KEY (checklist_id) REFERENCES user_checklists(id) ON DELETE CASCADE,
    CONSTRAINT fk_checklist_item_master FOREIGN KEY (equipment_item_id) REFERENCES equipment_items(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================
-- COMMUNITY
-- =========================
CREATE TABLE post_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE community_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED DEFAULT NULL,
    mountain_id BIGINT UNSIGNED DEFAULT NULL,
    judul VARCHAR(180) DEFAULT NULL,
    konten TEXT NOT NULL,
    lokasi_text VARCHAR(150) DEFAULT NULL,
    gambar_post VARCHAR(255) DEFAULT NULL,
    status_post ENUM('published','hidden','deleted') NOT NULL DEFAULT 'published',
    jumlah_like INT UNSIGNED NOT NULL DEFAULT 0,
    jumlah_komentar INT UNSIGNED NOT NULL DEFAULT 0,
    jumlah_share INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_post_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_post_category FOREIGN KEY (category_id) REFERENCES post_categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_post_mountain FOREIGN KEY (mountain_id) REFERENCES mountains(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE post_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_comment_id BIGINT UNSIGNED DEFAULT NULL,
    komentar TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_comment_post FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_comment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_comment_parent FOREIGN KEY (parent_comment_id) REFERENCES post_comments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE post_likes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_like_post FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_like_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_post_like (post_id, user_id)
) ENGINE=InnoDB;

CREATE TABLE user_follows (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    follower_id BIGINT UNSIGNED NOT NULL,
    following_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_follow_follower FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_follow_following FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_follow (follower_id, following_id)
) ENGINE=InnoDB;

-- =========================
-- ACHIEVEMENTS / HIKE LOGS
-- =========================
CREATE TABLE achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_badge VARCHAR(120) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    deskripsi VARCHAR(255) DEFAULT NULL,
    icon_name VARCHAR(50) DEFAULT NULL,
    warna_badge VARCHAR(30) DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE user_achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    achievement_id BIGINT UNSIGNED NOT NULL,
    earned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_achievement_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_achievement_achievement FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_achievement (user_id, achievement_id)
) ENGINE=InnoDB;

CREATE TABLE hike_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    mountain_id BIGINT UNSIGNED NOT NULL,
    trail_id BIGINT UNSIGNED DEFAULT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE DEFAULT NULL,
    jarak_km DECIMAL(6,2) DEFAULT 0.00,
    durasi_jam DECIMAL(6,2) DEFAULT 0.00,
    elevation_gain_m INT UNSIGNED DEFAULT 0,
    status_hike ENUM('planned','ongoing','completed','cancelled') NOT NULL DEFAULT 'planned',
    catatan TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hikelog_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_hikelog_mountain FOREIGN KEY (mountain_id) REFERENCES mountains(id) ON DELETE CASCADE,
    CONSTRAINT fk_hikelog_trail FOREIGN KEY (trail_id) REFERENCES trails(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================
-- SEED DATA
-- =========================
INSERT INTO roles (id, nama_role) VALUES
(1, 'admin'),
(2, 'pendaki');

INSERT INTO users (id, role_id, nama_lengkap, username, email, password_hash, foto_profil, bio, level_pendaki, status_akun) VALUES
(1, 2, 'Raditya Pratama', 'raditya_explorer', 'radit@gunungku.id', '$2y$10$demo.hash.ganti.sendiri', 'uploads/profile/raditya.jpg', 'Life is better at 3,000 meters. Always seeking the next ridge and the perfect sunrise above the clouds.', 'Pro Guide', 'aktif'),
(2, 2, 'Raka Rimba', 'raka_rimba', 'raka@gunungku.id', '$2y$10$demo.hash.ganti.sendiri', 'uploads/profile/raka.jpg', 'Pendaki penikmat sunrise dan jalur klasik.', 'Explorer', 'aktif'),
(3, 2, 'Bagas Trail', 'bagas_trail', 'bagas@gunungku.id', '$2y$10$demo.hash.ganti.sendiri', 'uploads/profile/bagas.jpg', 'Suka eksplor jalur, gear, dan review basecamp.', 'Explorer', 'aktif'),
(4, 1, 'Admin Gunungku', 'admin', 'admin@gunungku.id', '$2y$10$demo.hash.ganti.sendiri', NULL, 'Administrator sistem Gunungku.', 'Admin', 'aktif');

INSERT INTO user_stats (user_id, total_summit, total_km, total_elevation_gain, total_jam_trail, avg_pace) VALUES
(1, 24, 1200.00, 42850, 312, 4.20),
(2, 5, 110.00, 3650, 29, 3.80),
(3, 5, 96.00, 2900, 22, 4.00);

INSERT INTO mountains (id, nama_gunung, slug, lokasi, provinsi, ketinggian_mdpl, tingkat_kesulitan, durasi_hari, deskripsi, status_gunung, cuaca_ringkas, suhu_min, suhu_max, koordinat_lat, koordinat_lng, gambar_utama) VALUES
(1, 'Gunung Merbabu', 'gunung-merbabu', 'Boyolali / Magelang / Semarang', 'Jawa Tengah', 3142, 'sedang', 2, 'Gunung favorit pendaki dengan panorama sabana dan banyak pilihan jalur.', 'buka', 'Cerah Berawan', 8.0, 22.0, -7.4540000, 110.4400000, 'uploads/mountains/merbabu.jpg'),
(2, 'Gunung Gede Pangrango', 'gunung-gede-pangrango', 'Cianjur / Sukabumi / Bogor', 'Jawa Barat', 2958, 'sedang', 2, 'Kawasan taman nasional populer dengan jalur resmi dan kuota simaksi.', 'buka', 'Hujan Ringan', 12.0, 22.0, -6.7860000, 106.9820000, 'uploads/mountains/gede.jpg'),
(3, 'Gunung Semeru', 'gunung-semeru', 'Lumajang / Malang', 'Jawa Timur', 3676, 'sulit', 3, 'Puncak tertinggi di Pulau Jawa dengan jalur panjang dan regulasi ketat.', 'terbatas', 'Berangin', 4.0, 18.0, -8.1080000, 112.9220000, 'uploads/mountains/semeru.jpg');

INSERT INTO trails (id, mountain_id, nama_jalur, titik_awal, jarak_km, estimasi_jam, tingkat_kesulitan, status_jalur, deskripsi, peta_offline_file) VALUES
(1, 1, 'Via Selo', 'Basecamp Selo', 10.50, 7.50, 'sedang', 'aktif', 'Jalur Merbabu paling populer dengan view sabana dan titik pos yang jelas.', 'offline_maps/merbabu_selo.gpx'),
(2, 1, 'Via Wekas', 'Basecamp Wekas', 8.20, 6.50, 'sedang', 'aktif', 'Pendakian relatif singkat namun tanjakan cukup intens.', 'offline_maps/merbabu_wekas.gpx'),
(3, 2, 'Via Cibodas', 'Cibodas', 9.80, 8.00, 'sedang', 'aktif', 'Jalur resmi menuju Gede Pangrango dengan akses air dan shelter.', 'offline_maps/gede_cibodas.gpx');

INSERT INTO trail_waypoints (trail_id, nama_waypoint, urutan, ketinggian_mdpl, suhu_c, jarak_dari_start_km, estimasi_menit_dari_start, lat, lng, keterangan) VALUES
(1, 'Basecamp Selo', 1, 1800, 20.0, 0.00, 0, -7.4800000, 110.4500000, 'Titik awal registrasi dan briefing.'),
(1, 'Pos 1', 2, 1900, 18.0, 1.80, 45, -7.4700000, 110.4470000, 'Jalur mulai menanjak.'),
(1, 'Pos 2 - Pandean', 3, 2045, 14.0, 3.40, 110, -7.4630000, 110.4440000, 'Lokasi istirahat populer sesuai tampilan peta.'),
(1, 'Sabana 1', 4, 2500, 11.0, 6.70, 210, -7.4560000, 110.4410000, 'Area terbuka dengan view luas.'),
(1, 'Puncak Merbabu', 5, 3142, 8.0, 10.50, 450, -7.4540000, 110.4400000, 'Titik summit.');

INSERT INTO mountain_weather (mountain_id, waypoint_id, waktu_pencatatan, kondisi, suhu_c, kelembapan, kecepatan_angin, catatan) VALUES
(1, 3, NOW(), 'Cerah Berawan', 14.0, 72, 12.5, 'Update terkini sekitar Pos 2'),
(1, 5, NOW(), 'Berangin', 8.0, 80, 22.0, 'Suhu summit lebih dingin');

INSERT INTO equipment_categories (id, nama_kategori, icon_name, urutan) VALUES
(1, 'Shelter & Sleep', 'bedtime', 1),
(2, 'Logistik & Konsumsi', 'restaurant', 2),
(3, 'Navigasi & Keamanan', 'map', 3),
(4, 'Pakaian', 'checkroom', 4);

INSERT INTO equipment_items (category_id, nama_item, deskripsi_singkat, berat_kg, prioritas, is_default) VALUES
(1, 'Tenda Ultralight 2P', 'Naturehike Cloud Up 2', 1.70, 'essential', 1),
(1, 'Sleeping Bag Down', 'Limit -5°C Thermal Tech', 0.90, 'comfort', 1),
(1, 'Matras Tiup', 'Insulated R-Value 3.2', 0.50, 'optional', 1),
(2, 'Kompor Lapangan & Gas', 'Windproof burner + 230g canister', 0.60, 'essential', 1),
(2, 'Botol Air / Hydration', 'Minimal 2 liter', 2.00, 'essential', 1),
(2, 'Makanan Instan', 'Kalori tinggi untuk 2 hari', 1.20, 'essential', 1),
(3, 'Headlamp', 'Lampu kepala + baterai cadangan', 0.20, 'essential', 1),
(3, 'P3K', 'First aid kit dasar', 0.35, 'essential', 1),
(4, 'Jaket Gunung', 'Windproof & water resistant', 0.70, 'essential', 1);

INSERT INTO user_checklists (id, user_id, mountain_id, trail_id, nama_checklist, total_weight_kg, total_items, packed_items, status_checklist) VALUES
(1, 1, 1, 1, 'Checklist Pendakian Merbabu Via Selo', 12.40, 24, 18, 'aktif');

INSERT INTO user_checklist_items (checklist_id, equipment_item_id, nama_item_custom, deskripsi_item, berat_kg, prioritas, is_packed, urutan) VALUES
(1, 1, NULL, 'Naturehike Cloud Up 2', 1.70, 'essential', 1, 1),
(1, 2, NULL, 'Limit -5°C Thermal Tech', 0.90, 'comfort', 1, 2),
(1, 3, NULL, 'Insulated R-Value 3.2', 0.50, 'optional', 0, 3),
(1, 4, NULL, 'Windproof burner + 230g canister', 0.60, 'essential', 1, 4),
(1, 8, NULL, 'First aid kit dasar', 0.35, 'essential', 1, 5);

INSERT INTO post_categories (id, nama_kategori, slug) VALUES
(1, 'Semua Postingan', 'semua-postingan'),
(2, 'Cerita Puncak', 'cerita-puncak'),
(3, 'Tips & Gear', 'tips-gear'),
(4, 'Info Jalur', 'info-jalur'),
(5, 'Diskusi Terbuka', 'diskusi-terbuka');

INSERT INTO community_posts (id, user_id, category_id, mountain_id, judul, konten, lokasi_text, gambar_post, status_post, jumlah_like, jumlah_komentar, jumlah_share) VALUES
(1, 2, 2, 1, 'Sunrise di Kenteng Songo', 'Akhirnya sampai di puncak Kenteng Songo tepat saat matahari terbit. Anginnya cukup kencang tapi pemandangan Merapi di seberang sana tak ternilai harganya. Siapa yang ada rencana ke sini akhir bulan?', 'Gunung Merbabu', 'uploads/posts/post_merbabu_sunrise.jpg', 'published', 1200, 84, 12),
(2, 3, 5, NULL, 'Diskusi carrier ideal untuk 2D1N', 'Menurut kalian carrier 45L cukup untuk pendakian 2 hari 1 malam kalau bawa tenda sharing?', NULL, NULL, 'published', 256, 31, 4);

INSERT INTO post_comments (post_id, user_id, parent_comment_id, komentar) VALUES
(1, 1, NULL, 'View dari Kenteng Songo memang luar biasa. Pastikan cek angin sebelum summit attack.'),
(2, 1, NULL, 'Kalau gear ringkas dan tenda sharing, 45L biasanya masih aman.');

INSERT INTO post_likes (post_id, user_id) VALUES
(1, 1),
(1, 3),
(2, 1);

INSERT INTO user_follows (follower_id, following_id) VALUES
(1, 2),
(1, 3),
(2, 1);

INSERT INTO achievements (id, nama_badge, slug, deskripsi, icon_name, warna_badge) VALUES
(1, 'Seven Summiter', 'seven-summiter', 'Badge untuk pendaki dengan pencapaian summit tinggi.', 'military_tech', 'gold'),
(2, 'Pro Guide', 'pro-guide', 'Badge untuk pengguna dengan pengalaman mendaki lanjutan.', 'verified', 'green'),
(3, 'Sunrise Hunter', 'sunrise-hunter', 'Badge untuk log pendakian yang sering summit pagi.', 'wb_sunny', 'orange');

INSERT INTO user_achievements (user_id, achievement_id, earned_at) VALUES
(1, 1, NOW()),
(1, 2, NOW()),
(2, 3, NOW());

INSERT INTO hike_logs (user_id, mountain_id, trail_id, tanggal_mulai, tanggal_selesai, jarak_km, durasi_jam, elevation_gain_m, status_hike, catatan) VALUES
(1, 1, 1, '2026-04-18', '2026-04-19', 21.00, 24.00, 1342, 'completed', 'Pendakian lancar via Selo.'),
(1, 2, 3, '2026-03-12', '2026-03-13', 18.50, 20.00, 1100, 'completed', 'Cuaca cukup lembap.'),
(1, 3, NULL, '2026-05-10', NULL, 0.00, 0.00, 0, 'planned', 'Rencana pendakian berikutnya.');

INSERT INTO simaksi_applications (kode_simaksi, user_id, mountain_id, trail_id, nama_pengaju, nik, tanggal_naik, tanggal_turun, jumlah_peserta, status_pengajuan, setuju_peraturan, catatan_admin) VALUES
('SIM-202604-0001', 1, 2, 3, 'Raditya Pratama', '3276012345678901', '2026-05-03', '2026-05-04', 2, 'diajukan', 1, NULL),
('SIM-202604-0002', 1, 1, 1, 'Raditya Pratama', '3276012345678901', '2026-05-17', '2026-05-18', 1, 'draft', 1, NULL);

INSERT INTO simaksi_documents (simaksi_id, jenis_dokumen, nama_file, path_file, ukuran_kb, mime_type) VALUES
(1, 'ktp', 'ktp_raditya.pdf', 'uploads/simaksi/ktp_raditya.pdf', 512, 'application/pdf');

COMMIT;
