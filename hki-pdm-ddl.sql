-- =====================================================
-- PHYSICAL DATA MODEL (PDM) - SISTEM PENGAJUAN HKI
-- DDL Script untuk MySQL/MariaDB
-- =====================================================

-- Database Creation
CREATE DATABASE IF NOT EXISTS hki_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hki_system;

-- =====================================================
-- Table: users (Data Pengguna)
-- =====================================================
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('mahasiswa', 'dosen', 'direktur', 'admin') NOT NULL,
    nip_nim VARCHAR(50),
    no_hp VARCHAR(20),
    ttd_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_users_role (role),
    INDEX idx_users_email (email),
    INDEX idx_users_username (username)
);

-- =====================================================
-- Table: mahasiswas (Data Mahasiswa)
-- =====================================================
CREATE TABLE mahasiswas (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    program_studi VARCHAR(255) NOT NULL,
    fakultas VARCHAR(255) NOT NULL,
    semester INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_mahasiswas_nim (nim),
    INDEX idx_mahasiswas_user_id (user_id)
);

-- =====================================================
-- Table: dosens (Data Dosen)
-- =====================================================
CREATE TABLE dosens (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    nip VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    fakultas VARCHAR(255) NOT NULL,
    jabatan VARCHAR(100),
    bidang_keahlian VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_dosens_nip (nip),
    INDEX idx_dosens_user_id (user_id)
);

-- =====================================================
-- Table: pengajuan_hkis (Data Pengajuan HKI)
-- =====================================================
CREATE TABLE pengajuan_hkis (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    nomor_pengajuan VARCHAR(255) UNIQUE,
    judul_ciptaan VARCHAR(255) NOT NULL,
    deskripsi_ciptaan TEXT,
    jenis_ciptaan VARCHAR(255),
    tanggal_ciptaan DATE,
    jumlah_pencipta INT DEFAULT 1,
    nama_pencipta JSON,
    nama_pengusul VARCHAR(255),
    file_karya VARCHAR(255),
    file_dokumen_pendukung JSON,
    status ENUM('draft', 'menunggu_validasi', 'divalidasi_sedang_diproses', 'menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'disetujui', 'selesai', 'ditolak') DEFAULT 'draft',
    catatan TEXT,
    tahun_usulan YEAR,
    billing_code VARCHAR(255),
    bukti_pembayaran VARCHAR(255),
    sertifikat VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_pengajuan_status (status),
    INDEX idx_pengajuan_user_id (user_id),
    INDEX idx_pengajuan_nomor (nomor_pengajuan),
    INDEX idx_pengajuan_tahun (tahun_usulan)
);

-- =====================================================
-- Table: dokumen_hkis (Dokumen Pengajuan)
-- =====================================================
CREATE TABLE dokumen_hkis (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_hki_id BIGINT NOT NULL,
    jenis_dokumen VARCHAR(255) NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    path_file VARCHAR(255) NOT NULL,
    ukuran_file BIGINT DEFAULT 0,
    is_uploaded BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pengajuan_hki_id) REFERENCES pengajuan_hkis(id) ON DELETE CASCADE,
    INDEX idx_dokumen_pengajuan_id (pengajuan_hki_id),
    INDEX idx_dokumen_jenis (jenis_dokumen)
);

-- =====================================================
-- Table: validasi_p3ms (Proses Validasi)
-- =====================================================
CREATE TABLE validasi_p3ms (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_hki_id BIGINT NOT NULL,
    validator_id BIGINT,
    tanggal_validasi DATETIME,
    status_validasi ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    catatan_validasi TEXT,
    dokumen_ttd VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pengajuan_hki_id) REFERENCES pengajuan_hkis(id) ON DELETE CASCADE,
    FOREIGN KEY (validator_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_validasi_pengajuan_id (pengajuan_hki_id),
    INDEX idx_validasi_status (status_validasi),
    INDEX idx_validasi_validator (validator_id)
);

-- =====================================================
-- Table: persetujuan_direkturs (Proses Persetujuan)
-- =====================================================
CREATE TABLE persetujuan_direkturs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_hki_id BIGINT NOT NULL,
    direktur_id BIGINT,
    tanggal_persetujuan DATETIME,
    status_persetujuan ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    catatan_persetujuan TEXT,
    dokumen_ttd VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pengajuan_hki_id) REFERENCES pengajuan_hkis(id) ON DELETE CASCADE,
    FOREIGN KEY (direktur_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_persetujuan_pengajuan_id (pengajuan_hki_id),
    INDEX idx_persetujuan_status (status_persetujuan),
    INDEX idx_persetujuan_direktur (direktur_id)
);

-- =====================================================
-- Table: pembayarans (Data Pembayaran)
-- =====================================================
CREATE TABLE pembayarans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_hki_id BIGINT NOT NULL,
    billing_code VARCHAR(255) NOT NULL,
    nominal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    metode_pembayaran ENUM('transfer', 'tunai', 'e_wallet') DEFAULT 'transfer',
    tanggal_bayar DATETIME,
    bukti_pembayaran VARCHAR(255),
    status_pembayaran ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    verifikator_id BIGINT,
    tanggal_verifikasi DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pengajuan_hki_id) REFERENCES pengajuan_hkis(id) ON DELETE CASCADE,
    FOREIGN KEY (verifikator_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_pembayaran_pengajuan_id (pengajuan_hki_id),
    INDEX idx_pembayaran_status (status_pembayaran),
    INDEX idx_pembayaran_billing (billing_code)
);

