-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Server version: 8.0.30
-- PHP Version: 8.1.10
--
-- Basis Data CDSS Skrining Risiko Kanker Payudara
-- Sesuai dengan spesifikasi CDSS_Skrining_Risiko_Kanker_Payudara.xlsx
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cdss_kanker_payudara`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_hitung_skrining` (IN `p_id_skrining` INT)
BEGIN
  DECLARE v_total INT DEFAULT 0;
  DECLARE v_override BOOLEAN DEFAULT FALSE;
  DECLARE v_kategori INT;

  -- Langkah 0a: Sinkronisasi otomatis F10 dari skrining.imt jika belum terisi
  UPDATE detail_skrining d
  JOIN skrining s ON s.id_skrining = d.id_skrining
  SET d.nilai_input = CAST(s.imt AS CHAR)
  WHERE d.id_skrining = p_id_skrining 
    AND d.id_faktor = 'F10' 
    AND (d.nilai_input IS NULL OR d.nilai_input = '')
    AND s.imt IS NOT NULL;

  -- Langkah 0b: Khusus F06, jika status_menopause bukan 'Sudah menopause', nilai_input dikosongkan agar tidak terpenuhi
  UPDATE detail_skrining d
  JOIN skrining s ON s.id_skrining = d.id_skrining
  SET d.nilai_input = ''
  WHERE d.id_skrining = p_id_skrining 
    AND d.id_faktor = 'F06' 
    AND (s.status_menopause IS NULL OR s.status_menopause != 'Sudah menopause');

  -- Langkah 1: Evaluasi tiap faktor (terpenuhi atau tidak)
  UPDATE detail_skrining d
  JOIN faktor_risiko f ON f.id_faktor = d.id_faktor
  SET d.terpenuhi =
    CASE
      WHEN d.nilai_input IS NULL OR d.nilai_input = '' THEN 0
      WHEN f.tipe_input = 'angka' THEN
        CASE f.operator
          WHEN '>=' THEN CAST(d.nilai_input AS DECIMAL(6,2)) >= f.ambang
          WHEN '>'  THEN CAST(d.nilai_input AS DECIMAL(6,2)) >  f.ambang
          WHEN '<'  THEN CAST(d.nilai_input AS DECIMAL(6,2)) <  f.ambang
          WHEN '<=' THEN CAST(d.nilai_input AS DECIMAL(6,2)) <= f.ambang
          WHEN '='  THEN CAST(d.nilai_input AS DECIMAL(6,2)) =  f.ambang
          ELSE 0
        END
      ELSE FIND_IN_SET(d.nilai_input, f.nilai_pemicu) > 0
    END
  WHERE d.id_skrining = p_id_skrining;

  -- Langkah 2: Skor per faktor = terpenuhi x bobot
  UPDATE detail_skrining d
  JOIN faktor_risiko f ON f.id_faktor = d.id_faktor
  SET d.skor = IF(d.terpenuhi, f.bobot, 0)
  WHERE d.id_skrining = p_id_skrining;

  -- Langkah 3: Total skor dan status override
  SELECT COALESCE(SUM(d.skor), 0),
         COALESCE(MAX(d.terpenuhi AND f.override_tinggi), 0)
    INTO v_total, v_override
  FROM detail_skrining d
  JOIN faktor_risiko f ON f.id_faktor = d.id_faktor
  WHERE d.id_skrining = p_id_skrining;

  -- Langkah 4: Kategori (override => Tinggi; selain itu berdasarkan skor minimum)
  IF v_override THEN
    SELECT id_kategori INTO v_kategori FROM kategori_risiko WHERE nama_kategori = 'Tinggi';
  ELSE
    SELECT id_kategori INTO v_kategori
    FROM kategori_risiko
    WHERE skor_min <= v_total
    ORDER BY skor_min DESC
    LIMIT 1;
  END IF;

  UPDATE skrining
  SET total_skor = v_total, override_aktif = v_override, id_kategori = v_kategori
  WHERE id_skrining = p_id_skrining;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'simpan hash (bcrypt/argon2), bukan teks asli',
  `role` enum('admin','nakes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nakes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Dr. dr. Rian Pratama, Sp.B(K)Onk', 'admin', '$2y$10$DBbajMJ28uZjeS/Uoy9uHelBOkNbGkI5iXGACQZUXWyHqV9ZEdl7G', 'admin', '2026-10-01 08:00:00'),
