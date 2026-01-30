-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Jan 2026 pada 05.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ppi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('head_admin','admin') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`admin_id`, `nama`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Head_Admin', 'simt.its.ac.id', '123', 'head_admin', '2026-01-29 03:58:28', '2026-01-29 04:04:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `dosen_id` int(11) NOT NULL,
  `nama_dosen` varchar(100) NOT NULL,
  `bidang_keahlian` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`dosen_id`, `nama_dosen`, `bidang_keahlian`, `created_at`, `updated_at`) VALUES
(1, 'Dr. Siti Aminah', 'Data Science', '2026-01-29 03:48:55', '2026-01-29 03:48:55'),
(2, 'Dr. Budi Santoso', 'Rekayasa Perangkat Lunak', '2026-01-29 03:48:55', '2026-01-29 03:48:55'),
(3, 'Dr. Rina Kurnia', 'Jaringan Komputer', '2026-01-29 03:48:55', '2026-01-29 03:48:55'),
(4, 'Dr. Ahmad Fauzi', 'Keamanan Informasi', '2026-01-29 03:48:55', '2026-01-29 03:48:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `pendaftar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_pendaftar` varchar(100) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `nama_dosen` varchar(100) NOT NULL,
  `pembimbing_lapangan` varchar(100) NOT NULL,
  `no_telp` varchar(20) NOT NULL,
  `file_kesediaan_pembimbing` varchar(255) DEFAULT NULL,
  `file_kesediaan_praktik` varchar(255) DEFAULT NULL,
  `file_proposal` varchar(255) DEFAULT NULL,
  `status` enum('sudah_diverifikasi','belum_diverifikasi') NOT NULL DEFAULT 'belum_diverifikasi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`pendaftar_id`, `user_id`, `nama_pendaftar`, `judul`, `nama_dosen`, `pembimbing_lapangan`, `no_telp`, `file_kesediaan_pembimbing`, `file_kesediaan_praktik`, `file_proposal`, `status_pendaftaran`, `created_at`, `updated_at`) VALUES
(1, 1, 'farhan', 'suki liar mengejar abi yg terjatuh', 'Dr. Ahmad Fauzi', 'dr. tirta', '0895365996602', 'uploads/1769659012_kb_CV DHIA MAULIDIAH .pdf', 'uploads/1769659012_kp_Daftar_Pustaka_Revisi_Fenanda.pdf', 'uploads/1769659012_Daftar_Pustaka_Revisi.pdf', 'diajukan', '2026-01-29 03:56:52', '2026-01-29 03:56:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian`
--

CREATE TABLE `penilaian` (
  `nilai_id` int(11) NOT NULL,
  `pendaftar_id` int(11) NOT NULL,
  `nilai_pembimbing1` int(11) DEFAULT NULL,
  `nilai_pembimbing2` int(11) DEFAULT NULL,
  `nilai_penguji1` int(11) DEFAULT NULL,
  `nilai_penguji2` int(11) DEFAULT NULL,
  `nilai_akhir` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penjadwalan`
--

CREATE TABLE `penjadwalan` (
  `penjadwalan_id` int(11) NOT NULL,
  `pendaftaran_id` int(11) NOT NULL,
  `penguji_1` varchar(100) DEFAULT NULL,
  `penguji_2` varchar(100) DEFAULT NULL,
  `status` enum('sudah_diverifikasi','belum_diverifikasi') NOT NULL DEFAULT 'belum_diverifikasi',
  `tanggal_sidang` date DEFAULT NULL,
  `jam_sidang` time DEFAULT NULL,
  `link_zoom` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penjadwalan`
--

INSERT INTO `penjadwalan` (`penjadwalan_id`, `pendaftaran_id`, `penguji_1`, `penguji_2`, `status_sidang`, `tanggal_sidang`, `jam_sidang`, `link_zoom`, `created_at`, `updated_at`) VALUES
(1, 1, 'dr sui', 'dr liar', 'terjadwal', '2026-01-29', '00:00:00', 'aowdmahdaodhaoisnd', '2026-01-29 04:12:05', '2026-01-29 04:42:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nrp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bidang_keahlian` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `status` enum('sudah_diverifikasi','belum_diverifikasi') NOT NULL DEFAULT 'belum_diverifikasi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`user_id`, `email`, `nama_lengkap`, `nrp`, `password`, `bidang_keahlian`, `no_hp`, `status`, `created_at`, `updated_at`) VALUES
(1, 'farhantegar75@gmail.com', 'farhan', '123456789', 'vian', 'suki liar', '0895365996602', 'belum_diverifikasi', '2026-01-29 03:43:09', '2026-01-29 03:43:09');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`dosen_id`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`pendaftar_id`),
  ADD KEY `user_id` (`user_id`);
  ADD KEY `status` (`status`);

--
-- Indeks untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`nilai_id`),
  ADD KEY `pendaftar_id` (`pendaftar_id`);

--
-- Indeks untuk tabel `penjadwalan`
--
ALTER TABLE `penjadwalan`
  ADD PRIMARY KEY (`penjadwalan_id`),
  ADD KEY `pendaftaran_id` (`pendaftaran_id`);
  ADD KEY `status` (`status`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nrp` (`nrp`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `dosen`
--
ALTER TABLE `dosen`
  MODIFY `dosen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `pendaftar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `nilai_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penjadwalan`
--
ALTER TABLE `penjadwalan`
  MODIFY `penjadwalan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `fk_pendaftaran_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `fk_penilaian_pendaftaran` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftaran` (`pendaftar_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penjadwalan`
--
ALTER TABLE `penjadwalan`
  ADD CONSTRAINT `fk_penjadwalan_pendaftaran` FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftaran` (`pendaftar_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