-- =====================================================
-- Table: sertifikats (Sertifikat HKI)
-- =====================================================
CREATE TABLE sertifikats (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_hki_id BIGINT NOT NULL,
    nomor_sertifikat VARCHAR(255) NOT NULL UNIQUE,
    tanggal_terbit DATE NOT NULL,
    tanggal_berlaku DATE NOT NULL,
    tanggal_berakhir DATE NOT NULL,
    file_sertifikat VARCHAR(255),
    qr_code VARCHAR(255),
    penerbit_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pengajuan_hki_id) REFERENCES pengajuan_hkis(id) ON DELETE CASCADE,
    FOREIGN KEY (penerbit_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sertifikat_pengajuan_id (pengajuan_hki_id),
    INDEX idx_sertifikat_nomor (nomor_sertifikat),
    INDEX idx_sertifikat_terbit (tanggal_terbit)
);

-- =====================================================
-- Table: riwayat_statuses (Audit Trail)
-- =====================================================
CREATE TABLE riwayat_statuses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_hki_id BIGINT NOT NULL,
    user_id BIGINT,
    status_lama VARCHAR(255),
    status_baru VARCHAR(255) NOT NULL,
    catatan TEXT,
    tanggal_perubahan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pengajuan_hki_id) REFERENCES pengajuan_hkis(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_riwayat_pengajuan_id (pengajuan_hki_id),
    INDEX idx_riwayat_tanggal (tanggal_perubahan),
    INDEX idx_riwayat_user (user_id)
);

-- =====================================================
-- TRIGGERS untuk Audit Trail
-- =====================================================

DELIMITER $$

-- Trigger untuk mencatat perubahan status pengajuan
CREATE TRIGGER tr_pengajuan_status_change 
    AFTER UPDATE ON pengajuan_hkis
    FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO riwayat_statuses (
            pengajuan_hki_id, 
            status_lama, 
            status_baru, 
            catatan,
            tanggal_perubahan
        ) VALUES (
            NEW.id, 
            OLD.status, 
            NEW.status, 
            CONCAT('Status changed from ', OLD.status, ' to ', NEW.status),
            NOW()
        );
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- VIEWS untuk Reporting
-- =====================================================

-- View untuk Dashboard Admin
CREATE VIEW v_dashboard_admin AS
SELECT 
    COUNT(*) as total_pengajuan,
    COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft,
    COUNT(CASE WHEN status = 'menunggu_validasi' THEN 1 END) as menunggu_validasi,
    COUNT(CASE WHEN status = 'divalidasi_sedang_diproses' THEN 1 END) as sedang_diproses,
    COUNT(CASE WHEN status = 'menunggu_pembayaran' THEN 1 END) as menunggu_pembayaran,
    COUNT(CASE WHEN status = 'menunggu_verifikasi_pembayaran' THEN 1 END) as menunggu_verifikasi,
    COUNT(CASE WHEN status = 'disetujui' THEN 1 END) as disetujui,
    COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai,
    COUNT(CASE WHEN status = 'ditolak' THEN 1 END) as ditolak
FROM pengajuan_hkis;

-- View untuk detail pengajuan lengkap
CREATE VIEW v_pengajuan_lengkap AS
SELECT 
    p.id,
    p.nomor_pengajuan,
    p.judul_ciptaan,
    p.status,
    u.nama_lengkap as nama_pengaju,
    u.role as role_pengaju,
    p.created_at as tanggal_pengajuan,
    v.status_validasi,
    v.tanggal_validasi,
    pr.status_persetujuan,
    pr.tanggal_persetujuan,
    pb.status_pembayaran,
    pb.tanggal_bayar,
    s.nomor_sertifikat,
    s.tanggal_terbit
FROM pengajuan_hkis p
LEFT JOIN users u ON p.user_id = u.id
LEFT JOIN validasi_p3ms v ON p.id = v.pengajuan_hki_id
LEFT JOIN persetujuan_direkturs pr ON p.id = pr.pengajuan_hki_id
LEFT JOIN pembayarans pb ON p.id = pb.pengajuan_hki_id
LEFT JOIN sertifikats s ON p.id = s.pengajuan_hki_id;

-- =====================================================
-- SAMPLE DATA (Optional)
-- =====================================================

-- Insert sample users
INSERT INTO users (username, nama_lengkap, email, password, role, nip_nim, no_hp) VALUES
('admin', 'Administrator System', 'admin@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ADM001', '081234567890'),
('direktur', 'Prof. Dr. John Doe', 'direktur@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'direktur', 'DIR001', '081234567891'),
('dosen1', 'Dr. Jane Smith', 'jane.smith@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen', '198501012010121001', '081234567892'),
('mahasiswa1', 'Ahmad Wijaya', 'ahmad.wijaya@student.university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', '20210001', '081234567893');

-- Insert sample mahasiswa
INSERT INTO mahasiswas (user_id, nim, nama, program_studi, fakultas, semester) VALUES
(4, '20210001', 'Ahmad Wijaya', 'Teknik Informatika', 'Fakultas Teknik', 6);

-- Insert sample dosen
INSERT INTO dosens (user_id, nip, nama, fakultas, jabatan, bidang_keahlian) VALUES
(3, '198501012010121001', 'Dr. Jane Smith', 'Fakultas Teknik', 'Lektor Kepala', 'Artificial Intelligence');

-- =====================================================
-- End of DDL Script
-- ===================================================== 