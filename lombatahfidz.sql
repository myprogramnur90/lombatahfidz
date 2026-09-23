-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 22, 2026 at 04:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lombatahfid`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `email`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator', 'admin@lombatahfidz.com', '2025-09-20 15:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_juri_final`
--

CREATE TABLE `assignment_juri_final` (
  `id` int(11) NOT NULL,
  `juri_id` int(11) NOT NULL,
  `peserta_final_id` int(11) NOT NULL,
  `status` enum('Assigned','Completed') DEFAULT 'Assigned',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_juri_final`
--

INSERT INTO `assignment_juri_final` (`id`, `juri_id`, `peserta_final_id`, `status`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 'Assigned', '2026-09-17 13:53:22', '2026-09-17 13:53:22'),
(4, 1, 2, 'Completed', '2026-09-17 13:53:22', '2026-09-17 14:16:55'),
(5, 2, 1, 'Assigned', '2026-09-17 13:53:22', '2026-09-17 13:53:22'),
(6, 2, 2, 'Completed', '2026-09-17 13:53:22', '2026-09-17 14:15:52'),
(7, 3, 1, 'Assigned', '2026-09-17 13:53:22', '2026-09-17 13:53:22'),
(8, 3, 2, 'Completed', '2026-09-17 13:53:22', '2026-09-17 14:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_berka`
--

CREATE TABLE `dokumen_berka` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `jenis_dokumen` enum('Surat Keterangan Aktif','KTS') NOT NULL,
  `file_dokumen` varchar(255) NOT NULL,
  `status_dokumen` enum('Pending','Diterima','Ditolak') DEFAULT 'Pending',
  `tanggal_upload` timestamp NULL DEFAULT current_timestamp(),
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dokumen_berka`
--

INSERT INTO `dokumen_berka` (`id`, `sekolah_id`, `jenis_dokumen`, `file_dokumen`, `status_dokumen`, `tanggal_upload`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 6, 'Surat Keterangan Aktif', '6_Surat Keterangan Aktif_1758512600_Suket zidna.pdf', 'Diterima', '2025-09-22 03:43:20', NULL, '2025-09-22 03:43:20', '2025-09-23 02:42:15'),
(2, 8, 'Surat Keterangan Aktif', '8_Surat Keterangan Aktif_1758604494_SUKET AKTIF KEISHA.jpeg', 'Diterima', '2025-09-23 05:14:54', NULL, '2025-09-23 05:14:54', '2025-09-23 12:12:28'),
(3, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1758688236_PAI Alvian.jpg', 'Diterima', '2025-09-24 04:30:36', NULL, '2025-09-24 04:30:36', '2025-09-24 04:49:06'),
(4, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1758688252_PAI Farhanah.jpg', 'Diterima', '2025-09-24 04:30:52', NULL, '2025-09-24 04:30:52', '2025-09-24 04:49:06'),
(5, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1758688268_PAI Hamka.jpg', 'Diterima', '2025-09-24 04:31:08', NULL, '2025-09-24 04:31:08', '2025-09-24 04:49:06'),
(6, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1758688277_PAI Nazwa.jpg', 'Diterima', '2025-09-24 04:31:17', NULL, '2025-09-24 04:31:17', '2025-09-24 04:49:06'),
(7, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1758688285_PAI Radit.jpg', 'Diterima', '2025-09-24 04:31:25', NULL, '2025-09-24 04:31:25', '2025-09-24 04:49:06'),
(8, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1758688294_PAI Tira.jpg', 'Diterima', '2025-09-24 04:31:34', NULL, '2025-09-24 04:31:34', '2025-09-24 04:49:06'),
(9, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1758688302_PAI Vino.jpg', 'Diterima', '2025-09-24 04:31:42', NULL, '2025-09-24 04:31:42', '2025-09-24 04:49:06'),
(10, 11, 'Surat Keterangan Aktif', '11_Surat Keterangan Aktif_1758717330_Surat Keterangan Aktif MQS889.pdf', 'Diterima', '2025-09-24 12:35:30', NULL, '2025-09-24 12:35:30', '2025-09-28 13:21:19'),
(11, 8, 'Surat Keterangan Aktif', '8_Surat Keterangan Aktif_1758758975_SUKET AKTIF PARAMITHA.jpeg', 'Diterima', '2025-09-25 00:09:35', NULL, '2025-09-25 00:09:35', '2025-09-28 13:22:00'),
(12, 9, 'Surat Keterangan Aktif', '9_Surat Keterangan Aktif_1759204631_SISWA PESERTA LOMBA TAHFIDZ.pdf', 'Diterima', '2025-09-30 03:57:11', NULL, '2025-09-30 03:57:11', '2025-10-01 03:17:44'),
(13, 14, 'Surat Keterangan Aktif', '14_Surat Keterangan Aktif_1759288556_20251001_100130.jpg', 'Diterima', '2025-10-01 03:15:56', NULL, '2025-10-01 03:15:56', '2025-10-01 03:17:07'),
(14, 14, 'Surat Keterangan Aktif', '14_Surat Keterangan Aktif_1759288629_20251001_100246.jpg', 'Diterima', '2025-10-01 03:17:09', NULL, '2025-10-01 03:17:09', '2025-10-01 03:17:16'),
(15, 17, 'Surat Keterangan Aktif', '17_Surat Keterangan Aktif_1759456233_Surat Keterangan aktif AIQO_signed.pdf', 'Diterima', '2025-10-03 01:50:33', NULL, '2025-10-03 01:50:33', '2025-10-08 12:55:18'),
(16, 17, 'Surat Keterangan Aktif', '17_Surat Keterangan Aktif_1759456259_Surat Keterangan aktif QONITA_signed.pdf', 'Diterima', '2025-10-03 01:50:59', NULL, '2025-10-03 01:50:59', '2025-10-08 12:55:18'),
(17, 15, 'Surat Keterangan Aktif', '15_Surat Keterangan Aktif_1759587498_Suket Aninda.pdf', 'Diterima', '2025-10-04 14:18:18', NULL, '2025-10-04 14:18:18', '2025-10-04 14:28:48'),
(18, 15, 'Surat Keterangan Aktif', '15_Surat Keterangan Aktif_1759587515_surat keterangan aulia.pdf', 'Diterima', '2025-10-04 14:18:35', NULL, '2025-10-04 14:18:35', '2025-10-04 14:28:48'),
(19, 15, 'Surat Keterangan Aktif', '15_Surat Keterangan Aktif_1759587530_surat keterangan kanza.pdf', 'Diterima', '2025-10-04 14:18:50', NULL, '2025-10-04 14:18:50', '2025-10-04 14:28:48'),
(20, 15, 'Surat Keterangan Aktif', '15_Surat Keterangan Aktif_1759587544_surat keterangan naya.pdf', 'Diterima', '2025-10-04 14:19:04', NULL, '2025-10-04 14:19:04', '2025-10-04 14:28:48'),
(21, 15, 'Surat Keterangan Aktif', '15_Surat Keterangan Aktif_1759587563_surat keterangan syafa.pdf', 'Diterima', '2025-10-04 14:19:23', NULL, '2025-10-04 14:19:23', '2025-10-04 14:28:48'),
(22, 15, 'Surat Keterangan Aktif', '15_Surat Keterangan Aktif_1759587878_GucwDyiZ-1.pdf', 'Diterima', '2025-10-04 14:24:38', NULL, '2025-10-04 14:24:38', '2025-10-04 14:28:48'),
(23, 16, 'Surat Keterangan Aktif', '16_Surat Keterangan Aktif_1759719485_135.25. (SURAT KETERANGAN AKTIF) - Copy.pdf', 'Diterima', '2025-10-06 02:58:05', NULL, '2025-10-06 02:58:05', '2025-10-06 06:24:32'),
(24, 12, 'Surat Keterangan Aktif', '12_Surat Keterangan Aktif_1759735608_SURAT KETERANGAN AKTIF LOMBA TAHFIDZ.pdf', 'Diterima', '2025-10-06 07:26:48', NULL, '2025-10-06 07:26:48', '2025-10-11 06:40:53'),
(25, 20, 'Surat Keterangan Aktif', '20_Surat Keterangan Aktif_1759746544_17597464751436465074149329277660.jpg', 'Diterima', '2025-10-06 10:29:04', NULL, '2025-10-06 10:29:04', '2025-10-08 12:58:29'),
(26, 21, 'Surat Keterangan Aktif', '21_Surat Keterangan Aktif_1759806280_Surat_Keterangan Aktif_MHQS_2025.pdf', 'Diterima', '2025-10-07 03:04:40', NULL, '2025-10-07 03:04:40', '2025-10-08 12:56:14'),
(27, 19, 'Surat Keterangan Aktif', '19_Surat Keterangan Aktif_1759827102_IMG-20250914-WA0022.jpg', 'Diterima', '2025-10-07 08:51:42', NULL, '2025-10-07 08:51:42', '2025-10-08 12:52:56'),
(28, 19, 'Surat Keterangan Aktif', '19_Surat Keterangan Aktif_1759827125_IMG-20250914-WA0023.jpg', 'Diterima', '2025-10-07 08:52:05', NULL, '2025-10-07 08:52:05', '2025-10-08 12:52:56'),
(29, 19, 'Surat Keterangan Aktif', '19_Surat Keterangan Aktif_1759827144_IMG-20250914-WA0024.jpg', 'Diterima', '2025-10-07 08:52:24', NULL, '2025-10-07 08:52:24', '2025-10-08 12:52:56'),
(30, 16, 'Surat Keterangan Aktif', '16_Surat Keterangan Aktif_1759914881_CamScanner 08-10-2025 16.04_1.jpg', 'Diterima', '2025-10-08 09:14:41', NULL, '2025-10-08 09:14:41', '2025-10-08 12:54:29'),
(31, 11, 'Surat Keterangan Aktif', '11_Surat Keterangan Aktif_1759972597_Surat Keterangan Aktif MQS.pdf', 'Diterima', '2025-10-09 01:16:37', NULL, '2025-10-09 01:16:37', '2025-10-13 01:18:50'),
(32, 11, 'Surat Keterangan Aktif', '11_Surat Keterangan Aktif_1759979164_Surat Keterangan Aktif MQS.pdf', 'Diterima', '2025-10-09 03:06:04', NULL, '2025-10-09 03:06:04', '2025-10-13 01:18:50'),
(33, 24, 'Surat Keterangan Aktif', '24_Surat Keterangan Aktif_1760016711_surat ket.geby.jpg', 'Diterima', '2025-10-09 13:31:51', NULL, '2025-10-09 13:31:51', '2025-10-09 13:34:57'),
(34, 23, 'Surat Keterangan Aktif', '23_Surat Keterangan Aktif_1760055423_SURAT KETERANGAN AKTIF SEKOLAH 3.pdf', 'Diterima', '2025-10-10 00:17:03', NULL, '2025-10-10 00:17:03', '2025-10-13 01:12:18'),
(35, 22, 'Surat Keterangan Aktif', '22_Surat Keterangan Aktif_1760072701_Chalista.pdf', 'Pending', '2025-10-10 05:05:01', NULL, '2025-10-10 05:05:01', '2025-10-10 05:05:01'),
(36, 22, 'Surat Keterangan Aktif', '22_Surat Keterangan Aktif_1760072733_fatimah zein.pdf', 'Pending', '2025-10-10 05:05:33', NULL, '2025-10-10 05:05:33', '2025-10-10 05:05:33'),
(37, 18, 'Surat Keterangan Aktif', '18_Surat Keterangan Aktif_1760149486_surat keterangan andra.jpg', 'Pending', '2025-10-11 02:24:46', NULL, '2025-10-11 02:24:46', '2025-10-11 02:24:46'),
(38, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1760161455_Pandu.pdf', 'Diterima', '2025-10-11 05:44:15', NULL, '2025-10-11 05:44:15', '2025-10-13 01:08:03'),
(39, 2, 'Surat Keterangan Aktif', '2_Surat Keterangan Aktif_1760161480_zoelva.pdf', 'Diterima', '2025-10-11 05:44:40', NULL, '2025-10-11 05:44:40', '2025-10-13 01:08:03'),
(40, 25, 'Surat Keterangan Aktif', '25_Surat Keterangan Aktif_1760170653_surat ket.aktif.jpg', 'Diterima', '2025-10-11 08:17:33', NULL, '2025-10-11 08:17:33', '2025-10-13 01:21:01'),
(41, 26, 'Surat Keterangan Aktif', '26_Surat Keterangan Aktif_1760319532_1001757865.jpg', 'Diterima', '2025-10-13 01:38:52', NULL, '2025-10-13 01:38:52', '2025-10-22 02:13:09'),
(42, 26, 'Surat Keterangan Aktif', '26_Surat Keterangan Aktif_1760319575_1001757872.jpg', 'Diterima', '2025-10-13 01:39:35', NULL, '2025-10-13 01:39:35', '2025-10-22 02:13:09'),
(43, 11, 'Surat Keterangan Aktif', '11_Surat Keterangan Aktif_1760425462_Surat Keterangan Aktif Tambahan.pdf', 'Diterima', '2025-10-14 07:04:22', NULL, '2025-10-14 07:04:22', '2025-10-17 09:55:23'),
(44, 17, 'Surat Keterangan Aktif', '17_Surat Keterangan Aktif_1760711201_Surat Keterangan aktif Salman.pdf', 'Pending', '2025-10-17 14:26:41', NULL, '2025-10-17 14:26:41', '2025-10-17 14:26:41'),
(45, 17, 'Surat Keterangan Aktif', '17_Surat Keterangan Aktif_1760711220_Surat Keterangan aktif  Nurul.pdf', 'Pending', '2025-10-17 14:27:00', NULL, '2025-10-17 14:27:00', '2025-10-17 14:27:00'),
(46, 17, 'Surat Keterangan Aktif', '17_Surat Keterangan Aktif_1760711240_Surat Keterangan aktif Dwi.pdf', 'Pending', '2025-10-17 14:27:20', NULL, '2025-10-17 14:27:20', '2025-10-17 14:27:20'),
(47, 29, 'Surat Keterangan Aktif', '29_Surat Keterangan Aktif_1760714189_2. SURAT KETERANGAN AKTIF-GHALIA-LOMBA HAFIDZ JUZ30.pdf', 'Diterima', '2025-10-17 15:16:29', NULL, '2025-10-17 15:16:29', '2025-10-21 03:24:39'),
(48, 29, 'Surat Keterangan Aktif', '29_Surat Keterangan Aktif_1760714340_2. SURAT KETERANGAN AKTIF-GHALIA-LOMBA HAFIDZ JUZ30.pdf', 'Diterima', '2025-10-17 15:19:00', NULL, '2025-10-17 15:19:00', '2025-10-21 03:24:39'),
(49, 31, 'Surat Keterangan Aktif', '31_Surat Keterangan Aktif_1761013400_surat keterangan aktif.jpg', 'Diterima', '2025-10-21 02:23:20', NULL, '2025-10-21 02:23:20', '2025-10-21 03:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `juri`
--

CREATE TABLE `juri` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `spesialisasi` varchar(100) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `juri`
--

INSERT INTO `juri` (`id`, `nama_lengkap`, `username`, `password`, `email`, `no_hp`, `spesialisasi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ust. Salim', 'juri1', '25d55ad283aa400af464c76d713c07ad', 'juri1@lombatahfidz.com', '081111111111', 'Kelancaran (Tahfidz)', 'Aktif', '2025-10-07 14:02:54', '2026-09-17 13:36:14'),
(2, 'Ust. Fauzi', 'juri2', '25d55ad283aa400af464c76d713c07ad', 'juri2@lombatahfidz.com', '081222222222', 'Makhraj Tajwid', 'Aktif', '2025-10-07 14:02:54', '2026-09-17 09:06:09'),
(3, 'Ust. Fudali', 'juri3', '25d55ad283aa400af464c76d713c07ad', 'juri3@lombatahfidz.com', '081333333333', 'Tajwid dan Adab', 'Aktif', '2025-10-07 14:02:54', '2026-09-17 13:36:21');

-- --------------------------------------------------------

--
-- Table structure for table `musabaqoh_aktif`
--

CREATE TABLE `musabaqoh_aktif` (
  `id` int(11) NOT NULL,
  `jenis` enum('penyisihan','final') NOT NULL DEFAULT 'penyisihan',
  `peserta_id` int(11) DEFAULT NULL,
  `no_soal` int(11) DEFAULT NULL,
  `status` enum('Aktif','Selesai') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `musabaqoh_aktif`
--

INSERT INTO `musabaqoh_aktif` (`id`, `jenis`, `peserta_id`, `no_soal`, `status`, `created_at`, `updated_at`) VALUES
(1, 'penyisihan', 102, 2, 'Selesai', '2026-09-17 02:23:20', '2026-09-17 09:13:12'),
(2, 'penyisihan', 41, 1, 'Selesai', '2026-09-17 02:26:11', '2026-09-17 09:01:25'),
(3, 'penyisihan', 91, 3, 'Selesai', '2026-09-17 09:07:32', '2026-09-17 09:08:16'),
(4, 'penyisihan', 9, 14, 'Selesai', '2026-09-17 09:13:37', '2026-09-17 09:14:23'),
(5, 'penyisihan', 89, 15, 'Selesai', '2026-09-17 09:14:31', '2026-09-17 09:14:36'),
(6, 'penyisihan', 115, 4, 'Selesai', '2026-09-17 10:19:31', '2026-09-17 10:19:51'),
(7, 'penyisihan', 50, 5, 'Selesai', '2026-09-17 10:22:06', '2026-09-17 10:23:19'),
(8, 'penyisihan', 16, 1, 'Selesai', '2026-09-17 10:58:52', '2026-09-17 11:01:21'),
(9, 'penyisihan', 67, 3, 'Selesai', '2026-09-17 11:01:35', '2026-09-17 11:02:10'),
(10, 'penyisihan', 65, 10, 'Selesai', '2026-09-17 11:02:15', '2026-09-17 13:36:32'),
(11, 'final', 67, 3, 'Aktif', '2026-09-17 13:54:48', '2026-09-17 13:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `nominal` decimal(10,2) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('Pending','Lunas','Ditolak') DEFAULT 'Pending',
  `tanggal_bayar` timestamp NULL DEFAULT current_timestamp(),
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `sekolah_id`, `nominal`, `bukti_pembayaran`, `status_pembayaran`, `tanggal_bayar`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 6, 35000.00, '6_1758507564_WhatsApp Image 2025-09-22 at 09.18.23.jpeg', 'Lunas', '2025-09-22 02:19:24', NULL, '2025-09-22 02:19:24', '2025-09-23 02:42:15'),
(2, 8, 35000.00, '8_1758602729_WhatsApp Image 2025-09-23 at 11.44.37.jpeg', 'Lunas', '2025-09-23 04:45:29', NULL, '2025-09-23 04:45:29', '2025-09-23 12:12:28'),
(3, 2, 245.00, '2_1758686881_Bukti Pendaftaran PAI.jpg', 'Lunas', '2025-09-24 04:08:01', NULL, '2025-09-24 04:08:01', '2025-09-24 04:49:06'),
(4, 2, 245000.00, '2_1758686906_Bukti Pendaftaran PAI.jpg', 'Lunas', '2025-09-24 04:08:26', NULL, '2025-09-24 04:08:26', '2025-09-24 04:49:06'),
(5, 11, 105000.00, '11_1758717951_Bukti Tranfser MHQ.jpeg', 'Lunas', '2025-09-24 12:45:51', NULL, '2025-09-24 12:45:51', '2025-09-28 13:21:19'),
(6, 8, 35000.00, '8_1758758945_WhatsApp Image 2025-09-25 at 06.42.03.jpeg', 'Lunas', '2025-09-25 00:09:05', NULL, '2025-09-25 00:09:05', '2025-09-28 13:22:00'),
(7, 14, 35000.00, '14_1759199621_Tue Sep 30 09_16_47 GMT+07_00 2025.jpg', 'Lunas', '2025-09-30 02:33:41', NULL, '2025-09-30 02:33:41', '2025-10-01 03:17:07'),
(8, 9, 140000.00, '9_1759202926_WhatsApp Image 2025-09-30 at 10.17.44.jpeg', 'Lunas', '2025-09-30 03:28:46', NULL, '2025-09-30 03:28:46', '2025-10-01 03:17:44'),
(9, 17, 75000.00, '17_1759357660_WhatsApp Image 2025-10-02 at 05.19.38.jpeg', 'Lunas', '2025-10-01 22:27:40', NULL, '2025-10-01 22:27:40', '2025-10-08 12:55:18'),
(10, 18, 35000.00, '18_1759373965_FT723356387.png', 'Lunas', '2025-10-02 02:59:25', NULL, '2025-10-02 02:59:25', '2025-10-08 12:57:05'),
(11, 15, 175.00, '15_1759498177_IMG-20251003-WA0025.jpg', 'Lunas', '2025-10-03 13:29:37', NULL, '2025-10-03 13:29:37', '2025-10-04 14:28:48'),
(12, 19, 35000.00, '19_1759576255_IMG-20251004-WA0020.jpeg', 'Lunas', '2025-10-04 11:10:55', NULL, '2025-10-04 11:10:55', '2025-10-08 12:52:56'),
(13, 16, 35000.00, '16_1759666459_SALMA.jpeg', 'Lunas', '2025-10-05 12:14:19', NULL, '2025-10-05 12:14:19', '2025-10-06 06:24:32'),
(14, 16, 35000.00, '16_1759714665_WhatsApp Image 2025-10-06 at 08.26.49.jpeg', 'Lunas', '2025-10-06 01:37:45', NULL, '2025-10-06 01:37:45', '2025-10-06 06:24:32'),
(15, 20, 35000.00, '20_1759746443_IMG-20251006-WA0047.jpg', 'Lunas', '2025-10-06 10:27:23', NULL, '2025-10-06 10:27:23', '2025-10-08 12:58:29'),
(16, 21, 140000.00, '21_1759806172_IMG-20251006-WA0005.jpg', 'Lunas', '2025-10-07 03:02:52', NULL, '2025-10-07 03:02:52', '2025-10-08 12:56:14'),
(17, 22, 70000.00, '22_1759891797_IMG-20251008-WA0020.jpg', 'Lunas', '2025-10-08 02:49:57', NULL, '2025-10-08 02:49:57', '2025-10-08 12:59:00'),
(18, 1, 35000.00, '1_1759931314_IMG-20251004-WA0042.jpg', 'Lunas', '2025-10-08 13:48:34', NULL, '2025-10-08 13:48:34', '2025-10-08 13:57:28'),
(19, 24, 35000.00, '24_1760016434_Screenshot_20251009-202343_WhatsApp.jpg', 'Lunas', '2025-10-09 13:27:14', NULL, '2025-10-09 13:27:14', '2025-10-09 13:34:57'),
(20, 23, 35000.00, '23_1760053850_IMG-20251010-WA0000.jpg', 'Lunas', '2025-10-09 23:50:50', NULL, '2025-10-09 23:50:50', '2025-10-13 01:12:18'),
(21, 12, 35000.00, '12_1760161397_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:17', NULL, '2025-10-11 05:43:17', '2025-10-11 06:40:53'),
(22, 12, 35000.00, '12_1760161402_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:22', NULL, '2025-10-11 05:43:22', '2025-10-11 06:40:53'),
(23, 12, 35000.00, '12_1760161403_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:23', NULL, '2025-10-11 05:43:23', '2025-10-11 06:40:53'),
(24, 12, 35000.00, '12_1760161406_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:26', NULL, '2025-10-11 05:43:26', '2025-10-11 06:40:53'),
(25, 12, 35000.00, '12_1760161408_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:28', NULL, '2025-10-11 05:43:28', '2025-10-11 06:40:53'),
(26, 12, 35000.00, '12_1760161411_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:31', NULL, '2025-10-11 05:43:31', '2025-10-11 06:40:53'),
(27, 12, 35000.00, '12_1760161414_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:34', NULL, '2025-10-11 05:43:34', '2025-10-11 06:40:53'),
(28, 12, 35000.00, '12_1760161418_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:38', NULL, '2025-10-11 05:43:38', '2025-10-11 06:40:53'),
(29, 12, 35000.00, '12_1760161423_IMG-20251009-WA0027.jpg', 'Lunas', '2025-10-11 05:43:43', NULL, '2025-10-11 05:43:43', '2025-10-11 06:40:53'),
(30, 2, 70000.00, '2_1760161546_IMG-20251011-WA0020.jpg', 'Lunas', '2025-10-11 05:45:46', NULL, '2025-10-11 05:45:46', '2025-10-13 01:08:03'),
(31, 13, 35000.00, '13_1760171141_IMG-20251008-WA0025.jpg', 'Lunas', '2025-10-11 08:25:41', NULL, '2025-10-11 08:25:41', '2025-10-13 01:17:21'),
(32, 13, 35000.00, '13_1760171180_IMG-20251008-WA0025.jpg', 'Lunas', '2025-10-11 08:26:20', NULL, '2025-10-11 08:26:20', '2025-10-13 01:17:21'),
(33, 13, 175.00, '13_1760171233_IMG-20251008-WA0025.jpg', 'Lunas', '2025-10-11 08:27:13', NULL, '2025-10-11 08:27:13', '2025-10-13 01:17:21'),
(34, 25, 35000.00, '25_1760180776_Screenshot_20251011_180552_bale by BTN.jpg', 'Lunas', '2025-10-11 11:06:16', NULL, '2025-10-11 11:06:16', '2025-10-13 01:21:01'),
(35, 26, 35000.00, '26_1760181983_Screenshot_20251011_182541.jpg', 'Lunas', '2025-10-11 11:26:23', NULL, '2025-10-11 11:26:23', '2025-10-13 01:16:17'),
(36, 4, 245.00, '4_1760225048_IMG-20251011-WA0100.jpg', 'Lunas', '2025-10-11 23:24:08', NULL, '2025-10-11 23:24:08', '2025-10-13 01:24:16'),
(37, 27, 35000.00, '27_1760315804_1000252935.jpg', 'Lunas', '2025-10-13 00:36:44', NULL, '2025-10-13 00:36:44', '2025-10-13 01:09:54'),
(38, 11, 70000.00, '11_1760581471_Bukti Pembayaran MHQS Tambaha.jpeg', 'Lunas', '2025-10-16 02:24:31', NULL, '2025-10-16 02:24:31', '2025-10-17 09:55:23'),
(39, 11, 175000.00, '11_1760581789_Bukti Pembayaran MHQS 5 orang.pdf', 'Lunas', '2025-10-16 02:29:49', NULL, '2025-10-16 02:29:49', '2025-10-17 09:55:23'),
(40, 28, 140000.00, '28_1760589545_Gambar WhatsApp 2025-10-16 pukul 11.36.59_c3e6ec57.jpg', 'Lunas', '2025-10-16 04:39:05', NULL, '2025-10-16 04:39:05', '2025-10-17 09:53:47'),
(41, 17, 105.00, '17_1760679810_Screenshot_20251017_124048.jpg', 'Lunas', '2025-10-17 05:43:30', NULL, '2025-10-17 05:43:30', '2025-10-17 09:52:40'),
(42, 29, 35000.00, '29_1760711967_Screenshot_2025-10-17-21-39-12-48_f0d5010b4078101d669cabe708845c5e.jpg', 'Lunas', '2025-10-17 14:39:27', NULL, '2025-10-17 14:39:27', '2025-10-21 03:24:39'),
(43, 31, 315000.00, '31_1761013244_Bukti Pembayaran.jpg', 'Lunas', '2025-10-21 02:20:44', NULL, '2025-10-21 02:20:44', '2025-10-21 03:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `nama_pengaturan` varchar(100) NOT NULL,
  `nilai` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_pengaturan`, `nilai`, `keterangan`, `updated_at`) VALUES
(1, 'nama_lomba', 'SMP Negeri 1 Sumenep', 'Nama lomba yang sedang berlangsung', '2025-09-20 15:30:59'),
(2, 'tanggal_penutupan', '2025-10-11', 'Tanggal penutupan pendaftaran', '2025-09-20 15:20:16'),
(3, 'biaya_pendaftaran', '35000', 'Biaya pendaftaran dalam rupiah', '2025-09-20 15:20:16'),
(4, 'kontak_panitia', '087870300326', 'Nomor kontak panitia', '2025-09-20 15:20:16'),
(5, 'alamat_sekretariat', 'Jl. Payudan Barat No. 11', 'Alamat sekretariat lomba', '2025-09-20 15:20:16'),
(6, 'jumlah_peserta_final', '6', 'Jumlah peserta yang masuk ke babak final', '2025-10-23 22:36:39'),
(7, 'status_penyisihan', 'Selesai', 'Status babak penyisihan (Aktif/Selesai)', '2026-09-17 13:42:17'),
(8, 'status_final', 'Aktif', 'Status babak final (Aktif/Selesai)', '2026-09-17 13:42:17'),
(9, 'bobot_tajwid_final', '0.3', 'Bobot penilaian tajwid untuk final', '2025-10-23 22:36:39'),
(10, 'bobot_fluency_final', '0.4', 'Bobot penilaian fluency untuk final', '2025-10-23 22:36:39'),
(11, 'bobot_makhraj_final', '0.3', 'Bobot penilaian makhraj untuk final', '2025-10-23 22:36:39');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id` int(11) NOT NULL,
  `peserta_id` int(11) NOT NULL,
  `juri_id` int(11) NOT NULL,
  `skor_spesialisasi` decimal(5,2) NOT NULL DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `tanggal_penilaian` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id`, `peserta_id`, `juri_id`, `skor_spesialisasi`, `catatan`, `tanggal_penilaian`, `created_at`, `updated_at`) VALUES
(1, 16, 2, 90.00, '', '2026-09-17 11:00:03', '2026-09-17 11:00:03', '2026-09-17 11:00:03'),
(2, 67, 2, 80.00, '', '2026-09-17 11:01:51', '2026-09-17 11:01:51', '2026-09-17 11:01:51'),
(3, 65, 2, 70.00, '', '2026-09-17 11:02:25', '2026-09-17 11:02:25', '2026-09-17 11:02:25'),
(4, 16, 1, 90.00, '', '2026-09-17 13:37:13', '2026-09-17 13:37:13', '2026-09-17 13:37:13'),
(5, 67, 1, 80.00, '', '2026-09-17 13:37:34', '2026-09-17 13:37:34', '2026-09-17 13:37:34'),
(6, 65, 1, 70.00, '', '2026-09-17 13:37:53', '2026-09-17 13:37:53', '2026-09-17 13:37:53'),
(7, 16, 3, 80.00, '', '2026-09-17 13:38:28', '2026-09-17 13:38:28', '2026-09-17 13:38:28'),
(8, 67, 3, 70.00, '', '2026-09-17 13:38:50', '2026-09-17 13:38:50', '2026-09-17 13:38:50'),
(9, 65, 3, 100.00, '', '2026-09-17 13:39:11', '2026-09-17 13:39:11', '2026-09-17 13:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian_final`
--

CREATE TABLE `penilaian_final` (
  `id` int(11) NOT NULL,
  `peserta_final_id` int(11) NOT NULL,
  `juri_id` int(11) NOT NULL,
  `skor_spesialisasi` decimal(5,2) NOT NULL DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `tanggal_penilaian` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penilaian_final`
--

INSERT INTO `penilaian_final` (`id`, `peserta_final_id`, `juri_id`, `skor_spesialisasi`, `catatan`, `tanggal_penilaian`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 90.00, '', '2026-09-17 14:15:52', '2026-09-17 14:15:52', '2026-09-17 14:15:52'),
(2, 2, 3, 90.00, '', '2026-09-17 14:16:32', '2026-09-17 14:16:32', '2026-09-17 14:16:32'),
(3, 2, 1, 90.00, '', '2026-09-17 14:16:55', '2026-09-17 14:16:55', '2026-09-17 14:16:55');

-- --------------------------------------------------------

--
-- Table structure for table `peserta`
--

CREATE TABLE `peserta` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `tanggal_daftar` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Diterima','Ditolak') DEFAULT 'Pending',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peserta`
--

INSERT INTO `peserta` (`id`, `sekolah_id`, `nama_lengkap`, `nisn`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `no_hp`, `kelas`, `tanggal_daftar`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 6, 'ZIDNA ILMA ISHANI', '3160233889', 'SUMENEP', '2016-09-03', 'P', 'Dusun Duwa\' Labuh RT 01/RW 02 Ketawang Daleman Kec. Ganding Kab. Sumenep', '085231648841', 'III 1(Tiga Satu)', '2025-09-22 02:15:24', 'Diterima', '', '2025-09-22 02:15:24', '2025-09-23 02:42:15'),
(2, 8, 'Keisha Zahra', '3155076754', 'Sumenep', '2015-07-27', 'P', 'Jalan Garuda RT 002 RW 002 Pandian', '+62 859-5478-40', '4', '2025-09-23 04:41:07', 'Diterima', '', '2025-09-23 04:41:07', '2025-09-23 12:12:28'),
(3, 11, 'Bilqisatul Inasiyah Ghassani', '3149960602', 'Sumenep', '2014-04-19', 'P', 'Pesisir Pakandangan. Barat', '085336902194', 'VI', '2025-09-24 03:20:44', 'Diterima', '', '2025-09-24 03:20:44', '2025-09-28 13:21:34'),
(4, 11, 'Kaila Rifa Zafira', '3140256952', 'Sumenep', '2014-04-01', 'P', 'Pakandangan Barat', '085336902192', 'VI', '2025-09-24 03:45:09', 'Diterima', '', '2025-09-24 03:45:09', '2025-09-28 13:21:26'),
(5, 2, 'Moh. Alvino Syamsi Ramadhana', '3147543261', 'Sumenep', '2014-07-24', 'L', 'Dusun Lang Alang Desa Batang batang Daya', '082338677848', 'V', '2025-09-24 03:57:15', 'Diterima', '', '2025-09-24 03:57:15', '2025-09-24 04:50:13'),
(6, 2, 'Rodhitu Billah Al Safa', '0149730873', 'Sumenep', '2014-01-12', 'L', 'Dusun Tangere Desa Batang Batang Daya', '082338677848', 'VI', '2025-09-24 03:59:01', 'Diterima', '', '2025-09-24 03:59:01', '2025-09-24 04:50:03'),
(7, 2, 'Ethratul Athirah', '0143301067', 'Sumenep', '2014-02-16', 'P', 'Desa Nyabakan Timur', '082338677848', 'VI', '2025-09-24 04:00:13', 'Diterima', '', '2025-09-24 04:00:13', '2025-09-24 04:49:53'),
(8, 2, 'Alvian Aidan Rizqi Mubarok El-Fauzi', '0154458087', 'Sumenep', '2015-06-08', 'L', 'Dusun Lang Alang Desa Batang Batang Daya', '082338677848', 'V', '2025-09-24 04:01:54', 'Diterima', '', '2025-09-24 04:01:54', '2025-09-24 04:49:37'),
(9, 2, 'AISYAH NAZWA', '0147307138', 'Sumenep', '2014-11-02', 'P', 'Dusun Taroman Desa Batang Batang Daya', '082338677848', 'V', '2025-09-24 04:03:00', 'Diterima', '', '2025-09-24 04:03:00', '2025-09-24 04:49:25'),
(10, 2, 'Hamka A. Tirmidzi', '0147146034', 'Sumenep', '2014-01-30', 'L', 'Dusun Ares Daja Desa Totosan', '082338677848', 'V', '2025-09-24 04:04:08', 'Diterima', '', '2025-09-24 04:04:08', '2025-09-24 04:49:16'),
(11, 2, 'AISYA FARHANAH', '0151313134', 'Sumenep', '2015-03-11', 'P', 'Dusun Somor Messe Desa Batang Batang Daya', '082338677848', 'V', '2025-09-24 04:05:25', 'Diterima', '', '2025-09-24 04:05:25', '2025-09-24 04:49:06'),
(12, 11, 'Jihan Najwa Aulia Putri', '3135202270', 'Sumenep', '2013-12-15', 'P', 'Dusun Pesisir Desa Pakandangan Barat Kecamatan Bluto', '085335130033', 'VI', '2025-09-24 12:34:42', 'Diterima', '', '2025-09-24 12:34:42', '2025-10-09 01:13:51'),
(13, 8, 'Paramitha Meilani Putri Haryono', '3157291335', 'Sumenep', '2015-05-07', 'P', 'Jalan Garuda RT 002 RW 002 Pandian', '082330004671', '4', '2025-09-25 00:01:53', 'Diterima', '', '2025-09-25 00:01:53', '2025-09-28 13:22:00'),
(14, 1, 'Nabilatul kamilah', '60720660', 'Sumenep', '2014-03-07', 'P', 'Jln Raya rubaru desa Tambaksari kecamatan rubaru', '087854353555', '6', '2025-09-28 13:13:15', 'Diterima', '', '2025-09-28 13:13:15', '2025-10-08 13:57:28'),
(16, 14, 'ARIFATUL MAULA', '0146174638', 'SUMENEP', '2014-03-17', 'P', 'Dusun Longgara RT 02 RW 03 Kebunan-Sumenep', '089506234490', '5', '2025-09-30 02:44:39', 'Diterima', '', '2025-09-30 02:44:39', '2025-10-01 03:17:07'),
(17, 14, 'SALSABILA ARSYL ROMADHANI', '3178580559', 'SUMENEP', '2017-05-22', 'P', 'Jl.Longgara RT 01 RW 03\r\nKebunan-Sumenep', '01908245261', '3', '2025-09-30 02:51:29', 'Diterima', '', '2025-09-30 02:51:29', '2025-10-01 03:17:16'),
(18, 9, 'AHMAD IBRIZIL MUABBADI', '3136152963', 'Sumenep', '2013-03-22', 'L', 'Dusun Kalang Langgar RT.006/RW.001 Dapenda Batang Batang Sumenep', '085259693610', '6', '2025-09-30 03:08:18', 'Diterima', '', '2025-09-30 03:08:18', '2025-10-01 03:18:03'),
(19, 9, 'JIHAN TALITA ULFA', '3147790481', 'Sumenep', '2014-05-24', 'P', 'Dusun Pesisir Timur, RT/RW:002/002 Legung Timur Batang Batang Sumenep', '085259693610', '5', '2025-09-30 03:11:59', 'Diterima', '', '2025-09-30 03:11:59', '2025-10-01 03:17:57'),
(20, 9, 'RIQQOTUS SABILAH', '3133821253', 'Sumenep', '2013-12-01', 'P', 'Dusun Pasaran RT.004/RW.004 Legung Timur Batang Batang Sumenep', '085259693610', '6', '2025-09-30 03:14:35', 'Diterima', '', '2025-09-30 03:14:35', '2025-10-01 03:17:51'),
(21, 9, 'ROFA ANNATUL SUDANA', '3160696793', 'Sumenep', '2016-03-25', 'P', 'Dusun Pesisir Barat, RT.003/RW.003 Legung Timur Batang Batang Sumenep', '085259693610', '4', '2025-09-30 03:16:10', 'Diterima', '', '2025-09-30 03:16:10', '2025-10-01 03:17:44'),
(22, 7, 'Farzana Eiliyanisa Nufah', '3133068645', 'Sumenep', '2013-10-03', 'P', 'Sumenep', '0819-3900-8260', '6', '2025-10-01 02:35:13', 'Diterima', '', '2025-10-01 02:35:13', '2025-10-22 02:14:08'),
(23, 7, 'Ibnu Rakha Arsyad', '0134517500', 'Sumenep', '2013-10-29', 'L', 'Sumenep', '0822-3353-8727', '6', '2025-10-01 02:37:04', 'Diterima', '', '2025-10-01 02:37:04', '2025-10-22 02:14:03'),
(24, 7, 'Nashifah Mertawijaya', '0148021923', 'Sumenep', '2014-06-15', 'P', 'Sumenep', '0822-3317-2224', '5', '2025-10-01 02:39:33', 'Diterima', '', '2025-10-01 02:39:33', '2025-10-22 02:13:57'),
(25, 7, 'Raiquinzi Khanindia Afni', '0151972645', 'Sumenep', '2015-06-16', 'P', 'Sumenep', '0819-3813-3338', '5', '2025-10-01 02:42:28', 'Diterima', '', '2025-10-01 02:42:28', '2025-10-22 02:13:52'),
(26, 17, 'Faiqotudz Dzakiyah', '0144530246', 'Sumenep', '2014-02-15', 'P', 'Jl.Raya Gapura, Desa Gapura Barat', '082229495873', 'VI (Enam)', '2025-10-01 22:26:07', 'Diterima', '', '2025-10-01 22:26:07', '2025-10-08 12:55:24'),
(27, 17, 'Qonitatus Shofiyyah', '0136395913', 'Sumenep', '2013-10-23', 'P', 'Jl. Raya Gapura, Desa Gapura Barat', '082229495873', 'VI (Enam)', '2025-10-01 22:30:24', 'Diterima', '', '2025-10-01 22:30:24', '2025-10-08 12:55:18'),
(28, 3, 'Nur Zaharah mafazah', '202201', 'Sumenep', '2013-12-06', 'P', 'Kolor Sumenep', '081914724335', '6', '2025-10-02 02:39:23', 'Diterima', '', '2025-10-02 02:39:23', '2025-10-09 13:11:39'),
(29, 3, 'Annisa Nur Salsabila', '202302', 'Sumenep', '2014-05-29', 'P', 'Manding Sumenep', '081914724335', '6', '2025-10-02 02:41:03', 'Diterima', '', '2025-10-02 02:41:03', '2025-10-09 13:11:32'),
(30, 18, 'Deandra Putra Mustafir', '0145565102', 'Sumenep', '2014-07-21', 'L', 'Jln.KH.Wahid Hasyim V/26 Kolor Sumenep', '085770001366', '5', '2025-10-02 02:48:43', 'Diterima', '', '2025-10-02 02:48:43', '2025-10-08 12:57:05'),
(31, 16, 'Kalila Rifda Zaneva', '0146356105', 'Sumenep', '2014-05-28', 'P', 'Jl. saluran air no 2 ds kebunan kec kota Sumenep', '081805122228', '5', '2025-10-03 02:31:36', 'Diterima', '', '2025-10-03 02:31:36', '2025-10-06 06:24:32'),
(32, 16, 'Muhammad Atharizzy Yanno', '3167410548', 'Malang', '2016-01-05', 'L', 'Jl. Asoka G-4 Perum Graha Soemarom Pajagalan', '081938253435', '4', '2025-10-03 02:32:43', 'Diterima', '', '2025-10-03 02:32:43', '2025-10-09 08:09:09'),
(33, 16, 'Ailani Arsyila Nasywa', '0173149616', 'Sumenep', '2016-10-13', 'P', 'Dusun Pajagalan, Gapura Barat, Kec. Gapura', '085232080771', '3', '2025-10-03 02:34:04', 'Diterima', '', '2025-10-03 02:34:04', '2025-10-06 06:25:15'),
(34, 16, 'Aisyah Azzahra Odisan', '0172790500', 'Sumenep', '2017-10-24', 'P', 'Perum Agung Residence Blok E nomor 27 Babbalan-Batuan', '085330249234', '2', '2025-10-03 02:35:50', 'Diterima', '', '2025-10-03 02:35:50', '2025-10-06 06:25:07'),
(35, 16, 'Sofiyyah Rahma Iskandar', '3169878175', 'Sumenep', '2016-09-20', 'P', 'Dusun Laok Lorong, Batudinding, Kec Gapura', '087740693584', '3', '2025-10-03 02:36:59', 'Diterima', '', '2025-10-03 02:36:59', '2025-10-06 06:24:56'),
(36, 15, 'Varisha Khanza Ramadhani', '0156661266', 'Sumenep', '2015-06-27', 'P', 'Jl. Lingkar Barat Gedungan', '081770411070', '5C', '2025-10-03 13:27:59', 'Diterima', '', '2025-10-03 13:27:59', '2025-10-04 14:29:19'),
(37, 3, 'Anindita keisya oktaviana Zahra', '212003', 'Sumenep', '2015-10-18', 'P', 'Kalianget', '081914724335', '4', '2025-10-04 01:49:30', 'Diterima', '', '2025-10-04 01:49:30', '2025-10-09 13:11:24'),
(38, 3, 'M. Zulfikar Roisjuna', '222004', 'Sumenep', '2014-12-13', 'L', 'Karangduak Sumenep', '081914724335', '5', '2025-10-04 01:51:46', 'Diterima', '', '2025-10-04 01:51:46', '2025-10-09 13:11:14'),
(40, 12, 'Baiq Bariroh', '0152970970', 'Sumenep', '2015-02-12', 'P', 'Jl. Pesantren Terate Pandian Sumenep', '085903635672', 'V (Lima)', '2025-10-04 02:40:38', 'Diterima', '', '2025-10-04 02:40:38', '2025-10-11 06:41:16'),
(41, 12, 'Adinda Rizky Sadina', '0151078518', 'Sumenep', '2015-05-06', 'P', 'Jl. Teuku Umar GG II Pandian Sumenep', '085218245717', 'IV (Empat)', '2025-10-04 02:43:39', 'Diterima', '', '2025-10-04 02:43:39', '2025-10-11 06:41:08'),
(42, 12, 'Iftitah Ilmi Kautsar Lukman', '0141083068', 'Sumenep', '2015-09-20', 'P', 'Jl. Raya Lenteng Kebunagung Sumenep', '087850360081', 'IV (Empat)', '2025-10-04 02:47:27', 'Diterima', '', '2025-10-04 02:47:27', '2025-10-11 06:41:01'),
(43, 12, 'Rizqi Ayuni Fatahillah', '0149805906', 'Sumenep', '2014-11-23', 'P', 'Dusun Utara Jembatan RT 10/ RW 04 Kebunagung Sumenep', '081939039157', 'V (Lima)', '2025-10-04 02:48:37', 'Diterima', '', '2025-10-04 02:48:37', '2025-10-11 06:40:53'),
(44, 19, 'Qurrotul A\'yuni Yulia Ramadhani', '3137579139', 'Sumenep', '2013-07-10', 'P', 'Daleman desa poreh kecamatan lenteng', '081916581282', '6', '2025-10-04 11:06:13', 'Diterima', '', '2025-10-04 11:06:13', '2025-10-08 12:53:13'),
(45, 19, 'Asa Aulia Syafika Hanin', '0144868995', 'Sumenep', '2014-03-25', 'P', 'Lenteng Timur Kecamatan Lenteng', '081916581282', '6', '2025-10-04 11:08:09', 'Diterima', '', '2025-10-04 11:08:09', '2025-10-08 12:53:07'),
(46, 19, 'Feiyaz Kayyisah Fikri', '3173070806', 'Sumenep', '2017-05-15', 'P', 'Desa Lenteng barat kecamatan Lenteng Timur', '081916581282', '2', '2025-10-04 11:10:12', 'Diterima', '', '2025-10-04 11:10:12', '2025-10-08 12:52:56'),
(47, 15, 'Nur Aulia Putri', '0144792165', 'Sumenep', '2014-06-20', 'P', 'Jl. KH. Mansyur V/88 Pangarangan', '081770411070', '5C', '2025-10-04 13:59:31', 'Diterima', '', '2025-10-04 13:59:31', '2025-10-04 14:29:10'),
(48, 15, 'Inayatun Najawiyyah', '0142662387', 'Sumenep', '2014-09-27', 'P', 'Gedungan barat, Batuan', '081770411070', '6A', '2025-10-04 14:02:08', 'Diterima', '', '2025-10-04 14:02:08', '2025-10-04 14:29:04'),
(49, 15, 'Inara Syafa Darmawan', '0137059776', 'Sumenep', '2016-02-05', 'P', 'Jl. Perum Alam Permai Asri Selatan BB 12 Desa Kolor Kota Sumenep', '081770411070', '4C', '2025-10-04 14:04:44', 'Diterima', '', '2025-10-04 14:04:44', '2025-10-04 14:28:58'),
(50, 15, 'Aninda Nur Syafitri', '3189731145', 'Sumenep', '2018-06-15', 'P', 'Dusun Kombira, RT 05 RW 01, Desa Rubaru, Kecamatan Rubaru', '081770411070', '1B', '2025-10-04 14:08:26', 'Diterima', '', '2025-10-04 14:08:26', '2025-10-04 14:28:48'),
(51, 16, 'SALMA TSABITA ASILAH', '0167267145', 'SUMENEP', '2016-02-09', 'P', 'JL. PERKUTUT RT/RW 001/001 PAMOLOKAN', '081333484969', '4', '2025-10-05 12:13:03', 'Diterima', '', '2025-10-05 12:13:03', '2025-10-06 06:24:44'),
(52, 20, 'Nadhira Azmi Falisha Wardani', '3181143162', 'Sumenep', '2018-08-14', 'P', 'Jl. Kurma No. 482 Pangarangan', '081803168303', '1', '2025-10-06 10:25:14', 'Diterima', '', '2025-10-06 10:25:14', '2025-10-08 12:58:29'),
(53, 21, 'Zul Asfi Rayhan', '0156553816', 'Sumenep', '2015-02-10', 'L', 'Dusun Podak Kacongan', '087750003814', '5', '2025-10-07 03:11:07', 'Diterima', '', '2025-10-07 03:11:07', '2025-10-08 12:56:32'),
(54, 21, 'Bilqis Khumairah', '0143537157', 'Sumenep', '2014-10-14', 'P', 'Jl. KH. MANSYUR  RT 01 RW 03 KACONGAN', '087750003814', '5', '2025-10-07 03:13:02', 'Diterima', '', '2025-10-07 03:13:02', '2025-10-08 12:56:26'),
(55, 21, 'Nafiisah Putri Oktaviani', '0138844754', 'Sumenep', '2013-10-09', 'P', 'Jl. Guntur No 10 Pabian', '087750003814', '6', '2025-10-07 03:14:54', 'Diterima', '', '2025-10-07 03:14:54', '2025-10-08 12:56:20'),
(56, 21, 'Siti Fatima Azzahro', '0141223206', 'Sumenep', '2014-11-04', 'P', 'Jl. KH. MANSYUR RT 01 RW 06 PABIAN', '087750003814', '5', '2025-10-07 03:16:37', 'Diterima', '', '2025-10-07 03:16:37', '2025-10-08 12:56:14'),
(57, 22, 'callista arsyfa Salsabila', '145204807', 'Sumenep', '2014-01-25', 'P', 'jln Urip Sumoharjo no 21', '082334676159', '6', '2025-10-08 02:45:14', 'Diterima', '', '2025-10-08 02:45:14', '2025-10-08 12:59:06'),
(58, 22, 'Srf Fatimah Zein Alhabsyi', '3158204726', 'Sumenep', '2015-07-21', 'P', 'Pajagalan', '+62 823-3804-14', '4', '2025-10-08 02:49:22', 'Diterima', '', '2025-10-08 02:49:22', '2025-10-08 12:59:00'),
(59, 4, 'Rania Mecca Azzahra', '222322', 'Sumenep', '2015-05-29', 'P', 'pak h. haris, sudah bayar', '085130368419', 'IV', '2025-10-08 13:10:09', 'Diterima', 'pak haji haris, bayar langsun ke saya', '2025-10-08 13:10:09', '2025-10-13 01:25:18'),
(60, 13, 'Ashila Salsabila', '-', 'Sumenep', '2017-04-20', 'P', 'Perumahan Kasokan Epon Blok B no.05 Kacongan Sumenep', '087701984110', '3', '2025-10-09 05:28:09', 'Diterima', '', '2025-10-09 05:28:09', '2025-10-13 01:17:42'),
(61, 13, 'Izzah Shofiyah', '-', 'Sumenep', '2019-05-18', 'P', 'Jl.Urip Sumoharjo Gg.3 Pangarangan', '087701984110', '1', '2025-10-09 05:35:13', 'Diterima', '', '2025-10-09 05:35:13', '2025-10-13 01:17:37'),
(62, 13, 'Kirania Maryam Chairunnisa', '-', 'Sumenep', '2018-06-25', 'P', 'Jl.Wahid Hasyim Gg.5 Kolor Sumenep', '087701984110', '1', '2025-10-09 05:37:57', 'Diterima', '', '2025-10-09 05:37:57', '2025-10-13 01:17:32'),
(63, 13, 'Hilyatul Akifa Ramadhini', '-', 'Kediri', '2017-06-04', 'P', 'Torbeng Timur Batuan', '087701984110', '3', '2025-10-09 05:39:45', 'Diterima', '', '2025-10-09 05:39:45', '2025-10-13 01:17:28'),
(64, 24, 'HILWA GABRIELLA MUSHAN', '111235290235220047', 'Sumenep', '2014-12-05', 'P', 'Dsn.lebak desa pasongsongan kec.pasongsongan kab.sumenep', '082333070376', '6', '2025-10-09 13:20:04', 'Diterima', '', '2025-10-09 13:20:04', '2025-10-09 13:34:57'),
(65, 7, 'Arsy Faizza Khairani Hamzah', '0157701069', 'Sumenep', '2015-03-10', 'P', 'Sumenep', '0823-0227-7774', '5', '2025-10-10 00:06:05', 'Diterima', '', '2025-10-10 00:06:05', '2025-10-22 02:13:47'),
(66, 7, 'Karisa Diva Nur Maulida', '3154215628', 'Sumenep', '2015-12-20', 'P', 'Sumenep', '0817-7046-4446', '4', '2025-10-10 00:07:52', 'Diterima', '', '2025-10-10 00:07:52', '2025-10-22 02:13:41'),
(67, 7, 'Annisah Nur Firdausi', '3156948155', 'Sumenep', '2015-05-09', 'P', 'Sumenep', '082231735074', '4', '2025-10-10 00:10:46', 'Diterima', '', '2025-10-10 00:10:46', '2025-10-22 02:13:35'),
(68, 23, 'MUHAMMAD SUDAHRI AINUL YAQIN', '0136920900', 'Sumenep', '2013-12-26', 'L', 'Jl. Lumba-lumba no. 15c', '082332699940', '6', '2025-10-10 00:15:37', 'Diterima', '', '2025-10-10 00:15:37', '2025-10-13 01:12:18'),
(69, 7, 'Alfiyah Khairin Kamila', '3152086730', 'Sumenep', '2015-08-15', 'P', 'Sumenep', '085231735074', '4', '2025-10-10 00:15:47', 'Diterima', '', '2025-10-10 00:15:47', '2025-10-22 02:13:30'),
(70, 7, 'Fayha Khumairo Ainuha Taurayya', '3161244846', 'Sumenep', '2016-03-23', 'P', 'Sumenep', '085231735074', '4', '2025-10-10 00:18:31', 'Diterima', '', '2025-10-10 00:18:31', '2025-10-22 02:13:26'),
(71, 7, 'Amira Salma', '0159131464', 'Sumenep', '2015-05-31', 'P', 'Sumenep', '085231735074', '4', '2025-10-10 00:23:09', 'Diterima', '', '2025-10-10 00:23:09', '2025-10-22 02:13:22'),
(72, 26, 'Safa Aulia Rizqiyah Rahman', '3161188019', 'Sumenep', '2016-05-29', 'P', 'Nyabakan Barat', '081909073343', '4', '2025-10-11 04:54:32', 'Diterima', '', '2025-10-11 04:54:32', '2025-10-13 01:16:17'),
(73, 2, 'Moh. Pandu Hidayat', '0146115365', 'Sumenep', '2014-03-20', 'L', 'Desa batang batang daya', '082338677848', 'VI (enam)', '2025-10-11 05:39:45', 'Diterima', '', '2025-10-11 05:39:45', '2025-10-13 01:08:13'),
(74, 2, 'Zoelva Ahmad Alkindi', '3156618590', 'Sumenep', '2015-05-09', 'L', 'Desa batang batang daya', '082338677848', 'V (lima)', '2025-10-11 05:41:36', 'Diterima', '', '2025-10-11 05:41:36', '2025-10-13 01:08:03'),
(76, 27, 'Jihan az-zahra', '3158019038', 'Sumenep', '2015-05-14', 'P', 'Desa Karang Budi, RT 01/RW 03,Gapura, Sumenep', '+6287853935431', 'Empat', '2025-10-11 06:59:19', 'Diterima', '', '2025-10-11 06:59:19', '2025-10-13 01:09:54'),
(77, 25, 'Princess Radhien Arsyila Efendi', '0131400321', 'Sumenep', '2013-08-27', 'P', 'Dusun sumber payung desa bataal barat labupaten sumenep', '085257000634', '6', '2025-10-11 08:14:02', 'Diterima', '', '2025-10-11 08:14:02', '2025-10-13 01:21:01'),
(78, 13, 'Bilqis Khoirotun Hisan', '-', 'Sumenep', '2016-11-25', 'P', 'Perumahan BSA jl. Cendana BK 14 Kolor Sumenep', '087701984110', '3', '2025-10-11 08:22:36', 'Diterima', '', '2025-10-11 08:22:36', '2025-10-13 01:17:21'),
(80, 7, 'HAURA NAZHIFA ALAYDRUS', '0157065826', 'Sumenep', '2015-10-11', 'P', 'Sumenep', '085231735074', '4', '2025-10-11 11:56:52', 'Diterima', '', '2025-10-11 11:56:52', '2025-10-22 02:13:17'),
(81, 4, 'Azzam Firas Al-Fatih', '0151075663', 'Sumenep', '2015-08-03', 'L', 'Desa pasongsongan kec. Pasongsongan', '085735510451', '4', '2025-10-11 17:17:13', 'Diterima', '', '2025-10-11 17:17:13', '2025-10-13 01:24:45'),
(82, 4, 'Hafidzah Althafun Nisa\'', '3175508987', 'Sumenep', '2017-01-22', 'P', 'Jl. Raya Rubaru', '085735510451', '3', '2025-10-11 17:20:45', 'Diterima', '', '2025-10-11 17:20:45', '2025-10-13 01:24:40'),
(83, 4, 'Valisha Nahira Ahmad', '3161926513', 'Sumenep', '2016-11-28', 'P', 'Jl. Trunojoyo GG IX', '085735510451', '3', '2025-10-11 17:22:41', 'Diterima', '', '2025-10-11 17:22:41', '2025-10-13 01:24:36'),
(84, 4, 'Talita Zahran Riyadi', '0177765711', 'Sumenep', '2017-06-30', 'P', 'Dusun Paoto\'an', '085735510451', '2', '2025-10-11 17:24:21', 'Diterima', '', '2025-10-11 17:24:21', '2025-10-13 01:24:31'),
(85, 4, 'Muhammad', '3186704806', 'Sumenep', '2018-04-14', 'L', 'Jl. Diponegoro No. 128', '085735510451', '2', '2025-10-11 17:26:13', 'Diterima', '', '2025-10-11 17:26:13', '2025-10-13 01:24:27'),
(86, 4, 'Mohammad Nabhan', '3164864375', 'Sumenep', '2016-12-26', 'L', 'Masjid Agung Langgundi Ketawang Ganding', '085735510451', '3', '2025-10-11 17:28:46', 'Diterima', '', '2025-10-11 17:28:46', '2025-10-13 01:24:22'),
(87, 4, 'Fakhma Azaria Mufidah', '3149706352', 'Sumenep', '2014-09-09', 'P', 'Jl. Kangean Blok B No.21 Bangkal', '085735510451', '5', '2025-10-11 17:31:14', 'Diterima', '', '2025-10-11 17:31:14', '2025-10-13 01:24:16'),
(88, 26, 'Nadziroh Ulfi Tazkiyah', '0157952805', 'Sumenep', '2015-04-20', 'P', 'Totosan, Batang-batang', '087842891606', '5', '2025-10-12 03:08:00', 'Diterima', 'akan bayar saat acara', '2025-10-12 03:08:00', '2025-10-22 02:13:09'),
(89, 11, 'Aminah Azzahirotur Robbaniyah', '3163794631', 'Sumenep', '2016-10-15', 'P', 'Pakandangan Barat', '085335130033', '3', '2025-10-14 07:02:35', 'Diterima', '', '2025-10-14 07:02:35', '2025-10-17 09:55:30'),
(90, 11, 'Aida Indina Zulfa', '3177483017', 'Sumenep', '2017-04-04', 'P', 'Pakandangan Barat Kecamatan Bluto', '085335130033', '2', '2025-10-14 07:03:51', 'Diterima', '', '2025-10-14 07:03:51', '2025-10-17 09:55:23'),
(91, 28, 'ALHIMNA RUSYDA', '3153953709', 'Sumenep', '2014-09-15', 'P', 'DUSUN TEMOR LEKE RT. 004 RW. 001, Kel. SAROKA, Kec. SARONGGI, SUMENEP, JAWA TIMUR, 69467', '089518732961', '5 (LIMA)', '2025-10-16 04:05:42', 'Diterima', '', '2025-10-16 04:05:42', '2025-10-17 09:54:04'),
(92, 28, 'FARDA HABIBA ALRIZA', '3159516569', 'SUMENEP', '2015-11-19', 'P', 'DUSUN GULUNGAN RT. 10 RW. 03, Kel. SAROKA, Kec. SARONGGI, SUMENEP, JAWA TIMUR, 69467', '089518732961', '4 (EMPAT)', '2025-10-16 04:10:18', 'Diterima', '', '2025-10-16 04:10:18', '2025-10-17 09:53:58'),
(93, 28, 'GWEN FILDZA DZAKIRAH HANI', '3132615208', 'SUMENEP', '2013-12-22', 'P', 'DUSUN MARAAN RT. 014 RW. 004, Kel. SAROKA, Kec. SARONGGI, SUMENEP, JAWA TIMUR, 69467', '089518732961', '6 (ENAM)', '2025-10-16 04:19:42', 'Diterima', '', '2025-10-16 04:19:42', '2025-10-17 09:53:53'),
(94, 28, 'RISQY AMANIYAH', '0139243275', 'SUMENEP', '2013-11-26', 'P', 'DUSUN DAJA LORONG RT. 011 RW. 003, Kel. TANAMERAH, Kec. SARONGGI, SUMENEP, JAWA TIMUR, 69467', '089518732961', '6 (ENAM)', '2025-10-16 04:23:03', 'Diterima', '', '2025-10-16 04:23:03', '2025-10-17 09:53:47'),
(95, 17, 'Salman Abdullah', '3152613483', 'Sumenep', '2015-10-08', 'L', 'Jln. Raya Gapura, Gapura Barat', '082229495873', 'IV (Empat)', '2025-10-17 05:31:50', 'Diterima', '', '2025-10-17 05:31:50', '2025-10-17 09:52:56'),
(96, 17, 'Nurul Kamaliyah', '0136761175', 'Sumenep', '2015-07-30', 'P', 'Jln. Raya Gapura, Gapura Barat', '082229495873', 'IV (Empat)', '2025-10-17 05:33:50', 'Diterima', '', '2025-10-17 05:33:50', '2025-10-17 09:52:48'),
(97, 17, 'Dwi Revalina Deswita', '3156936639', 'Sumenep', '2015-12-19', 'P', 'Jln Raya Gapura, Gapura Barat', '082229495873', 'IV (Empat)', '2025-10-17 05:35:23', 'Diterima', '', '2025-10-17 05:35:23', '2025-10-17 09:52:40'),
(98, 3, 'AKIFA SHAKILA PUTRI ERMILLA', '232021', 'Sumenep', '2006-10-04', 'P', 'Batuan', '081914724335', '3', '2025-10-17 11:37:55', 'Diterima', '', '2025-10-17 11:37:55', '2025-10-22 02:12:35'),
(99, 29, 'GHALIYA FARANESA', '3189548461', 'SUMENEP', '2018-05-24', 'P', 'Dusun Aeng Bato Kapedi Bluto Sumenep', '085235439112', 'II', '2025-10-17 14:38:32', 'Diterima', '', '2025-10-17 14:38:32', '2025-10-21 03:24:39'),
(101, 30, 'Farhatus Sholehah', '0138621542', 'Sumenep', '2013-04-15', 'P', 'Jl. Lenteng dsn semtani desa juluk kec. Saronggi Sumenep', '082301079200', 'VI', '2025-10-18 03:32:28', 'Diterima', 'sudah bayar langsung', '2025-10-18 03:32:28', '2025-10-21 03:20:34'),
(102, 30, 'ACH. Davin Maulana as-sabil', '0131272977', 'Sumenep', '2013-04-15', 'L', 'Jl. Lenteng dsn polai desa juluk kec. Saronggi Sumenep', '082301079200', 'VI', '2025-10-18 03:34:58', 'Diterima', 'sudah bayar langsung', '2025-10-18 03:34:58', '2025-10-21 03:20:23'),
(103, 30, 'Safira wardatun nazilah', '0133059977', 'Sumenep', '2013-05-27', 'P', 'Jl.raya Lenteng dsn semtani desa juluk kec. Saronggi Sumenep', '082301079200', 'VI', '2025-10-18 03:35:31', 'Diterima', 'sudah bayar langsung', '2025-10-18 03:35:31', '2025-10-21 03:20:14'),
(104, 4, 'Muhammad Fawwaz Ubaidillah', '013814610', 'Sumenep', '2015-09-12', 'L', 'Jl. Angkasa Barat kolor Sumenep', '085735510451', '4', '2025-10-19 02:20:52', 'Diterima', '', '2025-10-19 02:20:52', '2025-10-21 03:24:07'),
(106, 4, 'Mohammad Ashraf Razi', '0136825556', 'Sumenep', '2013-06-18', 'L', 'Jl. Basuki Rahmat Pajagalan Sumenep', '085735510451', '6', '2025-10-21 01:55:38', 'Diterima', '', '2025-10-21 01:55:38', '2025-10-21 03:23:58'),
(107, 31, 'Muhammad Althaf Athaullah', '0133649732', 'SUMENEP', '2013-11-11', 'L', 'Jl. Semangka Blok Melati No. 63 Perum Bumi Sumekar Asri', '082330501189', 'VI', '2025-10-21 01:59:05', 'Diterima', '', '2025-10-21 01:59:05', '2025-10-21 03:23:44'),
(108, 31, 'RA. Fajra Najmi Syakila', '0145870657', 'SUMENEP', '2014-05-24', 'P', 'Pangarangan Kec. Kota Sumenep', '082330501189', 'VI', '2025-10-21 02:01:30', 'Diterima', '', '2025-10-21 02:01:30', '2025-10-21 03:23:37'),
(109, 31, 'QIANDRA ANAYA CHANTIKA PUTRI', '0143048854', 'SUMENEP', '2014-06-03', 'P', 'Jl. Adirasa Kolor Kec. Kota Sumenep', '082330501189', 'V', '2025-10-21 02:03:42', 'Diterima', '', '2025-10-21 02:03:42', '2025-10-21 03:23:28'),
(110, 31, 'Ezki Nafisah Ruzwan', '0132333986', 'SUMENEP', '2013-10-09', 'P', 'Perum Alam Permai Asri Blok D No. 3 Kolor Kec. Kota Sumenep', '082330501189', 'VI', '2025-10-21 02:06:07', 'Diterima', '', '2025-10-21 02:06:07', '2025-10-21 03:23:14'),
(111, 31, 'Sumayyah Kamilah', '3130546781', 'SUMENEP', '2013-10-24', 'P', 'Jalan Cendana No. 09 / BK. 14 Perum BSA, Kolor Kota Sumenep', '082330501189', 'VI', '2025-10-21 02:08:16', 'Diterima', '', '2025-10-21 02:08:16', '2025-10-21 03:23:07'),
(112, 31, 'DZAKIRA ZAHIRA FAIRUZ MARGONO', '0142761149', 'TEGAL', '2014-10-09', 'P', 'PAKEMBARAN Kec. Slawi', '082330501189', 'VI', '2025-10-21 02:11:02', 'Diterima', '', '2025-10-21 02:11:02', '2025-10-21 03:23:00'),
(113, 31, 'Tuba \'Izzatul Wahida', '3146438901', 'PONTIANAK', '2014-06-26', 'P', 'Dusun Karang - Mandala Rubaru', '082330501189', 'IV', '2025-10-21 02:12:46', 'Diterima', '', '2025-10-21 02:12:46', '2025-10-21 03:22:50'),
(114, 31, 'Qalesya Humaira Azzahra', '3156120038', 'SUMENEP', '2015-04-17', 'P', 'PBSA Kolor BB II, Kec. Kota Sumenep', '082330501189', 'IV', '2025-10-21 02:15:43', 'Diterima', '', '2025-10-21 02:15:43', '2025-10-21 03:22:44'),
(115, 31, 'Almira Fathinah Az Zahra', '0165460773', 'MAlANG', '2016-05-16', 'P', 'Perum Alam Permai Asri Blok N - 17 Kolor, Kota Sumenep', '082330501189', 'IV', '2025-10-21 02:18:08', 'Diterima', '', '2025-10-21 02:18:08', '2025-10-21 03:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `peserta_final`
--

CREATE TABLE `peserta_final` (
  `id` int(11) NOT NULL,
  `peserta_id` int(11) NOT NULL,
  `skor_penyisihan` decimal(5,2) NOT NULL,
  `peringkat_penyisihan` int(11) NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peserta_final`
--

INSERT INTO `peserta_final` (`id`, `peserta_id`, `skor_penyisihan`, `peringkat_penyisihan`, `status`, `created_at`, `updated_at`) VALUES
(1, 16, 88.00, 1, 'Aktif', '2026-09-17 13:42:17', '2026-09-17 13:42:17'),
(2, 67, 78.00, 2, 'Aktif', '2026-09-17 13:42:17', '2026-09-17 13:42:17');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `konten` text NOT NULL,
  `jenis_post` enum('Persyaratan','Pengumuman','Informasi','Lainnya') DEFAULT 'Informasi',
  `target_audience` enum('Sekolah','Juri','Umum') DEFAULT 'Umum',
  `status` enum('Draft','Published') DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(200) NOT NULL,
  `npsn` varchar(20) DEFAULT NULL,
  `alamat_sekolah` text NOT NULL,
  `no_hp_sekolah` varchar(15) DEFAULT NULL,
  `email_sekolah` varchar(100) DEFAULT NULL,
  `nama_kepala_sekolah` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`id`, `nama_sekolah`, `npsn`, `alamat_sekolah`, `no_hp_sekolah`, `email_sekolah`, `nama_kepala_sekolah`, `username`, `password`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MI Nurus Shobah', '60720660', 'TAMBAKSARI', '087854353555', 'minurusshobah@gmail.com', 'kepala', 'minurusshobah', '$2y$10$osttCoPB70eqqTxEJ00VT.8STB.vFzbPlhD6SjyHFFaCGZhU.MdkC', 'Aktif', '2025-09-20 15:23:57', '2025-09-20 15:23:57'),
(2, 'SD NEGERI BATANG BATANG DAYA I', '20529759', 'Batang batang', '082338677848', 'batang@gmail.com', '', 'sdnbatangbatangd1', '$2y$10$Aq3di2u5tFVA6DIQ235.ZuCqC5VqAvM2e0dkDsw3cd5h2u1eAMzNa', 'Aktif', '2025-09-21 00:26:58', '2025-09-21 00:26:58'),
(3, 'MIN 1 Sumenep', '60720456', 'Pandian Sumenep', '081914724335', 'min1@gmail.com', 'Kepsek', 'min1sumenep', '$2y$10$Wtpq6G8UiItyUCQLzZZb1.tcl344e0SN5bt4pd4upWdbHdUI48Jxy', 'Aktif', '2025-09-21 01:35:51', '2025-09-23 02:06:06'),
(4, 'SDT2Q INSAN PERMATA MULIA', '70001032', 'Kolor sumenep', '087864535597', 'sdit2q@gmail.com', 'Kepseksd', 'sdit2qsumenep', '$2y$10$zwaySMeO9AqqgAeDyzkcWehM7L2Y/Hc9cSIEQ7GXooqgXT9RekJxK', 'Aktif', '2025-09-21 03:57:33', '2025-09-23 07:37:06'),
(5, 'SDN Manding Laok', '20530070', 'Manding laok', '087772166346', 'mandinglaok@gmail.com', 'Kep', 'sdnmandinglaok', '$2y$10$JNZ7w/.lKfMcLYnirTi8IupDkH3Tbk8inaqNW9drp9MtGZ1A2TNym', 'Aktif', '2025-09-21 04:15:25', '2025-09-21 04:15:25'),
(6, 'SD Negeri Daleman 1 Ganding', '20529620', 'Ganding', '081703316163', 'ganding@gmail.com', 'kepala', 'sdnegeridaleman', '$2y$10$hNUbq9cwkKKBQdul2GjIFOWUoA55HBrP/2KIszN7x1dX8lrE9zO06', 'Aktif', '2025-09-22 00:32:22', '2025-10-04 13:17:35'),
(7, 'SDIT Al Wathoniyah', '69786591', 'Kepanjen', '+62 852-3173-50', 'wathniyah@gmail.com', 'Kepp', 'sditalwathoniyah', '$2y$10$0KkrNdmTPIupwPc2Ly6V7uvjgAugbEIg1vPCQAJJBCZ2/WVnlaneO', 'Aktif', '2025-09-22 12:01:13', '2025-09-22 12:01:13'),
(8, 'SDN Pandian V', '1236', 'Pandian', '+62 823-3148-16', 'sdnpandian5@gmail.com', 'Keps', 'sdnpandian5', '$2y$10$Nvxgh5otWNgY7EgrjFqIl.O3uTvNTo3.EzZsUNlQ7AwA1meUNBJMS', 'Aktif', '2025-09-23 04:07:37', '2025-09-23 04:07:37'),
(9, 'MI lughatul islamiyah legung timur', '122', 'Legung', '+62 878-5833-95', 'salim@gmail.com', 'Salim', 'milughatulislamiyah', '$2y$10$6Tf2osq5cARrQS1U.UHAweDkeJxwUAh/Hr9uikL2gLwDeuS4vibKa', 'Aktif', '2025-09-23 13:19:38', '2025-09-24 00:20:26'),
(11, 'MIS NURUL HUDA I PAKANDANGAN BARAT', '60720301', 'Pakandangan', '62 853-3513-003', 'pakandangan@gmail.com', 'kepsek', 'minurulhuda1', '$2y$10$FraW7/Qyqz5hrBESJDWAv.Kas/bI63SXw0SdpVzy0/1TCvrtmpo1O', 'Aktif', '2025-09-24 00:22:21', '2025-09-24 00:22:21'),
(12, 'SDN pandian I', '788', 'Pandian', '0852', 'pandian@gmail.com', 'Jk', 'sdnpandian1', '$2y$10$Ti83.bQYFYW.MU84MCyUbuG3y9g8sFgWQJJvYgXOh2mSk8MrIPPiS', 'Aktif', '2025-09-29 11:53:21', '2025-09-29 11:53:21'),
(13, 'SD QISMU', '7865', 'Sumenep', '+62 877-0198-41', 'sdqismu@gmail.com', 'Yunita', 'sdqismu', '$2y$10$9x0eL.TO/mwknMYFCSknD.LrCncNRAnsbBLSEu8LVFznzh1XRKMNW', 'Aktif', '2025-09-29 22:00:30', '2025-09-29 22:00:30'),
(14, 'SDN Kebunan II', '76543', 'Kebunan', '+62 817-0315-50', 'sdnkebunan2@gmail.com', 'Kepala', 'sdnkebunan2', '$2y$10$x.by0/gOqgVZaHRvIzKdkObirbBk8CBuAprVVWxfqLxK6JVHRAUqe', 'Aktif', '2025-09-30 02:04:16', '2025-09-30 02:04:16'),
(15, 'MIN 2 Sumenep', '321321', 'Sumenep', '+62 817-7041-10', 'min2@gmail.com', 'kepsekk', 'min2sumenep', '$2y$10$NvhIDcb0SkhUxrJ2Tmkdde/o2H9MweX56VCaYHIAiZ3WOT6fHJdX6', 'Aktif', '2025-10-01 02:39:11', '2025-10-01 02:39:11'),
(16, 'SDIT Al Hidayah', '76890', 'Sumenep', '+62 823-3292-02', 'sditalhidayah@gmail.com', 'Kepsek', 'sditalhidayah', '$2y$10$6IgHtpnsYHDW4h1l6TWN/OxhhFjmoJ6h4sNDRUkyHQ11hBRlCii8q', 'Aktif', '2025-10-01 06:35:09', '2025-10-01 06:35:09'),
(17, 'SDN Gapura Barat 1', '67589', 'Alamat', '+62 822-2949-58', 'sdngapurabarat@gmail.com', 'Up', 'sdngapurabarat', '$2y$10$1J7upTNWgne7KQkPS2xc1ee4XX/UAfss4GL/QwcP6sOYH43RgmQdm', 'Aktif', '2025-10-01 06:50:50', '2025-10-01 06:50:50'),
(18, 'SDN Pajagalan II', '234', 'sumenep', '+62 857-7000-13', 'sdnpajagalan2@gmail.com', 'kepsek', 'sdnpajagalan2', '$2y$10$SASU/IgJi5ASAW5OmzzfcuklCV.Oz01YA3yMoWNfPwwfig2PnCllC', 'Aktif', '2025-10-02 02:20:28', '2025-10-02 02:20:28'),
(19, 'SD Islam Ar Rahmah', '876', 'Lenteng', '+62 819-1658-12', 'arrahmah@gmail.com', '', 'sdiarrahmah', '$2y$10$8gjMJLo2XoNzXSYXxZtSvO0B8yOvTRDFFnSxucww4LRkeuIkFCHLu', 'Aktif', '2025-10-03 08:23:42', '2025-10-04 11:01:38'),
(20, 'SDN Pangarangan 1', '7654', 'Pangarangan', '', '', 'Kepsek', 'sdnpangarangan1', '$2y$10$1oDi4nANXqu1knoUZY6sz.shm5W2Qud2ypzaee5siDn9pptqKC1Rm', 'Aktif', '2025-10-06 10:20:05', '2025-10-06 10:20:05'),
(21, 'SDN PABIAN III', '34523', 'Pabian', '087750003814', 'pabian@gmail.com', 'sdf', 'sdnpabian3', '$2y$10$J88WGepclV78EvKY01iecO6f8Q42K/3yNoTi998/M57If2WTyalfa', 'Aktif', '2025-10-07 03:00:14', '2025-10-07 03:00:14'),
(22, 'SDN PANGARANGAN III', '23423', 'Pengarangan', '900', 'salim@gmail.com', 'df', 'sdnpengarangan3', '$2y$10$oqAyVmq3bbZmPTyxiM9Qku14oCxtbV1F7WFQJzOjKTUoCtMGJi0oO', 'Aktif', '2025-10-07 10:29:51', '2025-10-07 10:29:51'),
(23, 'MI Hubbul Wathon', '3423', 'Sumenep', '453', 'salim@gmail.com', 'kepala', 'mihubbulwathon', '$2y$10$FNwqMM0fMPj5apyuz3HX3./A8ChU73SBT/iCbsYFyVsZXizRIuJlu', 'Aktif', '2025-10-09 07:10:35', '2025-10-09 08:31:04'),
(24, 'MI Annajah Pasongsongan', '768', 'Pasongsongan', '082333070376', 'mian@gmail.com', 'Kep', 'miannajahpasonsongan', '$2y$10$C039kSaVfyUDeqNqFxXk.OCOtKyy9PU8gMZ0vE96oTJCANwTWHcuK', 'Aktif', '2025-10-09 13:08:31', '2025-10-09 13:08:31'),
(25, 'SDN Bataal Barat 1 Ganding', '8665', 'Ganding', '085257000634', 'bataal@gmail.com', 'Kepsek', 'sdnbataalbarat1', '$2y$10$5KYlyF4XsynfHop6gZMw7uMXTS.mqJW4pTPRgo68QqShYUKHJ3FE2', 'Aktif', '2025-10-10 11:45:25', '2025-10-10 11:45:25'),
(26, 'SDN Totosan III', '12323', 'Batang batang', '081909073343', 'sdntotosan@gmail.com', 'kepsek', 'sdntotosan3', '$2y$10$LfY1KERoiz5q7L11XvSmyepzALs1.AHvD2NZUXXpHjKRCQ.f0qsge', 'Aktif', '2025-10-11 00:52:27', '2025-10-11 00:52:27'),
(27, 'SDN Baban 1 gapura', '345', 'sumenep', '087884731777', 'salim1@gmail.com', 'kepala', 'sdnbaban1gapura', '$2y$10$v4pd3bpB0k9KSGxTCPYe4.QJgwWgEIpeUQa.rEs7/cVvx1TEx7.wW', 'Aktif', '2025-10-11 05:25:26', '2025-10-11 05:25:26'),
(28, 'MI al-ittihad tanah merah saronggi', '2342', 'saronggi', '090', 'mialittihad@gmail.com', 'kepsek', 'mialittihad', '$2y$10$0pV/95VLdg767c62d4pGgubm7xQoYYL4w/j.O8To1oaf0N63NW5A6', 'Aktif', '2025-10-13 01:00:45', '2025-10-13 01:00:45'),
(29, 'Mi Raudlatul ihsan', '898989', 'Ganding', '32', 'g@gmail.com', '', 'miraudlatulihsan', '$2y$10$tDJy6mkdCp9TDPJVkqa7eeeiMnE6dHPoy.CyKI0b3.DdmRYc/IOeW', 'Aktif', '2025-10-17 14:14:50', '2025-10-17 14:14:50'),
(30, 'SDN Juluk 1', '8655', 'Juluk', '', 'rene@gmail.com', 'Suami Bu rene', 'sdnjuluk1', '$2y$10$NGPI/0BVKQg69xlSs2lRFOEkM5xGmxQ2q3QWqGcwmPVyDVgRx/F6O', 'Aktif', '2025-10-18 02:52:14', '2025-10-18 02:52:14'),
(31, 'SD Integral Lukman Al Hakim', '1232', 'pak yon', '', '', '', 'sdilukman', '$2y$10$UnzJdt.xxclsE4crXSNm2e5YN.n3GgjZTpXju6kx2wrWK47ZkFNaq', 'Aktif', '2025-10-20 10:27:03', '2025-10-20 10:27:03');

-- --------------------------------------------------------

--
-- Table structure for table `soal_musabaqoh`
--

CREATE TABLE `soal_musabaqoh` (
  `id` int(11) NOT NULL,
  `no_soal` int(11) NOT NULL,
  `soal1` text NOT NULL,
  `soal2` text NOT NULL,
  `soal3` text NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal_musabaqoh`
--

INSERT INTO `soal_musabaqoh` (`id`, `no_soal`, `soal1`, `soal2`, `soal3`, `status`, `created_at`, `updated_at`) VALUES
(16, 1, 'كَلَّا سَيَعْلَمُوْنَۙ ۝٤ثُمَّ كَلَّا سَيَعْلَمُوْنَ ۝٥اَلَمْ نَجْعَلِ الْاَرْضَ مِهٰدًاۙ ۝٦وَّالْجِبَالَ اَوْتَادًاۖ ۝٧وَّخَلَقْنٰكُمْ اَزْوَاجًاۙ ۝٨وَّجَعَلْنَا نَوْمَكُمْ سُبَاتًاۙ ۝٩وَّجَعَلْنَا الَّيْلَ لِبَاسًاۙ ۝١٠وَّجَعَلْنَا النَّهَارَ مَعَاشًاۚ ۝١١وَبَنَيْنَا فَوْقَكُمْ سَبْعًا شِدَادًاۙ ۝١٢وَّجَعَلْنَا سِرَاجًا وَّهَّاجًاۖ ۝١٣', 'وَالْفَجْرِۙ ۝١وَلَيَالٍ عَشْرٍۙ ۝٢وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩', 'وَمَا تَشَاۤءُوْنَ اِلَّآ اَنْ يَّشَاۤءَ اللّٰهُ رَبُّ الْعٰلَمِيْنَࣖ ۝٢٩اِذَا السَّمَاۤءُ انْفَطَرَتْۙ ۝١وَاِذَا الْكَوَاكِبُ انْتَثَرَتْۙ ۝٢وَاِذَا الْبِحَارُ فُجِّرَتْۙ ۝٣وَاِذَا الْقُبُوْرُ بُعْثِرَتْۙ ۝٤عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(17, 2, 'اَلَمْ نَجْعَلِ الْاَرْضَ مِهٰدًاۙ ۝٦وَّالْجِبَالَ اَوْتَادًاۖ ۝٧وَّخَلَقْنٰكُمْ اَزْوَاجًاۙ ۝٨وَّجَعَلْنَا نَوْمَكُمْ سُبَاتًاۙ ۝٩وَّجَعَلْنَا الَّيْلَ لِبَاسًاۙ ۝١٠وَّجَعَلْنَا النَّهَارَ مَعَاشًاۚ ۝١١وَبَنَيْنَا فَوْقَكُمْ سَبْعًا شِدَادًاۙ ۝١٢وَّجَعَلْنَا سِرَاجًا وَّهَّاجًاۖ ۝١٣وَّاَنْزَلْنَا مِنَ الْمُعْصِرٰتِ مَاۤءً ثَجَّاجًاۙ ۝١٤', 'وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١', 'اُولٰۤىِٕكَ هُمُ الْكَفَرَةُ الْفَجَرَةُࣖ ۝٤٢اِذَا الشَّمْسُ كُوِّرَتْۖ ۝١وَاِذَا النُّجُوْمُ انْكَدَرَتْۖ ۝٢وَاِذَا الْجِبَالُ سُيِّرَتْۖ ۝٣وَاِذَا الْعِشَارُ عُطِّلَتْۖ ۝٤وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(18, 3, 'وَّجَعَلْنَا الَّيْلَ لِبَاسًاۙ ۝١٠وَّجَعَلْنَا النَّهَارَ مَعَاشًاۚ ۝١١وَبَنَيْنَا فَوْقَكُمْ سَبْعًا شِدَادًاۙ ۝١٢وَّجَعَلْنَا سِرَاجًا وَّهَّاجًاۖ ۝١٣وَّاَنْزَلْنَا مِنَ الْمُعْصِرٰتِ مَاۤءً ثَجَّاجًاۙ ۝١٤لِّنُخْرِجَ بِهٖ حَبًّا وَّنَبَاتًاۙ ۝١٥وَّجَنّٰتٍ اَلْفَافًاۗ ۝١٦', 'اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢', 'كَاَنَّهُمْ يَوْمَ يَرَوْنَهَا لَمْ يَلْبَثُوْٓا اِلَّا عَشِيَّةً اَوْ ضُحٰىهَاࣖ ۝٤٦عَبَسَ وَتَوَلّٰىٓۙ ۝١اَنْ جَاۤءَهُ الْاَعْمٰىۗ ۝٢وَمَا يُدْرِيْكَ لَعَلَّهٗ يَزَّكّٰىٓۙ ۝٣اَوْ يَذَّكَّرُ فَتَنْفَعَهُ الذِّكْرٰىۗ ۝٤اَمَّا مَنِ اسْتَغْنٰىۙ ۝٥فَاَنْتَ لَهٗ تَصَدّٰىۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(19, 4, 'وَّاَنْزَلْنَا مِنَ الْمُعْصِرٰتِ مَاۤءً ثَجَّاجًاۙ ۝١٤لِّنُخْرِجَ بِهٖ حَبًّا وَّنَبَاتًاۙ ۝١٥وَّجَنّٰتٍ اَلْفَافًاۗ ۝١٦اِنَّ يَوْمَ الْفَصْلِ كَانَ مِيْقَاتًاۙ ۝١٧يَّوْمَ يُنْفَخُ فِى الصُّوْرِ فَتَأْتُوْنَ اَفْوَاجًاۙ ۝١٨وَّفُتِحَتِ السَّمَاۤءُ فَكَانَتْ اَبْوَابًاۙ ۝١٩وَّسُيِّرَتِ الْجِبَالُ فَكَانَتْ سَرَابًاۗ ۝٢٠', 'اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣', 'فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣تَبَّتْ يَدَآ اَبِيْ لَهَبٍ وَّتَبَّۗ ۝١مَآ اَغْنٰى عَنْهُ مَالُهٗ وَمَا كَسَبَۗ ۝٢سَيَصْلٰى نَارًا ذَاتَ لَهَبٍۙ ۝٣وَّامْرَاَتُهٗۗ حَمَّالَةَ الْحَطَبِۚ ۝٤فِيْ جِيْدِهَا حَبْلٌ مِّنْ مَّسَدٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(20, 5, 'اِنَّ يَوْمَ الْفَصْلِ كَانَ مِيْقَاتًاۙ ۝١٧يَّوْمَ يُنْفَخُ فِى الصُّوْرِ فَتَأْتُوْنَ اَفْوَاجًاۙ ۝١٨وَّفُتِحَتِ السَّمَاۤءُ فَكَانَتْ اَبْوَابًاۙ ۝١٩وَّسُيِّرَتِ الْجِبَالُ فَكَانَتْ سَرَابًاۗ ۝٢٠لِّلطّٰغِيْنَ مَاٰبًاۙ ۝٢٢لّٰبِثِيْنَ فِيْهَآ اَحْقَابًاۚ ۝٢٣', 'وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥', 'لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦اِذَا جَاۤءَ نَصْرُ اللّٰهِ وَالْفَتْحُۙ ۝١وَرَاَيْتَ النَّاسَ يَدْخُلُوْنَ فِيْ دِيْنِ اللّٰهِ اَفْوَاجًاۙ ۝٢فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(21, 6, 'وَّسُيِّرَتِ الْجِبَالُ فَكَانَتْ سَرَابًاۗ ۝٢٠اِنَّ جَهَنَّمَ كَانَتْ مِرْصَادًاۙ ۝٢١لِّلطّٰغِيْنَ مَاٰبًاۙ ۝٢٢لّٰبِثِيْنَ فِيْهَآ اَحْقَابًاۚ ۝٢٣لَا يَذُوْقُوْنَ فِيْهَا بَرْدًا وَّلَا شَرَابًاۙ ۝٢٤اِلَّا حَمِيْمًا وَّغَسَّاقًاۙ ۝٢٥جَزَاۤءً وِّفَاقًاۗ ۝٢٦اِنَّهُمْ كَانُوْا لَا يَرْجُوْنَ حِسَابًاۙ ۝٢٧وَّكَذَّبُوْا بِاٰيٰتِنَا كِذَّابًاۗ ۝٢٨', 'فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦', 'فِيْ عَمَدٍ مُّمَدَّدَةٍࣖ ۝٩اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِاَصْحٰبِ الْفِيْلِۗ ۝١اَلَمْ يَجْعَلْ كَيْدَهُمْ فِيْ تَضْلِيْلٍۙ ۝٢وَّاَرْسَلَ عَلَيْهِمْ طَيْرًا اَبَابِيْلَۙ ۝٣تَرْمِيْهِمْ بِحِجَارَةٍ مِّنْ سِجِّيْلٍۙ ۝٤فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(22, 7, 'لَا يَذُوْقُوْنَ فِيْهَا بَرْدًا وَّلَا شَرَابًاۙ ۝٢٤اِلَّا حَمِيْمًا وَّغَسَّاقًاۙ ۝٢٥جَزَاۤءً وِّفَاقًاۗ ۝٢٦اِنَّهُمْ كَانُوْا لَا يَرْجُوْنَ حِسَابًاۙ ۝٢٧وَّكَذَّبُوْا بِاٰيٰتِنَا كِذَّابًاۗ ۝٢٨وَكُلَّ شَيْءٍ اَحْصَيْنٰهُ كِتٰبًاۙ ۝٢٩فَذُوْقُوْا فَلَنْ نَّزِيْدَكُمْ اِلَّا عَذَابًاࣖ ۝٣٠', 'اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦كَلَّا بَلْ لَّا تُكْرِمُوْنَ الْيَتِيْمَۙ ۝١٧وَلَا تَحٰۤضُّوْنَ عَلٰى طَعَامِ الْمِسْكِيْنِۙ ۝١٨', 'فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥لِاِيْلٰفِ قُرَيْشٍۙ ۝١اٖلٰفِهِمْ رِحْلَةَ الشِّتَاۤءِ وَالصَّيْفِۚ ۝٢فَلْيَعْبُدُوْا رَبَّ هٰذَا الْبَيْتِۙ ۝٣الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙ وَّاٰمَنَهُمْ مِّنْ خَوْفٍࣖ ۝٤', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(23, 8, 'اِنَّهُمْ كَانُوْا لَا يَرْجُوْنَ حِسَابًاۙ ۝٢٧وَّكَذَّبُوْا بِاٰيٰتِنَا كِذَّابًاۗ ۝٢٨وَكُلَّ شَيْءٍ اَحْصَيْنٰهُ كِتٰبًاۙ ۝٢٩فَذُوْقُوْا فَلَنْ نَّزِيْدَكُمْ اِلَّا عَذَابًاࣖ ۝٣٠اِنَّ لِلْمُتَّقِيْنَ مَفَازًاۙ ۝٣١حَدَاۤىِٕقَ وَاَعْنَابًاۙ ۝٣٢وَّكَوَاعِبَ اَتْرَابًاۙ ۝٣٣وَّكَأْسًا دِهَاقًاۗ ۝٣٤', 'وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦كَلَّا بَلْ لَّا تُكْرِمُوْنَ الْيَتِيْمَۙ ۝١٧وَلَا تَحٰۤضُّوْنَ عَلٰى طَعَامِ الْمِسْكِيْنِۙ ۝١٨وَتَأْكُلُوْنَ التُّرَاثَ اَكْلًا لَّمًّاۙ ۝١٩وَّتُحِبُّوْنَ الْمَالَ حُبًّا جَمًّاۗ ۝٢٠', 'الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙ وَّاٰمَنَهُمْ مِّنْ خَوْفٍࣖ ۝٤اَرَءَيْتَ الَّذِيْ يُكَذِّبُ بِالدِّيْنِۗ ۝١فَذٰلِكَ الَّذِيْ يَدُعُّ الْيَتِيْمَۙ ۝٢وَلَا يَحُضُّ عَلٰى طَعَامِ الْمِسْكِيْنِۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(24, 9, 'وَكُلَّ شَيْءٍ اَحْصَيْنٰهُ كِتٰبًاۙ ۝٢٩فَذُوْقُوْا فَلَنْ نَّزِيْدَكُمْ اِلَّا عَذَابًاࣖ ۝٣٠اِنَّ لِلْمُتَّقِيْنَ مَفَازًاۙ ۝٣١حَدَاۤىِٕقَ وَاَعْنَابًاۙ ۝٣٢وَّكَوَاعِبَ اَتْرَابًاۙ ۝٣٣وَّكَأْسًا دِهَاقًاۗ ۝٣٤لَا يَسْمَعُوْنَ فِيْهَا لَغْوًا وَّلَا كِذّٰبًا ۝٣٥جَزَاۤءً مِّنْ رَّبِّكَ عَطَاۤءً حِسَابًاۙ ۝٣٦', 'وَتَأْكُلُوْنَ التُّرَاثَ اَكْلًا لَّمًّاۙ ۝١٩وَّتُحِبُّوْنَ الْمَالَ حُبًّا جَمًّاۗ ۝٢٠كَلَّآ اِذَا دُكَّتِ الْاَرْضُ دَكًّا دَكًّاۙ ۝٢١وَّجَآءَ رَبُّكَ وَالْمَلَكُ صَفًّا صَفًّاۚ ۝٢٢وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣', 'اِنَّ رَبَّهُمْ بِهِمْ يَوْمَىِٕذٍ لَّخَبِيْرٌࣖ ۝١١اَلْقَارِعَةُۙ ۝١مَا الْقَارِعَةُۚ ۝٢وَمَآ اَدْرٰىكَ مَا الْقَارِعَةُۗ ۝٣يَوْمَ يَكُوْنُ النَّاسُ كَالْفَرَاشِ الْمَبْثُوْثِۙ ۝٤وَتَكُوْنُ الْجِبَالُ كَالْعِهْنِ الْمَنْفُوْشِۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(25, 10, ' اِنَّ لِلْمُتَّقِيْنَ مَفَازًاۙ ۝٣١حَدَاۤىِٕقَ وَاَعْنَابًاۙ ۝٣٢وَّكَوَاعِبَ اَتْرَابًاۙ ۝٣٣وَّكَأْسًا دِهَاقًاۗ ۝٣٤لَا يَسْمَعُوْنَ فِيْهَا لَغْوًا وَّلَا كِذّٰبًا ۝٣٥جَزَاۤءً مِّنْ رَّبِّكَ عَطَاۤءً حِسَابًاۙ ۝٣٦رَّبِّ السَّمٰوٰتِ وَالْاَرْضِ وَمَا بَيْنَهُمَا الرَّحْمٰنِ لَا يَمْلِكُوْنَ مِنْهُ خِطَابًاۚ ۝٣٧', 'كَلَّآ اِذَا دُكَّتِ الْاَرْضُ دَكًّا دَكًّاۙ ۝٢١وَّجَآءَ رَبُّكَ وَالْمَلَكُ صَفًّا صَفًّاۚ ۝٢٢وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣يَقُوْلُ يٰلَيْتَنِيْ قَدَّمْتُ لِحَيَاتِيْۚ ۝٢٤فَيَوْمَىِٕذٍ لَّا يُعَذِّبُ عَذَابَهٗٓ اَحَدٌۙ ۝٢٥', 'وَمَنْ يَّعْمَلْ مِثْقَالَ ذَرَّةٍ شَرًّا يَّرَهٗࣖ ۝٨وَالْعٰدِيٰتِ ضَبْحًاۙ ۝١فَالْمُوْرِيٰتِ قَدْحًاۙ ۝٢فَالْمُغِيْرٰتِ صُبْحًاۙ ۝٣فَاَثَرْنَ بِهٖ نَقْعًاۙ ۝٤فَوَسَطْنَ بِهٖ جَمْعًاۙ ۝٥اِنَّ الْاِنْسَانَ لِرَبِّهٖ لَكَنُوْدٌۚ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(26, 11, 'جَزَاۤءً مِّنْ رَّبِّكَ عَطَاۤءً حِسَابًاۙ ۝٣٦رَّبِّ السَّمٰوٰتِ وَالْاَرْضِ وَمَا بَيْنَهُمَا الرَّحْمٰنِ لَا يَمْلِكُوْنَ مِنْهُ خِطَابًاۚ ۝٣٧يَوْمَ يَقُوْمُ الرُّوْحُ وَالْمَلٰۤىِٕكَةُ صَفًّاۙ لَّا يَتَكَلَّمُوْنَ اِلَّا مَنْ اَذِنَ لَهُ الرَّحْمٰنُ وَقَالَ صَوَابًا ۝٣٨ذٰلِكَ الْيَوْمُ الْحَقُّۚ فَمَنْ شَاۤءَ اتَّخَذَ اِلٰى رَبِّهٖ مَاٰبًا ۝٣٩', 'اَفَلَا يَنْظُرُوْنَ اِلَى الْاِبِلِ كَيْفَ خُلِقَتْۗ ۝١٧وَاِلَى السَّمَاۤءِ كَيْفَ رُفِعَتْۗ ۝١٨وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠فَذَكِّرْۗ اِنَّمَآ اَنْتَ مُذَكِّرٌۙ ۝٢١لَّسْتَ عَلَيْهِمْ بِمُصَيْطِرٍۙ ۝٢٢اِلَّا مَنْ تَوَلّٰى وَكَفَرَۙ ۝٢٣', 'وَمَا تَشَاۤءُوْنَ اِلَّآ اَنْ يَّشَاۤءَ اللّٰهُ رَبُّ الْعٰلَمِيْنَࣖ ۝٢٩اِذَا السَّمَاۤءُ انْفَطَرَتْۙ ۝١ وَاِذَا الْكَوَاكِبُ انْتَثَرَتْۙ ۝٢وَاِذَا الْبِحَارُ فُجِّرَتْۙ ۝٣وَاِذَا الْقُبُوْرُ بُعْثِرَتْۙ ۝٤عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(27, 12, 'يَوْمَ يَقُوْمُ الرُّوْحُ وَالْمَلٰۤىِٕكَةُ صَفًّاۙ لَّا يَتَكَلَّمُوْنَ اِلَّا مَنْ اَذِنَ لَهُ الرَّحْمٰنُ وَقَالَ صَوَابًا ۝٣٨ذٰلِكَ الْيَوْمُ الْحَقُّۚ فَمَنْ شَاۤءَ اتَّخَذَ اِلٰى رَبِّهٖ مَاٰبًا ۝٣٩اِنَّآ اَنْذَرْنٰكُمْ عَذَابًا قَرِيْبًا ەۙ يَّوْمَ يَنْظُرُ الْمَرْءُ مَا قَدَّمَتْ يَدَاهُ وَيَقُوْلُ الْكٰفِرُ يٰلَيْتَنِيْ كُنْتُ تُرٰبًاࣖ ۝٤٠', 'وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠فَذَكِّرْۗ اِنَّمَآ اَنْتَ مُذَكِّرٌۙ ۝٢١لَّسْتَ عَلَيْهِمْ بِمُصَيْطِرٍۙ ۝٢٢اِلَّا مَنْ تَوَلّٰى وَكَفَرَۙ ۝٢٣فَيُعَذِّبُهُ اللّٰهُ الْعَذَابَ الْاَكْبَرَۗ ۝٢٤اِنَّ اِلَيْنَآ اِيَابَهُمْ ۝٢٥ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦', 'اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ لَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍࣖ ۝٢٥وَالسَّمَاۤءِ ذَاتِ الْبُرُوْجِۙ ۝١وَالْيَوْمِ الْمَوْعُوْدِۙ ۝٢وَشَاهِدٍ وَّمَشْهُوْدٍۗ ۝٣قُتِلَ اَصْحٰبُ الْاُخْدُوْدِۙ ۝٤النَّارِ ذَاتِ الْوَقُوْدِۙ ۝٥اِذْ هُمْ عَلَيْهَا قُعُوْدٌۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(28, 13, 'اَمَّا مَنِ اسْتَغْنٰىۙ ۝٥فَاَنْتَ لَهٗ تَصَدّٰىۗ ۝٦وَمَا عَلَيْكَ اَلَّا يَزَّكّٰىۗ ۝٧وَاَمَّا مَنْ جَاۤءَكَ يَسْعٰىۙ ۝٨وَهُوَ يَخْشٰىۙ ۝٩فَاَنْتَ عَنْهُ تَلَهّٰىۚ ۝١٠كَلَّآ اِنَّهَا تَذْكِرَةٌۚ ۝١١فَمَنْ شَاۤءَ ذَكَرَهٗۘ ۝١٢فِيْ صُحُفٍ مُّكَرَّمَةٍۙ ۝١٣مَّرْفُوْعَةٍ مُّطَهَّرَةٍ ۢۙ ۝١٤بِاَيْدِيْ سَفَرَةٍۙ ۝١٥كِرَامٍ ۢ بَرَرَةٍۗ ۝١٦', 'وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣يَقُوْلُ يٰلَيْتَنِيْ قَدَّمْتُ لِحَيَاتِيْۚ ۝٢٤فَيَوْمَىِٕذٍ لَّا يُعَذِّبُ عَذَابَهٗٓ اَحَدٌۙ ۝٢٥وَّلَا يُوْثِقُ وَثَاقَهٗٓ اَحَدٌۗ ۝٢٦يٰٓاَيَّتُهَا النَّفْسُ الْمُطْمَىِٕنَّةُۙ ۝٢٧ارْجِعِيْٓ اِلٰى رَبِّكِ رَاضِيَةً مَّرْضِيَّةًۚ ۝٢٨', 'فَمَهِّلِ الْكٰفِرِيْنَ اَمْهِلْهُمْ رُوَيْدًاࣖ ۝١٧سَبِّحِ اسْمَ رَبِّكَ الْاَعْلَىۙ ۝١الَّذِيْ خَلَقَ فَسَوّٰىۖ ۝٢وَالَّذِيْ قَدَّرَ فَهَدٰىۖ ۝٣وَالَّذِيْٓ اَخْرَجَ الْمَرْعٰىۖ ۝٤فَجَعَلَهٗ غُثَاۤءً اَحْوٰىۖ ۝٥سَنُقْرِئُكَ فَلَا تَنْسٰىٓۖ ۝٦اِلَّا مَا شَاۤءَ اللّٰهُۗ اِنَّهٗ يَعْلَمُ الْجَهْرَ وَمَا يَخْفٰىۗ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(29, 14, 'وَاَمَّا مَنْ جَاۤءَكَ يَسْعٰىۙ ۝٨وَهُوَ يَخْشٰىۙ ۝٩فَاَنْتَ عَنْهُ تَلَهّٰىۚ ۝١٠كَلَّآ اِنَّهَا تَذْكِرَةٌۚ ۝١١فَمَنْ شَاۤءَ ذَكَرَهٗۘ ۝١٢فِيْ صُحُفٍ مُّكَرَّمَةٍۙ ۝١٣مَّرْفُوْعَةٍ مُّطَهَّرَةٍ ۢۙ ۝١٤بِاَيْدِيْ سَفَرَةٍۙ ۝١٥كِرَامٍ ۢ بَرَرَةٍۗ ۝١٦قُتِلَ الْاِنْسَانُ مَآ اَكْفَرَهٗۗ ۝١٧مِنْ اَيِّ شَيْءٍ خَلَقَهٗۗ ۝١٨مِنْ نُّطْفَةٍۗ خَلَقَهٗ فَقَدَّرَهٗۗ ۝١٩', 'يَقُوْلُ يٰلَيْتَنِيْ قَدَّمْتُ لِحَيَاتِيْۚ ۝٢٤فَيَوْمَىِٕذٍ لَّا يُعَذِّبُ عَذَابَهٗٓ اَحَدٌۙ ۝٢٥وَّلَا يُوْثِقُ وَثَاقَهٗٓ اَحَدٌۗ ۝٢٦يٰٓاَيَّتُهَا النَّفْسُ الْمُطْمَىِٕنَّةُۙ ۝٢٧ارْجِعِيْٓ اِلٰى رَبِّكِ رَاضِيَةً مَّرْضِيَّةًۚ ۝٢٨فَادْخُلِيْ فِيْ عِبٰدِيْۙ ۝٢٩وَادْخُلِيْ جَنَّتِيْࣖ ۝٣٠', 'ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦وَالْفَجْرِۙ ۝١وَلَيَالٍ عَشْرٍۙ ۝٢وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦ اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(30, 15, 'قُتِلَ الْاِنْسَانُ مَآ اَكْفَرَهٗۗ ۝١٧مِنْ اَيِّ شَيْءٍ خَلَقَهٗۗ ۝١٨مِنْ نُّطْفَةٍۗ خَلَقَهٗ فَقَدَّرَهٗۗ ۝١٩ثُمَّ السَّبِيْلَ يَسَّرَهٗۙ ۝٢٠ثُمَّ اَمَاتَهٗ فَاَقْبَرَهٗۙ ۝٢١ثُمَّ اِذَا شَاۤءَ اَنْشَرَهٗۗ ۝٢٢كَلَّا لَمَّا يَقْضِ مَآ اَمَرَهٗۗ ۝٢٣فَلْيَنْظُرِ الْاِنْسَانُ اِلٰى طَعَامِهٖٓۙ ۝٢٤اَنَّا صَبَبْنَا الْمَاۤءَ صَبًّاۙ ۝٢٥', 'وَالَّيْلِ اِذَا يَغْشٰىۙ ۝١وَالنَّهَارِ اِذَا تَجَلّٰىۙ ۝٢وَمَا خَلَقَ الذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١', 'وَلَا يَخَافُ عُقْبٰهَاࣖ ۝١٥وَالَّيْلِ اِذَا يَغْشٰىۙ ۝١وَالنَّهَارِ اِذَا تَجَلّٰىۙ ۝٢وَمَا خَلَقَ الذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(31, 16, 'ثُمَّ السَّبِيْلَ يَسَّرَهٗۙ ۝٢٠ثُمَّ اَمَاتَهٗ فَاَقْبَرَهٗۙ ۝٢١ثُمَّ اِذَا شَاۤءَ اَنْشَرَهٗۗ ۝٢٢كَلَّا لَمَّا يَقْضِ مَآ اَمَرَهٗۗ ۝٢٣فَلْيَنْظُرِ الْاِنْسَانُ اِلٰى طَعَامِهٖٓۙ ۝٢٤اَنَّا صَبَبْنَا الْمَاۤءَ صَبًّاۙ ۝٢٥ثُمَّ شَقَقْنَا الْاَرْضَ شَقًّاۙ ۝٢٦فَاَنْۢبَتْنَا فِيْهَا حَبًّاۙ ۝٢٧وَّعِنَبًا وَّقَضْبًاۙ ۝٢٨وَّزَيْتُوْنًا وَّنَخْلًاۙ ۝٢٩وَّحَدَاۤئِقَ غُلْبًا ۝٣٠', 'اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣', 'هَلْ ثُوِّبَ الْكُفَّارُ مَا كَانُوْا يَفْعَلُوْنَࣖ ۝٣٦اِذَا السَّمَاۤءُ انْشَقَّتْۙ ۝١وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۙ ۝٢وَاِذَا الْاَرْضُ مُدَّتْۙ ۝٣وَاَلْقَتْ مَا فِيْهَا وَتَخَلَّتْۙ ۝٤وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۗ ۝٥يٰٓاَيُّهَا الْاِنْسَانُ اِنَّكَ كَادِحٌ اِلٰى رَبِّكَ كَدْحًا فَمُلٰقِيْهِۚ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(32, 17, 'فَلْيَنْظُرِ الْاِنْسَانُ اِلٰى طَعَامِهٖٓۙ ۝٢٤اَنَّا صَبَبْنَا الْمَاۤءَ صَبًّاۙ ۝٢٥ثُمَّ شَقَقْنَا الْاَرْضَ شَقًّاۙ ۝٢٦فَاَنْۢبَتْنَا فِيْهَا حَبًّاۙ ۝٢٧وَّعِنَبًا وَّقَضْبًاۙ ۝٢٨وَّزَيْتُوْنًا وَّنَخْلًاۙ ۝٢٩وَّحَدَاۤئِقَ غُلْبًا ۝٣٠وَفَاكِهَةً وَّاَبًّا ۝٣١مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٢', 'فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤', 'سَلٰمٌۛ هِيَ حَتّٰى مَطْلَعِ الْفَجْرِࣖ ۝٥لَمْ يَكُنِ الَّذِيْنَ كَفَرُوْا مِنْ اَهْلِ الْكِتٰبِ وَالْمُشْرِكِيْنَ مُنْفَكِّيْنَ حَتّٰى تَأْتِيَهُمُ الْبَيِّنَةُۙ ۝١رَسُوْلٌ مِّنَ اللّٰهِ يَتْلُوْا صُحُفًا مُّطَهَّرَةًۙ ۝٢فِيْهَا كُتُبٌ قَيِّمَةٌۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(33, 18, 'فَاَنْۢبَتْنَا فِيْهَا حَبًّاۙ ۝٢٧وَّعِنَبًا وَّقَضْبًاۙ ۝٢٨وَّزَيْتُوْنًا وَّنَخْلًاۙ ۝٢٩وَّحَدَاۤئِقَ غُلْبًا ۝٣٠وَفَاكِهَةً وَّاَبًّا ۝٣١مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٢فَاِذَا جَاۤءَتِ الصَّاۤخَّةُۖ ۝٣٣يَوْمَ يَفِرُّ الْمَرْءُ مِنْ اَخِيْهِۙ ۝٣٤وَاُمِّهٖ وَاَبِيْهِۙ ۝٣٥وَصَاحِبَتِهٖ وَبَنِيْهِۗ ۝٣٦لِكُلِّ امْرِئٍ مِّنْهُمْ يَوْمَىِٕذٍ شَأْنٌ يُّغْنِيْهِۗ ۝٣٧', 'وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤لَا يَصْلٰىهَآ اِلَّا الْاَشْقَىۙ ۝١٥الَّذِيْ كَذَّبَ وَتَوَلّٰىۗ ۝١٦', 'جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ خٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْ خَشِيَ رَبَّهٗࣖ ۝٨اِذَا زُلْزِلَتِ الْاَرْضُ زِلْزَالَهَاۙ ۝١وَاَخْرَجَتِ الْاَرْضُ اَثْقَالَهَاۙ ۝٢', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(34, 19, 'وَّحَدَاۤئِقَ غُلْبًا ۝٣٠وَفَاكِهَةً وَّاَبًّا ۝٣١مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٢فَاِذَا جَاۤءَتِ الصَّاۤخَّةُۖ ۝٣٣يَوْمَ يَفِرُّ الْمَرْءُ مِنْ اَخِيْهِۙ ۝٣٤وَاُمِّهٖ وَاَبِيْهِۙ ۝٣٥وَصَاحِبَتِهٖ وَبَنِيْهِۗ ۝٣٦لِكُلِّ امْرِئٍ مِّنْهُمْ يَوْمَىِٕذٍ شَأْنٌ يُّغْنِيْهِۗ ۝٣٧وُجُوْهٌ يَّوْمَىِٕذٍ مُّسْفِرَةٌۙ ۝٣٨ضَاحِكَةٌ مُّسْتَبْشِرَةٌۚ ۝٣٩', 'فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤لَا يَصْلٰىهَآ اِلَّا الْاَشْقَىۙ ۝١٥الَّذِيْ كَذَّبَ وَتَوَلّٰىۗ ۝١٦وَسَيُجَنَّبُهَا الْاَتْقَىۙ ۝١٧الَّذِيْ يُؤْتِيْ مَالَهٗ يَتَزَكّٰىۚ ۝١٨', 'اِنَّ رَبَّهُمْ بِهِمْ يَوْمَىِٕذٍ لَّخَبِيْرٌࣖ ۝١١اَلْقَارِعَةُۙ ۝١مَا الْقَارِعَةُۚ ۝٢وَمَآ اَدْرٰىكَ مَا الْقَارِعَةُۗ ۝٣يَوْمَ يَكُوْنُ النَّاسُ كَالْفَرَاشِ الْمَبْثُوْثِۙ ۝٤وَتَكُوْنُ الْجِبَالُ كَالْعِهْنِ الْمَنْفُوْشِۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(35, 20, 'فَاِذَا جَاۤءَتِ الصَّاۤخَّةُۖ ۝٣٣يَوْمَ يَفِرُّ الْمَرْءُ مِنْ اَخِيْهِۙ ۝٣٤وَاُمِّهٖ وَاَبِيْهِۙ ۝٣٥وَصَاحِبَتِهٖ وَبَنِيْهِۗ ۝٣٦لِكُلِّ امْرِئٍ مِّنْهُمْ يَوْمَىِٕذٍ شَأْنٌ يُّغْنِيْهِۗ ۝٣٧وُجُوْهٌ يَّوْمَىِٕذٍ مُّسْفِرَةٌۙ ۝٣٨ضَاحِكَةٌ مُّسْتَبْشِرَةٌۚ ۝٣٩وَوُجُوْهٌ يَّوْمَىِٕذٍ عَلَيْهَا غَبَرَةٌۙ ۝٤٠تَرْهَقُهَا قَتَرَةٌۗ ۝٤١اُولٰۤىِٕكَ هُمُ الْكَفَرَةُ الْفَجَرَةُࣖ ۝٤٢', 'فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤لَا يَصْلٰىهَآ اِلَّا الْاَشْقَىۙ ۝١٥الَّذِيْ كَذَّبَ وَتَوَلّٰىۗ ۝١٦وَسَيُجَنَّبُهَا الْاَتْقَىۙ ۝١٧الَّذِيْ يُؤْتِيْ مَالَهٗ يَتَزَكّٰىۚ ۝١٨وَمَا لِاَحَدٍ عِنْدَهٗ مِنْ نِّعْمَةٍ تُجْزٰىٓۙ ۝١٩اِلَّا ابْتِغَاۤءَ وَجْهِ رَبِّهِ الْاَعْلٰىۚ ۝٢٠وَلَسَوْفَ يَرْضٰىࣖ ۝٢١', 'لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦اِذَا جَاۤءَ نَصْرُ اللّٰهِ وَالْفَتْحُۙ ۝١وَرَاَيْتَ النَّاسَ يَدْخُلُوْنَ فِيْ دِيْنِ اللّٰهِ اَفْوَاجًاۙ ۝٢فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(36, 21, 'اِذَا السَّمَاۤءُ انْفَطَرَتْۙ ۝١وَاِذَا الْكَوَاكِبُ انْتَثَرَتْۙ ۝٢وَاِذَا الْبِحَارُ فُجِّرَتْۙ ۝٣وَاِذَا الْقُبُوْرُ بُعْثِرَتْۙ ۝٤عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥‘يٰٓاَيُّهَا الْاِنْسَانُ مَا غَرَّكَ بِرَبِّكَ الْكَرِيْمِۙ ۝٦الَّذِيْ خَلَقَكَ فَسَوّٰىكَ فَعَدَلَكَۙ ۝٧', 'لَآ اُقْسِمُ بِهٰذَا الْبَلَدِۙ ۝١وَاَنْتَ حِلٌّۢ بِهٰذَا الْبَلَدِۙ ۝٢وَوَالِدٍ وَّمَا وَلَدَۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْ كَبَدٍۗ ۝٤اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩', 'اِنَّآ اَنْذَرْنٰكُمْ عَذَابًا قَرِيْبًا ەۙ يَّوْمَ يَنْظُرُ الْمَرْءُ مَا قَدَّمَتْ يَدَاهُ وَيَقُوْلُ الْكٰفِرُ يٰلَيْتَنِيْ كُنْتُ تُرٰبًاࣖ ۝٤٠وَالنّٰزِعٰتِ غَرْقًاۙ ۝١وَّالنّٰشِطٰتِ نَشْطًاۙ ۝٢وَّالسّٰبِحٰتِ سَبْحًاۙ ۝٣فَالسّٰبِقٰتِ سَبْقًاۙ ۝٤فَالْمُدَبِّرٰتِ اَمْرًاۘ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(37, 22, 'وَاِذَا الْبِحَارُ فُجِّرَتْۙ ۝٣وَاِذَا الْقُبُوْرُ بُعْثِرَتْۙ ۝٤عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥يٰٓاَيُّهَا الْاِنْسَانُ مَا غَرَّكَ بِرَبِّكَ الْكَرِيْمِۙ ۝٦الَّذِيْ خَلَقَكَ فَسَوّٰىكَ فَعَدَلَكَۙ ۝٧فِيْٓ اَيِّ صُوْرَةٍ مَّا شَاۤءَ رَكَّبَكَۗ ۝٨كَلَّا بَلْ تُكَذِّبُوْنَ بِالدِّيْنِۙ ۝٩', 'وَوَالِدٍ وَّمَا وَلَدَۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْ كَبَدٍۗ ۝٤اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠', 'كَاَنَّهُمْ يَوْمَ يَرَوْنَهَا لَمْ يَلْبَثُوْٓا اِلَّا عَشِيَّةً اَوْ ضُحٰىهَاࣖ ۝٤٦عَبَسَ وَتَوَلّٰىٓۙ ۝١اَنْ جَاۤءَهُ الْاَعْمٰىۗ ۝٢وَمَا يُدْرِيْكَ لَعَلَّهٗ يَزَّكّٰىٓۙ ۝٣اَوْ يَذَّكَّرُ فَتَنْفَعَهُ الذِّكْرٰىۗ ۝٤اَمَّا مَنِ اسْتَغْنٰىۙ ۝٥فَاَنْتَ لَهٗ تَصَدّٰىۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(38, 23, 'عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥يٰٓاَيُّهَا الْاِنْسَانُ مَا غَرَّكَ بِرَبِّكَ الْكَرِيْمِۙ ۝٦الَّذِيْ خَلَقَكَ فَسَوّٰىكَ فَعَدَلَكَۙ ۝٧فِيْٓ اَيِّ صُوْرَةٍ مَّا شَاۤءَ رَكَّبَكَۗ ۝٨كَلَّا بَلْ تُكَذِّبُوْنَ بِالدِّيْنِۙ ۝٩وَاِنَّ عَلَيْكُمْ لَحٰفِظِيْنَۙ ۝١٠كِرَامًا كٰتِبِيْنَۙ ۝١١', 'اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢', 'اُولٰۤىِٕكَ هُمُ الْكَفَرَةُ الْفَجَرَةُࣖ ۝٤٢اِذَا الشَّمْسُ كُوِّرَتْۖ ۝١وَاِذَا النُّجُوْمُ انْكَدَرَتْۖ ۝٢وَاِذَا الْجِبَالُ سُيِّرَتْۖ ۝٣وَاِذَا الْعِشَارُ عُطِّلَتْۖ ۝٤وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(39, 24, 'يٰٓاَيُّهَا الْاِنْسَانُ مَا غَرَّكَ بِرَبِّكَ الْكَرِيْمِۙ ۝٦الَّذِيْ خَلَقَكَ فَسَوّٰىكَ فَعَدَلَكَۙ ۝٧فِيْٓ اَيِّ صُوْرَةٍ مَّا شَاۤءَ رَكَّبَكَۗ ۝٨كَلَّا بَلْ تُكَذِّبُوْنَ بِالدِّيْنِۙ ۝٩وَاِنَّ عَلَيْكُمْ لَحٰفِظِيْنَۙ ۝١٠كِرَامًا كٰتِبِيْنَۙ ۝١١يَعْلَمُوْنَ مَا تَفْعَلُوْنَ ۝١٢', 'اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢فَكُّ رَقَبَةٍۙ ۝١٣اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦', 'وَمَا تَشَاۤءُوْنَ اِلَّآ اَنْ يَّشَاۤءَ اللّٰهُ رَبُّ الْعٰلَمِيْنَࣖ ۝٢٩اِذَا السَّمَاۤءُ انْفَطَرَتْۙ ۝١وَاِذَا الْكَوَاكِبُ انْتَثَرَتْۙ ۝٢وَاِذَا الْبِحَارُ فُجِّرَتْۙ ۝٣وَاِذَا الْقُبُوْرُ بُعْثِرَتْۙ ۝٤عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(40, 25, 'اِنَّ الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ اُولٰۤىِٕكَ هُمْ خَيْرُ الْبَرِيَّةِۗ ۝٧جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُخٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْخَشِيَ رَبَّهٗࣖ ۝٨', 'وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢فَكُّ رَقَبَةٍۙ ۝١٣اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦ثُمَّ كَانَ مِنَ الَّذِيْنَ اٰمَنُوْا وَتَوَاصَوْا بِالصَّبْرِ وَتَوَاصَوْا بِالْمَرْحَمَةِۗ ۝١٧', 'يَوْمَ لَا تَمْلِكُ نَفْسٌ لِّنَفْسٍ شَيْـًٔاۗ وَالْاَمْرُ يَوْمَىِٕذٍ لِّلّٰهِࣖ ۝١٩وَيْلٌ لِّلْمُطَفِّفِيْنَۙ ۝١الَّذِيْنَ اِذَا اكْتَالُوْا عَلَى النَّاسِ يَسْتَوْفُوْنَۖ ۝٢وَاِذَا كَالُوْهُمْ اَوْ وَّزَنُوْهُمْ يُخْسِرُوْنَۗ ۝٣اَلَا يَظُنُّ اُولٰۤىِٕكَ اَنَّهُمْ مَّبْعُوْثُوْنَۙ ۝٤لِيَوْمٍ عَظِيْمٍۙ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(41, 26, 'اِنَّ الْاَبْرَارَ لَفِيْ نَعِيْمٍۙ ۝١٣وَّاِنَّ الْفُجَّارَ لَفِيْ جَحِيْمٍ ۝١٤يَصْلَوْنَهَا يَوْمَ الدِّيْنِ ۝١٥وَمَا هُمْ عَنْهَا بِغَاۤىِٕبِيْنَۗ ۝١٦وَمَآ اَدْرٰىكَ مَا يَوْمُ الدِّيْنِۙ ۝١٧ثُمَّ مَآ اَدْرٰىكَ مَا يَوْمُ الدِّيْنِۗ ۝١٨يَوْمَ لَا تَمْلِكُ نَفْسٌ لِّنَفْسٍ شَيْـًٔاۗ وَالْاَمْرُ يَوْمَىِٕذٍ لِّلّٰهِࣖ ۝١٩', 'فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢فَكُّ رَقَبَةٍۙ ۝١٣اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦ثُمَّ كَانَ مِنَ الَّذِيْنَ اٰمَنُوْا وَتَوَاصَوْا بِالصَّبْروَتَوَاصَوْبِالْمَرْحَمَةِۗ ۝١٧اُولٰۤىِٕكَ اَصْحٰبُ الْمَيْمَنَةِۗ ۝١٨', 'هَلْ ثُوِّبَ الْكُفَّارُ مَا كَانُوْا يَفْعَلُوْنَࣖ ۝٣٦اِذَا السَّمَاۤءُ انْشَقَّتْۙ ۝١وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۙ ۝٢وَاِذَا الْاَرْضُ مُدَّتْۙ ۝٣وَاَلْقَتْ مَا فِيْهَا وَتَخَلَّتْۙ ۝٤وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۗ ۝٥يٰٓاَيُّهَا الْاِنْسَانُ اِنَّكَ كَادِحٌ اِلٰى رَبِّكَ كَدْحًا فَمُلٰقِيْهِۚ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(42, 27, 'وَيْلٌ لِّلْمُطَفِّفِيْنَۙ ۝١الَّذِيْنَ اِذَا اكْتَالُوْا عَلَى النَّاسِ يَسْتَوْفُوْنَۖ ۝٢وَاِذَا كَالُوْهُمْ اَوْ وَّزَنُوْهُمْ يُخْسِرُوْنَۗ ۝٣اَلَا يَظُنُّ اُولٰۤىِٕكَ اَنَّهُمْ مَّبْعُوْثُوْنَۙ ۝٤لِيَوْمٍ عَظِيْمٍۙ ۝٥يَّوْمَ يَقُوْمُ النَّاسُ لِرَبِّ الْعٰلَمِيْنَۗ ۝٦', 'اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦ثُمَّ كَانَ مِنَ الَّذِيْنَ اٰمَنُوْا وَتَوَاصَوْا بِالصَّبْرِ وَتَوَاصَوْبِالْمَرْحَمَةِۗ ۝١٧اُولٰۤىِٕكَ اَصْحٰبُ الْمَيْمَنَةِۗ ۝١٨وَالَّذِيْنَ كَفَرُوْا بِاٰيٰتِنَا هُمْ اَصْحٰبُ الْمَشْئَمَةِۗ ۝١٩عَلَيْهِمْ نَارٌ مُّؤْصَدَةٌࣖ ۝٢٠', 'اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ لَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍࣖ ۝٢٥وَالسَّمَاۤءِ ذَاتِ الْبُرُوْجِۙ ۝١وَالْيَوْمِ الْمَوْعُوْدِۙ ۝٢وَشَاهِدٍ وَّمَشْهُوْدٍۗ ۝٣قُتِلَ اَصْحٰبُ الْاُخْدُوْدِۙ ۝٤النَّارِ ذَاتِ الْوَقُوْدِۙ ۝٥اِذْ هُمْ عَلَيْهَا قُعُوْدٌۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(43, 28, 'وَاِذَا كَالُوْهُمْ اَوْ وَّزَنُوْهُمْ يُخْسِرُوْنَۗ ۝٣اَلَا يَظُنُّ اُولٰۤىِٕكَ اَنَّهُمْ مَّبْعُوْثُوْنَۙ ۝٤لِيَوْمٍ عَظِيْمٍۙ ۝٥يَّوْمَ يَقُوْمُ النَّاسُ لِرَبِّ الْعٰلَمِيْنَۗ ۝٦كَلَّآ اِنَّ كِتٰبَ الْفُجَّارِ لَفِيْ سِجِّيْنٍۗ ۝٧وَمَآ اَدْرٰىكَ مَا سِجِّيْنٌۗ ۝٨كِتٰبٌ مَّرْقُوْمٌۗ ۝٩', 'رَسُوْلٌ مِّنَ اللّٰهِ يَتْلُوْا صُحُفًا مُّطَهَّرَةًۙ ۝٢فِيْهَا كُتُبٌ قَيِّمَةٌۗ ۝٣وَمَا تَفَرَّقَ الَّذِيْنَ اُوْتُوا الْكِتٰبَ اِلَّا مِنْۢ بَعْدِ مَا جَاۤءَتْهُمُالْبَيِّنَةُۗ ۝٤وَمَآ اُمِرُوْٓا اِلَّا لِيَعْبُدُوا اللّٰهَ مُخْلِصِيْنَ لَهُ الدِّيْنَ ەۙ حُنَفَاۤءَوَيُقِيْمُوا الصَّلٰوةَ وَيُؤْتُوا الزَّكٰوةَ وَذٰلِكَ دِيْنُ الْقَيِّمَةِۗ ۝٥', 'فِيْ لَوْحٍ مَّحْفُوْظٍࣖ ۝٢٢وَالسَّمَاۤءِ وَالطَّارِقِۙ ۝١وَمَآ اَدْرٰىكَ مَا الطَّارِقُۙ ۝٢النَّجْمُ الثَّاقِبُۙ ۝٣اِنْ كُلُّ نَفْسٍ لَّمَّا عَلَيْهَا حَافِظٌۗ ۝٤فَلْيَنْظُرِ الْاِنْسَانُ مِمَّ خُلِقَ ۝٥خُلِقَ مِنْ مَّاۤءٍ دَافِقٍۙ ۝٦يَّخْرُجُ مِنْۢ بَيْنِ الصُّلْبِ وَالتَّرَاۤىِٕبِۗ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(44, 29, 'يَّوْمَ يَقُوْمُ النَّاسُ لِرَبِّ الْعٰلَمِيْنَۗ ۝٦كَلَّآ اِنَّ كِتٰبَ الْفُجَّارِ لَفِيْ سِجِّيْنٍۗ ۝٧وَمَآ اَدْرٰىكَ مَا سِجِّيْنٌۗ ۝٨كِتٰبٌ مَّرْقُوْمٌۗ ۝٩وَيْلٌ يَّوْمَىِٕذٍ لِّلْمُكَذِّبِيْنَۙ ۝١٠الَّذِيْنَ يُكَذِّبُوْنَ بِيَوْمِ الدِّيْنِۗ ۝١١وَمَا يُكَذِّبُ بِهٖٓ اِلَّا كُلُّ مُعْتَدٍ اَثِيْمٍۙ ۝١٢اِذَا تُتْلٰى عَلَيْهِ اٰيٰتُنَا قَالَ اَسَاطِيْرُ الْاَوَّلِيْنَۗ ۝١٣', 'وَمَآ اُمِرُوْٓا اِلَّا لِيَعْبُدُوا اللّٰهَ مُخْلِصِيْنَ لَهُ الدِّيْنَ ەۙحُنَفَاۤءوَيُقِيْمُوا الصَّلٰوةَ وَيُؤْتُوا الزَّكٰوةَ وَذٰلِكَ دِيْنُ الْقَيِّمَةِۗ ۝٥اِنَّ الَّذِيْنَ كَفَرُوْا مِنْ اَهْلِ الْكِتٰبِ وَالْمُشْرِكِيْنَ فِيْ نَارِ جَهَنَّمَخٰلِدِيْنَ فِيْهَاۗ اُولٰۤىِٕكَ هُمْ شَرُّ الْبَرِيَّةِۗ ۝٦', 'فَمَهِّلِ الْكٰفِرِيْنَ اَمْهِلْهُمْ رُوَيْدًاࣖ ۝١٧سَبِّحِ اسْمَ رَبِّكَ الْاَعْلَىۙ ۝١الَّذِيْ خَلَقَ فَسَوّٰىۖ ۝٢وَالَّذِيْ قَدَّرَ فَهَدٰىۖ ۝٣وَالَّذِيْٓ اَخْرَجَ الْمَرْعٰىۖ ۝٤فَجَعَلَهٗ غُثَاۤءً اَحْوٰىۖ ۝٥سَنُقْرِئُكَ فَلَا تَنْسٰىٓۖ ۝٦اِلَّا مَا شَاۤءَ اللّٰهُۗ اِنَّهٗ يَعْلَمُ الْجَهْرَ وَمَا يَخْفٰىۗ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(45, 30, 'كَلَّآ اِنَّ كِتٰبَ الْفُجَّارِ لَفِيْ سِجِّيْنٍۗ ۝٧وَمَآ اَدْرٰىكَ مَا سِجِّيْنٌۗ ۝٨كِتٰبٌ مَّرْقُوْمٌۗ ۝٩وَيْلٌ يَّوْمَىِٕذٍ لِّلْمُكَذِّبِيْنَۙ ۝١٠الَّذِيْنَ يُكَذِّبُوْنَ بِيَوْمِ الدِّيْنِۗ ۝١١وَمَا يُكَذِّبُ بِهٖٓ اِلَّا كُلُّ مُعْتَدٍ اَثِيْمٍۙ ۝١٢اِذَا تُتْلٰى عَلَيْهِ اٰيٰتُنَا قَالَ اَسَاطِيْرُ الْاَوَّلِيْنَۗ ۝١٣كَلَّا بَلْࣝ رَانَ عَلٰى قُلُوْبِهِمْ مَّا كَانُوْا يَكْسِبُوْنَ ۝١٤', 'اِنَّ الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ اُولٰۤىِٕكَ هُمْ خَيْرُ الْبَرِيَّةِۗ ۝٧جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُخٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْخَشِيَ رَبَّهٗࣖ ۝٨', 'صُحُفِ اِبْرٰهِيْمَ وَمُوْسٰىࣖ ۝١٩هَلْ اَتٰىكَ حَدِيْثُ الْغَاشِيَةِۗ ۝١وُجُوْهٌ يَّوْمَىِٕذٍ خَاشِعَةٌۙ ۝٢عَامِلَةٌ نَّاصِبَةٌۙ ۝٣تَصْلٰى نَارًا حَامِيَةًۙ ۝٤تُسْقٰى مِنْ عَيْنٍ اٰنِيَةٍۗ ۝٥لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(46, 31, 'وَيْلٌ يَّوْمَىِٕذٍ لِّلْمُكَذِّبِيْنَۙ ۝١٠الَّذِيْنَ يُكَذِّبُوْنَ بِيَوْمِ الدِّيْنِۗ ۝١١وَمَا يُكَذِّبُ بِهٖٓ اِلَّا كُلُّ مُعْتَدٍ اَثِيْمٍۙ ۝١٢اِذَا تُتْلٰى عَلَيْهِ اٰيٰتُنَا قَالَ اَسَاطِيْرُ الْاَوَّلِيْنَۗ ۝١٣كَلَّا بَلْࣝ رَانَ عَلٰى قُلُوْبِهِمْ مَّا كَانُوْا يَكْسِبُوْنَ ۝١٤كَلَّآ اِنَّهُمْ عَنْ رَّبِّهِمْ يَوْمَىِٕذٍ لَّمَحْجُوْبُوْنَۗ ۝١٥', 'وَالشَّمْسِ وَضُحٰىهَاۖ ۝١ وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢ وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣ وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤ وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠', 'ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦وَالْفَجْرِۙ ۝١وَلَيَالٍ عَشْرٍۙ ۝٢وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(47, 32, 'اِذَا تُتْلٰى عَلَيْهِ اٰيٰتُنَا قَالَ اَسَاطِيْرُ الْاَوَّلِيْنَۗ ۝١٣كَلَّا بَلْࣝ رَانَ عَلٰى قُلُوْبِهِمْ مَّا كَانُوْا يَكْسِبُوْنَ ۝١٤كَلَّآ اِنَّهُمْ عَنْ رَّبِّهِمْ يَوْمَىِٕذٍ لَّمَحْجُوْبُوْنَۗ ۝١٥ثُمَّ اِنَّهُمْ لَصَالُوا الْجَحِيْمِۗ ۝١٦ثُمَّ يُقَالُ هٰذَا الَّذِيْ كُنْتُمْ بِهٖ تُكَذِّبُوْنَۗ ۝١٧كَلَّآ اِنَّ كِتٰبَ الْاَبْرَارِ لَفِيْ عِلِّيِّيْنَۗ ۝١٨', 'وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣ وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤ وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١', 'وَادْخُلِيْ جَنَّتِيْࣖ ۝٣٠لَآ اُقْسِمُ بِهٰذَا الْبَلَدِۙ ۝١وَاَنْتَ حِلٌّۢ بِهٰذَا الْبَلَدِۙ ۝٢وَوَالِدٍ وَّمَا وَلَدَۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْ كَبَدٍۗ ۝٤اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(48, 33, 'كَلَّآ اِنَّهُمْ عَنْ رَّبِّهِمْ يَوْمَىِٕذٍ لَّمَحْجُوْبُوْنَۗ ۝١٥ثُمَّ اِنَّهُمْ لَصَالُوا الْجَحِيْمِۗ ۝١٦ثُمَّ يُقَالُ هٰذَا الَّذِيْ كُنْتُمْ بِهٖ تُكَذِّبُوْنَۗ ۝١٧كَلَّآ اِنَّ كِتٰبَ الْاَبْرَارِ لَفِيْ عِلِّيِّيْنَۗ ۝١٨وَمَآ اَدْرٰىكَ مَا عِلِّيُّوْنَۗ ۝١٩كِتٰبٌ مَّرْقُوْمٌۙ ۝٢٠يَّشْهَدُهُ الْمُقَرَّبُوْنَۗ ۝٢١', 'وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢', 'عَلَيْهِمْ نَارٌ مُّؤْصَدَةٌࣖ ۝٢٠وَالشَّمْسِ وَضُحٰىهَاۖ ۝١وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(49, 34, 'ثُمَّ يُقَالُ هٰذَا الَّذِيْ كُنْتُمْ بِهٖ تُكَذِّبُوْنَۗ ۝١٧كَلَّآ اِنَّ كِتٰبَ الْاَبْرَارِ لَفِيْ عِلِّيِّيْنَۗ ۝١٨وَمَآ اَدْرٰىكَ مَا عِلِّيُّوْنَۗ ۝١٩كِتٰبٌ مَّرْقُوْمٌۙ ۝٢٠يَّشْهَدُهُ الْمُقَرَّبُوْنَۗ ۝٢١اِنَّ الْاَبْرَارَ لَفِيْ نَعِيْمٍۙ ۝٢٢عَلَى الْاَرَاۤىِٕكِ يَنْظُرُوْنَۙ ۝٢٣تَعْرِفُ فِيْ وُجُوْهِهِمْ نَضْرَةَ النَّعِيْمِۚ ۝٢٤', 'وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢ فَقَالَ لَهُمْ رَسُوْلُ اللّٰهِ نَاقَةَ اللّٰهِ وَسُقْيٰهَاۗ ۝١٣', 'وَلَا يَخَافُ عُقْبٰهَاࣖ ۝١٥وَالَّيْلِ اِذَا يَغْشٰىۙ ۝١وَالنَّهَارِ اِذَا تَجَلّٰىۙ ۝٢وَمَا خَلَقَ الذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(50, 35, 'كَلَّآ اِنَّ كِتٰبَ الْاَبْرَارِ لَفِيْ عِلِّيِّيْنَۗ ۝١٨وَمَآ اَدْرٰىكَ مَا عِلِّيُّوْنَۗ ۝١٩كِتٰبٌ مَّرْقُوْمٌۙ ۝٢٠يَّشْهَدُهُ الْمُقَرَّبُوْنَۗ ۝٢١اِنَّ الْاَبْرَارَ لَفِيْ نَعِيْمٍۙ ۝٢٢عَلَى الْاَرَاۤىِٕكِ يَنْظُرُوْنَۙ ۝٢٣تَعْرِفُ فِيْ وُجُوْهِهِمْ نَضْرَةَ النَّعِيْمِۚ ۝٢٤يُسْقَوْنَ مِنْ رَّحِيْقٍ مَّخْتُوْمٍۙ ۝٢٥', 'وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢ فَقَالَ لَهُمْ رَسُوْلُ اللّٰهِ نَاقَةَ اللّٰهِ وَسُقْيٰهَاۗ ۝١٣ فَكَذَّبُوْهُ فَعَقَرُوْهَاۖ فَدَمْدَمَ عَلَيْهِمْ رَبُّهُمْ بِذَنْۢبِهِمْ فَسَوّٰىهَاۖ ۝١٤ وَلَا يَخَافُ عُقْبٰهَاࣖ ۝١٥', 'وَلَسَوْفَ يَرْضٰىࣖ ۝٢١وَالضُّحٰىۙ ۝١وَالَّيْلِ اِذَا سَجٰىۙ ۝٢مَا وَدَّعَكَ رَبُّكَ وَمَا قَلٰىۗ ۝٣وَلَلْاٰخِرَةُ خَيْرٌ لَّكَ مِنَ الْاُوْلٰىۗ ۝٤وَلَسَوْفَ يُعْطِيْكَ رَبُّكَ فَتَرْضٰىۗ ۝٥اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(51, 36, 'اِنَّ الْاَبْرَارَ لَفِيْ نَعِيْمٍۙ ۝٢٢عَلَى الْاَرَاۤىِٕكِ يَنْظُرُوْنَۙ ۝٢٣تَعْرِفُ فِيْ وُجُوْهِهِمْ نَضْرَةَ النَّعِيْمِۚ ۝٢٤يُسْقَوْنَ مِنْ رَّحِيْقٍ مَّخْتُوْمٍۙ ۝٢٥خِتٰمُهٗ مِسْكٌۗ وَفِيْ ذٰلِكَ فَلْيَتَنَافَسِ الْمُتَنٰفِسُوْنَۗ ۝٢٦وَمِزَاجُهٗ مِنْ تَسْنِيْمٍۙ ۝٢٧عَيْنًا يَّشْرَبُ بِهَا الْمُقَرَّبُوْنَۗ ۝٢٨', 'وَطُوْرِ سِيْنِيْنَۙ ۝٢ وَهٰذَا الْبَلَدِ الْاَمِيْنِۙ ۝٣ لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْٓ اَحْسَنِ تَقْوِيْمٍۖ ۝٤ ثُمَّ رَدَدْنٰهُ اَسْفَلَ سٰفِلِيْنَۙ ۝٥ اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ فَلَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍۗ ۝٦ فَمَا يُكَذِّبُكَ بَعْدُ بِالدِّيْنِۗ ۝٧ اَلَيْسَ اللّٰهُ بِاَحْكَمِ الْحٰكِمِيْنَࣖ ۝٨', 'وَاَمَّا بِنِعْمَةِ رَبِّكَ فَحَدِّثْࣖ ۝١١اَلَمْ نَشْرَحْ لَكَ صَدْرَكَۙ ۝١وَوَضَعْنَا عَنْكَ وِزْرَكَۙ ۝٢الَّذِيْٓ اَنْقَضَ ظَهْرَكَۙ ۝٣وَرَفَعْنَا لَكَ ذِكْرَكَۗ ۝٤فَاِنَّ مَعَ الْعُسْرِ يُسْرًاۙ ۝٥اِنَّ مَعَ الْعُسْرِ يُسْرًاۗ ۝٦فَاِذَا فَرَغْتَ فَانْصَبْۙ ۝٧وَاِلٰى رَبِّكَ فَارْغَبْࣖ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(52, 37, 'تَعْرِفُ فِيْ وُجُوْهِهِمْ نَضْرَةَ النَّعِيْمِۚ ۝٢٤يُسْقَوْنَ مِنْ رَّحِيْقٍ مَّخْتُوْمٍۙ ۝٢٥خِتٰمُهٗ مِسْكٌۗ وَفِيْ ذٰلِكَ فَلْيَتَنَافَسِ الْمُتَنٰفِسُوْنَۗ ۝٢٦وَمِزَاجُهٗ مِنْ تَسْنِيْمٍۙ ۝٢٧عَيْنًا يَّشْرَبُ بِهَا الْمُقَرَّبُوْنَۗ ۝٢٨اِنَّ الَّذِيْنَ اَجْرَمُوْا كَانُوْا مِنَ الَّذِيْنَ اٰمَنُوْا يَضْحَكُوْنَۖ ۝٢٩', 'اِقْرَأْ وَرَبُّكَ الْاَكْرَمُۙ ۝٣ الَّذِيْ عَلَّمَ بِالْقَلَمِۙ ۝٤ عَلَّمَ الْاِنْسَانَ مَا لَمْ يَعْلَمْۗ ۝٥ كَلَّآ اِنَّ الْاِنْسَانَ لَيَطْغٰىٓۙ ۝٦ اَنْ رَّاٰهُ اسْتَغْنٰىۗ ۝٧ اِنَّ اِلٰى رَبِّكَ الرُّجْعٰىۗ ۝٨ اَرَاَيْتَ الَّذِيْ يَنْهٰىۙ ۝٩ عَبْدًا اِذَا صَلّٰىۗ ۝١٠ اَرَاَيْتَ اِنْ كَانَ عَلَى الْهُدٰىٓۙ ۝١١ اَوْ اَمَرَ بِالتَّقْوٰىۗ ۝١٢ اَرَاَيْتَ اِنْ كَذَّبَ وَتَوَلّٰىۗ ۝١٣', 'وَاِلٰى رَبِّكَ فَارْغَبْࣖ ۝٨وَالتِّيْنِ وَالزَّيْتُوْنِۙ ۝١وَطُوْرِ سِيْنِيْنَۙ ۝٢وَهٰذَا الْبَلَدِ الْاَمِيْنِۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْٓ اَحْسَنِ تَقْوِيْمٍۖ ۝٤ثُمَّ رَدَدْنٰهُ اَسْفَلَ سٰفِلِيْنَۙ ۝٥اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ فَلَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(53, 38, 'خِتٰمُهٗ مِسْكٌۗ وَفِيْ ذٰلِكَ فَلْيَتَنَافَسِ الْمُتَنٰفِسُوْنَۗ ۝٢٦وَمِزَاجُهٗ مِنْ تَسْنِيْمٍۙ ۝٢٧عَيْنًا يَّشْرَبُ بِهَا الْمُقَرَّبُوْنَۗ ۝٢٨اِنَّ الَّذِيْنَ اَجْرَمُوْا كَانُوْا مِنَ الَّذِيْنَ اٰمَنُوْا يَضْحَكُوْنَۖ ۝٢٩وَاِذَا مَرُّوْا بِهِمْ يَتَغَامَزُوْنَۖ ۝٣٠', 'عَلَّمَ الْاِنْسَانَ مَا لَمْ يَعْلَمْۗ ۝٥ كَلَّآ اِنَّ الْاِنْسَانَ لَيَطْغٰىٓۙ ۝٦ اَنْ رَّاٰهُ اسْتَغْنٰىۗ ۝٧ اِنَّ اِلٰى رَبِّكَ الرُّجْعٰىۗ ۝٨ اَرَاَيْتَ الَّذِيْ يَنْهٰىۙ ۝٩ عَبْدًا اِذَا صَلّٰىۗ ۝١٠ اَرَاَيْتَ اِنْ كَانَ عَلَى الْهُدٰىٓۙ ۝١١ اَوْ اَمَرَ بِالتَّقْوٰىۗ ۝١٢ اَرَاَيْتَ اِنْ كَذَّبَ وَتَوَلّٰىۗ ۝١٣ اَلَمْ يَعْلَمْ بِاَنَّ اللّٰهَ يَرٰىۗ ۝١٤', 'اَلَيْسَ اللّٰهُ بِاَحْكَمِ الْحٰكِمِيْنَࣖ ۝٨اِقْرَأْ بِاسْمِ رَبِّكَ الَّذِيْ خَلَقَۚ ۝١خَلَقَ الْاِنْسَانَ مِنْ عَلَقٍۚ ۝٢اِقْرَأْ وَرَبُّكَ الْاَكْرَمُۙ ۝٣الَّذِيْ عَلَّمَ بِالْقَلَمِۙ ۝٤عَلَّمَ الْاِنْسَانَ مَا لَمْ يَعْلَمْۗ ۝٥كَلَّآ اِنَّ الْاِنْسَانَ لَيَطْغٰىٓۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(54, 39, 'اِنَّ الَّذِيْنَ اَجْرَمُوْا كَانُوْا مِنَ الَّذِيْنَ اٰمَنُوْا يَضْحَكُوْنَۖ ۝٢٩وَاِذَا مَرُّوْا بِهِمْ يَتَغَامَزُوْنَۖ ۝٣٠وَاِذَا انْقَلَبُوْٓا اِلٰٓى اَهْلِهِمُ انْقَلَبُوْا فَكِهِيْنَۖ ۝٣١وَاِذَا رَاَوْهُمْ قَالُوْٓا اِنَّ هٰٓؤُلَاۤءِ لَضَاۤلُّوْنَۙ ۝٣٢وَمَآ اُرْسِلُوْا عَلَيْهِمْ حٰفِظِيْنَۗ ۝٣٣', 'اَنْ رَّاٰهُ اسْتَغْنٰىۗ ۝٧ اِنَّ اِلٰى رَبِّكَ الرُّجْعٰىۗ ۝٨ اَرَاَيْتَ الَّذِيْ يَنْهٰىۙ ۝٩ عَبْدًا اِذَا صَلّٰىۗ ۝١٠ اَرَاَيْتَ اِنْ كَانَ عَلَى الْهُدٰىٓۙ ۝١١ اَوْ اَمَرَ بِالتَّقْوٰىۗ ۝١٢ اَرَاَيْتَ اِنْ كَذَّبَ وَتَوَلّٰىۗ ۝١٣ اَلَمْ يَعْلَمْ بِاَنَّ اللّٰهَ يَرٰىۗ ۝١٤ كَلَّا لَىِٕنْ لَّمْ يَنْتَهِ ەۙ لَنَسْفَعًا ۢ بِالنَّاصِيَةِۙ ۝١٥ نَاصِيَةٍ كَاذِبَةٍ خَاطِئَةٍۚ ۝١٦', 'سَلٰمٌۛ هِيَ حَتّٰى مَطْلَعِ الْفَجْرِࣖ ۝٥لَمْ يَكُنِ الَّذِيْنَ كَفَرُوْا مِنْ اَهْلِ الْكِتٰبِ وَالْمُشْرِكِيْنَ مُنْفَكِّيْنَ حَتّٰى تَأْتِيَهُمُ الْبَيِّنَةُۙ ۝١رَسُوْلٌ مِّنَ اللّٰهِ يَتْلُوْا صُحُفًا مُّطَهَّرَةًۙ ۝٢فِيْهَا كُتُبٌ قَيِّمَةٌۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(55, 40, 'وَاِذَا مَرُّوْا بِهِمْ يَتَغَامَزُوْنَۖ ۝٣٠وَاِذَا انْقَلَبُوْٓا اِلٰٓى اَهْلِهِمُ انْقَلَبُوْا فَكِهِيْنَۖ ۝٣١وَاِذَا رَاَوْهُمْ قَالُوْٓا اِنَّ هٰٓؤُلَاۤءِ لَضَاۤلُّوْنَۙ ۝٣٢وَمَآ اُرْسِلُوْا عَلَيْهِمْ حٰفِظِيْنَۗ ۝٣٣فَالْيَوْمَ الَّذِيْنَ اٰمَنُوْا مِنَ الْكُفَّارِ يَضْحَكُوْنَۙ ۝٣٤عَلَى الْاَرَاۤىِٕكِ يَنْظُرُوْنَۗ ۝٣٥هَلْ ثُوِّبَ الْكُفَّارُ مَا كَانُوْا يَفْعَلُوْنَࣖ ۝٣٦', 'وَيَتَجَنَّبُهَا الْاَشْقَىۙ ۝١١ الَّذِيْ يَصْلَى النَّارَ الْكُبْرٰىۚ ۝١٢ ثُمَّ لَا يَمُوْتُ فِيْهَا وَلَا يَحْيٰىۗ ۝١٣ قَدْ اَفْلَحَ مَنْ تَزَكّٰىۙ ۝١٤ وَذَكَرَ اسْمَ رَبِّهٖ فَصَلّٰىۗ ۝١٥ بَلْ تُؤْثِرُوْنَ الْحَيٰوةَ الدُّنْيَاۖ ۝١٦ وَالْاٰخِرَةُ خَيْرٌ وَّاَبْقٰىۗ ۝١٧ اِنَّ هٰذَا لَفِى الصُّحُفِ الْاُوْلٰىۙ ۝١٨ صُحُفِ اِبْرٰهِيْمَ وَمُوْسٰىࣖ ۝١٩', 'جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ خٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْ خَشِيَ رَبَّهٗࣖ ۝٨اِذَا زُلْزِلَتِ الْاَرْضُ زِلْزَالَهَاۙ ۝١وَاَخْرَجَتِ الْاَرْضُ اَثْقَالَهَاۙ ۝٢', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(56, 41, 'فَالسّٰبِقٰتِ سَبْقًاۙ ۝٤فَالْمُدَبِّرٰتِ اَمْرًاۘ ۝٥يَوْمَ تَرْجُفُ الرَّاجِفَةُۙ ۝٦تَتْبَعُهَا الرَّادِفَةُۗ ۝٧قُلُوْبٌ يَّوْمَىِٕذٍ وَّاجِفَةٌۙ ۝٨اَبْصَارُهَا خَاشِعَةٌۘ ۝٩يَقُوْلُوْنَ ءَاِنَّا لَمَرْدُوْدُوْنَ فِى الْحَافِرَةِۗ ۝١٠ءَاِذَا كُنَّا عِظَامًا نَّخِرَةًۗ ۝١١', 'مَا وَدَّعَكَ رَبُّكَ وَمَا قَلٰىۗ ۝٣وَلَلْاٰخِرَةُ خَيْرٌ لَّكَ مِنَ الْاُوْلٰىۗ ۝٤وَلَسَوْفَ يُعْطِيْكَ رَبُّكَ فَتَرْضٰىۗ ۝٥اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦وَوَجَدَكَ ضَاۤلًّا فَهَدٰىۖ ۝٧وَوَجَدَكَ عَاۤىِٕلًا فَاَغْنٰىۗ ۝٨فَاَمَّا الْيَتِيْمَ فَلَا تَقْهَرْۗ ۝٩', 'فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣تَبَّتْ يَدَآ اَبِيْ لَهَبٍوَّتَبَّۗ ۝١مَآ اَغْنٰى عَنْهُ مَالُهٗ وَمَا كَسَبَۗ ۝٢سَيَصْلٰى نَارًا ذَاتَ لَهَبٍۙ ۝٣وَّامْرَاَتُهٗۗ حَمَّالَةَ الْحَطَبِۚ ۝٤فِيْ جِيْدِهَا حَبْلٌ مِّنْ مَّسَدٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(57, 42, 'يَوْمَ تَرْجُفُ الرَّاجِفَةُۙ ۝٦تَتْبَعُهَا الرَّادِفَةُۗ ۝٧قُلُوْبٌ يَّوْمَىِٕذٍ وَّاجِفَةٌۙ ۝٨اَبْصَارُهَا خَاشِعَةٌۘ ۝٩يَقُوْلُوْنَ ءَاِنَّا لَمَرْدُوْدُوْنَ فِى الْحَافِرَةِۗ ۝١٠ءَاِذَا كُنَّا عِظَامًا نَّخِرَةًۗ ۝١١قَالُوْا تِلْكَ اِذًا كَرَّةٌ خَاسِرَةٌۘ ۝١٢فَاِنَّمَا هِيَ زَجْرَةٌ وَّاحِدَةٌۙ ۝١٣', 'وَلَسَوْفَ يُعْطِيْكَ رَبُّكَ فَتَرْضٰىۗ ۝٥اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦وَوَجَدَكَ ضَاۤلًّا فَهَدٰىۖ ۝٧وَوَجَدَكَ عَاۤىِٕلًا فَاَغْنٰىۗ ۝٨فَاَمَّا الْيَتِيْمَ فَلَا تَقْهَرْۗ ۝٩وَاَمَّا السَّاۤىِٕلَ فَلَا تَنْهَرْ ۝١٠وَاَمَّا بِنِعْمَةِ رَبِّكَ فَحَدِّثْ۝١١ࣖ', 'لَكُمْ دِيْنُكُمْ وَلِيَدِيْنِࣖ۝٦اِذَاجَاۤءَنَصْرُ اللّٰهِ وَالْفَتْحُۙ ۝١وَرَاَيْتَ النَّاسَ يَدْخُلُوْنَ فِيْ دِيْنِ اللّٰهِ اَفْوَاجًاۙ ۝٢فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(58, 43, 'قُلُوْبٌ يَّوْمَىِٕذٍ وَّاجِفَةٌۙ ۝٨اَبْصَارُهَا خَاشِعَةٌۘ ۝٩يَقُوْلُوْنَ ءَاِنَّا لَمَرْدُوْدُوْنَ فِى الْحَافِرَةِۗ ۝١٠ءَاِذَا كُنَّا عِظَامًا نَّخِرَةًۗ ۝١١قَالُوْا تِلْكَ اِذًا كَرَّةٌ خَاسِرَةٌۘ ۝١٢فَاِنَّمَا هِيَ زَجْرَةٌ وَّاحِدَةٌۙ ۝١٣فَاِذَا هُمْ بِالسَّاهِرَةِۗ ۝١٤هَلْ اَتٰىكَ حَدِيْثُ مُوْسٰىۘ ۝١٥', 'وَالْعٰدِيٰتِ ضَبْحًاۙ ۝١فَالْمُوْرِيٰتِ قَدْحًاۙ ۝٢فَالْمُغِيْرٰتِ صُبْحًاۙ ۝٣فَاَثَرْنَ بِهٖ نَقْعًاۙ ۝٤فَوَسَطْنَ بِهٖ جَمْعًاۙ ۝٥اِنَّ الْاِنْسَانَ لِرَبِّهٖ لَكَنُوْدٌۚ ۝٦وَاِنَّهٗ عَلٰى ذٰلِكَ لَشَهِيْدٌۚ ۝٧وَاِنَّهٗ لِحُبِّ الْخَيْرِ لَشَدِيْدٌۗ ۝٨', 'اِنَّ شَانِئَكَ هُوَ الْاَبْتَرُࣖ ۝٣قُلْ يٰٓاَيُّهَا الْكٰفِرُوْنَۙ ۝١لَآ اَعْبُدُ مَا تَعْبُدُوْنَۙ ۝٢وَلَآ اَنْتُمْ عٰبِدُوْنَ مَآ اَعْبُدُۚ ۝٣وَلَآ اَنَا۠ عَابِدٌ مَّا عَبَدْتُّمْۙ ۝٤وَلَآ اَنْتُمْ عٰبِدُوْنَ مَآ اَعْبُدُۗ۝٥لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(59, 44, 'ءَاِذَا كُنَّا عِظَامًا نَّخِرَةًۗ ۝١١قَالُوْا تِلْكَ اِذًا كَرَّةٌ خَاسِرَةٌۘ ۝١٢فَاِنَّمَا هِيَ زَجْرَةٌ وَّاحِدَةٌۙ ۝١٣فَاِذَا هُمْ بِالسَّاهِرَةِۗ ۝١٤هَلْ اَتٰىكَ حَدِيْثُ مُوْسٰىۘ ۝١٥اِذْ نَادٰىهُ رَبُّهٗ بِالْوَادِ الْمُقَدَّسِ طُوًىۚ ۝١٦اِذْهَبْ اِلٰى فِرْعَوْنَ اِنَّهٗ طَغٰىۖ ۝١٧', 'فَوَسَطْنَ بِهٖ جَمْعًاۙ ۝٥اِنَّ الْاِنْسَانَ لِرَبِّهٖ لَكَنُوْدٌۚ ۝٦وَاِنَّهٗ عَلٰى ذٰلِكَ لَشَهِيْدٌۚ ۝٧وَاِنَّهٗ لِحُبِّ الْخَيْرِ لَشَدِيْدٌۗ ۝٨۞ اَفَلَا يَعْلَمُ اِذَا بُعْثِرَ مَا فِىالْقُبُوْرِۙ ۝٩وَحُصِّلَ مَا فِى الصُّدُوْرِۙ ۝١٠اِنَّ رَبَّهُمْ بِهِمْ يَوْمَىِٕذٍ لَّخَبِيْرٌࣖ ۝١١', 'الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙ وَّاٰمَنَهُمْ مِّنْ خَوْفٍࣖ ۝٤اَرَءَيْتَالَّذِيْيُكَذِّبُبِالدِّيْنِۗ ۝١فَذٰلِكَالَّذِيْيَدُعُّالْيَتِيْمَۙ۝٢وَلَايَحُضُّ عَلٰىطَعَامِالْمِسْكِيْنِۗ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(60, 45, 'فَاِنَّمَا هِيَ زَجْرَةٌ وَّاحِدَةٌۙ ۝١٣فَاِذَا هُمْ بِالسَّاهِرَةِۗ ۝١٤هَلْ اَتٰىكَ حَدِيْثُ مُوْسٰىۘ ۝١٥اِذْ نَادٰىهُ رَبُّهٗ بِالْوَادِ الْمُقَدَّسِ طُوًىۚ ۝١٦اِذْهَبْ اِلٰى فِرْعَوْنَ اِنَّهٗ طَغٰىۖ ۝١٧فَقُلْ هَلْ لَّكَ اِلٰٓى اَنْ تَزَكّٰىۙ ۝١٨وَاَهْدِيَكَ اِلٰى رَبِّكَ فَتَخْشٰىۚ ۝١٩فَاَرٰىهُ الْاٰيَةَ الْكُبْرٰىۖ ۝٢٠', 'يَوْمَىِٕذٍ تُحَدِّثُ اَخْبَارَهَاۙ ۝٤بِاَنَّ رَبَّكَ اَوْحٰى لَهَاۗ ۝٥يَوْمَىِٕذٍ يَّصْدُرُ النَّاسُ اَشْتَاتًا ەۙ لِّيُرَوْا اَعْمَالَهُمْۗ ۝٦فَمَنْ يَّعْمَلْ مِثْقَالَ ذَرَّةٍ خَيْرًا يَّرَهٗۚ ۝٧وَمَنْ يَّعْمَلْ مِثْقَالَ ذَرَّةٍ شَرًّا يَّرَهٗࣖ ۝٨', 'فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥لِاِيْلٰفِ قُرَيْشٍۙ۝١اٖلٰفِهِمْرِحْلَةَالشِّتَاۤءِوَالصَّيْفِۚ ۝٢فَلْيَعْبُدُوْا رَبَّ هٰذَا الْبَيْتِۙ۝٣الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙوَّاٰمَنَهُمْمِّنْخَوْفٍࣖ ۝٤', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(61, 46, 'هَلْ اَتٰىكَ حَدِيْثُ مُوْسٰىۘ ۝١٥اِذْ نَادٰىهُ رَبُّهٗ بِالْوَادِ الْمُقَدَّسِ طُوًىۚ ۝١٦اِذْهَبْ اِلٰى فِرْعَوْنَ اِنَّهٗ طَغٰىۖ ۝١٧فَقُلْ هَلْ لَّكَ اِلٰٓى اَنْ تَزَكّٰىۙ ۝١٨وَاَهْدِيَكَ اِلٰى رَبِّكَ فَتَخْشٰىۚ ۝١٩فَاَرٰىهُ الْاٰيَةَ الْكُبْرٰىۖ ۝٢٠فَكَذَّبَ وَعَصٰىۖ ۝٢١ثُمَّ اَدْبَرَ يَسْعٰىۖ ۝٢٢فَحَشَرَ فَنَادٰىۖ ۝٢٣', 'وَتَكُوْنُ الْجِبَالُ كَالْعِهْنِ الْمَنْفُوْشِۗ ۝٥فَاَمَّا مَنْ ثَقُلَتْ مَوَازِينُهٗۙ ۝٦فَهُوَ فِيْ عِيْشَةٍ رَّاضِيَةٍۗ ۝٧وَاَمَّا مَنْ خَفَّتْ مَوَازِيْنُهٗۙ ۝٨فَاُمُّهٗ هَاوِيَةٌۗ ۝٩وَمَآ اَدْرٰىكَ مَا هِيَهْۗ ۝١٠نَارٌ حَامِيَةٌࣖ ۝١١', 'فِيْ عَمَدٍ مُّمَدَّدَةٍࣖ ۝٩اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِاَصْحٰبِ الْفِيْلِۗ ۝١اَلَمْ يَجْعَلْ كَيْدَهُمْ فِيْ تَضْلِيْلٍۙ ۝٢وَّاَرْسَلَ عَلَيْهِمْ طَيْرًا اَبَابِيْلَۙ ۝٣تَرْمِيْهِمْ بِحِجَارَةٍ مِّنْ سِجِّيْلٍۙ ۝٤فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(62, 47, 'فَقُلْ هَلْ لَّكَ اِلٰٓى اَنْ تَزَكّٰىۙ ۝١٨وَاَهْدِيَكَ اِلٰى رَبِّكَ فَتَخْشٰىۚ ۝١٩فَاَرٰىهُ الْاٰيَةَ الْكُبْرٰىۖ ۝٢٠فَكَذَّبَ وَعَصٰىۖ ۝٢١ثُمَّ اَدْبَرَ يَسْعٰىۖ ۝٢٢فَحَشَرَ فَنَادٰىۖ ۝٢٣فَقَالَ اَنَا۠ رَبُّكُمُ الْاَعْلٰىۖ ۝٢٤فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦', 'حَتّٰى زُرْتُمُ الْمَقَابِرَۗ ۝٢كَلَّا سَوْفَ تَعْلَمُوْنَۙ ۝٣ثُمَّ كَلَّا سَوْفَ تَعْلَمُوْنَ ۝٤كَلَّا لَوْ تَعْلَمُوْنَ عِلْمَ الْيَقِيْنِۗ ۝٥لَتَرَوُنَّ الْجَحِيْمَۙ ۝٦ثُمَّ لَتَرَوُنَّهَا عَيْنَ الْيَقِيْنِۙ ۝٧ثُمَّ لَتُسْـَٔلُنَّ يَوْمَىِٕذٍ عَنِ النَّعِيْمِࣖ ۝٨', 'اِنَّ رَبَّهُمْبِهِمْيَوْمَىِٕذٍلَّخَبِيْرٌࣖ۝١١اَلْقَارِعَةُۙ ۝١مَا الْقَارِعَةُۚ ۝٢وَمَآ اَدْرٰىكَ مَا الْقَارِعَةُۗ ۝٣يَوْمَ يَكُوْنُالنَّاسُكَالْفَرَاشِ الْمَبْثُوْثِۙ ۝٤وَتَكُوْنُ الْجِبَالُ كَالْعِهْنِ الْمَنْفُوْشِۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(63, 48, 'فَاَرٰىهُ الْاٰيَةَ الْكُبْرٰىۖ ۝٢٠فَكَذَّبَ وَعَصٰىۖ ۝٢١ثُمَّ اَدْبَرَ يَسْعٰىۖ ۝٢٢فَحَشَرَ فَنَادٰىۖ ۝٢٣فَقَالَ اَنَا۠ رَبُّكُمُ الْاَعْلٰىۖ ۝٢٤فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦ءَاَنْتُمْ اَشَدُّ خَلْقًا اَمِ السَّمَاۤءُۚ بَنٰىهَاۗ ۝٢٧رَفَعَ سَمْكَهَا فَسَوّٰىهَاۙ ۝٢٨', 'يٰٓاَيُّهَا الْاِنْسَانُ مَا غَرَّكَ بِرَبِّكَ الْكَرِيْمِۙ ۝٦الَّذِيْ خَلَقَكَ فَسَوّٰىكَ فَعَدَلَكَۙ ۝٧فِيْٓ اَيِّ صُوْرَةٍ مَّا شَاۤءَ رَكَّبَكَۗ ۝٨كَلَّا بَلْ تُكَذِّبُوْنَ بِالدِّيْنِۙ ۝٩وَاِنَّ عَلَيْكُمْ لَحٰفِظِيْنَۙ ۝١٠كِرَامًا كٰتِبِيْنَۙ ۝١١يَعْلَمُوْنَ مَا تَفْعَلُوْنَ ۝١٢', 'وَمَنْيَّعْمَلْمِثْقَالَذَرَّةٍشَرًّايَّرَهٗࣖ۝٨وَالْعٰدِيٰتِضَبْحًاۙ۝١فَالْمُوْرِيٰتِقَدْحًاۙ۝٢فَالْمُغِيْرٰتِ صُبْحًاۙ۝٣فَاَثَرْنَبِهٖنَقْعًاۙ۝٤فَوَسَطْنَ بِهٖ جَمْعًاۙ ۝٥اِنَّ الْاِنْسَانَ لِرَبِّهٖ لَكَنُوْدٌۚ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(64, 49, 'فَكَذَّبَ وَعَصٰىۖ ۝٢١ثُمَّ اَدْبَرَ يَسْعٰىۖ ۝٢٢فَحَشَرَ فَنَادٰىۖ ۝٢فَقَالَ اَنَا۠ رَبُّكُمُ الْاَعْلٰىۖ ۝٢٤فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦ءَاَنْتُمْ اَشَدُّ خَلْقًا اَمِ السَّمَاۤءُۚ بَنٰىهَاۗ ۝٢٧رَفَعَ سَمْكَهَا فَسَوّٰىهَاۙ ۝٢٨وَاَغْطَشَ لَيْلَهَا وَاَخْرَجَ ضُحٰىهَاۖ ۝٢٩', 'فَقُلْ هَلْ لَّكَ اِلٰٓى اَنْ تَزَكّٰىۙ ۝١٨وَاَهْدِيَكَ اِلٰى رَبِّكَ فَتَخْشٰىۚ ۝١٩فَاَرٰىهُ الْاٰيَةَ الْكُبْرٰىۖ ۝٢٠فَكَذَّبَ وَعَصٰىۖ ۝٢١ثُمَّ اَدْبَرَ يَسْعٰىۖ ۝٢٢فَحَشَرَ فَنَادٰىۖ ۝٢٣فَقَالَ اَنَا۠ رَبُّكُمُ الْاَعْلٰىۖ ۝٢٤فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦', 'جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ خٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْ خَشِيَ رَبَّهٗࣖ ۝٨اِذَا زُلْزِلَتِ الْاَرْضُ زِلْزَالَهَاۙ ۝١وَاَخْرَجَتِ الْاَرْضُ اَثْقَالَهَاۙ ۝٢', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(65, 50, 'فَحَشَرَ فَنَادٰىۖ ۝٢٣فَقَالَ اَنَا۠ رَبُّكُمُ الْاَعْلٰىۖ ۝٢٤فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦ءَاَنْتُمْ اَشَدُّ خَلْقًا اَمِ السَّمَاۤءُۚ بَنٰىهَاۗ ۝٢٧رَفَعَ سَمْكَهَا فَسَوّٰىهَاۙ ۝٢٨وَاَغْطَشَ لَيْلَهَا وَاَخْرَجَ ضُحٰىهَاۖ ۝٢٩وَالْاَرْضَ بَعْدَ ذٰلِكَ دَحٰىهَاۗ ۝٣٠', 'وَاَمَّا مَنْ جَاۤءَكَ يَسْعٰىۙ ۝٨وَهُوَ يَخْشٰىۙ ۝٩فَاَنْتَ عَنْهُ تَلَهّٰىۚ ۝١٠كَلَّآ اِنَّهَا تَذْكِرَةٌۚ ۝١١فَمَنْ شَاۤءَ ذَكَرَهٗۘ ۝١٢فِيْ صُحُفٍ مُّكَرَّمَةٍۙ ۝١٣مَّرْفُوْعَةٍ مُّطَهَّرَةٍ ۢۙ ۝١٤بِاَيْدِيْ سَفَرَةٍۙ ۝١٥كِرَامٍ ۢ بَرَرَةٍۗ ۝١٦قُتِلَ الْاِنْسَانُ مَآ اَكْفَرَهٗۗ ۝١٧مِنْ اَيِّ شَيْءٍ خَلَقَهٗۗ ۝١٨مِنْ نُّطْفَةٍۗ خَلَقَهٗ فَقَدَّرَهٗۗ ۝١٩', 'سَلٰمٌۛ هِيَ حَتّٰى مَطْلَعِ الْفَجْرِࣖ ۝٥لَمْ يَكُنِ الَّذِيْنَ كَفَرُوْا مِنْ اَهْلِ الْكِتٰبِ وَالْمُشْرِكِيْنَ مُنْفَكِّيْنَ حَتّٰى تَأْتِيَهُمُ الْبَيِّنَةُۙ ۝١رَسُوْلٌ مِّنَ اللّٰهِيَتْلُوْاصُحُفًا مُّطَهَّرَةًۙ ۝٢فِيْهَا كُتُبٌ قَيِّمَةٌۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(66, 51, 'فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦ءَاَنْتُمْ اَشَدُّ خَلْقًا اَمِ السَّمَاۤءُۚ بَنٰىهَاۗ ۝٢٧رَفَعَ سَمْكَهَا فَسَوّٰىهَاۙ ۝٢٨وَاَغْطَشَ لَيْلَهَا وَاَخْرَجَ ضُحٰىهَاۖ ۝٢٩وَالْاَرْضَ بَعْدَ ذٰلِكَ دَحٰىهَاۗ ۝٣٠اَخْرَجَ مِنْهَا مَاۤءَهَا وَمَرْعٰىهَاۖ ۝٣١', 'وَالْفَجْرِۙ ۝١وَلَيَالٍ عَشْرٍۙ ۝٢وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩', 'اَلَيْسَ اللّٰهُ بِاَحْكَمِ الْحٰكِمِيْنَࣖ۝٨اِقْرَأْ بِاسْمِ رَبِّكَ الَّذِيْخَلَقَۚ۝١خَلَقَالْاِنْسَانَمِنْعَلَقٍۚ۝٢اِقْرَأْ وَرَبُّكَالْاَكْرَمُۙ۝٣الَّذِيْعَلَّمَبِالْقَلَمِۙ۝٤عَلَّمَالْاِنْسَانَ مَا لَمْ يَعْلَمْۗ ۝٥كَلَّآ اِنَّ الْاِنْسَانَ لَيَطْغٰىٓۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24');
INSERT INTO `soal_musabaqoh` (`id`, `no_soal`, `soal1`, `soal2`, `soal3`, `status`, `created_at`, `updated_at`) VALUES
(67, 52, 'اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦ءَاَنْتُمْ اَشَدُّ خَلْقًا اَمِ السَّمَاۤءُۚ بَنٰىهَاۗ ۝٢٧رَفَعَ سَمْكَهَا فَسَوّٰىهَاۙ ۝٢٨وَاَغْطَشَ لَيْلَهَا وَاَخْرَجَ ضُحٰىهَاۖ ۝٢٩وَالْاَرْضَ بَعْدَ ذٰلِكَ دَحٰىهَاۗ ۝٣٠اَخْرَجَ مِنْهَا مَاۤءَهَا وَمَرْعٰىهَاۖ ۝٣١وَالْجِبَالَ اَرْسٰىهَاۙ ۝٣٢مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٣', 'وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١', 'وَاِلٰى رَبِّكَفَارْغَبْࣖ۝٨وَالتِّيْنِوَالزَّيْتُوْنِۙ ۝١وَطُوْرِسِيْنِيْنَۙ۝٢وَهٰذَاالْبَلَدِالْاَمِيْنِۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَفِيْٓاَحْسَنِتَقْوِيْمٍۖ ۝٤ثُمَّ رَدَدْنٰهُاَسْفَلَسٰفِلِيْنَۙ۝٥اِلَّاالَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ فَلَهُمْاَجْرٌغَيْرُ مَمْنُوْنٍۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(68, 53, 'رَفَعَ سَمْكَهَا فَسَوّٰىهَاۙ ۝٢٨وَاَغْطَشَ لَيْلَهَا وَاَخْرَجَ ضُحٰىهَاۖ ۝٢٩وَالْاَرْضَ بَعْدَ ذٰلِكَ دَحٰىهَاۗ ۝٣٠اَخْرَجَ مِنْهَا مَاۤءَهَا وَمَرْعٰىهَاۖ ۝٣١وَالْجِبَالَ اَرْسٰىهَاۙ ۝٣٢مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٣فَاِذَا جَاۤءَتِ الطَّاۤمَّةُ الْكُبْرٰىۖ ۝٣٤يَوْمَ يَتَذَكَّرُ الْاِنْسَانُ مَا سَعٰىۙ ۝٣٥', 'اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢', 'وَاَمَّابِنِعْمَةِرَبِّكَفَحَدِّثْࣖ۝١١اَلَمْنَشْرَحْلَكَصَدْكَۙ ۝١وَوَضَعْنَاعَنْكَوِزْرَكَۙ۝٢الَّذِيْٓاَنْقَضَظَهْرَ۝٣وَرَفَعْنَالَكَذِكْرَكَۗ۝٤فَاِنَّمَعَالْعُسْرِيُسْرًاۙ۝٥اِنَّمَعَالْعُسْرِ يُسْرًاۗ ۝٦فَاِذَا فَرَغْتَفَانْصَبْۙ۝٧وَاِلٰى رَبِّكَ فَارْغَبْࣖ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(69, 54, 'وَالْاَرْضَ بَعْدَ ذٰلِكَ دَحٰىهَاۗ ۝٣٠اَخْرَجَ مِنْهَا مَاۤءَهَا وَمَرْعٰىهَاۖ ۝٣١وَالْجِبَالَ اَرْسٰىهَاۙ ۝٣٢مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٣فَاِذَا جَاۤءَتِ الطَّاۤمَّةُ الْكُبْرٰىۖ ۝٣٤يَوْمَ يَتَذَكَّرُ الْاِنْسَانُ مَا سَعٰىۙ ۝٣٥وَبُرِّزَتِ الْجَحِيْمُ لِمَنْ يَّرٰى ۝٣٦', 'اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣', 'وَلَسَوْفَيَرْضٰىࣖ۝٢١وَالضُّحٰىۙ۝١وَالَّيْلِاِذَاسَجٰىۙ۝٢مَاوَدَّعَكَرَبُّكَوَمَاقَلٰىۗ۝٣وَلَلْاٰخِرَةُخَيْرٌلَّكَمِنَالْاُوْلٰىۗ۝٤وَلَسَوْفَيُعْطِيْكَرَبُّكَفَتَرْضٰىۗ۝٥اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(70, 55, 'وَالْجِبَالَ اَرْسٰىهَاۙ ۝٣٢مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٣فَاِذَا جَاۤءَتِ الطَّاۤمَّةُ الْكُبْرٰىۖ ۝٣٤يَوْمَ يَتَذَكَّرُ الْاِنْسَانُ مَا سَعٰىۙ ۝٣٥وَبُرِّزَتِ الْجَحِيْمُ لِمَنْ يَّرٰى ۝٣٦فَاَمَّا مَنْ طَغٰىۖ ۝٣٧وَاٰثَرَ الْحَيٰوةَ الدُّنْيَاۙ ۝٣٨فَاِنَّ الْجَحِيْمَ هِيَ الْمَأْوٰىۗ ۝٣٩', 'وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥', 'وَلَا يَخَافُ عُقْبٰهَاࣖ۝١٥وَالَّيْلِاِذَايَغْشٰىۙ ۝١وَالنَّهَارِ اِذَاتَجَلّٰىۙ۝٢وَمَاخَلَقَالذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣اِنَّ سَعْيَكُمْلَشَتّٰىۗ۝٤فَاَمَّا مَنْ اَعْطٰىوَاتَّقٰىۙ۝٥وَصَدَّقَبِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗلِلْيُسْرٰىۗ۝٧وَاَمَّامَنْۢبَخِلَ وَاسْتَغْنٰىۙ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(71, 56, 'فَاِذَا جَاۤءَتِ الطَّاۤمَّةُ الْكُبْرٰىۖ ۝٣٤يَوْمَ يَتَذَكَّرُ الْاِنْسَانُ مَا سَعٰىۙ ۝٣٥وَبُرِّزَتِ الْجَحِيْمُ لِمَنْ يَّرٰى ۝٣٦فَاَمَّا مَنْ طَغٰىۖ ۝٣٧وَاٰثَرَ الْحَيٰوةَ الدُّنْيَاۙ ۝٣٨فَاِنَّ الْجَحِيْمَ هِيَ الْمَأْوٰىۗ ۝٣٩وَاَمَّا مَنْ خَافَ مَقَامَ رَبِّهٖ وَنَهَى النَّفْسَ عَنِ الْهَوٰىۙ ۝٤٠فَاِنَّ الْجَنَّةَ هِيَ الْمَأْوٰىۗ ۝٤١', 'فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦', 'عَلَيْهِمْنَارٌمُّؤْصَدَةٌࣖ۝٢٠وَالشَّمْسِوَضُحٰىهَاۖ ۝١وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣وَالَّيْلِ اِذَايَغْشٰىهَاۖ۝٤وَالسَّمَاۤءِوَمَابَنٰىهَاۖ ۝٥وَالْاَرْضِوَمَاطَحٰىهَاۖ۝٦وَنَفْسٍوَّمَاسَوّٰىهَاۖ ۝٧فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(72, 57, 'فَاَمَّا مَنْ طَغٰىۖ ۝٣٧وَاٰثَرَ الْحَيٰوةَ الدُّنْيَاۙ ۝٣٨فَاِنَّ الْجَحِيْمَ هِيَ الْمَأْوٰىۗ ۝٣٩وَاَمَّا مَنْ خَافَ مَقَامَ رَبِّهٖ وَنَهَى النَّفْسَ عَنِ الْهَوٰىۙ ۝٤٠فَاِنَّ الْجَنَّةَ هِيَ الْمَأْوٰىۗ ۝٤١يَسْـَٔلُوْنَكَ عَنِ السَّاعَةِ اَيَّانَ مُرْسٰىهَاۗ ۝٤٢فِيْمَ اَنْتَ مِنْ ذِكْرٰىهَاۗ ۝٤٣اِلٰى رَبِّكَ مُنْتَهٰىهَاۗ ۝٤٤', 'اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦كَلَّا بَلْ لَّا تُكْرِمُوْنَ الْيَتِيْمَۙ ۝١٧وَلَا تَحٰۤضُّوْنَ عَلٰى طَعَامِ الْمِسْكِيْنِۙ ۝١٨', 'وَادْخُلِيْجَنَّتِيْࣖ۝٣٠لَآاُقْسِمُبِهٰذَاالْبَلَدِۙ۝١وَاَنْتَ حِلٌّۢبِهٰذَاالْبَلَدِۙ۝٢وَوَالِدٍوَّمَاوَلَدَۙ۝٣لَقَدْخَلَقْنَاالْاِنْسَانَفِيْكَبَدٍۗ۝٤اَيَحْسَبُاَنْلَّنْيَّقْدِرَعَلَيْهِاَحَدٌۘ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(73, 58, 'وَاَمَّا مَنْ خَافَ مَقَامَ رَبِّهٖ وَنَهَى النَّفْسَ عَنِ الْهَوٰىۙ ۝٤٠فَاِنَّ الْجَنَّةَ هِيَ الْمَأْوٰىۗ ۝٤١يَسْـَٔلُوْنَكَ عَنِ السَّاعَةِ اَيَّانَ مُرْسٰىهَاۗ ۝٤٢فِيْمَ اَنْتَ مِنْ ذِكْرٰىهَاۗ ۝٤٣اِلٰى رَبِّكَ مُنْتَهٰىهَاۗ ۝٤٤اِنَّمَآ اَنْتَ مُنْذِرُ مَنْ يَّخْشٰىهَاۗ ۝٤٥كَاَنَّهُمْ يَوْمَ يَرَوْنَهَا لَمْ يَلْبَثُوْٓا اِلَّا عَشِيَّةً اَوْ ضُحٰىهَاࣖ ۝٤٦', 'وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦كَلَّا بَلْ لَّا تُكْرِمُوْنَ الْيَتِيْمَۙ ۝١٧وَلَا تَحٰۤضُّوْنَ عَلٰى طَعَامِ الْمِسْكِيْنِۙ ۝١٨وَتَأْكُلُوْنَ التُّرَاثَ اَكْلًا لَّمًّاۙ ۝١٩وَّتُحِبُّوْنَ الْمَالَ حُبًّا جَمًّاۗ ۝٢٠', 'ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦وَالْفَجْرِۙ ۝١وَلَيَالٍ عَشْرٍۙ ۝٢وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(74, 59, 'وَاِذَا الْجِبَالُ سُيِّرَتْۖ ۝٣وَاِذَا الْعِشَارُ عُطِّلَتْۖ ۝٤وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠', 'وَتَأْكُلُوْنَ التُّرَاثَ اَكْلًا لَّمًّاۙ ۝١٩وَّتُحِبُّوْنَ الْمَالَ حُبًّا جَمًّاۗ ۝٢٠كَلَّآ اِذَا دُكَّتِ الْاَرْضُ دَكًّا دَكًّاۙ ۝٢١وَّجَآءَ رَبُّكَ وَالْمَلَكُ صَفًّا صَفًّاۚ ۝٢٢وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣', 'صُحُفِاِبْرٰهِيْمَوَمُوْسٰىࣖ۝١٩هَلْاَتٰىكَحَدِيْثُالْغَاشِيَةِۗ۝١وُجُوْهٌيَّوْمَىِٕذٍخَاشِعَةٌۙ۝٢عَامِلَةٌنَّصِبَةٌۙ۝٣تَصْلٰىنَارًاحَامِيَةًۙ ۝٤تُسْقٰىمِنْعَيْنٍاٰنِيَةٍۗ ۝٥لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(75, 60, 'وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣', 'كَلَّآ اِذَا دُكَّتِ الْاَرْضُ دَكًّا دَكًّاۙ ۝٢١وَّجَآءَ رَبُّكَ وَالْمَلَكُ صَفًّا صَفًّاۚ ۝٢٢وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣يَقُوْلُ يٰلَيْتَنِيْ قَدَّمْتُ لِحَيَاتِيْۚ ۝٢٤فَيَوْمَىِٕذٍ لَّا يُعَذِّبُ عَذَابَهٗٓ اَحَدٌۙ ۝٢٥', 'فَمَهِّلِ الْكٰفِرِيْنَاَمْهِلْهُمْرُوَيْدًاࣖ۝١٧سَبِّحِ اسْمَ رَبِّكَ الْاَعْلَىۙ ۝١الَّذِيْخَلَقَفَسَوّٰىۖ ۝٢وَالَّذِيْ قَدَّرَفَهَدٰىۖ۝٣وَالَّذِيْٓاَخْرَجَالْمَرْعٰىۖ۝٤فَجَعَلَهٗغُثَاۤءًاَحْوٰىۖ۝٥سَنُقْرِئُكَ فَلَا تَنْسٰىٓۖ ۝٦اِلَّامَا شَاۤءَاللّٰهُۗاِنَّهٗ يَعْلَمُ الْجَهْرَ وَمَا يَخْفٰىۗ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(76, 61, 'وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧ وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨ بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩ وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠ وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١ وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢ وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣ عَلِمَتْ نَفْسٌ مَّآ اَحْضَرَتْۗ ۝١٤', 'اَفَلَا يَنْظُرُوْنَ اِلَى الْاِبِلِ كَيْفَ خُلِقَتْۗ ۝١٧وَاِلَى السَّمَاۤءِ كَيْفَ رُفِعَتْۗ ۝١٨وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠فَذَكِّرْۗ اِنَّمَآ اَنْتَ مُذَكِّرٌۙ ۝٢١لَّسْتَ عَلَيْهِمْ بِمُصَيْطِرٍۙ ۝٢٢اِلَّا مَنْ تَوَلّٰى وَكَفَرَۙ ۝٢٣', 'فَمَهِّلِ الْكٰفِرِيْنَ اَمْهِلْهُمْ رُوَيْدًاࣖ ۝١٧  سَبِّحِ اسْمَ رَبِّكَ الْاَعْلَىۙ ۝١ الَّذِيْ خَلَقَ فَسَوّٰىۖ ۝٢ وَالَّذِيْ قَدَّرَ فَهَدٰىۖ ۝٣ وَالَّذِيْٓ اَخْرَجَ الْمَرْعٰىۖ ۝٤ فَجَعَلَهٗ غُثَاۤءً اَحْوٰىۖ ۝٥ سَنُقْرِئُكَ فَلَا تَنْسٰىٓۖ ۝٦ اِلَّا مَا شَاۤءَ اللّٰهُۗ اِنَّهٗ يَعْلَمُ الْجَهْرَ وَمَا يَخْفٰىۗ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(77, 62, 'بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩ وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠ وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١ وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢ وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣ عَلِمَتْ نَفْسٌ مَّآ اَحْضَرَتْۗ ۝١٤ فَلَآ اُقْسِمُ بِالْخُنَّسِۙ ۝١٥ الْجَوَارِ الْكُنَّسِۙ ۝١٦ وَالَّيْلِ اِذَا عَسْعَسَۙ ۝١٧', 'وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠فَذَكِّرْۗ اِنَّمَآ اَنْتَ مُذَكِّرٌۙ ۝٢١لَّسْتَ عَلَيْهِمْ بِمُصَيْطِرٍۙ ۝٢٢اِلَّا مَنْ تَوَلّٰى وَكَفَرَۙ ۝٢٣فَيُعَذِّبُهُ اللّٰهُ الْعَذَابَ الْاَكْبَرَۗ ۝٢٤اِنَّ اِلَيْنَآ اِيَابَهُمْ ۝٢٥ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦', 'صُحُفِ اِبْرٰهِيْمَ وَمُوْسٰىࣖ ۝١٩ هَلْ اَتٰىكَ حَدِيْثُ الْغَاشِيَةِۗ ۝١ وُجُوْهٌ يَّوْمَىِٕذٍ خَاشِعَةٌۙ ۝٢ عَامِلَةٌ نَّاصِبَةٌۙ ۝٣ تَصْلٰى نَارًا حَامِيَةًۙ ۝٤ تُسْقٰى مِنْ عَيْنٍ اٰنِيَةٍۗ ۝٥ لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(78, 63, 'وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١ وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢ وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣ عَلِمَتْ نَفْسٌ مَّآ اَحْضَرَتْۗ ۝١٤ فَلَآ اُقْسِمُ بِالْخُنَّسِۙ ۝١٥ الْجَوَارِ الْكُنَّسِۙ ۝١٦ وَالَّيْلِ اِذَا عَسْعَسَۙ ۝١٧ وَالصُّبْحِ اِذَا تَنَفَّسَۙ ۝١٨ اِنَّهٗ لَقَوْلُ رَسُوْلٍ كَرِيْمٍۙ ۝١٩', 'وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣يَقُوْلُ يٰلَيْتَنِيْ قَدَّمْتُ لِحَيَاتِيْۚ ۝٢٤فَيَوْمَىِٕذٍ لَّا يُعَذِّبُ عَذَابَهٗٓ اَحَدٌۙ ۝٢٥وَّلَا يُوْثِقُ وَثَاقَهٗٓ اَحَدٌۗ ۝٢٦يٰٓاَيَّتُهَا النَّفْسُ الْمُطْمَىِٕنَّةُۙ ۝٢٧ارْجِعِيْٓ اِلٰى رَبِّكِ رَاضِيَةً مَّرْضِيَّةًۚ ۝٢٨', 'ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦ وَالْفَجْرِۙ ۝١ وَلَيَالٍ عَشْرٍۙ ۝٢ وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣ وَالَّيْلِ اِذَا يَسْرِۚ ۝٤ هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥ اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦ اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(79, 64, 'وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣ عَلِمَتْ نَفْسٌ مَّآ اَحْضَرَتْۗ ۝١٤ فَلَآ اُقْسِمُ بِالْخُنَّسِۙ ۝١٥ الْجَوَارِ الْكُنَّسِۙ ۝١٦ وَالَّيْلِ اِذَا عَسْعَسَۙ ۝١٧ وَالصُّبْحِ اِذَا تَنَفَّسَۙ ۝١٨ اِنَّهٗ لَقَوْلُ رَسُوْلٍ كَرِيْمٍۙ ۝١٩ ذِيْ قُوَّةٍ عِنْدَ ذِى الْعَرْشِ مَكِيْنٍۙ ۝٢٠ مُّطَاعٍ ثَمَّ اَمِيْنٍۗ ۝٢١', 'يَقُوْلُ يٰلَيْتَنِيْ قَدَّمْتُ لِحَيَاتِيْۚ ۝٢٤فَيَوْمَىِٕذٍ لَّا يُعَذِّبُ عَذَابَهٗٓ اَحَدٌۙ ۝٢٥وَّلَا يُوْثِقُ وَثَاقَهٗٓ اَحَدٌۗ ۝٢٦يٰٓاَيَّتُهَا النَّفْسُ الْمُطْمَىِٕنَّةُۙ ۝٢٧ارْجِعِيْٓ اِلٰى رَبِّكِ رَاضِيَةً مَّرْضِيَّةًۚ ۝٢٨فَادْخُلِيْ فِيْ عِبٰدِيْۙ ۝٢٩وَادْخُلِيْ جَنَّتِيْࣖ ۝٣٠', 'وَادْخُلِيْ جَنَّتِيْࣖ ۝٣٠ لَآ اُقْسِمُ بِهٰذَا الْبَلَدِۙ ۝١ وَاَنْتَ حِلٌّۢ بِهٰذَا الْبَلَدِۙ ۝٢ وَوَالِدٍ وَّمَا وَلَدَۙ ۝٣ لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْ كَبَدٍۗ ۝٤ اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥ يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(80, 65, 'فَلَآ اُقْسِمُ بِالْخُنَّسِۙ ۝١٥ الْجَوَارِ الْكُنَّسِۙ ۝١٦ وَالَّيْلِ اِذَا عَسْعَسَۙ ۝١٧ وَالصُّبْحِ اِذَا تَنَفَّسَۙ ۝١٨ اِنَّهٗ لَقَوْلُ رَسُوْلٍ كَرِيْمٍۙ ۝١٩ ذِيْ قُوَّةٍ عِنْدَ ذِى الْعَرْشِ مَكِيْنٍۙ ۝٢٠ مُّطَاعٍ ثَمَّ اَمِيْنٍۗ ۝٢١ وَمَا صَاحِبُكُمْ بِمَجْنُوْنٍۚ ۝٢٢ وَلَقَدْ رَاٰهُ بِالْاُفُقِ الْمُبِيْنِۚ ۝٢٣', 'وَالَّيْلِ اِذَا يَغْشٰىۙ ۝١وَالنَّهَارِ اِذَا تَجَلّٰىۙ ۝٢وَمَا خَلَقَ الذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١', 'عَلَيْهِمْ نَارٌ مُّؤْصَدَةٌࣖ ۝٢٠ وَالشَّمْسِ وَضُحٰىهَاۖ ۝١ وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢ وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣ وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤ وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(81, 66, 'وَالصُّبْحِ اِذَا تَنَفَّسَۙ ۝١٨ اِنَّهٗ لَقَوْلُ رَسُوْلٍ كَرِيْمٍۙ ۝١٩ ذِيْ قُوَّةٍ عِنْدَ ذِى الْعَرْشِ مَكِيْنٍۙ ۝٢٠ مُّطَاعٍ ثَمَّ اَمِيْنٍۗ ۝٢١ وَمَا صَاحِبُكُمْ بِمَجْنُوْنٍۚ ۝٢٢ وَلَقَدْ رَاٰهُ بِالْاُفُقِ الْمُبِيْنِۚ ۝٢٣ وَمَا هُوَ عَلَى الْغَيْبِ بِضَنِيْنٍۚ ۝٢٤ وَمَا هُوَ بِقَوْلِ شَيْطٰنٍ رَّجِيْمٍۚ ۝٢٥', 'اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣', 'وَلَا يَخَافُ عُقْبٰهَاࣖ ۝١٥ وَالَّيْلِ اِذَا يَغْشٰىۙ ۝١ وَالنَّهَارِ اِذَا تَجَلّٰىۙ ۝٢ وَمَا خَلَقَ الذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣ اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤ فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥ وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦ فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧ وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(82, 67, 'ذِيْ قُوَّةٍ عِنْدَ ذِى الْعَرْشِ مَكِيْنٍۙ ۝٢٠ مُّطَاعٍ ثَمَّ اَمِيْنٍۗ ۝٢١ وَمَا صَاحِبُكُمْ بِمَجْنُوْنٍۚ ۝٢٢ وَلَقَدْ رَاٰهُ بِالْاُفُقِ الْمُبِيْنِۚ ۝٢٣ وَمَا هُوَ عَلَى الْغَيْبِ بِضَنِيْنٍۚ ۝٢٤ وَمَا هُوَ بِقَوْلِ شَيْطٰنٍ رَّجِيْمٍۚ ۝٢٥ فَاَيْنَ تَذْهَبُوْنَۗ ۝٢٦ اِنْ هُوَ اِلَّا ذِكْرٌ لِّلْعٰلَمِيْنَۙ ۝٢٧', 'فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤', 'وَلَسَوْفَ يَرْضٰىࣖ ۝٢١ وَالضُّحٰىۙ ۝١ وَالَّيْلِ اِذَا سَجٰىۙ ۝٢ مَا وَدَّعَكَ رَبُّكَ وَمَا قَلٰىۗ ۝٣ وَلَلْاٰخِرَةُ خَيْرٌ لَّكَ مِنَ الْاُوْلٰىۗ ۝٤ وَلَسَوْفَ يُعْطِيْكَ رَبُّكَ فَتَرْضٰىۗ ۝٥ اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(83, 68, 'وَلَقَدْ رَاٰهُ بِالْاُفُقِ الْمُبِيْنِۚ ۝٢٣ وَمَا هُوَ عَلَى الْغَيْبِ بِضَنِيْنٍۚ ۝٢٤ وَمَا هُوَ بِقَوْلِ شَيْطٰنٍ رَّجِيْمٍۚ ۝٢٥ فَاَيْنَ تَذْهَبُوْنَۗ ۝٢٦ اِنْ هُوَ اِلَّا ذِكْرٌ لِّلْعٰلَمِيْنَۙ ۝٢٧ لِمَنْ شَاۤءَ مِنْكُمْ اَنْ يَّسْتَقِيْمَۗ ۝٢٩ وَمَا تَشَاۤءُوْنَ اِلَّآ اَنْ يَّشَاۤءَ اللّٰهُ رَبُّ الْعٰلَمِيْنَࣖ ۝٢٩', 'وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤لَا يَصْلٰىهَآ اِلَّا الْاَشْقَىۙ ۝١٥الَّذِيْ كَذَّبَ وَتَوَلّٰىۗ ۝١٦', 'وَاَمَّا بِنِعْمَةِ رَبِّكَ فَحَدِّثْࣖ ۝١١ اَلَمْ نَشْرَحْ لَكَ صَدْرَكَۙ ۝١ وَوَضَعْنَا عَنْكَ وِزْرَكَۙ ۝٢ الَّذِيْٓ اَنْقَضَ ظَهْرَكَۙ ۝٣ وَرَفَعْنَا لَكَ ذِكْرَكَۗ ۝٤ فَاِنَّ مَعَ الْعُسْرِ يُسْرًاۙ ۝٥ اِنَّ مَعَ الْعُسْرِ يُسْرًاۗ ۝٦ فَاِذَا فَرَغْتَ فَانْصَبْۙ ۝٧ وَاِلٰى رَبِّكَ فَارْغَبْࣖ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(84, 69, 'وَاِذَا الْاَرْضُ مُدَّتْۙ ۝٣ وَاَلْقَتْ مَا فِيْهَا وَتَخَلَّتْۙ ۝٤ وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۗ ۝٥ يٰٓاَيُّهَا الْاِنْسَانُ اِنَّكَ كَادِحٌ اِلٰى رَبِّكَ كَدْحًا فَمُلٰقِيْهِۚ ۝٦ فَاَمَّا مَنْ اُوْتِيَ كِتٰبَهٗ بِيَمِيْنِهٖۙ ۝٧ فَسَوْفَ يُحَاسَبُ حِسَابًا يَّسِيْرًاۙ ۝٨ وَّيَنْقَلِبُ اِلٰٓى اَهْلِهٖ مَسْرُوْرًاۗ ۝٩', 'فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤لَا يَصْلٰىهَآ اِلَّا الْاَشْقَىۙ ۝١٥الَّذِيْ كَذَّبَ وَتَوَلّٰىۗ ۝١٦وَسَيُجَنَّبُهَا الْاَتْقَىۙ ۝١٧الَّذِيْ يُؤْتِيْ مَالَهٗ يَتَزَكّٰىۚ ۝١٨', 'وَاِلٰى رَبِّكَ فَارْغَبْࣖ ۝٨ وَالتِّيْنِ وَالزَّيْتُوْنِۙ ۝١ وَطُوْرِ سِيْنِيْنَۙ ۝٢ وَهٰذَا الْبَلَدِ الْاَمِيْنِۙ ۝٣ لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْٓ اَحْسَنِ تَقْوِيْمٍۖ ۝٤ ثُمَّ رَدَدْنٰهُ اَسْفَلَ سٰفِلِيْنَۙ ۝٥ اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ فَلَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(85, 70, 'يٰٓاَيُّهَا الْاِنْسَانُ اِنَّكَ كَادِحٌ اِلٰى رَبِّكَ كَدْحًا فَمُلٰقِيْهِۚ ۝٦ فَاَمَّا مَنْ اُوْتِيَ كِتٰبَهٗ بِيَمِيْنِهٖۙ ۝٧ فَسَوْفَ يُحَاسَبُ حِسَابًا يَّسِيْرًاۙ ۝٨ وَّيَنْقَلِبُ اِلٰٓى اَهْلِهٖ مَسْرُوْرًاۗ ۝٩ وَاَمَّا مَنْ اُوْتِيَ كِتٰبَهٗ وَرَاۤءَ ظَهْرِهٖۙ ۝١٠ فَسَوْفَ يَدْعُوْ ثُبُوْرًاۙ ۝١١', 'فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤لَا يَصْلٰىهَآ اِلَّا الْاَشْقَىۙ ۝١٥الَّذِيْ كَذَّبَ وَتَوَلّٰىۗ ۝١٦وَسَيُجَنَّبُهَا الْاَتْقَىۙ ۝١٧الَّذِيْ يُؤْتِيْ مَالَهٗ يَتَزَكّٰىۚ ۝١٨وَمَا لِاَحَدٍ عِنْدَهٗ مِنْ نِّعْمَةٍ تُجْزٰىٓۙ ۝١٩اِلَّا ابْتِغَاۤءَ وَجْهِ رَبِّهِ الْاَعْلٰىۚ ۝٢٠وَلَسَوْفَ يَرْضٰىࣖ ۝٢١', 'اَلَيْسَ اللّٰهُ بِاَحْكَمِ الْحٰكِمِيْنَࣖ ۝٨ اِقْرَأْ بِاسْمِ رَبِّكَ الَّذِيْ خَلَقَۚ ۝١ خَلَقَ الْاِنْسَانَ مِنْ عَلَقٍۚ ۝٢ اِقْرَأْ وَرَبُّكَ الْاَكْرَمُۙ ۝٣ الَّذِيْ عَلَّمَ بِالْقَلَمِۙ ۝٤ عَلَّمَ الْاِنْسَانَ مَا لَمْ يَعْلَمْۗ ۝٥ كَلَّآ اِنَّ الْاِنْسَانَ لَيَطْغٰىٓۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(86, 71, 'فَاَمَّا مَنْ اُوْتِيَ كِتٰبَهٗ بِيَمِيْنِهٖۙ ۝٧ فَسَوْفَ يُحَاسَبُ حِسَابًا يَّسِيْرًاۙ ۝٨ وَّيَنْقَلِبُ اِلٰٓى اَهْلِهٖ مَسْرُوْرًاۗ ۝٩ وَاَمَّا مَنْ اُوْتِيَ كِتٰبَهٗ وَرَاۤءَ ظَهْرِهٖۙ ۝١٠ فَسَوْفَ يَدْعُوْ ثُبُوْرًاۙ ۝١١ وَّيَصْلٰى سَعِيْرًاۗ ۝١٢ اِنَّهٗ كَانَ فِيْٓ اَهْلِهٖ مَسْرُوْرًاۗ ۝١٣', 'لَآ اُقْسِمُ بِهٰذَا الْبَلَدِۙ ۝١وَاَنْتَ حِلٌّۢ بِهٰذَا الْبَلَدِۙ ۝٢وَوَالِدٍ وَّمَا وَلَدَۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْ كَبَدٍۗ ۝٤اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩', 'سَلٰمٌۛ هِيَ حَتّٰى مَطْلَعِ الْفَجْرِࣖ ۝٥ لَمْ يَكُنِ الَّذِيْنَ كَفَرُوْا مِنْ اَهْلِ الْكِتٰبِ وَالْمُشْرِكِيْنَ مُنْفَكِّيْنَ حَتّٰى تَأْتِيَهُمُ الْبَيِّنَةُۙ ۝١ رَسُوْلٌ مِّنَ اللّٰهِ يَتْلُوْا صُحُفًا مُّطَهَّرَةًۙ ۝٢ فِيْهَا كُتُبٌ قَيِّمَةٌۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(87, 72, 'وَاَمَّا مَنْ اُوْتِيَ كِتٰبَهٗ وَرَاۤءَ ظَهْرِهٖۙ ۝١٠ فَسَوْفَ يَدْعُوْ ثُبُوْرًاۙ ۝١١ وَّيَصْلٰى سَعِيْرًاۗ ۝١٢ اِنَّهٗ كَانَ فِيْٓ اَهْلِهٖ مَسْرُوْرًاۗ ۝١٣ اِنَّهٗ ظَنَّ اَنْ لَّنْ يَّحُوْرَۛ ۝١٤ بَلٰىۛ اِنَّ رَبَّهٗ كَانَ بِهٖ بَصِيْرًاۗ ۝١٥ فَلَآ اُقْسِمُ بِالشَّفَقِۙ ۝١٦ وَالَّيْلِ وَمَا وَسَقَۙ ۝١٧', 'وَوَالِدٍ وَّمَا وَلَدَۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْ كَبَدٍۗ ۝٤اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠', 'جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ خٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْ خَشِيَ رَبَّهٗࣖ ۝٨ اِذَا زُلْزِلَتِ الْاَرْضُ زِلْزَالَهَاۙ ۝١ وَاَخْرَجَتِ الْاَرْضُ اَثْقَالَهَاۙ ۝٢', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(88, 73, 'اِنَّهٗ ظَنَّ اَنْ لَّنْ يَّحُوْرَۛ ۝١٤ بَلٰىۛ اِنَّ رَبَّهٗ كَانَ بِهٖ بَصِيْرًاۗ ۝١٥ فَلَآ اُقْسِمُ بِالشَّفَقِۙ ۝١٦ وَالَّيْلِ وَمَا وَسَقَۙ ۝١٧ وَالْقَمَرِ اِذَا اتَّسَقَۙ ۝١٨ لَتَرْكَبُنَّ طَبَقًا عَنْ طَبَقٍۗ ۝١٩ فَمَا لَهُمْ لَا يُؤْمِنُوْنَۙ ۝٢٠ وَاِذَا قُرِئَ عَلَيْهِمُ الْقُرْاٰنُ لَا يَسْجُدُوْنَۗ ۩ ۝٢١', 'اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢', 'وَمَنْ يَّعْمَلْ مِثْقَالَ ذَرَّةٍ شَرًّا يَّرَهٗࣖ ۝٨ وَالْعٰدِيٰتِ ضَبْحًاۙ ۝١ فَالْمُوْرِيٰتِ قَدْحًاۙ ۝٢ فَالْمُغِيْرٰتِ صُبْحًاۙ ۝٣ فَاَثَرْنَ بِهٖ نَقْعًاۙ ۝٤ فَوَسَطْنَ بِهٖ جَمْعًاۙ ۝٥ اِنَّ الْاِنْسَانَ لِرَبِّهٖ لَكَنُوْدٌۚ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(89, 74, 'فَلَآ اُقْسِمُ بِالشَّفَقِۙ ۝١٦ وَالَّيْلِ وَمَا وَسَقَۙ ۝١٧ وَالْقَمَرِ اِذَا اتَّسَقَۙ ۝١٨ لَتَرْكَبُنَّ طَبَقًا عَنْ طَبَقٍۗ ۝١٩ فَمَا لَهُمْ لَا يُؤْمِنُوْنَۙ ۝٢٠ وَاِذَا قُرِئَ عَلَيْهِمُ الْقُرْاٰنُ لَا يَسْجُدُوْنَۗ ۩ ۝٢١ بَلِ الَّذِيْنَ كَفَرُوْا يُكَذِّبُوْنَۖ ۝٢٢', 'اَيَحْسَبُ اَنْ لَّمْ يَرَهٗٓ اَحَدٌۗ ۝٧اَلَمْ نَجْعَلْ لَّهٗ عَيْنَيْنِۙ ۝٨وَلِسَانًا وَّشَفَتَيْنِۙ ۝٩وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢فَكُّ رَقَبَةٍۙ ۝١٣اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦', 'اِنَّ رَبَّهُمْ بِهِمْ يَوْمَىِٕذٍ لَّخَبِيْرٌࣖ ۝١١ اَلْقَارِعَةُۙ ۝١ مَا الْقَارِعَةُۚ ۝٢ وَمَآ اَدْرٰىكَ مَا الْقَارِعَةُۗ ۝٣ يَوْمَ يَكُوْنُ النَّاسُ كَالْفَرَاشِ الْمَبْثُوْثِۙ ۝٤ وَتَكُوْنُ الْجِبَالُ كَالْعِهْنِ الْمَنْفُوْشِۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(90, 75, 'لَتَرْكَبُنَّ طَبَقًا عَنْ طَبَقٍۗ ۝١٩ فَمَا لَهُمْ لَا يُؤْمِنُوْنَۙ ۝٢٠ وَاِذَا قُرِئَ عَلَيْهِمُ الْقُرْاٰنُ لَا يَسْجُدُوْنَۗ ۩ ۝٢١ بَلِ الَّذِيْنَ كَفَرُوْا يُكَذِّبُوْنَۖ ۝٢٢ وَاللّٰهُ اَعْلَمُ بِمَا يُوْعُوْنَۖ ۝٢٣ فَبَشِّرْهُمْ بِعَذَابٍ اَلِيْمٍۙ ۝٢٤', 'وَهَدَيْنٰهُ النَّجْدَيْنِۙ ۝١٠فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢فَكُّ رَقَبَةٍۙ ۝١٣اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦ثُمَّ كَانَ مِنَ الَّذِيْنَ اٰمَنُوْا وَتَوَاصَوْا بِالصَّبْرِ وَتَوَاصَوْا بِالْمَرْحَمَةِۗ ۝١٧', 'فِيْ عَمَدٍ مُّمَدَّدَةٍࣖ ۝٩ اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِاَصْحٰبِ الْفِيْلِۗ ۝١ اَلَمْ يَجْعَلْ كَيْدَهُمْ فِيْ تَضْلِيْلٍۙ ۝٢ وَّاَرْسَلَ عَلَيْهِمْ طَيْرًا اَبَابِيْلَۙ ۝٣ تَرْمِيْهِمْ بِحِجَارَةٍ مِّنْ سِجِّيْلٍۙ ۝٤ فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(91, 76, 'وَاِذَا قُرِئَ عَلَيْهِمُ الْقُرْاٰنُ لَا يَسْجُدُوْنَۗ ۩ ۝٢١ بَلِ الَّذِيْنَ كَفَرُوْا يُكَذِّبُوْنَۖ ۝٢٢ وَاللّٰهُ اَعْلَمُ بِمَا يُوْعُوْنَۖ ۝٢٣ فَبَشِّرْهُمْ بِعَذَابٍ اَلِيْمٍۙ ۝٢٤ اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ لَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍࣖ ۝٢٥', 'فَلَا اقْتَحَمَ الْعَقَبَةَۖ ۝١١وَمَآ اَدْرٰىكَ مَا الْعَقَبَةُۗ ۝١٢فَكُّ رَقَبَةٍۙ ۝١٣اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦ثُمَّ كَانَ مِنَ الَّذِيْنَ اٰمَنُوْا وَتَوَاصَوْا بِالصَّبْروَتَوَاصَوْبِالْمَرْحَمَةِۗ ۝١٧اُولٰۤىِٕكَ اَصْحٰبُ الْمَيْمَنَةِۗ ۝١٨', 'فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥ لِاِيْلٰفِ قُرَيْشٍۙ ۝١ اٖلٰفِهِمْ رِحْلَةَ الشِّتَاۤءِ وَالصَّيْفِۚ ۝٢ فَلْيَعْبُدُوْا رَبَّ هٰذَا الْبَيْتِۙ ۝٣ الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙ وَّاٰمَنَهُمْ مِّنْ خَوْفٍࣖ ۝٤', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(92, 77, 'وَالسَّمَاۤءِ وَالطَّارِقِۙ ۝١ وَمَآ اَدْرٰىكَ مَا الطَّارِقُۙ ۝٢ النَّجْمُ الثَّاقِبُۙ ۝٣ اِنْ كُلُّ نَفْسٍ لَّمَّا عَلَيْهَا حَافِظٌۗ ۝٤ فَلْيَنْظُرِ الْاِنْسَانُ مِمَّ خُلِقَ ۝٥ خُلِقَ مِنْ مَّاۤءٍ دَافِقٍۙ ۝٦ يَّخْرُجُ مِنْۢ بَيْنِ الصُّلْبِ وَالتَّرَاۤىِٕبِۗ ۝٧ اِنَّهٗ عَلٰى رَجْعِهٖ لَقَادِرٌۗ ۝٨ يَوْمَ تُبْلَى السَّرَاۤىِٕرُۙ ۝٩ فَمَا لَهٗ مِنْ قُوَّةٍ وَّلَا نَاصِرٍۗ ۝١٠', 'اَوْ اِطْعَامٌ فِيْ يَوْمٍ ذِيْ مَسْغَبَةٍۙ ۝١٤يَّتِيْمًا ذَا مَقْرَبَةٍۙ ۝١٥اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦ثُمَّ كَانَ مِنَ الَّذِيْنَ اٰمَنُوْا وَتَوَاصَوْا بِالصَّبْرِ وَتَوَاصَوْبِالْمَرْحَمَةِۗ ۝١٧اُولٰۤىِٕكَ اَصْحٰبُ الْمَيْمَنَةِۗ ۝١٨وَالَّذِيْنَ كَفَرُوْا بِاٰيٰتِنَا هُمْ اَصْحٰبُ الْمَشْئَمَةِۗ ۝١٩عَلَيْهِمْ نَارٌ مُّؤْصَدَةٌࣖ ۝٢٠', 'الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙ وَّاٰمَنَهُمْ مِّنْ خَوْفٍࣖ ۝٤ اَرَءَيْتَ الَّذِيْ يُكَذِّبُ بِالدِّيْنِۗ ۝١ فَذٰلِكَ الَّذِيْ يَدُعُّ الْيَتِيْمَۙ ۝٢ وَلَا يَحُضُّ عَلٰى طَعَامِ الْمِسْكِيْنِۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(93, 78, 'اِنْ كُلُّ نَفْسٍ لَّمَّا عَلَيْهَا حَافِظٌۗ ۝٤ فَلْيَنْظُرِ الْاِنْسَانُ مِمَّ خُلِقَ ۝٥ خُلِقَ مِنْ مَّاۤءٍ دَافِقٍۙ ۝٦ يَّخْرُجُ مِنْۢ بَيْنِ الصُّلْبِ وَالتَّرَاۤىِٕبِۗ ۝٧ اِنَّهٗ عَلٰى رَجْعِهٖ لَقَادِرٌۗ ۝٨ يَوْمَ تُبْلَى السَّرَاۤىِٕرُۙ ۝٩ فَمَا لَهٗ مِنْ قُوَّةٍ وَّلَا نَاصِرٍۗ ۝١٠ وَالسَّمَاۤءِ ذَاتِ الرَّجْعِۙ ۝١١ وَالْاَرْضِ ذَاتِ الصَّدْعِۙ ۝١٢', 'رَسُوْلٌ مِّنَ اللّٰهِ يَتْلُوْا صُحُفًا مُّطَهَّرَةًۙ ۝٢فِيْهَا كُتُبٌ قَيِّمَةٌۗ ۝٣وَمَا تَفَرَّقَ الَّذِيْنَ اُوْتُوا الْكِتٰبَ اِلَّا مِنْۢ بَعْدِ مَا جَاۤءَتْهُمُالْبَيِّنَةُۗ ۝٤وَمَآ اُمِرُوْٓا اِلَّا لِيَعْبُدُوا اللّٰهَ مُخْلِصِيْنَ لَهُ الدِّيْنَ ەۙ حُنَفَاۤءَوَيُقِيْمُوا الصَّلٰوةَ وَيُؤْتُوا الزَّكٰوةَ وَذٰلِكَ دِيْنُ الْقَيِّمَةِۗ ۝٥', 'اِنَّ شَانِئَكَ هُوَ الْاَبْتَرُࣖ ۝٣ قُلْ يٰٓاَيُّهَا الْكٰفِرُوْنَۙ ۝١ لَآ اَعْبُدُ مَا تَعْبُدُوْنَۙ ۝٢ وَلَآ اَنْتُمْ عٰبِدُوْنَ مَآ اَعْبُدُۚ ۝٣ وَلَآ اَنَا۠ عَابِدٌ مَّا عَبَدْتُّمْۙ ۝٤ وَلَآ اَنْتُمْ عٰبِدُوْنَ مَآ اَعْبُدُۗ ۝٥ لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(94, 79, 'فَلْيَنْظُرِ الْاِنْسَانُ مِمَّ خُلِقَ ۝٥ خُلِقَ مِنْ مَّاۤءٍ دَافِقٍۙ ۝٦ يَّخْرُجُ مِنْۢ بَيْنِ الصُّلْبِ وَالتَّرَاۤىِٕبِۗ ۝٧ اِنَّهٗ عَلٰى رَجْعِهٖ لَقَادِرٌۗ ۝٨ يَوْمَ تُبْلَى السَّرَاۤىِٕرُۙ ۝٩ فَمَا لَهٗ مِنْ قُوَّةٍ وَّلَا نَاصِرٍۗ ۝١٠ وَالسَّمَاۤءِ ذَاتِ الرَّجْعِۙ ۝١١ وَالْاَرْضِ ذَاتِ الصَّدْعِۙ ۝١٢ اِنَّهٗ لَقَوْلٌ فَصْلٌۙ ۝١٣ وَّمَا هُوَ بِالْهَزْلِۗ ۝١٤', 'وَمَآ اُمِرُوْٓا اِلَّا لِيَعْبُدُوا اللّٰهَ مُخْلِصِيْنَ لَهُ الدِّيْنَ ەۙحُنَفَاۤءوَيُقِيْمُوا الصَّلٰوةَ وَيُؤْتُوا الزَّكٰوةَ وَذٰلِكَ دِيْنُ الْقَيِّمَةِۗ ۝٥اِنَّ الَّذِيْنَ كَفَرُوْا مِنْ اَهْلِ الْكِتٰبِ وَالْمُشْرِكِيْنَ فِيْ نَارِ جَهَنَّمَخٰلِدِيْنَ فِيْهَاۗ اُولٰۤىِٕكَ هُمْ شَرُّ الْبَرِيَّةِۗ ۝٦', 'لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦ اِذَا جَاۤءَ نَصْرُ اللّٰهِ وَالْفَتْحُۙ ۝١ وَرَاَيْتَ النَّاسَ يَدْخُلُوْنَ فِيْ دِيْنِ اللّٰهِ اَفْوَاجًاۙ ۝٢ فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(95, 80, 'اِنَّهٗ عَلٰى رَجْعِهٖ لَقَادِرٌۗ ۝٨ يَوْمَ تُبْلَى السَّرَاۤىِٕرُۙ ۝٩ فَمَا لَهٗ مِنْ قُوَّةٍ وَّلَا نَاصِرٍۗ ۝١٠ وَالسَّمَاۤءِ ذَاتِ الرَّجْعِۙ ۝١١ وَالْاَرْضِ ذَاتِ الصَّدْعِۙ ۝١٢ اِنَّهٗ لَقَوْلٌ فَصْلٌۙ ۝١٣ وَّمَا هُوَ بِالْهَزْلِۗ ۝١٤ اِنَّهُمْ يَكِيْدُوْنَ كَيْدًاۙ ۝١٥ وَّاَكِيْدُ كَيْدًاۖ ۝١٦ فَمَهِّلِ الْكٰفِرِيْنَ اَمْهِلْهُمْ رُوَيْدًاࣖ ۝١٧', 'اِنَّ الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ اُولٰۤىِٕكَ هُمْ خَيْرُ الْبَرِيَّةِۗ ۝٧جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُخٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْخَشِيَ رَبَّهٗࣖ ۝٨', 'فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣ تَبَّتْ يَدَآ اَبِيْ لَهَبٍ وَّتَبَّۗ ۝١ مَآ اَغْنٰى عَنْهُ مَالُهٗ وَمَا كَسَبَۗ ۝٢ سَيَصْلٰى نَارًا ذَاتَ لَهَبٍۙ ۝٣ وَّامْرَاَتُهٗۗ حَمَّالَةَ الْحَطَبِۚ ۝٤ فِيْ جِيْدِهَا حَبْلٌ مِّنْ مَّسَدٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(96, 81, 'اِذْ هُمْ عَلَيْهَا قُعُوْدٌۙ ۝٦وَّهُمْ عَلٰى مَا يَفْعَلُوْنَ بِالْمُؤْمِنِيْنَ شُهُوْدٌۗ ۝٧وَمَا نَقَمُوْا مِنْهُمْ اِلَّآ اَنْ يُّؤْمِنُوْا بِاللّٰهِ الْعَزِيْزِ الْحَمِيْدِۙ ۝٨الَّذِيْ لَهٗ مُلْكُ السَّمٰوٰتِ وَالْاَرْضِۗ وَاللّٰهُ عَلٰى كُلِّ شَيْءٍ شَهِيْدٌۗ ۝٩', 'وَالشَّمْسِ وَضُحٰىهَاۖ ۝١ وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢ وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣ وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤ وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠', 'فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣تَبَّتْ يَدَآ اَبِيْ لَهَبٍ وَّتَبَّۗ ۝١مَآ اَغْنٰى عَنْهُ مَالُهٗ وَمَا كَسَبَۗ ۝٢سَيَصْلٰى نَارًا ذَاتَ لَهَبٍۙ ۝٣وَّامْرَاَتُهٗۗ حَمَّالَةَ الْحَطَبِۚ ۝٤فِيْ جِيْدِهَا حَبْلٌ مِّنْ مَّسَدٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(97, 82, 'اِنَّ الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ اُولٰۤىِٕكَ هُمْ خَيْرُ الْبَرِيَّةِۗ ۝٧جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُخٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْخَشِيَ رَبَّهٗࣖ ۝٨', 'وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣ وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤ وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١', 'لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦اِذَا جَاۤءَ نَصْرُ اللّٰهِ وَالْفَتْحُۙ ۝١وَرَاَيْتَ النَّاسَ يَدْخُلُوْنَ فِيْ دِيْنِ اللّٰهِ اَفْوَاجًاۙ ۝٢فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(98, 83, 'وَمَا نَقَمُوْا مِنْهُمْ اِلَّآ اَنْ يُّؤْمِنُوْا بِاللّٰهِ الْعَزِيْزِ الْحَمِيْدِۙ ۝٨الَّذِيْ لَهٗ مُلْكُ السَّمٰوٰتِ وَالْاَرْضِۗ وَاللّٰهُ عَلٰى كُلِّ شَيْءٍ شَهِيْدٌۗ ۝٩اِنَّ الَّذِيْنَ فَتَنُوا الْمُؤْمِنِيْنَ وَالْمُؤْمِنٰتِ ثُمَّ لَمْ يَتُوْبُوْا فَلَهُمْ عَذَابُ جَهَنَّمَ وَلَهُمْ عَذَابُ الْحَرِيْقِۗ ۝١٠', 'وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢', 'اِنَّ شَانِئَكَ هُوَ الْاَبْتَرُࣖ ۝٣قُلْ يٰٓاَيُّهَا الْكٰفِرُوْنَۙ ۝١لَآ اَعْبُدُ مَا تَعْبُدُوْنَۙ ۝٢وَلَآ اَنْتُمْ عٰبِدُوْنَ مَآ اَعْبُدُۚ ۝٣وَلَآ اَنَا۠ عَابِدٌ مَّا عَبَدْتُّمْۙ ۝٤وَلَآ اَنْتُمْ عٰبِدُوْنَ مَآ اَعْبُدُۗ ۝٥لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(99, 84, 'اِنَّ الَّذِيْنَ فَتَنُوا الْمُؤْمِنِيْنَ وَالْمُؤْمِنٰتِ ثُمَّ لَمْ يَتُوْبُوْا فَلَهُمْ عَذَابُ جَهَنَّمَ وَلَهُمْ عَذَابُ الْحَرِيْقِۗ ۝١٠اِنَّ الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ لَهُمْ جَنّٰتٌ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ ەۗ ذٰلِكَ الْفَوْزُ الْكَبِيْرُۗ ۝١١', 'وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢ فَقَالَ لَهُمْ رَسُوْلُ اللّٰهِ نَاقَةَ اللّٰهِ وَسُقْيٰهَاۗ ۝١٣', 'الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙ وَّاٰمَنَهُمْ مِّنْ خَوْفٍࣖ ۝٤اَرَءَيْتَ الَّذِيْ يُكَذِّبُ بِالدِّيْنِۗ ۝١فَذٰلِكَ الَّذِيْ يَدُعُّ الْيَتِيْمَۙ ۝٢وَلَا يَحُضُّ عَلٰى طَعَامِ الْمِسْكِيْنِۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(100, 85, 'اِنَّ الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ لَهُمْ جَنّٰتٌ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ ەۗ ذٰلِكَ الْفَوْزُ الْكَبِيْرُۗ ۝١اِنَّ بَطْشَ رَبِّكَ لَشَدِيْدٌۗ ۝١اِنَّهٗ هُوَ يُبْدِئُ وَيُعِيْدُۚ ۝١٣وَهُوَ الْغَفُوْرُ الْوَدُوْدُۙ ۝١٤ذُو الْعَرْشِ الْمَجِيْدُۙ ۝١٥فَعَّالٌ لِّمَا يُرِيْدُۗ ۝١٦', 'وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢ فَقَالَ لَهُمْ رَسُوْلُ اللّٰهِ نَاقَةَ اللّٰهِ وَسُقْيٰهَاۗ ۝١٣ فَكَذَّبُوْهُ فَعَقَرُوْهَاۖ فَدَمْدَمَ عَلَيْهِمْ رَبُّهُمْ بِذَنْۢبِهِمْ فَسَوّٰىهَاۖ ۝١٤ وَلَا يَخَافُ عُقْبٰهَاࣖ ۝١٥', 'فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥لِاِيْلٰفِ قُرَيْشٍۙ ۝١اٖلٰفِهِمْ رِحْلَةَ الشِّتَاۤءِ وَالصَّيْفِۚ ۝٢فَلْيَعْبُدُوْا رَبَّ هٰذَا الْبَيْتِۙ ۝٣الَّذِيْٓ اَطْعَمَهُمْ مِّنْ جُوْعٍ ەۙ وَّاٰمَنَهُمْ مِّنْ خَوْفٍࣖ ۝٤', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(101, 86, 'اِنَّ بَطْشَ رَبِّكَ لَشَدِيْدٌۗ ۝١اِنَّهٗ هُوَ يُبْدِئُ وَيُعِيْدُۚ ۝١٣وَهُوَ الْغَفُوْرُ الْوَدُوْدُۙ ۝١٤ذُو الْعَرْشِ الْمَجِيْدُۙ ۝١٥فَعَّالٌ لِّمَا يُرِيْدُۗ ۝١٦هَلْ اَتٰىكَ حَدِيْثُ الْجُنُوْدِۙ ۝١٧فِرْعَوْنَ وَثَمُوْدَۗ ۝١٨بَلِ الَّذِيْنَ كَفَرُوْا فِيْ تَكْذِيْبٍۙ ۝١٩وَّاللّٰهُ مِنْ وَّرَاۤىِٕهِمْ مُّحِيْطٌۚ ۝٢٠', 'وَطُوْرِ سِيْنِيْنَۙ ۝٢ وَهٰذَا الْبَلَدِ الْاَمِيْنِۙ ۝٣ لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْٓ اَحْسَنِ تَقْوِيْمٍۖ ۝٤ ثُمَّ رَدَدْنٰهُ اَسْفَلَ سٰفِلِيْنَۙ ۝٥ اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ فَلَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍۗ ۝٦ فَمَا يُكَذِّبُكَ بَعْدُ بِالدِّيْنِۗ ۝٧ اَلَيْسَ اللّٰهُ بِاَحْكَمِ الْحٰكِمِيْنَࣖ ۝٨', 'فِيْ عَمَدٍ مُّمَدَّدَةٍࣖ ۝٩اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِاَصْحٰبِ الْفِيْلِۗ ۝١اَلَمْ يَجْعَلْ كَيْدَهُمْ فِيْ تَضْلِيْلٍۙ ۝٢وَّاَرْسَلَ عَلَيْهِمْ طَيْرًا اَبَابِيْلَۙ ۝٣تَرْمِيْهِمْ بِحِجَارَةٍ مِّنْ سِجِّيْلٍۙ ۝٤فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(102, 87, 'سَنُقْرِئُكَ فَلَا تَنْسٰىٓۖ ۝٦اِلَّا مَا شَاۤءَ اللّٰهُۗ اِنَّهٗ يَعْلَمُ الْجَهْرَ وَمَا يَخْفٰىۗ ۝٧وَنُيَسِّرُكَ لِلْيُسْرٰىۖ ۝٨فَذَكِّرْ اِنْ نَّفَعَتِ الذِّكْرٰىۗ ۝٩سَيَذَّكَّرُ مَنْ يَّخْشٰىۙ ۝١٠وَيَتَجَنَّبُهَا الْاَشْقَىۙ ۝١١الَّذِيْ يَصْلَى النَّارَ الْكُبْرٰىۚ ۝١٢ثُمَّ لَا يَمُوْتُ فِيْهَا وَلَا يَحْيٰىۗ ۝١٣', 'اَنْ رَّاٰهُ اسْتَغْنٰىۗ ۝٧ اِنَّ اِلٰى رَبِّكَ الرُّجْعٰىۗ ۝٨ اَرَاَيْتَ الَّذِيْ يَنْهٰىۙ ۝٩ عَبْدًا اِذَا صَلّٰىۗ ۝١٠ اَرَاَيْتَ اِنْ كَانَ عَلَى الْهُدٰىٓۙ ۝١١ اَوْ اَمَرَ بِالتَّقْوٰىۗ ۝١٢ اَرَاَيْتَ اِنْ كَذَّبَ وَتَوَلّٰىۗ ۝١٣ اَلَمْ يَعْلَمْ بِاَنَّ اللّٰهَ يَرٰىۗ ۝١٤ كَلَّا لَىِٕنْ لَّمْ يَنْتَهِ ەۙ لَنَسْفَعًا ۢ بِالنَّاصِيَةِۙ ۝١٥ نَاصِيَةٍ كَاذِبَةٍ خَاطِئَةٍۚ ۝١٦', 'كَاَنَّهُمْ يَوْمَ يَرَوْنَهَا لَمْ يَلْبَثُوْٓا اِلَّا عَشِيَّةً اَوْ ضُحٰىهَاࣖ ۝٤٦عَبَسَ وَتَوَلّٰىٓۙ ۝١اَنْ جَاۤءَهُ الْاَعْمٰىۗ ۝٢وَمَا يُدْرِيْكَ لَعَلَّهٗ يَزَّكّٰىٓۙ ۝٣اَوْ يَذَّكَّرُ فَتَنْفَعَهُ الذِّكْرٰىۗ ۝٤\nاَمَّا مَنِ اسْتَغْنٰىۙ ۝٥فَاَنْتَ لَهٗ تَصَدّٰىۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(103, 88, 'وَنُيَسِّرُكَ لِلْيُسْرٰىۖ ۝٨فَذَكِّرْ اِنْ نَّفَعَتِ الذِّكْرٰىۗ ۝٩سَيَذَّكَّرُ مَنْ يَّخْشٰىۙ ۝١٠وَيَتَجَنَّبُهَا الْاَشْقَىۙ ۝١١الَّذِيْ يَصْلَى النَّارَ الْكُبْرٰىۚ ۝١٢ثُمَّ لَا يَمُوْتُ فِيْهَا وَلَا يَحْيٰىۗ ۝١٣قَدْ اَفْلَحَ مَنْ تَزَكّٰىۙ ۝١٤وَذَكَرَ اسْمَ رَبِّهٖ فَصَلّٰىۗ ۝١٥', 'مَا وَدَّعَكَ رَبُّكَ وَمَا قَلٰىۗ ۝٣وَلَلْاٰخِرَةُ خَيْرٌ لَّكَ مِنَ الْاُوْلٰىۗ ۝٤وَلَسَوْفَ يُعْطِيْكَ رَبُّكَ فَتَرْضٰىۗ ۝٥اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦وَوَجَدَكَ ضَاۤلًّا فَهَدٰىۖ ۝٧وَوَجَدَكَ عَاۤىِٕلًا فَاَغْنٰىۗ ۝٨فَاَمَّا الْيَتِيْمَ فَلَا تَقْهَرْۗ ۝٩', 'يَوْمَ لَا تَمْلِكُ نَفْسٌ لِّنَفْسٍ شَيْـًٔاۗ وَالْاَمْرُ يَوْمَىِٕذٍ لِّلّٰهِࣖ ۝١٩وَيْلٌ لِّلْمُطَفِّفِيْنَۙ ۝١الَّذِيْنَ اِذَا اكْتَالُوْا عَلَى النَّاسِ يَسْتَوْفُوْنَۖ ۝٢وَاِذَا كَالُوْهُمْ اَوْ وَّزَنُوْهُمْ يُخْسِرُوْنَۗ ۝٣اَلَا يَظُنُّ اُولٰۤىِٕكَ اَنَّهُمْ مَّبْعُوْثُوْنَۙ ۝٤', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(104, 89, 'فَذَكِّرْ اِنْ نَّفَعَتِ الذِّكْرٰىۗ ۝٩سَيَذَّكَّرُ مَنْ يَّخْشٰىۙ ۝١٠وَيَتَجَنَّبُهَا الْاَشْقَىۙ ۝١١الَّذِيْ يَصْلَى النَّارَ الْكُبْرٰىۚ ۝١٢ثُمَّ لَا يَمُوْتُ فِيْهَا وَلَا يَحْيٰىۗ ۝١٣قَدْ اَفْلَحَ مَنْ تَزَكّٰىۙ ۝١٤وَذَكَرَ اسْمَ رَبِّهٖ فَصَلّٰىۗ ۝١٥بَلْ تُؤْثِرُوْنَ الْحَيٰوةَ الدُّنْيَاۖ ۝١٦', 'وَلَسَوْفَ يُعْطِيْكَ رَبُّكَ فَتَرْضٰىۗ ۝٥اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦وَوَجَدَكَ ضَاۤلًّا فَهَدٰىۖ ۝٧وَوَجَدَكَ عَاۤىِٕلًا فَاَغْنٰىۗ ۝٨فَاَمَّا الْيَتِيْمَ فَلَا تَقْهَرْۗ ۝٩وَاَمَّا السَّاۤىِٕلَ فَلَا تَنْهَرْ ۝١٠وَاَمَّا بِنِعْمَةِ رَبِّكَ فَحَدِّثْ۝١١ࣖ', 'فِيْ لَوْحٍ مَّحْفُوْظٍࣖ ۝٢وَالسَّمَاۤءِ وَالطَّارِقِۙ ۝١وَمَآ اَدْرٰىكَ مَا الطَّارِقُۙ ۝٢النَّجْمُ الثَّاقِبُۙ ۝٣اِنْ كُلُّ نَفْسٍ لَّمَّا عَلَيْهَا حَافِظٌۗ ۝٤فَلْيَنْظُرِ الْاِنْسَانُ مِمَّ خُلِقَ ۝٥\nخُلِقَ مِنْ مَّاۤءٍ دَافِقٍۙ ۝٦يَّخْرُجُ مِنْۢ بَيْنِ الصُّلْبِ وَالتَّرَاۤىِٕبِۗ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(105, 90, 'ثُمَّ لَا يَمُوْتُ فِيْهَا وَلَا يَحْيٰىۗ ۝١٣قَدْ اَفْلَحَ مَنْ تَزَكّٰىۙ ۝١٤وَذَكَرَ اسْمَ رَبِّهٖ فَصَلّٰىۗ ۝١٥بَلْ تُؤْثِرُوْنَ الْحَيٰوةَ الدُّنْيَاۖ ۝١٦وَالْاٰخِرَةُ خَيْرٌ وَّاَبْقٰىۗ ۝١٧اِنَّ هٰذَا لَفِى الصُّحُفِ الْاُوْلٰىۙ ۝١٨صُحُفِ اِبْرٰهِيْمَ وَمُوْسٰىࣖ ۝١٩', 'وَالْعٰدِيٰتِ ضَبْحًاۙ ۝١فَالْمُوْرِيٰتِ قَدْحًاۙ ۝٢فَالْمُغِيْرٰتِ صُبْحًاۙ ۝٣فَاَثَرْنَ بِهٖ نَقْعًاۙ ۝٤فَوَسَطْنَ بِهٖ جَمْعًاۙ ۝٥اِنَّ الْاِنْسَانَ لِرَبِّهٖ لَكَنُوْدٌۚ ۝٦وَاِنَّهٗ عَلٰى ذٰلِكَ لَشَهِيْدٌۚ ۝٧وَاِنَّهٗ لِحُبِّ الْخَيْرِ لَشَدِيْدٌۗ ۝٨', 'فَمَهِّلِ الْكٰفِرِيْنَ اَمْهِلْهُمْ رُوَيْدًاࣖ ۝١٧سَبِّحِ اسْمَ رَبِّكَ الْاَعْلَىۙ ۝١الَّذِيْ خَلَقَ فَسَوّٰىۖ ۝٢وَالَّذِيْ قَدَّرَ فَهَدٰىۖ ۝٣وَالَّذِيْٓ اَخْرَجَ الْمَرْعٰىۖ ۝٤فَجَعَلَهٗ غُثَاۤءً اَحْوٰىۖ ۝٥سَنُقْرِئُكَ فَلَا تَنْسٰىٓۖ ۝٦اِلَّا مَا شَاۤءَ اللّٰهُۗ اِنَّهٗ يَعْلَمُ الْجَهْرَ وَمَا يَخْفٰىۗ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(106, 91, 'هَلْ اَتٰىكَ حَدِيْثُ الْغَاشِيَةِۗ ۝١وُجُوْهٌ يَّوْمَىِٕذٍ خَاشِعَةٌۙ ۝٢عَامِلَةٌ نَّاصِبَةٌۙ ۝٣تَصْلٰى نَارًا حَامِيَةًۙ ۝٤تُسْقٰى مِنْ عَيْنٍ اٰنِيَةٍۗ ۝٥لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦لَّا يُسْمِنُ وَلَا يُغْنِيْ مِنْ جُوْعٍۗ ۝٧', 'فَوَسَطْنَ بِهٖ جَمْعًاۙ ۝٥اِنَّ الْاِنْسَانَ لِرَبِّهٖ لَكَنُوْدٌۚ ۝٦وَاِنَّهٗ عَلٰى ذٰلِكَ لَشَهِيْدٌۚ ۝٧وَاِنَّهٗ لِحُبِّ الْخَيْرِ لَشَدِيْدٌۗ ۝٨۞ اَفَلَا يَعْلَمُ اِذَا بُعْثِرَ مَا فِىالْقُبُوْرِۙ ۝٩وَحُصِّلَ مَا فِى الصُّدُوْرِۙ ۝١٠اِنَّ رَبَّهُمْ بِهِمْ يَوْمَىِٕذٍ لَّخَبِيْرٌࣖ ۝١١', 'اِنَّآ اَنْذَرْنٰكُمْ عَذَابًا قَرِيْبًا ەۙ يَّوْمَ يَنْظُرُ الْمَرْءُ مَا قَدَّمَتْ يَدَاهُ وَيَقُوْلُ الْكٰفِرُ يٰلَيْتَنِيْ كُنْتُ تُرٰبًاࣖ ۝٤٠ وَالنّٰزِعٰتِ غَرْقًاۙ ۝١وَّالنّٰشِطٰتِ نَشْطًاۙ ۝٢وَّالسّٰبِحٰتِ سَبْحًاۙ ۝٣فَالسّٰبِقٰتِ سَبْقًاۙ ۝٤فَالْمُدَبِّرٰتِ اَمْرًاۘ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(107, 92, 'عَامِلَةٌ نَّاصِبَةٌۙ ۝٣تَصْلٰى نَارًا حَامِيَةًۙ ۝٤تُسْقٰى مِنْ عَيْنٍ اٰنِيَةٍۗ ۝٥لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦لَّا يُسْمِنُ وَلَا يُغْنِيْ مِنْ جُوْعٍۗ ۝٧وُجُوْهٌ يَّوْمَىِٕذٍ نَّاعِمَةٌۙ ۝٨لِّسَعْيِهَا رَاضِيَةٌۙ ۝٩فِيْ جَنَّةٍ عَالِيَةٍۙ ۝١٠لَّا تَسْمَعُ فِيْهَا لَاغِيَةًۗ۝١١', 'يَوْمَىِٕذٍ تُحَدِّثُ اَخْبَارَهَاۙ ۝٤بِاَنَّ رَبَّكَ اَوْحٰى لَهَاۗ ۝٥يَوْمَىِٕذٍ يَّصْدُرُ النَّاسُ اَشْتَاتًا ەۙ لِّيُرَوْا اَعْمَالَهُمْۗ ۝٦فَمَنْ يَّعْمَلْ مِثْقَالَ ذَرَّةٍ خَيْرًا يَّرَهٗۚ ۝٧وَمَنْ يَّعْمَلْ مِثْقَالَ ذَرَّةٍ شَرًّا يَّرَهٗࣖ ۝٨', 'اُولٰۤىِٕكَ هُمُ الْكَفَرَةُ الْفَجَرَةُࣖ ۝٤٢اِذَا الشَّمْسُ كُوِّرَتْۖ ۝١وَاِذَا النُّجُوْمُ انْكَدَرَتْۖ ۝٢وَاِذَا الْجِبَالُ سُيِّرَتْۖ ۝٣وَاِذَا الْعِشَارُ عُطِّلَتْۖ ۝٤وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(108, 93, 'لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦لَّا يُسْمِنُ وَلَا يُغْنِيْ مِنْ جُوْعٍۗ ۝٧وُجُوْهٌ يَّوْمَىِٕذٍ نَّاعِمَةٌۙ ۝٨لِّسَعْيِهَا رَاضِيَةٌۙ ۝٩فِيْ جَنَّةٍ عَالِيَةٍۙ ۝١٠لَّا تَسْمَعُ فِيْهَا لَاغِيَةًۗ ۝١١فِيْهَا عَيْنٌ جَارِيَةٌۘ ۝١٢فِيْهَا سُرُرٌ مَّرْفُوْعَةٌۙ ۝١٣وَّاَكْوَابٌ مَّوْضُوْعَةٌۙ ۝١٤', 'وَتَكُوْنُ الْجِبَالُ كَالْعِهْنِ الْمَنْفُوْشِۗ ۝٥فَاَمَّا مَنْ ثَقُلَتْ مَوَازِينُهٗۙ ۝٦فَهُوَ فِيْ عِيْشَةٍ رَّاضِيَةٍۗ ۝٧وَاَمَّا مَنْ خَفَّتْ مَوَازِيْنُهٗۙ ۝٨فَاُمُّهٗ هَاوِيَةٌۗ ۝٩وَمَآ اَدْرٰىكَ مَا هِيَهْۗ ۝١٠نَارٌ حَامِيَةٌࣖ ۝١١', 'وَمَا تَشَاۤءُوْنَ اِلَّآ اَنْ يَّشَاۤءَ اللّٰهُ رَبُّ الْعٰلَمِيْنَࣖ ۝٢٩ اِذَا السَّمَاۤءُ انْفَطَرَتْۙ ۝١وَاِذَا الْكَوَاكِبُ انْتَثَرَتْۙ ۝٢وَاِذَا الْبِحَارُ فُجِّرَتْۙ ۝٣وَاِذَا الْقُبُوْرُ بُعْثِرَتْۙ ۝٤عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(109, 94, 'وُجُوْهٌ يَّوْمَىِٕذٍ نَّاعِمَةٌۙ ۝٨لِّسَعْيِهَا رَاضِيَةٌۙ ۝٩فِيْ جَنَّةٍ عَالِيَةٍۙ ۝١٠لَّا تَسْمَعُ فِيْهَا لَاغِيَةًۗ ۝١١فِيْهَا عَيْنٌ جَارِيَةٌۘ ۝١٢فِيْهَا سُرُرٌ مَّرْفُوْعَةٌۙ ۝١٣وَّاَكْوَابٌ مَّوْضُوْعَةٌۙ ۝١٤وَّنَمَارِقُ مَصْفُوْفَةٌۙ ۝١٥وَّزَرَابِيُّ مَبْثُوْثَةٌۗ ۝١٦اَفَلَا يَنْظُرُوْنَ اِلَى الْاِبِلِ كَيْفَخُلِقَتْۗ ۝١٧', 'حَتّٰى زُرْتُمُ الْمَقَابِرَۗ ۝٢ثُمَّ كَلَّا سَوْفَ تَعْلَمُوْنَ ۝٤كَلَّا لَوْ تَعْلَمُوْنَ عِلْمَ الْيَقِيْنِۗ ۝٥لَتَرَوُنَّ الْجَحِيْمَۙ ۝٦ثُمَّ لَتَرَوُنَّهَا عَيْنَ الْيَقِيْنِۙ ۝٧ثُمَّ لَتُسْـَٔلُنَّ يَوْمَىِٕذٍ عَنِ النَّعِيْمِࣖ ۝٨', 'جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ خٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْ خَشِيَ رَبَّهٗࣖ ۝٨اِذَا زُلْزِلَتِ الْاَرْضُ زِلْزَالَهَاۙ ۝١وَاَخْرَجَتِ الْاَرْضُ اَثْقَالَهَاۙ ۝٢', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(110, 95, 'فِيْ جَنَّةٍ عَالِيَةٍۙ ۝١لَّا تَسْمَعُ فِيْهَا لَاغِيَةًۗ ۝١١فِيْهَا عَيْنٌ جَارِيَةٌۘ ۝١٢فِيْهَا سُرُرٌ مَّرْفُوْعَةٌۙ ۝١٣وَّاَكْوَابٌ مَّوْضُوْعَةٌۙ ۝١٤وَّنَمَارِقُ مَصْفُوْفَةٌۙ ۝١٥وَّزَرَابِيُّ مَبْثُوْثَةٌۗ ۝١٦اَفَلَا يَنْظُرُوْنَ اِلَى الْاِبِلِ كَيْفَخُلِقَتْۗ ۝١٧وَاِلَى السَّمَاۤءِ كَيْفَ رُفِعَتْۗ ۝١٨', 'يٰٓاَيُّهَا الْاِنْسَانُ مَا غَرَّكَ بِرَبِّكَ الْكَرِيْمِۙ ۝٦الَّذِيْ خَلَقَكَ فَسَوّٰىكَ فَعَدَلَكَۙ ۝٧فِيْٓ اَيِّ صُوْرَةٍ مَّا شَاۤءَ رَكَّبَكَۗ ۝٨كَلَّا بَلْ تُكَذِّبُوْنَ بِالدِّيْنِۙ ۝٩وَاِنَّ عَلَيْكُمْ لَحٰفِظِيْنَۙ ۝١٠كِرَامًا كٰتِبِيْنَۙ ۝١١يَعْلَمُوْنَ مَا تَفْعَلُوْنَ ۝١٢', 'هَلْ ثُوِّبَ الْكُفَّارُ مَا كَانُوْا يَفْعَلُوْنَࣖ ۝٣٦اِذَا السَّمَاۤءُ انْشَقَّتْۙ ۝١وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۙ ۝٢وَاِذَا الْاَرْضُ مُدَّتْۙ ۝٣وَاَلْقَتْ مَا فِيْهَا وَتَخَلَّتْۙ ۝٤وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۗ ۝٥يٰٓاَيُّهَا الْاِنْسَانُ اِنَّكَ كَادِحٌ اِلٰى رَبِّكَ كَدْحًا فَمُلٰقِيْهِۚ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(111, 96, 'فِيْهَا عَيْنٌ جَارِيَةٌۘ ۝١٢فِيْهَا سُرُرٌ مَّرْفُوْعَةٌۙ ۝١٣وَّاَكْوَابٌ مَّوْضُوْعَةٌۙ ۝١٤وَّنَمَارِقُ مَصْفُوْفَةٌۙ ۝١٥وَّزَرَابِيُّ مَبْثُوْثَةٌۗ ۝١٦اَفَلَا يَنْظُرُوْنَ اِلَى الْاِبِلِ كَيْفَ خُلِقَتْۗ ۝١٧وَاِلَى السَّمَاۤءِ كَيْفَ رُفِعَتْۗ ۝١٨وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠', 'فَقُلْ هَلْ لَّكَ اِلٰٓى اَنْ تَزَكّٰىۙ ۝١٨وَاَهْدِيَكَ اِلٰى رَبِّكَ فَتَخْشٰىۚ ۝١٩فَاَرٰىهُ الْاٰيَةَ الْكُبْرٰىۖ ۝٢٠فَكَذَّبَ وَعَصٰىۖ ۝٢١ثُمَّ اَدْبَرَ يَسْعٰىۖ ۝٢٢فَحَشَرَ فَنَادٰىۖ ۝٢٣فَقَالَ اَنَا۠ رَبُّكُمُ الْاَعْلٰىۖ ۝٢٤فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦', 'وَادْخُلِيْ جَنَّتِيْࣖ ۝٣٠لَآ اُقْسِمُ بِهٰذَا الْبَلَدِۙ ۝١وَاَنْتَ حِلٌّۢ بِهٰذَا الْبَلَدِۙ ۝٢وَوَالِدٍ وَّمَا وَلَدَۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْ كَبَدٍۗ ۝٤اَيَحْسَبُ اَنْ لَّنْ يَّقْدِرَ عَلَيْهِ اَحَدٌۘ ۝٥يَقُوْلُ اَهْلَكْتُ مَالًا لُّبَدًاۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(112, 97, 'وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣', 'وَاَمَّا مَنْ جَاۤءَكَ يَسْعٰىۙ ۝٨وَهُوَ يَخْشٰىۙ ۝٩فَاَنْتَ عَنْهُ تَلَهّٰىۚ ۝١٠كَلَّآ اِنَّهَا تَذْكِرَةٌۚ ۝١١فَمَنْ شَاۤءَ ذَكَرَهٗۘ ۝١٢فِيْ صُحُفٍ مُّكَرَّمَةٍۙ ۝١٣مَّرْفُوْعَةٍ مُّطَهَّرَةٍ ۢۙ ۝١٤بِاَيْدِيْ سَفَرَةٍۙ ۝١٥كِرَامٍ ۢ بَرَرَةٍۗ ۝١٦قُتِلَ الْاِنْسَانُ مَآ اَكْفَرَهٗۗ ۝١٧مِنْ اَيِّ شَيْءٍ خَلَقَهٗۗ ۝١٨مِنْ نُّطْفَةٍۗ خَلَقَهٗ فَقَدَّرَهٗۗ ۝١٩', 'وَاِلٰى رَبِّكَ فَارْغَبْࣖ ۝٨وَالتِّيْنِ وَالزَّيْتُوْنِۙ ۝١وَطُوْرِ سِيْنِيْنَۙ ۝٢وَهٰذَا الْبَلَدِ الْاَمِيْنِۙ ۝٣لَقَدْ خَلَقْنَا الْاِنْسَانَ فِيْٓ اَحْسَنِ تَقْوِيْمٍۖ ۝٤ثُمَّ رَدَدْنٰهُ اَسْفَلَ سٰفِلِيْنَۙ ۝٥اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ فَلَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(113, 98, 'لَا يَذُوْقُوْنَ فِيْهَا بَرْدًا وَّلَا شَرَابًاۙ ۝٢٤اِلَّا حَمِيْمًا وَّغَسَّاقًاۙ ۝٢٥جَزَاۤءً وِّفَاقًاۗ ۝٢٦اِنَّهُمْ كَانُوْا لَا يَرْجُوْنَ حِسَابًاۙ ۝٢٧وَّكَذَّبُوْا بِاٰيٰتِنَا كِذَّابًاۗ ۝٢٨وَكُلَّ شَيْءٍ اَحْصَيْنٰهُ كِتٰبًاۙ ۝٢٩فَذُوْقُوْا فَلَنْ نَّزِيْدَكُمْ اِلَّا عَذَابًاࣖ ۝٣٠', 'اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧الَّتِيْ لَمْ يُخْلَقْ مِثْلُهَا فِى الْبِلَادِۖ ۝٨وَثَمُوْدَ الَّذِيْنَ جَابُوا الصَّخْرَ بِالْوَادِۖ ۝٩وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣', 'صُحُفِ اِبْرٰهِيْمَ وَمُوْسٰىࣖ ۝١هَلْ اَتٰىكَ حَدِيْثُ الْغَاشِيَةِۗ ۝١وُجُوْهٌ يَّوْمَىِٕذٍ خَاشِعَةٌۙ ۝٢عَامِلَةٌ نَّاصِبَةٌۙ ۝٣عَامِلَةٌ نَّاصِبَةٌۙ ۝٣تُسْقٰى مِنْ عَيْنٍ اٰنِيَةٍۗ ۝٥لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(114, 99, 'اِنَّهُمْ كَانُوْا لَا يَرْجُوْنَ حِسَابًاۙ ۝٢٧وَّكَذَّبُوْا بِاٰيٰتِنَا كِذَّابًاۗ ۝٢٨وَكُلَّ شَيْءٍ اَحْصَيْنٰهُ كِتٰبًاۙ ۝٢٩فَذُوْقُوْا فَلَنْ نَّزِيْدَكُمْ اِلَّا عَذَابًاࣖ ۝٣٠اِنَّ لِلْمُتَّقِيْنَ مَفَازًاۙ ۝٣١حَدَاۤىِٕقَ وَاَعْنَابًاۙ ۝٣٢وَّكَوَاعِبَ اَتْرَابًاۙ ۝٣٣وَّكَأْسًا دِهَاقًاۗ ۝٣٤', 'وَفِرْعَوْنَ ذِى الْاَوْتَادِۖ ۝١٠الَّذِيْنَ طَغَوْا فِى الْبِلَادِۖ ۝١١فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥', 'عَلَيْهِمْ نَارٌ مُّؤْصَدَةٌࣖ ۝٢٠وَالشَّمْسِ وَضُحٰىهَاۖ ۝١وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(115, 100, 'وَكُلَّ شَيْءٍ اَحْصَيْنٰهُ كِتٰبًاۙ ۝٢٩فَذُوْقُوْا فَلَنْ نَّزِيْدَكُمْ اِلَّا عَذَابًاࣖ ۝٣٠اِنَّ لِلْمُتَّقِيْنَ مَفَازًاۙ ۝٣١حَدَاۤىِٕقَ وَاَعْنَابًاۙ ۝٣٢وَّكَوَاعِبَ اَتْرَابًاۙ ۝٣٣وَّكَأْسًا دِهَاقًاۗ ۝٣٤لَا يَسْمَعُوْنَ فِيْهَا لَغْوًا وَّلَا كِذّٰبًا ۝٣٥جَزَاۤءً مِّنْ رَّبِّكَ عَطَاۤءً حِسَابًاۙ ۝٣٦', 'فَاَكْثَرُوْا فِيْهَا الْفَسَادَۖ ۝١٢فَصَبَّ عَلَيْهِمْ رَبُّكَ سَوْطَ عَذَابٍۖ ۝١٣اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦', 'وَلَسَوْفَ يَرْضٰىࣖ ۝٢١وَالضُّحٰىۙ ۝١وَالَّيْلِ اِذَا سَجٰىۙ ۝٢مَا وَدَّعَكَ رَبُّكَ وَمَا قَلٰىۗ ۝٣وَلَلْاٰخِرَةُ خَيْرٌ لَّكَ مِنَ الْاُوْلٰىۗ ۝٤وَلَسَوْفَ يُعْطِيْكَ رَبُّكَ فَتَرْضٰىۗ ۝٥اَلَمْ يَجِدْكَ يَتِيْمًا فَاٰوٰىۖ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(116, 101, 'وَاِذَا الْجِبَالُ سُيِّرَتْۖ ۝٣وَاِذَا الْعِشَارُ عُطِّلَتْۖ ۝٤وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠', 'اِنَّ رَبَّكَ لَبِالْمِرْصَادِۗ ۝١٤فَاَمَّا الْاِنْسَانُ اِذَا مَا ابْتَلٰىهُ رَبُّهٗ فَاَكْرَمَهٗ وَنَعَّمَهٗۙ فَيَقُوْلُ رَبِّيْٓ اَكْرَمَنِۗ ۝١٥وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦كَلَّا بَلْ لَّا تُكْرِمُوْنَ الْيَتِيْمَۙ ۝١٧وَلَا تَحٰۤضُّوْنَ عَلٰى طَعَامِ الْمِسْكِيْنِۙ ۝١٨', 'ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦وَالْفَجْرِۙ ۝١وَلَيَالٍ عَشْرٍۙ ۝٢وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣وَالَّيْلِ اِذَا يَسْرِۚ ۝٤هَلْ فِيْ ذٰلِكَ قَسَمٌ لِّذِيْ حِجْرٍۗ ۝٥اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِعَادٍۖ ۝٦ اِرَمَ ذَاتِ الْعِمَادِۖ ۝٧', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(117, 102, 'وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣', 'وَاَمَّآ اِذَا مَا ابْتَلٰىهُ فَقَدَرَ عَلَيْهِ رِزْقَهٗ ەۙ فَيَقُوْلُ رَبِّيْٓ اَهَانَنِۚ ۝١٦كَلَّا بَلْ لَّا تُكْرِمُوْنَ الْيَتِيْمَۙ ۝١٧وَلَا تَحٰۤضُّوْنَ عَلٰى طَعَامِ الْمِسْكِيْنِۙ ۝١٨وَتَأْكُلُوْنَ التُّرَاثَ اَكْلًا لَّمًّاۙ ۝١٩وَّتُحِبُّوْنَ الْمَالَ حُبًّا جَمًّاۗ ۝٢٠', 'وَلَا يَخَافُ عُقْبٰهَاࣖ ۝١٥وَالَّيْلِ اِذَا يَغْشٰىۙ ۝١وَالنَّهَارِ اِذَا تَجَلّٰىۙ ۝٢وَمَا خَلَقَ الذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣اِنَّ سَعْيَكُمْ لَشَتّٰىۗ ۝٤فَاَمَّا مَنْ اَعْطٰى وَاتَّقٰىۙ ۝٥وَصَدَّقَ بِالْحُسْنٰىۙ ۝٦فَسَنُيَسِّرُهٗ لِلْيُسْرٰىۗ ۝٧وَاَمَّا مَنْۢ بَخِلَ وَاسْتَغْنٰىۙ ۝٨', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(118, 103, 'وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧ وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨ بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩ وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠ وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١ وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢ وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣ عَلِمَتْ نَفْسٌ مَّآ اَحْضَرَتْۗ ۝١٤', 'وَتَأْكُلُوْنَ التُّرَاثَ اَكْلًا لَّمًّاۙ ۝١٩وَّتُحِبُّوْنَ الْمَالَ حُبًّا جَمًّاۗ ۝٢٠كَلَّآ اِذَا دُكَّتِ الْاَرْضُ دَكًّا دَكًّاۙ ۝٢١وَّجَآءَ رَبُّكَ وَالْمَلَكُ صَفًّا صَفًّاۚ ۝٢٢وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣', 'هَلْ ثُوِّبَ الْكُفَّارُ مَا كَانُوْا يَفْعَلُوْنَࣖ ۝٣٦اِذَا السَّمَاۤءُ انْشَقَّتْۙ ۝١وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۙ ۝٢وَاِذَا الْاَرْضُ مُدَّتْۙ ۝٣وَاَلْقَتْ مَا فِيْهَا وَتَخَلَّتْۙ ۝٤وَاَذِنَتْ لِرَبِّهَا وَحُقَّتْۗ ۝٥يٰٓاَيُّهَا الْاِنْسَانُ اِنَّكَ كَادِحٌ اِلٰى رَبِّكَ كَدْحًا فَمُلٰقِيْهِۚ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24');
INSERT INTO `soal_musabaqoh` (`id`, `no_soal`, `soal1`, `soal2`, `soal3`, `status`, `created_at`, `updated_at`) VALUES
(119, 104, 'ثُمَّ لَا يَمُوْتُ فِيْهَا وَلَا يَحْيٰىۗ ۝١٣قَدْ اَفْلَحَ مَنْ تَزَكّٰىۙ ۝١٤وَذَكَرَ اسْمَ رَبِّهٖ فَصَلّٰىۗ ۝١٥بَلْ تُؤْثِرُوْنَ الْحَيٰوةَ الدُّنْيَاۖ ۝١٦وَالْاٰخِرَةُ خَيْرٌ وَّاَبْقٰىۗ ۝١٧اِنَّ هٰذَا لَفِى الصُّحُفِ الْاُوْلٰىۙ ۝١٨صُحُفِ اِبْرٰهِيْمَ وَمُوْسٰىࣖ ۝١٩', 'كَلَّآ اِذَا دُكَّتِ الْاَرْضُ دَكًّا دَكًّاۙ ۝٢١وَّجَآءَ رَبُّكَ وَالْمَلَكُ صَفًّا صَفًّاۚ ۝٢٢وَجِايْۤءَ يَوْمَىِٕذٍ ۢ بِجَهَنَّمَۙ يَوْمَىِٕذٍ يَّتَذَكَّرُ الْاِنْسَانُ وَاَنّٰى لَهُ الذِّكْرٰىۗ ۝٢٣يَقُوْلُ يٰلَيْتَنِيْ قَدَّمْتُ لِحَيَاتِيْۚ ۝٢٤فَيَوْمَىِٕذٍ لَّا يُعَذِّبُ عَذَابَهٗٓ اَحَدٌۙ ۝٢٥', 'سَلٰمٌۛ هِيَ حَتّٰى مَطْلَعِ الْفَجْرِࣖ ۝٥لَمْ يَكُنِ الَّذِيْنَ كَفَرُوْا مِنْ اَهْلِ الْكِتٰبِ وَالْمُشْرِكِيْنَ مُنْفَكِّيْنَ حَتّٰى تَأْتِيَهُمُ الْبَيِّنَةُۙ ۝١رَسُوْلٌ مِّنَ اللّٰهِ يَتْلُوْا صُحُفًا مُّطَهَّرَةًۙ ۝٢فِيْهَا كُتُبٌ قَيِّمَةٌۗ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(120, 105, 'هَلْ اَتٰىكَ حَدِيْثُ الْغَاشِيَةِۗ ۝١وُجُوْهٌ يَّوْمَىِٕذٍ خَاشِعَةٌۙ ۝٢عَامِلَةٌ نَّاصِبَةٌۙ ۝٣تَصْلٰى نَارًا حَامِيَةًۙ ۝٤تُسْقٰى مِنْ عَيْنٍ اٰنِيَةٍۗ ۝٥لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦لَّا يُسْمِنُ وَلَا يُغْنِيْ مِنْ جُوْعٍۗ ۝٧', 'اَفَلَا يَنْظُرُوْنَ اِلَى الْاِبِلِ كَيْفَ خُلِقَتْۗ ۝١٧وَاِلَى السَّمَاۤءِ كَيْفَ رُفِعَتْۗ ۝١٨وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠فَذَكِّرْۗ اِنَّمَآ اَنْتَ مُذَكِّرٌۙ ۝٢١لَّسْتَ عَلَيْهِمْ بِمُصَيْطِرٍۙ ۝٢٢اِلَّا مَنْ تَوَلّٰى وَكَفَرَۙ ۝٢٣', 'جَزَاۤؤُهُمْ عِنْدَ رَبِّهِمْ جَنّٰتُ عَدْنٍ تَجْرِيْ مِنْ تَحْتِهَا الْاَنْهٰرُ خٰلِدِيْنَ فِيْهَآ اَبَدًاۗ رَضِيَ اللّٰهُ عَنْهُمْ وَرَضُوْا عَنْهُۗ ذٰلِكَ لِمَنْ خَشِيَ رَبَّهٗࣖ ۝٨اِذَا زُلْزِلَتِ الْاَرْضُ زِلْزَالَهَاۙ ۝١وَاَخْرَجَتِ الْاَرْضُ اَثْقَالَهَاۙ ۝٢', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(121, 106, 'عَامِلَةٌ نَّاصِبَةٌۙ ۝٣تَصْلٰى نَارًا حَامِيَةًۙ ۝٤تُسْقٰى مِنْ عَيْنٍ اٰنِيَةٍۗ ۝٥لَيْسَ لَهُمْ طَعَامٌ اِلَّا مِنْ ضَرِيْعٍۙ ۝٦لَّا يُسْمِنُ وَلَا يُغْنِيْ مِنْ جُوْعٍۗ ۝٧وُجُوْهٌ يَّوْمَىِٕذٍ نَّاعِمَةٌۙ ۝٨لِّسَعْيِهَا رَاضِيَةٌۙ ۝٩فِيْ جَنَّةٍ عَالِيَةٍۙ ۝١٠لَّا تَسْمَعُ فِيْهَا لَاغِيَةًۗ۝١١', 'وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠فَذَكِّرْۗ اِنَّمَآ اَنْتَ مُذَكِّرٌۙ ۝٢١لَّسْتَ عَلَيْهِمْ بِمُصَيْطِرٍۙ ۝٢٢اِلَّا مَنْ تَوَلّٰى وَكَفَرَۙ ۝٢٣فَيُعَذِّبُهُ اللّٰهُ الْعَذَابَ الْاَكْبَرَۗ ۝٢٤اِنَّ اِلَيْنَآ اِيَابَهُمْ ۝٢٥ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦', 'اِنَّ رَبَّهُمْ بِهِمْ يَوْمَىِٕذٍ لَّخَبِيْرٌࣖ ۝١١اَلْقَارِعَةُۙ ۝١مَا الْقَارِعَةُۚ ۝٢وَمَآ اَدْرٰىكَ مَا الْقَارِعَةُۗ ۝٣يَوْمَ يَكُوْنُ النَّاسُ كَالْفَرَاشِ الْمَبْثُوْثِۙ ۝٤وَتَكُوْنُ الْجِبَالُ كَالْعِهْنِ الْمَنْفُوْشِۗ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(122, 107, 'فَلْيَنْظُرِ الْاِنْسَانُ مِمَّ خُلِقَ ۝٥ خُلِقَ مِنْ مَّاۤءٍ دَافِقٍۙ ۝٦ يَّخْرُجُ مِنْۢ بَيْنِ الصُّلْبِ وَالتَّرَاۤىِٕبِۗ ۝٧ اِنَّهٗ عَلٰى رَجْعِهٖ لَقَادِرٌۗ ۝٨ يَوْمَ تُبْلَى السَّرَاۤىِٕرُۙ ۝٩ فَمَا لَهٗ مِنْ قُوَّةٍ وَّلَا نَاصِرٍۗ ۝١٠ وَالسَّمَاۤءِ ذَاتِ الرَّجْعِۙ ۝١١ وَالْاَرْضِ ذَاتِ الصَّدْعِۙ ۝١٢ اِنَّهٗ لَقَوْلٌ فَصْلٌۙ ۝١٣ وَّمَا هُوَ بِالْهَزْلِۗ ۝١٤', 'وَالشَّمْسِ وَضُحٰىهَاۖ ۝١ وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢ وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣ وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤ وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠', 'لَكُمْ دِيْنُكُمْ وَلِيَ دِيْنِࣖ ۝٦اِذَا جَاۤءَ نَصْرُ اللّٰهِ وَالْفَتْحُۙ ۝١وَرَاَيْتَ النَّاسَ يَدْخُلُوْنَ فِيْ دِيْنِ اللّٰهِ اَفْوَاجًاۙ ۝٢فَسَبِّحْ بِحَمْدِ رَبِّكَ وَاسْتَغْفِرْهُۗ اِنَّهٗ كَانَ تَوَّابًاࣖ ۝٣', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(123, 108, 'اِنَّهٗ عَلٰى رَجْعِهٖ لَقَادِرٌۗ ۝٨ يَوْمَ تُبْلَى السَّرَاۤىِٕرُۙ ۝٩ فَمَا لَهٗ مِنْ قُوَّةٍ وَّلَا نَاصِرٍۗ ۝١٠ وَالسَّمَاۤءِ ذَاتِ الرَّجْعِۙ ۝١١ وَالْاَرْضِ ذَاتِ الصَّدْعِۙ ۝١٢ اِنَّهٗ لَقَوْلٌ فَصْلٌۙ ۝١٣ وَّمَا هُوَ بِالْهَزْلِۗ ۝١٤ اِنَّهُمْ يَكِيْدُوْنَ كَيْدًاۙ ۝١٥ وَّاَكِيْدُ كَيْدًاۖ ۝١٦ فَمَهِّلِ الْكٰفِرِيْنَ اَمْهِلْهُمْ رُوَيْدًاࣖ ۝١٧', 'وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣ وَالَّيْلِ اِذَا يَغْشٰىهَاۖ ۝٤ وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١', 'فِيْ عَمَدٍ مُّمَدَّدَةٍࣖ ۝٩اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِاَصْحٰبِ الْفِيْلِۗ ۝١اَلَمْ يَجْعَلْ كَيْدَهُمْ فِيْ تَضْلِيْلٍۙ ۝٢وَّاَرْسَلَ عَلَيْهِمْ طَيْرًا اَبَابِيْلَۙ ۝٣تَرْمِيْهِمْ بِحِجَارَةٍ مِّنْ سِجِّيْلٍۙ ۝٤فَجَعَلَهُمْ كَعَصْفٍ مَّأْكُوْلٍࣖ ۝٥', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(124, 109, 'اِذْ هُمْ عَلَيْهَا قُعُوْدٌۙ ۝٦وَّهُمْ عَلٰى مَا يَفْعَلُوْنَ بِالْمُؤْمِنِيْنَ شُهُوْدٌۗ ۝٧وَمَا نَقَمُوْا مِنْهُمْ اِلَّآ اَنْ يُّؤْمِنُوْا بِاللّٰهِ الْعَزِيْزِ الْحَمِيْدِۙ ۝٨الَّذِيْ لَهٗ مُلْكُ السَّمٰوٰتِ وَالْاَرْضِۗ وَاللّٰهُ عَلٰى كُلِّ شَيْءٍ شَهِيْدٌۗ ۝٩', 'وَالسَّمَاۤءِ وَمَا بَنٰىهَاۖ ۝٥ وَالْاَرْضِ وَمَا طَحٰىهَاۖ ۝٦ وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢', 'كَاَنَّهُمْ يَوْمَ يَرَوْنَهَا لَمْ يَلْبَثُوْٓا اِلَّا عَشِيَّةً اَوْ ضُحٰىهَاࣖ ۝٤٦عَبَسَ وَتَوَلّٰىٓۙ ۝١اَنْ جَاۤءَهُ الْاَعْمٰىۗ ۝٢وَمَا يُدْرِيْكَ لَعَلَّهٗ يَزَّكّٰىٓۙ ۝٣اَوْ يَذَّكَّرُ فَتَنْفَعَهُ الذِّكْرٰىۗ ۝٤\nاَمَّا مَنِ اسْتَغْنٰىۙ ۝٥فَاَنْتَ لَهٗ تَصَدّٰىۗ ۝٦', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24'),
(125, 110, 'وَالصُّبْحِ اِذَا تَنَفَّسَۙ ۝١٨ اِنَّهٗ لَقَوْلُ رَسُوْلٍ كَرِيْمٍۙ ۝١٩ ذِيْ قُوَّةٍ عِنْدَ ذِى الْعَرْشِ مَكِيْنٍۙ ۝٢٠ مُّطَاعٍ ثَمَّ اَمِيْنٍۗ ۝٢١ وَمَا صَاحِبُكُمْ بِمَجْنُوْنٍۚ ۝٢٢ وَلَقَدْ رَاٰهُ بِالْاُفُقِ الْمُبِيْنِۚ ۝٢٣ وَمَا هُوَ عَلَى الْغَيْبِ بِضَنِيْنٍۚ ۝٢٤ وَمَا هُوَ بِقَوْلِ شَيْطٰنٍ رَّجِيْمٍۚ ۝٢٥', 'وَنَفْسٍ وَّمَا سَوّٰىهَاۖ ۝٧ فَاَلْهَمَهَا فُجُوْرَهَا وَتَقْوٰىهَاۖ ۝٨ قَدْ اَفْلَحَ مَنْ زَكّٰىهَاۖ ۝٩ وَقَدْ خَابَ مَنْ دَسّٰىهَاۗ ۝١٠ كَذَّبَتْ ثَمُوْدُ بِطَغْوٰىهَآۖ ۝١١ اِذِ انْۢبَعَثَ اَشْقٰىهَاۖ ۝١٢ فَقَالَ لَهُمْ رَسُوْلُ اللّٰهِ نَاقَةَ اللّٰهِ وَسُقْيٰهَاۗ ۝١٣', 'يَوْمَ لَا تَمْلِكُ نَفْسٌ لِّنَفْسٍ شَيْـًٔاۗ وَالْاَمْرُ يَوْمَىِٕذٍ لِّلّٰهِࣖ ۝١٩وَيْلٌ لِّلْمُطَفِّفِيْنَۙ ۝١الَّذِيْنَ اِذَا اكْتَالُوْا عَلَى النَّاسِ يَسْتَوْفُوْنَۖ ۝٢وَاِذَا كَالُوْهُمْ اَوْ وَّزَنُوْهُمْ يُخْسِرُوْنَۗ ۝٣اَلَا يَظُنُّ اُولٰۤىِٕكَ اَنَّهُمْ مَّبْعُوْثُوْنَۙ ۝٤', 'Aktif', '2025-10-23 04:56:24', '2025-10-23 04:56:24');

-- --------------------------------------------------------

--
-- Table structure for table `soal_musabaqoh_final`
--

CREATE TABLE `soal_musabaqoh_final` (
  `id` int(11) NOT NULL,
  `no_soal` int(11) NOT NULL,
  `soal1` text NOT NULL,
  `soal2` text NOT NULL,
  `soal3` text NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal_musabaqoh_final`
--

INSERT INTO `soal_musabaqoh_final` (`id`, `no_soal`, `soal1`, `soal2`, `soal3`, `status`, `created_at`, `updated_at`) VALUES
(11, 1, 'وَمَا يُكَذِّبُ بِهٖٓ اِلَّا كُلُّ مُعْتَدٍ اَثِيْمٍۙ ۝١٢ اِذَا تُتْلٰى عَلَيْهِ اٰيٰتُنَا قَالَ اَسَاطِيْرُ الْاَوَّلِيْنَۗ ۝١٣ كَلَّا بَلْࣝ رَانَ عَلٰى قُلُوْبِهِمْ مَّا كَانُوْا يَكْسِبُوْنَ ۝١٤ كَلَّآ اِنَّهُمْ عَنْ رَّبِّهِمْ يَوْمَىِٕذٍ لَّمَحْجُوْبُوْنَۗ ۝١٥ ثُمَّ اِنَّهُمْ لَصَالُوا الْجَحِيْمِۗ ۝١٦ ثُمَّ يُقَالُ هٰذَا الَّذِيْ كُنْتُمْ بِهٖ تُكَذِّبُوْنَۗ ۝١٧ كَلَّآ اِنَّ كِتٰبَ الْاَبْرَارِ لَفِيْ عِلِّيِّيْنَۗ ۝١٨', 'ذِيْ قُوَّةٍ عِنْدَ ذِى الْعَرْشِ مَكِيْنٍۙ ۝٢٠ surah attakwir', 'اُولٰۤىِٕكَ هُمُ الْكَفَرَةُ الْفَجَرَةُࣖ ۝٤٢اِذَا الشَّمْسُ كُوِّرَتْۖ ۝١وَاِذَا النُّجُوْمُ انْكَدَرَتْۖ ۝٢وَاِذَا الْجِبَالُ سُيِّرَتْۖ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(12, 2, 'وَاِذَا الْعِشَارُ عُطِّلَتْۖ ۝٤ وَاِذَا الْوُحُوْشُ حُشِرَتْۖ ۝٥ وَاِذَا الْبِحَارُ سُجِّرَتْۖ ۝٦ وَاِذَا النُّفُوْسُ زُوِّجَتْۖ ۝٧ وَاِذَا الْمَوْءٗدَةُ سُىِٕلَتْۖ ۝٨ بِاَيِّ ذَنْۢبٍ قُتِلَتْۚ ۝٩ وَاِذَا الصُّحُفُ نُشِرَتْۖ ۝١٠ وَاِذَا السَّمَاۤءُ كُشِطَتْۖ ۝١١ وَاِذَا الْجَحِيْمُ سُعِّرَتْۖ ۝١٢ وَاِذَا الْجَنَّةُ اُزْلِفَتْۖ ۝١٣', 'عَيْنًا يَّشْرَبُ بِهَا الْمُقَرَّبُوْنَۗ ۝٢٨  Al-Muthaffifin', 'يَوْمَ لَا تَمْلِكُ نَفْسٌ لِّنَفْسٍ شَيْـًٔاۗ وَالْاَمْرُ يَوْمَىِٕذٍ لِّلّٰهِࣖ ۝١٩وَيْلٌ لِّلْمُطَفِّفِيْنَۙ ۝١الَّذِيْنَ اِذَا اكْتَالُوْا عَلَى النَّاسِ يَسْتَوْفُوْنَۖ ۝٢', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(13, 3, 'فَكَذَّبَ وَعَصٰىۖ ۝٢١ ثُمَّ اَدْبَرَ يَسْعٰىۖ ۝٢٢ فَحَشَرَ فَنَادٰىۖ ۝٢٣ فَقَالَ اَنَا۠ رَبُّكُمُ الْاَعْلٰىۖ ۝٢٤ فَاَخَذَهُ اللّٰهُ نَكَالَ الْاٰخِرَةِ وَالْاُوْلٰىۗ ۝٢٥ اِنَّ فِيْ ذٰلِكَ لَعِبْرَةً لِّمَنْ يَّخْشٰىۗࣖ ۝٢٦ ءَاَنْتُمْ اَشَدُّ خَلْقًا اَمِ السَّمَاۤءُۚ بَنٰىهَاۗ ۝٢٧ رَفَعَ سَمْكَهَا فَسَوّٰىهَاۙ ۝٢٨ وَاَغْطَشَ لَيْلَهَا وَاَخْرَجَ ضُحٰىهَاۖ ۝٢٩ وَالْاَرْضَ بَعْدَ ذٰلِكَ دَحٰىهَاۗ ۝٣٠', 'بَلِ الَّذِيْنَ كَفَرُوْا يُكَذِّبُوْنَۖ ۝٢٢ Al-Insyiqaq', 'اِلَّا الَّذِيْنَ اٰمَنُوْا وَعَمِلُوا الصّٰلِحٰتِ لَهُمْ اَجْرٌ غَيْرُ مَمْنُوْنٍࣖ ۝٢٥وَالسَّمَاۤءِ ذَاتِ الْبُرُوْجِۙ ۝١وَالْيَوْمِ الْمَوْعُوْدِۙ ۝٢وَشَاهِدٍ وَّمَشْهُوْدٍۗ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(14, 4, 'وَاِذَا الْكَوَاكِبُ انْتَثَرَتْۙ ۝٢ وَاِذَا الْبِحَارُ فُجِّرَتْۙ ۝٣ وَاِذَا الْقُبُوْرُ بُعْثِرَتْۙ ۝٤ عَلِمَتْ نَفْسٌ مَّا قَدَّمَتْ وَاَخَّرَتْۗ ۝٥ يٰٓاَيُّهَا الْاِنْسَانُ مَا غَرَّكَ بِرَبِّكَ الْكَرِيْمِۙ ۝٦ الَّذِيْ خَلَقَكَ فَسَوّٰىكَ فَعَدَلَكَۙ ۝٧ فِيْٓ اَيِّ صُوْرَةٍ مَّا شَاۤءَ رَكَّبَكَۗ ۝٨ كَلَّا بَلْ تُكَذِّبُوْنَ بِالدِّيْنِۙ ۝٩ وَاِنَّ عَلَيْكُمْ لَحٰفِظِيْنَۙ ۝١٠', 'قُتِلَ الْاِنْسَانُ مَآ اَكْفَرَهٗۗ ۝١٧ Al-Abasa', 'ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦وَالْفَجْرِۙ ۝١وَلَيَالٍ عَشْرٍۙ ۝٢وَّالشَّفْعِ وَالْوَتْرِۙ ۝٣وَالَّيْلِ اِذَا يَسْرِۚ ۝٤', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(15, 5, 'بَلٰىۛ اِنَّ رَبَّهٗ كَانَ بِهٖ بَصِيْرًاۗ ۝١٥ فَلَآ اُقْسِمُ بِالشَّفَقِۙ ۝١٦ وَالَّيْلِ وَمَا وَسَقَۙ ۝١٧ وَالْقَمَرِ اِذَا اتَّسَقَۙ ۝١٨ لَتَرْكَبُنَّ طَبَقًا عَنْ طَبَقٍۗ ۝١٩ فَمَا لَهُمْ لَا يُؤْمِنُوْنَۙ ۝٢٠ وَاِذَا قُرِئَ عَلَيْهِمُ الْقُرْاٰنُ لَا يَسْجُدُوْنَۗ ۩ ۝٢١ بَلِ الَّذِيْنَ كَفَرُوْا يُكَذِّبُوْنَۖ ۝٢٢ وَاللّٰهُ اَعْلَمُ بِمَا يُوْعُوْنَۖ ۝٢٣', 'ثُمَّ اِنَّهُمْ لَصَالُوا الْجَحِيْمِۗ ۝١٦ Al-Muthaffifin', 'عَلَيْهِمْ نَارٌ مُّؤْصَدَةٌࣖ ۝٢٠وَالشَّمْسِ وَضُحٰىهَاۖ ۝١وَالْقَمَرِ اِذَا تَلٰىهَاۖ ۝٢وَالنَّهَارِ اِذَا جَلّٰىهَاۖ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(16, 6, 'وَّحَدَاۤئِقَ غُلْبًا ۝٣٠وَفَاكِهَةً وَّاَبًّا ۝٣١مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٢فَاِذَا جَاۤءَتِ الصَّاۤخَّةُۖ ۝٣٣يَوْمَ يَفِرُّ الْمَرْءُ مِنْ اَخِيْهِۙ ۝٣٤وَاُمِّهٖ وَاَبِيْهِۙ ۝٣٥وَصَاحِبَتِهٖ وَبَنِيْهِۗ ۝٣٦لِكُلِّ امْرِئٍ مِّنْهُمْ يَوْمَىِٕذٍ شَأْنٌ يُّغْنِيْهِۗ ۝٣٧وُجُوْهٌ يَّوْمَىِٕذٍ مُّسْفِرَةٌۙ ۝٣٨ضَاحِكَةٌ مُّسْتَبْشِرَةٌۚ ۝٣٩وَوُجُوْهٌ يَّوْمَىِٕذٍ عَلَيْهَا غَبَرَةٌۙ ۝٤٠', 'كِرَامًا كٰتِبِيْنَۙ ۝١١ Al-Infitar', 'وَلَا يَخَافُ عُقْبٰهَاࣖ ۝١٥وَالَّيْلِ اِذَا يَغْشٰىۙ ۝١وَالنَّهَارِ اِذَا تَجَلّٰىۙ ۝٢وَمَا خَلَقَ الذَّكَرَ وَالْاُنْثٰىٓۙ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(17, 7, 'وَالْجِبَالَ اَرْسٰىهَاۙ ۝٣٢مَتَاعًا لَّكُمْ وَلِاَنْعَامِكُمْۗ ۝٣٣فَاِذَا جَاۤءَتِ الطَّاۤمَّةُ الْكُبْرٰىۖ ۝٣٤يَوْمَ يَتَذَكَّرُ الْاِنْسَانُ مَا سَعٰىۙ ۝٣٥وَبُرِّزَتِ الْجَحِيْمُ لِمَنْ يَّرٰى ۝٣٦فَاَمَّا مَنْ طَغٰىۖ ۝٣٧وَاٰثَرَ الْحَيٰوةَ الدُّنْيَاۙ ۝٣٨فَاِنَّ الْجَحِيْمَ هِيَ الْمَأْوٰىۗ ۝٣٩وَاَمَّا مَنْ خَافَ مَقَامَ رَبِّهٖ وَنَهَى النَّفْسَ عَنِ الْهَوٰىۙ ۝٤٠فَاِنَّ الْجَنَّةَ هِيَ الْمَأْوٰىۗ ۝٤١', 'وَتَأْكُلُوْنَ التُّرَاثَ اَكْلًا لَّمًّاۙ ۝١٩ Al-Fajr', 'وَاِلٰى رَبِّكَ فَارْغَبْࣖ ۝٨وَالتِّيْنِ وَالزَّيْتُوْنِۙ ۝١وَطُوْرِ سِيْنِيْنَۙ ۝٢وَهٰذَا الْبَلَدِ الْاَمِيْنِۙ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(18, 8, 'وَمَآ اَدْرٰىكَ مَا عِلِّيُّوْنَۗ ۝١٩كِتٰبٌ مَّرْقُوْمٌۙ ۝٢٠يَّشْهَدُهُ الْمُقَرَّبُوْنَۗ ۝٢١اِنَّ الْاَبْرَارَ لَفِيْ نَعِيْمٍۙ ۝٢٢عَلَى الْاَرَاۤىِٕكِ يَنْظُرُوْنَۙ ۝٢٣تَعْرِفُ فِيْ وُجُوْهِهِمْ نَضْرَةَ النَّعِيْمِۚ ۝٢٤يُسْقَوْنَ مِنْ رَّحِيْقٍ مَّخْتُوْمٍۙ ۝٢٥خِتٰمُهٗ مِسْكٌۗ وَفِيْ ذٰلِكَ فَلْيَتَنَافَسِ الْمُتَنٰفِسُوْنَۗ ۝٢٦وَمِزَاجُهٗ مِنْ تَسْنِيْمٍۙ ۝٢٧', 'اَوْ مِسْكِيْنًا ذَا مَتْرَبَةٍۗ ۝١٦ Al-Balad', 'وَمَنْ يَّعْمَلْ مِثْقَالَ ذَرَّةٍ شَرًّا يَّرَهٗࣖ ۝٨وَالْعٰدِيٰتِ ضَبْحًاۙ ۝١فَالْمُوْرِيٰتِ قَدْحًاۙ ۝٢فَالْمُغِيْرٰتِ صُبْحًاۙ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(19, 9, 'وَاِلَى السَّمَاۤءِ كَيْفَ رُفِعَتْۗ ۝١٨وَاِلَى الْجِبَالِ كَيْفَ نُصِبَتْۗ ۝١٩وَاِلَى الْاَرْضِ كَيْفَ سُطِحَتْۗ ۝٢٠فَذَكِّرْۗ اِنَّمَآ اَنْتَ مُذَكِّرٌۙ ۝٢١لَّسْتَ عَلَيْهِمْ بِمُصَيْطِرٍۙ ۝٢٢اِلَّا مَنْ تَوَلّٰى وَكَفَرَۙ ۝٢٣فَيُعَذِّبُهُ اللّٰهُ الْعَذَابَ الْاَكْبَرَۗ ۝٢٤اِنَّ اِلَيْنَآ اِيَابَهُمْ ۝٢٥ثُمَّ اِنَّ عَلَيْنَا حِسَابَهُمْࣖ ۝٢٦', 'يَصْلَوْنَهَا يَوْمَ الدِّيْنِ ۝١٥ Al-Infitar', 'اِنَّ رَبَّهُمْ بِهِمْ يَوْمَىِٕذٍ لَّخَبِيْرٌࣖ ۝١١اَلْقَارِعَةُۙ ۝١مَا الْقَارِعَةُۚ ۝٢وَمَآ اَدْرٰىكَ مَا الْقَارِعَةُۗ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43'),
(20, 10, 'وَكَذَّبَ بِالْحُسْنٰىۙ ۝٩فَسَنُيَسِّرُهٗ لِلْعُسْرٰىۗ ۝١٠وَمَا يُغْنِيْ عَنْهُ مَالُهٗٓ اِذَا تَرَدّٰىٓۙ ۝١١اِنَّ عَلَيْنَا لَلْهُدٰىۖ ۝١٢وَاِنَّ لَنَا لَلْاٰخِرَةَ وَالْاُوْلٰىۗ ۝١٣فَاَنْذَرْتُكُمْ نَارًا تَلَظّٰىۚ ۝١٤لَا يَصْلٰىهَآ اِلَّا الْاَشْقَىۙ ۝١٥الَّذِيْ كَذَّبَ وَتَوَلّٰىۗ ۝١٦وَسَيُجَنَّبُهَا الْاَتْقَىۙ ۝١٧الَّذِيْ يُؤْتِيْ مَالَهٗ يَتَزَكّٰىۚ ۝١٨', 'وَسَيُجَنَّبُهَا الْاَتْقَىۙ ۝١٧ Al-Lail', 'فِيْ عَمَدٍ مُّمَدَّدَةٍࣖ ۝٩اَلَمْ تَرَ كَيْفَ فَعَلَ رَبُّكَ بِاَصْحٰبِ الْفِيْلِۗ ۝١اَلَمْ يَجْعَلْ كَيْدَهُمْ فِيْ تَضْلِيْلٍۙ ۝٢وَّاَرْسَلَ عَلَيْهِمْ طَيْرًا اَبَابِيْلَۙ ۝٣', 'Aktif', '2025-10-23 04:56:43', '2025-10-23 04:56:43');

-- --------------------------------------------------------

--
-- Table structure for table `soal_terpakai`
--

CREATE TABLE `soal_terpakai` (
  `id` int(11) NOT NULL,
  `no_soal` int(11) NOT NULL,
  `tanggal_pakai` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Aktif','Reset') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal_terpakai`
--

INSERT INTO `soal_terpakai` (`id`, `no_soal`, `tanggal_pakai`, `status`) VALUES
(16, 2, '2026-09-17 02:23:31', 'Reset'),
(17, 1, '2026-09-17 02:26:18', 'Reset'),
(18, 3, '2026-09-17 09:07:38', 'Reset'),
(19, 14, '2026-09-17 09:13:37', 'Reset'),
(20, 15, '2026-09-17 09:14:31', 'Reset'),
(21, 4, '2026-09-17 10:19:31', 'Reset'),
(22, 5, '2026-09-17 10:22:06', 'Reset'),
(23, 1, '2026-09-17 10:58:52', 'Aktif'),
(24, 3, '2026-09-17 11:01:35', 'Aktif'),
(25, 10, '2026-09-17 11:02:15', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `soal_terpakai_final`
--

CREATE TABLE `soal_terpakai_final` (
  `id` int(11) NOT NULL,
  `no_soal` int(11) NOT NULL,
  `tanggal_pakai` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Aktif','Reset') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal_terpakai_final`
--

INSERT INTO `soal_terpakai_final` (`id`, `no_soal`, `tanggal_pakai`, `status`) VALUES
(11, 10, '2025-10-23 04:57:49', 'Reset'),
(12, 3, '2026-09-17 13:54:48', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `user_musabaqoh`
--

CREATE TABLE `user_musabaqoh` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_musabaqoh`
--

INSERT INTO `user_musabaqoh` (`id`, `username`, `password`, `nama_lengkap`, `status`, `created_at`, `updated_at`) VALUES
(1, 'soal', '17e8a079455e4fd957b9af0d4a0f0d3b', 'Soal Musabaqoh', 'Aktif', '2025-10-08 00:47:45', '2025-10-08 00:47:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `assignment_juri_final`
--
ALTER TABLE `assignment_juri_final`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_juri_peserta_final` (`juri_id`,`peserta_final_id`),
  ADD KEY `assignment_juri_final_ibfk_2` (`peserta_final_id`);

--
-- Indexes for table `dokumen_berka`
--
ALTER TABLE `dokumen_berka`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `juri`
--
ALTER TABLE `juri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `musabaqoh_aktif`
--
ALTER TABLE `musabaqoh_aktif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peserta_id` (`peserta_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_pengaturan` (`nama_pengaturan`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_penilaian` (`peserta_id`,`juri_id`),
  ADD KEY `juri_id` (`juri_id`);

--
-- Indexes for table `penilaian_final`
--
ALTER TABLE `penilaian_final`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_penilaian_final` (`peserta_final_id`,`juri_id`),
  ADD KEY `juri_id` (`juri_id`);

--
-- Indexes for table `peserta`
--
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `peserta_final`
--
ALTER TABLE `peserta_final`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_peserta_final` (`peserta_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `npsn` (`npsn`);

--
-- Indexes for table `soal_musabaqoh`
--
ALTER TABLE `soal_musabaqoh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_no_soal` (`no_soal`);

--
-- Indexes for table `soal_musabaqoh_final`
--
ALTER TABLE `soal_musabaqoh_final`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_no_soal_final` (`no_soal`);

--
-- Indexes for table `soal_terpakai`
--
ALTER TABLE `soal_terpakai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_soal` (`no_soal`);

--
-- Indexes for table `soal_terpakai_final`
--
ALTER TABLE `soal_terpakai_final`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_soal` (`no_soal`);

--
-- Indexes for table `user_musabaqoh`
--
ALTER TABLE `user_musabaqoh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignment_juri_final`
--
ALTER TABLE `assignment_juri_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dokumen_berka`
--
ALTER TABLE `dokumen_berka`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `juri`
--
ALTER TABLE `juri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `musabaqoh_aktif`
--
ALTER TABLE `musabaqoh_aktif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `penilaian_final`
--
ALTER TABLE `penilaian_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `peserta`
--
ALTER TABLE `peserta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `peserta_final`
--
ALTER TABLE `peserta_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `soal_musabaqoh`
--
ALTER TABLE `soal_musabaqoh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `soal_musabaqoh_final`
--
ALTER TABLE `soal_musabaqoh_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `soal_terpakai`
--
ALTER TABLE `soal_terpakai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `soal_terpakai_final`
--
ALTER TABLE `soal_terpakai_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_musabaqoh`
--
ALTER TABLE `user_musabaqoh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignment_juri_final`
--
ALTER TABLE `assignment_juri_final`
  ADD CONSTRAINT `assignment_juri_final_ibfk_1` FOREIGN KEY (`juri_id`) REFERENCES `juri` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_juri_final_ibfk_2` FOREIGN KEY (`peserta_final_id`) REFERENCES `peserta_final` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dokumen_berka`
--
ALTER TABLE `dokumen_berka`
  ADD CONSTRAINT `dokumen_berka_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `musabaqoh_aktif`
--
ALTER TABLE `musabaqoh_aktif`
  ADD CONSTRAINT `musabaqoh_aktif_ibfk_1` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penilaian_final`
--
ALTER TABLE `penilaian_final`
  ADD CONSTRAINT `penilaian_final_ibfk_1` FOREIGN KEY (`peserta_final_id`) REFERENCES `peserta_final` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penilaian_final_ibfk_2` FOREIGN KEY (`juri_id`) REFERENCES `juri` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peserta`
--
ALTER TABLE `peserta`
  ADD CONSTRAINT `peserta_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peserta_final`
--
ALTER TABLE `peserta_final`
  ADD CONSTRAINT `peserta_final_ibfk_1` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