(2, 'Ns. Maya Indah, S.Kep', 'nakes', '$2y$10$HXiZirqdwNeHdmcWVjXTR.gVUxm9LxLIsMytgp3xiGDuWAzUBQu5a', 'nakes', '2026-10-01 08:00:00'),
(3, 'dr. Amanda, Sp.B', 'amanda', '$2y$10$WN/G9NNeJOfHzVmTi3zpR.628DbxpcLl.ZjfMVwMbBDuFgbTe0UwG', 'admin', '2026-10-01 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int NOT NULL,
  `no_rm` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'nomor rekam medis / ID pasien',
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pasien`
-- (Mengacu pada Sheet Data_Pasien & Simulasi Excel: P-001, P-002, dst)
--

INSERT INTO `pasien` (`id_pasien`, `no_rm`, `nama`, `tanggal_lahir`, `alamat`, `created_at`) VALUES
(1, 'RM-2026-001', 'Ny. Ratna Sari', '1974-05-12', 'Jl. Melati No. 45, Jakarta Selatan', '2026-09-28 03:15:00'),
(2, 'RM-2026-002', 'Ny. Dewi Lestari', '1968-08-20', 'Jl. Dahlia No. 12, Bandung', '2026-09-29 07:30:00'),
(3, 'RM-2026-003', 'Nn. Anisa Putri', '1998-03-15', 'Jl. Anggrek No. 8, Surabaya', '2026-09-30 02:00:00'),
(4, 'RM-2026-004', 'Ny. Sri Wahyuni', '1981-11-04', 'Jl. Cempaka No. 22, Semarang', '2026-10-01 04:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `faktor_risiko`
-- (15 Parameter Faktor Risiko sesuai Sheet Parameter & Aturan Excel)
--

CREATE TABLE `faktor_risiko` (
  `id_faktor` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_faktor` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_input` enum('angka','pilihan') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'angka: bandingkan dengan ambang; pilihan: cocokkan dengan nilai_pemicu',
  `operator` enum('>=','>','<','<=','=') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'khusus tipe angka',
  `ambang` decimal(5,1) DEFAULT NULL COMMENT 'khusus tipe angka',
  `nilai_pemicu` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'khusus tipe pilihan; pisahkan dengan koma',
  `bobot` tinyint UNSIGNED NOT NULL,
  `override_tinggi` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'TRUE: bila terpenuhi, kategori langsung Tinggi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faktor_risiko`
--

INSERT INTO `faktor_risiko` (`id_faktor`, `nama_faktor`, `tipe_input`, `operator`, `ambang`, `nilai_pemicu`, `bobot`, `override_tinggi`) VALUES
('F01', 'Usia', 'angka', '>=', '50.0', NULL, 2, 0),
('F02', 'Riwayat kanker payudara pada keluarga derajat pertama', 'pilihan', NULL, NULL, 'Ibu,Saudari kandung,Anak', 3, 0),
('F03', 'Riwayat kanker payudara pribadi sebelumnya', 'pilihan', NULL, NULL, 'Ya', 4, 1),
('F04', 'Mutasi gen BRCA1/BRCA2 diketahui', 'pilihan', NULL, NULL, 'Ya', 5, 1),
('F05', 'Menarche dini', 'angka', '<', '12.0', NULL, 1, 0),
('F06', 'Menopause terlambat', 'angka', '>', '55.0', NULL, 1, 0),
('F07', 'Nulipara / melahirkan anak pertama usia > 30 tahun', 'pilihan', NULL, NULL, 'Ya', 1, 0),
('F08', 'Tidak pernah menyusui', 'pilihan', NULL, NULL, 'Ya', 1, 0),
('F09', 'Kontrasepsi hormonal / terapi sulih hormon jangka panjang', 'pilihan', NULL, NULL, 'Ya', 1, 0),
('F10', 'Obesitas (IMT)', 'angka', '>=', '30.0', NULL, 1, 0),
('F11', 'Konsumsi alkohol', 'pilihan', NULL, NULL, 'Rutin', 1, 0),
('F12', 'Aktivitas fisik kurang', 'pilihan', NULL, NULL, 'Kurang', 1, 0),
('F13', 'Riwayat biopsi payudara dengan hiperplasia atipikal', 'pilihan', NULL, NULL, 'Ya', 3, 0),
('F14', 'Riwayat radioterapi di area dada', 'pilihan', NULL, NULL, 'Ya', 3, 1),
('F15', 'Jaringan payudara padat (BI-RADS C atau D)', 'pilihan', NULL, NULL, 'C,D', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_risiko`
-- (3 Kategori Risiko sesuai Sheet Parameter Excel)
--

CREATE TABLE `kategori_risiko` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skor_min` int NOT NULL COMMENT 'skor minimum kategori ini',
  `rekomendasi` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_risiko`
--

INSERT INTO `kategori_risiko` (`id_kategori`, `nama_kategori`, `skor_min`, `rekomendasi`) VALUES
(1, 'Rendah', 0, 'Edukasi dan SADARI rutin setiap bulan; pemeriksaan klinis payudara (SADANIS) berkala; ulangi skrining sesuai jadwal.'),
(2, 'Sedang', 4, 'SADARI + SADANIS oleh tenaga kesehatan secara berkala; pertimbangkan USG/mamografi sesuai usia dan saran dokter.'),
(3, 'Tinggi', 8, 'Rujuk ke dokter spesialis untuk evaluasi lanjut (mis. mamografi/USG, konseling genetik bila diperlukan).');

-- --------------------------------------------------------

--
-- Table structure for table `skrining`
--

CREATE TABLE `skrining` (
  `id_skrining` int NOT NULL,
  `id_pasien` int NOT NULL,
  `id_user` int NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_menopause` enum('Belum menopause','Sudah menopause') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_badan` decimal(5,1) DEFAULT NULL COMMENT 'kg',
  `tinggi_badan` decimal(5,1) DEFAULT NULL COMMENT 'cm',
  `imt` decimal(4,1) GENERATED ALWAYS AS (if((`tinggi_badan` > 0),round((`berat_badan` / pow((`tinggi_badan` / 100),2)),1),NULL)) STORED,
  `total_skor` int DEFAULT NULL,
  `override_aktif` tinyint(1) DEFAULT NULL,
  `id_kategori` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skrining`
--

INSERT INTO `skrining` (`id_skrining`, `id_pasien`, `id_user`, `tanggal`, `status_menopause`, `berat_badan`, `tinggi_badan`, `total_skor`, `override_aktif`, `id_kategori`) VALUES
(1, 1, 2, '2026-09-28 10:15:00', 'Belum menopause', '68.0', '155.0', 7, 0, 2),
(2, 2, 2, '2026-09-29 14:30:00', 'Sudah menopause', '78.0', '152.0', 15, 1, 3),
(3, 3, 2, '2026-09-30 09:00:00', 'Belum menopause', '52.0', '160.0', 0, 0, 1),
(4, 4, 1, '2026-10-01 11:20:00', 'Belum menopause', '64.0', '158.0', 3, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `detail_skrining`
--

CREATE TABLE `detail_skrining` (
  `id_detail` int NOT NULL,
  `id_skrining` int NOT NULL,
  `id_faktor` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_input` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terpenuhi` tinyint(1) NOT NULL DEFAULT '0',
  `skor` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_skrining`
-- (15 Faktor x 4 Data Skrining = 60 Evaluasi Lengkap Sesuai Sheet Data_Pasien)
--

INSERT INTO `detail_skrining` (`id_detail`, `id_skrining`, `id_faktor`, `nilai_input`, `terpenuhi`, `skor`) VALUES
(1, 1, 'F01', '52', 1, 2),
(2, 1, 'F02', 'Ibu', 1, 3),
(3, 1, 'F03', 'Tidak', 0, 0),
(4, 1, 'F04', 'Tidak', 0, 0),
(5, 1, 'F05', '11', 1, 1),
(6, 1, 'F06', '', 0, 0),
(7, 1, 'F07', 'Tidak', 0, 0),
(8, 1, 'F08', 'Tidak', 0, 0),
(9, 1, 'F09', 'Tidak', 0, 0),
(10, 1, 'F10', '28.3', 0, 0),
(11, 1, 'F11', 'Tidak pernah', 0, 0),
(12, 1, 'F12', 'Kurang', 1, 1),
(13, 1, 'F13', 'Tidak', 0, 0),
(14, 1, 'F14', 'Tidak', 0, 0),
(15, 1, 'F15', 'B', 0, 0),
(16, 2, 'F01', '58', 1, 2),
(17, 2, 'F02', 'Saudari kandung', 1, 3),
(18, 2, 'F03', 'Tidak', 0, 0),
(19, 2, 'F04', 'Ya', 1, 5),
(20, 2, 'F05', '12', 0, 0),
(21, 2, 'F06', '56', 1, 1),
(22, 2, 'F07', 'Tidak', 0, 0),
(23, 2, 'F08', 'Ya', 1, 1),
(24, 2, 'F09', 'Tidak', 0, 0),
(25, 2, 'F10', '33.8', 1, 1),
(26, 2, 'F11', 'Kadang-kadang', 0, 0),
(27, 2, 'F12', 'Cukup', 0, 0),
(28, 2, 'F13', 'Tidak', 0, 0),
(29, 2, 'F14', 'Tidak', 0, 0),
(30, 2, 'F15', 'D', 1, 2),
(31, 3, 'F01', '28', 0, 0),
(32, 3, 'F02', 'Tidak ada', 0, 0),
(33, 3, 'F03', 'Tidak', 0, 0),
(34, 3, 'F04', 'Tidak', 0, 0),
(35, 3, 'F05', '13', 0, 0),
(36, 3, 'F06', '', 0, 0),
(37, 3, 'F07', 'Tidak', 0, 0),
(38, 3, 'F08', 'Tidak', 0, 0),
(39, 3, 'F09', 'Tidak', 0, 0),
(40, 3, 'F10', '20.3', 0, 0),
(41, 3, 'F11', 'Tidak pernah', 0, 0),
(42, 3, 'F12', 'Cukup', 0, 0),
(43, 3, 'F13', 'Tidak', 0, 0),
(44, 3, 'F14', 'Tidak', 0, 0),
(45, 3, 'F15', 'A', 0, 0),
(46, 4, 'F01', '45', 0, 0),
(47, 4, 'F02', 'Tidak ada', 0, 0),
(48, 4, 'F03', 'Tidak', 0, 0),
(49, 4, 'F04', 'Tidak', 0, 0),
(50, 4, 'F05', '13', 0, 0),
(51, 4, 'F06', '', 0, 0),
(52, 4, 'F07', 'Tidak', 0, 0),
(53, 4, 'F08', 'Tidak', 0, 0),
(54, 4, 'F09', 'Tidak', 0, 0),
(55, 4, 'F10', '25.6', 0, 0),
(56, 4, 'F11', 'Tidak pernah', 0, 0),
(57, 4, 'F12', 'Cukup', 0, 0),
(58, 4, 'F13', 'Tidak', 0, 0),
(59, 4, 'F14', 'Ya', 1, 3),
(60, 4, 'F15', 'B', 0, 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_frekuensi_faktor`
--
DROP TABLE IF EXISTS `v_frekuensi_faktor`;
CREATE TABLE `v_frekuensi_faktor` (
  `id_faktor` varchar(5),
  `nama_faktor` varchar(150),
  `bobot` tinyint unsigned,
  `override_tinggi` tinyint(1),
  `jumlah_pasien` decimal(25,0),
  `persentase` decimal(5,1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_hasil_skrining`
--
DROP TABLE IF EXISTS `v_hasil_skrining`;
CREATE TABLE `v_hasil_skrining` (
  `id_skrining` int,
  `tanggal` datetime,
  `no_rm` varchar(30),
  `nama_pasien` varchar(100),
  `status_menopause` enum('Belum menopause','Sudah menopause'),
  `berat_badan` decimal(5,1),
  `tinggi_badan` decimal(5,1),
  `imt` decimal(4,1),
  `total_skor` int,
  `override_aktif` varchar(5),
  `kategori_risiko` varchar(20),
  `rekomendasi` text,
  `petugas` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_rekap_kategori`
--
DROP TABLE IF EXISTS `v_rekap_kategori`;
CREATE TABLE `v_rekap_kategori` (
  `id_kategori` int,
  `nama_kategori` varchar(20),
  `skor_min` int,
  `rekomendasi` text,
  `jumlah` bigint,
  `persentase` decimal(5,1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_data_pasien_lengkap`
-- (Matriks Lengkap Sesuai Sheet Data_Pasien Excel)
--
DROP TABLE IF EXISTS `v_data_pasien_lengkap`;
CREATE TABLE `v_data_pasien_lengkap` (
  `id_skrining` int,
  `tanggal_skrining` datetime,
  `id_pasien` int,
  `no_rm` varchar(30),
  `nama_pasien` varchar(100),
  `tanggal_lahir` date,
  `usia_saat_skrining` int,
  `status_menopause` enum('Belum menopause','Sudah menopause'),
  `berat_badan` decimal(5,1),
  `tinggi_badan` decimal(5,1),
  `imt` decimal(4,1),
  `total_skor` int,
  `override_aktif` varchar(5),
  `kategori_risiko` varchar(20),
  `rekomendasi` text,
  `petugas` varchar(100),
  `f01_input` varchar(30),
  `f01_terpenuhi` tinyint(1),
  `f02_input` varchar(30),
  `f02_terpenuhi` tinyint(1),
  `f03_input` varchar(30),
  `f03_terpenuhi` tinyint(1),
  `f04_input` varchar(30),
  `f04_terpenuhi` tinyint(1),
  `f05_input` varchar(30),
  `f05_terpenuhi` tinyint(1),
  `f06_input` varchar(30),
  `f06_terpenuhi` tinyint(1),
  `f07_input` varchar(30),
  `f07_terpenuhi` tinyint(1),
  `f08_input` varchar(30),
  `f08_terpenuhi` tinyint(1),
  `f09_input` varchar(30),
  `f09_terpenuhi` tinyint(1),
  `f10_input` varchar(30),
  `f10_terpenuhi` tinyint(1),
  `f11_input` varchar(30),
  `f11_terpenuhi` tinyint(1),
  `f12_input` varchar(30),
  `f12_terpenuhi` tinyint(1),
  `f13_input` varchar(30),
  `f13_terpenuhi` tinyint(1),
  `f14_input` varchar(30),
  `f14_terpenuhi` tinyint(1),
  `f15_input` varchar(30),
  `f15_terpenuhi` tinyint(1)
);

-- --------------------------------------------------------

--
-- Structure for view `v_frekuensi_faktor`
-- (Sesuai Sheet Rekap Excel: Menghitung Jumlah & Persentase)
--
DROP TABLE IF EXISTS `v_frekuensi_faktor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_frekuensi_faktor` AS
SELECT 
  `f`.`id_faktor` AS `id_faktor`,
  `f`.`nama_faktor` AS `nama_faktor`,
  `f`.`bobot` AS `bobot`,
  `f`.`override_tinggi` AS `override_tinggi`,
  COALESCE(SUM(`d`.`terpenuhi`), 0) AS `jumlah_pasien`,
  ROUND(COALESCE(SUM(`d`.`terpenuhi`), 0) * 100.0 / NULLIF((SELECT COUNT(*) FROM `skrining`), 0), 1) AS `persentase`
FROM `faktor_risiko` `f`
LEFT JOIN `detail_skrining` `d` ON (`d`.`id_faktor` = `f`.`id_faktor`)
GROUP BY `f`.`id_faktor`, `f`.`nama_faktor`, `f`.`bobot`, `f`.`override_tinggi`
ORDER BY `f`.`id_faktor` ASC;

-- --------------------------------------------------------

--
-- Structure for view `v_hasil_skrining`
--
DROP TABLE IF EXISTS `v_hasil_skrining`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_hasil_skrining` AS
SELECT 
  `s`.`id_skrining` AS `id_skrining`,
  `s`.`tanggal` AS `tanggal`,
  `p`.`no_rm` AS `no_rm`,
  `p`.`nama` AS `nama_pasien`,
  `s`.`status_menopause` AS `status_menopause`,
  `s`.`berat_badan` AS `berat_badan`,
  `s`.`tinggi_badan` AS `tinggi_badan`,
  `s`.`imt` AS `imt`,
  `s`.`total_skor` AS `total_skor`,
  IF(`s`.`override_aktif`, 'Ya', 'Tidak') AS `override_aktif`,
  `k`.`nama_kategori` AS `kategori_risiko`,
  `k`.`rekomendasi` AS `rekomendasi`,
  `u`.`nama` AS `petugas`
FROM `skrining` `s`
JOIN `pasien` `p` ON (`p`.`id_pasien` = `s`.`id_pasien`)
JOIN `users` `u` ON (`u`.`id_user` = `s`.`id_user`)
LEFT JOIN `kategori_risiko` `k` ON (`k`.`id_kategori` = `s`.`id_kategori`);

-- --------------------------------------------------------

--
-- Structure for view `v_rekap_kategori`
-- (Sesuai Sheet Rekap Excel: Menghitung Jumlah & Persentase Tiap Kategori)
--
DROP TABLE IF EXISTS `v_rekap_kategori`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_rekap_kategori` AS
SELECT 
  `k`.`id_kategori` AS `id_kategori`,
  `k`.`nama_kategori` AS `nama_kategori`,
  `k`.`skor_min` AS `skor_min`,
  `k`.`rekomendasi` AS `rekomendasi`,
  COUNT(`s`.`id_skrining`) AS `jumlah`,
  ROUND(IFNULL(COUNT(`s`.`id_skrining`) * 100.0 / NULLIF((SELECT COUNT(*) FROM `skrining`), 0), 0), 1) AS `persentase`
FROM `kategori_risiko` `k`
LEFT JOIN `skrining` `s` ON (`s`.`id_kategori` = `k`.`id_kategori`)
GROUP BY `k`.`id_kategori`, `k`.`nama_kategori`, `k`.`skor_min`, `k`.`rekomendasi`
ORDER BY `k`.`skor_min` ASC;

-- --------------------------------------------------------

--
-- Structure for view `v_data_pasien_lengkap`
-- (Matriks Register Data Pasien Sesuai Sheet Data_Pasien Excel)
--
DROP TABLE IF EXISTS `v_data_pasien_lengkap`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_data_pasien_lengkap` AS
SELECT 
  `s`.`id_skrining` AS `id_skrining`,
  `s`.`tanggal` AS `tanggal_skrining`,
  `p`.`id_pasien` AS `id_pasien`,
  `p`.`no_rm` AS `no_rm`,
  `p`.`nama` AS `nama_pasien`,
  `p`.`tanggal_lahir` AS `tanggal_lahir`,
  TIMESTAMPDIFF(YEAR, `p`.`tanggal_lahir`, `s`.`tanggal`) AS `usia_saat_skrining`,
  `s`.`status_menopause` AS `status_menopause`,
  `s`.`berat_badan` AS `berat_badan`,
  `s`.`tinggi_badan` AS `tinggi_badan`,
  `s`.`imt` AS `imt`,
  `s`.`total_skor` AS `total_skor`,
  IF(`s`.`override_aktif`, 'Ya', 'Tidak') AS `override_aktif`,
  `k`.`nama_kategori` AS `kategori_risiko`,
  `k`.`rekomendasi` AS `rekomendasi`,
  `u`.`nama` AS `petugas`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F01' THEN `d`.`nilai_input` END) AS `f01_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F01' THEN `d`.`terpenuhi` END) AS `f01_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F02' THEN `d`.`nilai_input` END) AS `f02_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F02' THEN `d`.`terpenuhi` END) AS `f02_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F03' THEN `d`.`nilai_input` END) AS `f03_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F03' THEN `d`.`terpenuhi` END) AS `f03_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F04' THEN `d`.`nilai_input` END) AS `f04_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F04' THEN `d`.`terpenuhi` END) AS `f04_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F05' THEN `d`.`nilai_input` END) AS `f05_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F05' THEN `d`.`terpenuhi` END) AS `f05_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F06' THEN `d`.`nilai_input` END) AS `f06_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F06' THEN `d`.`terpenuhi` END) AS `f06_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F07' THEN `d`.`nilai_input` END) AS `f07_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F07' THEN `d`.`terpenuhi` END) AS `f07_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F08' THEN `d`.`nilai_input` END) AS `f08_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F08' THEN `d`.`terpenuhi` END) AS `f08_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F09' THEN `d`.`nilai_input` END) AS `f09_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F09' THEN `d`.`terpenuhi` END) AS `f09_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F10' THEN `d`.`nilai_input` END) AS `f10_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F10' THEN `d`.`terpenuhi` END) AS `f10_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F11' THEN `d`.`nilai_input` END) AS `f11_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F11' THEN `d`.`terpenuhi` END) AS `f11_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F12' THEN `d`.`nilai_input` END) AS `f12_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F12' THEN `d`.`terpenuhi` END) AS `f12_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F13' THEN `d`.`nilai_input` END) AS `f13_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F13' THEN `d`.`terpenuhi` END) AS `f13_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F14' THEN `d`.`nilai_input` END) AS `f14_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F14' THEN `d`.`terpenuhi` END) AS `f14_terpenuhi`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F15' THEN `d`.`nilai_input` END) AS `f15_input`,
  MAX(CASE WHEN `d`.`id_faktor` = 'F15' THEN `d`.`terpenuhi` END) AS `f15_terpenuhi`
