-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 04:37 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hki`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_hki`
--

CREATE TABLE `dokumen_hki` (
  `id` int(11) NOT NULL,
  `pengajuan_hki_id` int(11) DEFAULT NULL,
  `jenis_dokumen` enum('proposal','surat_pernyataan','bukti_pembayaran','dokumen_pendukung') NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `status_verifikasi` enum('belum','sudah','ditolak') DEFAULT 'belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nidn` varchar(20) NOT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `program_studi` varchar(100) DEFAULT NULL,
  `fakultas` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nim` varchar(20) NOT NULL,
  `program_studi` varchar(100) DEFAULT NULL,
  `fakultas` varchar(100) DEFAULT NULL,
  `angkatan` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pengajuan_hki_id` int(11) DEFAULT NULL,
  `judul` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `status_baca` enum('belum','sudah') DEFAULT 'belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_hki`
--

CREATE TABLE `pengajuan_hki` (
  `id` int(11) NOT NULL,
  `judul_karya` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('draft','diajukan','divalidasi','disetujui','ditolak') DEFAULT 'draft',
  `tanggal_pengajuan` date DEFAULT NULL,
  `nama_pengusul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip_nidn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_sinta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_pencipta` enum('1 orang','2 orang','3 orang','4 orang','5 orang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pencipta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_pencipta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp_pencipta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_pencipta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan_pencipta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kodepos_pencipta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lanjut_pengisian` enum('identitas ciptaan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identitas_ciptaan` enum('karya tulis','karya audio visual','karya lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_jenis_ciptaan` enum('Buku','E-Book','Diktat','Modul','Buku Panduan/Petunjuk','Karya Ilmiah','Karya Tulis/Artikel','Laporan Penelitian','Jurnal') DEFAULT NULL,
  `judul_ciptaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uaraian_singkat_ciptaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pertama_kali_diumumkan` date DEFAULT NULL,
  `kategori` enum('Contoh CIptaan','Surat pengalihan Hak Cipta (Format PDF)','Surat Pernyataan Hak Cipta (Format PDF)','KTP (Seluruh Pencipta)') DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaju_hki`
--

CREATE TABLE `pengaju_hki` (
  `id` int(11) NOT NULL,
  `pengajuan_hki_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `peran` enum('ketua','anggota') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persetujuan_direktur`
--

CREATE TABLE `persetujuan_direktur` (
  `id` int(11) NOT NULL,
  `pengajuan_hki_id` int(11) DEFAULT NULL,
  `direktur_id` int(11) DEFAULT NULL,
  `status_persetujuan` enum('belum','disetujui','ditolak') DEFAULT 'belum',
  `catatan` text DEFAULT NULL,
  `tanggal_persetujuan` datetime DEFAULT NULL,
  `tanda_tangan_digital` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_status`
--

CREATE TABLE `riwayat_status` (
  `id` int(11) NOT NULL,
  `pengajuan_hki_id` int(11) DEFAULT NULL,
  `status_sebelumnya` enum('draft','diajukan','divalidasi','disetujui','ditolak') DEFAULT NULL,
  `status_baru` enum('draft','diajukan','divalidasi','disetujui','ditolak') DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('dosen','mahasiswa','admin_p3m','direktur') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `validasi_p3m`
--

CREATE TABLE `validasi_p3m` (
  `id` int(11) NOT NULL,
  `pengajuan_hki_id` int(11) DEFAULT NULL,
  `admin_p3m_id` int(11) DEFAULT NULL,
  `status_validasi` enum('belum','sudah','ditolak') DEFAULT 'belum',
  `catatan` text DEFAULT NULL,
  `tanggal_validasi` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokumen_hki`
--
ALTER TABLE `dokumen_hki`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_hki_id` (`pengajuan_hki_id`),
  ADD KEY `idx_dokumen_hki_status` (`status_verifikasi`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nidn` (`nidn`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pengajuan_hki_id` (`pengajuan_hki_id`),
  ADD KEY `idx_notifikasi_status` (`status_baca`);

--
-- Indexes for table `pengajuan_hki`
--
ALTER TABLE `pengajuan_hki`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pengajuan_hki_status` (`status`);

--
-- Indexes for table `pengaju_hki`
--
ALTER TABLE `pengaju_hki`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_hki_id` (`pengajuan_hki_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `persetujuan_direktur`
--
ALTER TABLE `persetujuan_direktur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengajuan_hki_id` (`pengajuan_hki_id`),
  ADD KEY `direktur_id` (`direktur_id`);

--
-- Indexes for table `riwayat_status`
--
ALTER TABLE `riwayat_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_hki_id` (`pengajuan_hki_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- Indexes for table `validasi_p3m`
--
ALTER TABLE `validasi_p3m`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengajuan_hki_id` (`pengajuan_hki_id`),
  ADD KEY `admin_p3m_id` (`admin_p3m_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokumen_hki`
--
ALTER TABLE `dokumen_hki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_hki`
--
ALTER TABLE `pengajuan_hki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaju_hki`
--
ALTER TABLE `pengaju_hki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persetujuan_direktur`
--
ALTER TABLE `persetujuan_direktur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_status`
--
ALTER TABLE `riwayat_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `validasi_p3m`
--
ALTER TABLE `validasi_p3m`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dokumen_hki`
--
ALTER TABLE `dokumen_hki`
  ADD CONSTRAINT `dokumen_hki_ibfk_1` FOREIGN KEY (`pengajuan_hki_id`) REFERENCES `pengajuan_hki` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifikasi_ibfk_2` FOREIGN KEY (`pengajuan_hki_id`) REFERENCES `pengajuan_hki` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengaju_hki`
--
ALTER TABLE `pengaju_hki`
  ADD CONSTRAINT `pengaju_hki_ibfk_1` FOREIGN KEY (`pengajuan_hki_id`) REFERENCES `pengajuan_hki` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengaju_hki_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `persetujuan_direktur`
--
ALTER TABLE `persetujuan_direktur`
  ADD CONSTRAINT `persetujuan_direktur_ibfk_1` FOREIGN KEY (`pengajuan_hki_id`) REFERENCES `pengajuan_hki` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `persetujuan_direktur_ibfk_2` FOREIGN KEY (`direktur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `riwayat_status`
--
ALTER TABLE `riwayat_status`
  ADD CONSTRAINT `riwayat_status_ibfk_1` FOREIGN KEY (`pengajuan_hki_id`) REFERENCES `pengajuan_hki` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_status_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `validasi_p3m`
--
ALTER TABLE `validasi_p3m`
  ADD CONSTRAINT `validasi_p3m_ibfk_1` FOREIGN KEY (`pengajuan_hki_id`) REFERENCES `pengajuan_hki` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `validasi_p3m_ibfk_2` FOREIGN KEY (`admin_p3m_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