FROM `skrining` `s`
JOIN `pasien` `p` ON (`p`.`id_pasien` = `s`.`id_pasien`)
LEFT JOIN `users` `u` ON (`u`.`id_user` = `s`.`id_user`)
LEFT JOIN `kategori_risiko` `k` ON (`k`.`id_kategori` = `s`.`id_kategori`)
LEFT JOIN `detail_skrining` `d` ON (`d`.`id_skrining` = `s`.`id_skrining`)
GROUP BY `s`.`id_skrining`
ORDER BY `s`.`id_skrining` ASC;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_skrining`
--
ALTER TABLE `detail_skrining`
  ADD PRIMARY KEY (`id_detail`),
  ADD UNIQUE KEY `uq_skrining_faktor` (`id_skrining`,`id_faktor`),
  ADD KEY `fk_detail_faktor` (`id_faktor`);

--
-- Indexes for table `faktor_risiko`
--
ALTER TABLE `faktor_risiko`
  ADD PRIMARY KEY (`id_faktor`);

--
-- Indexes for table `kategori_risiko`
--
ALTER TABLE `kategori_risiko`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`),
  ADD UNIQUE KEY `no_rm` (`no_rm`);

--
-- Indexes for table `skrining`
--
ALTER TABLE `skrining`
  ADD PRIMARY KEY (`id_skrining`),
  ADD KEY `fk_skrining_user` (`id_user`),
  ADD KEY `fk_skrining_kategori` (`id_kategori`),
  ADD KEY `idx_skrining_pasien` (`id_pasien`),
  ADD KEY `idx_skrining_tanggal` (`tanggal`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_skrining`
--
ALTER TABLE `detail_skrining`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `kategori_risiko`
--
ALTER TABLE `kategori_risiko`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `skrining`
--
ALTER TABLE `skrining`
  MODIFY `id_skrining` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_skrining`
--
ALTER TABLE `detail_skrining`
  ADD CONSTRAINT `fk_detail_faktor` FOREIGN KEY (`id_faktor`) REFERENCES `faktor_risiko` (`id_faktor`),
  ADD CONSTRAINT `fk_detail_skrining` FOREIGN KEY (`id_skrining`) REFERENCES `skrining` (`id_skrining`) ON DELETE CASCADE;

--
-- Constraints for table `skrining`
--
ALTER TABLE `skrining`
  ADD CONSTRAINT `fk_skrining_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_risiko` (`id_kategori`),
  ADD CONSTRAINT `fk_skrining_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`),
  ADD CONSTRAINT `fk_skrining_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;