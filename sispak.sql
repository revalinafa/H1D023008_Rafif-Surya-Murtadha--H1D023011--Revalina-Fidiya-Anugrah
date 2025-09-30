-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 30, 2025 at 04:21 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sispak`
--

-- --------------------------------------------------------

--
-- Table structure for table `gejala`
--

CREATE TABLE `gejala` (
  `kode_gejala` varchar(10) NOT NULL,
  `nama_gejala` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gejala`
--

INSERT INTO `gejala` (`kode_gejala`, `nama_gejala`) VALUES
('G01', 'Mual'),
('G010', 'Dada Panas'),
('G011', 'Berat Badan Menurun'),
('G012', 'Kesulitan saat Menelan (Disfagia)'),
('G013', 'Cepat Kenyang saat Makan'),
('G02', 'Sering Bersendawa'),
('G03', 'Nyeri Dada'),
('G04', 'Gangguan Pencernaan'),
('G05', 'Nafsu Makan Berkurang'),
('G06', 'Nyeri Tulang Dada'),
('G07', 'Sering Sakit Tenggorokan'),
('G08', 'Batuk Kering'),
('G09', 'Perut Terasa Mulas');

-- --------------------------------------------------------

--
-- Table structure for table `penyakit`
--

CREATE TABLE `penyakit` (
  `kode_penyakit` varchar(10) NOT NULL,
  `nama_penyakit` varchar(100) NOT NULL,
  `deskripsi` text,
  `solusi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penyakit`
--

INSERT INTO `penyakit` (`kode_penyakit`, `nama_penyakit`, `deskripsi`, `solusi`) VALUES
('P01', 'Serangan Jantung', 'Kondisi serius akibat aliran darah ke jantung terhenti.', 'Segera hubungi bantuan medis, hindari aktivitas berat.'),
('P02', 'Perikarditis', 'Peradangan pada lapisan luar jantung (perikardium).', 'Obat anti-inflamasi dan istirahat cukup.'),
('P03', 'Jantung Koroner', 'Penyempitan pembuluh darah koroner yang menyuplai jantung.', 'Perubahan gaya hidup, obat, atau tindakan medis.'),
('P04', 'Refluks Dada Naik', 'Naiknya asam lambung ke kerongkongan dan menyebabkan nyeri dada.', 'Hindari makanan pemicu, tidur dengan posisi kepala lebih tinggi.'),
('P05', 'Pankreatitis', 'Peradangan pada pankreas yang menyebabkan nyeri perut.', 'Rawat inap, diet khusus, dan pengobatan medis.'),
('P06', 'Otot Tegang', 'Ketegangan otot di area dada menyebabkan nyeri.', 'Istirahat, kompres hangat, dan relaksan otot.'),
('P07', 'Pneumonia', 'Infeksi paru-paru yang menyebabkan peradangan kantung udara.', 'Antibiotik, cairan cukup, dan istirahat.');

-- --------------------------------------------------------

--
-- Table structure for table `penyakit_gejala`
--

CREATE TABLE `penyakit_gejala` (
  `id` int NOT NULL,
  `kode_penyakit` varchar(10) DEFAULT NULL,
  `kode_gejala` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penyakit_gejala`
--

INSERT INTO `penyakit_gejala` (`id`, `kode_penyakit`, `kode_gejala`) VALUES
(1, 'P01', 'G01'),
(2, 'P01', 'G02'),
(3, 'P01', 'G03'),
(4, 'P01', 'G06'),
(5, 'P01', 'G07'),
(6, 'P02', 'G01'),
(7, 'P02', 'G02'),
(8, 'P02', 'G04'),
(9, 'P02', 'G05'),
(10, 'P02', 'G06'),
(11, 'P02', 'G07'),
(12, 'P03', 'G01'),
(13, 'P03', 'G02'),
(14, 'P03', 'G03'),
(15, 'P03', 'G06'),
(16, 'P03', 'G011'),
(18, 'P04', 'G01'),
(19, 'P04', 'G03'),
(20, 'P04', 'G04'),
(21, 'P04', 'G07'),
(22, 'P04', 'G013'),
(23, 'P05', 'G02'),
(24, 'P05', 'G03'),
(25, 'P05', 'G08'),
(26, 'P05', 'G09'),
(27, 'P05', 'G010'),
(29, 'P06', 'G01'),
(30, 'P06', 'G02'),
(31, 'P06', 'G04'),
(32, 'P07', 'G03'),
(33, 'P07', 'G06'),
(34, 'P07', 'G07'),
(35, 'P07', 'G08'),
(36, 'P07', 'G012'),
(37, 'P04', 'G012'),
(38, 'P06', 'G05'),
(39, 'P06', 'G09'),
(40, 'P06', 'G011'),
(41, 'P06', 'G013'),
(42, 'P07', 'G010');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_konsultasi`
--

CREATE TABLE `riwayat_konsultasi` (
  `id_riwayat` int NOT NULL,
  `id_user` int NOT NULL,
  `gejala_dipilih` text NOT NULL,
  `hasil_diagnosa` varchar(255) NOT NULL,
  `probabilitas` float NOT NULL,
  `kode_penyakit` varchar(10) DEFAULT NULL,
  `waktu_konsultasi` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `riwayat_konsultasi`
--

INSERT INTO `riwayat_konsultasi` (`id_riwayat`, `id_user`, `gejala_dipilih`, `hasil_diagnosa`, `probabilitas`, `kode_penyakit`, `waktu_konsultasi`) VALUES
(106, 25, '[\"G01\",\"G010\",\"G011\"]', 'Jantung Koroner', 0.000386744, 'P03', '2025-09-19 02:46:06'),
(107, 25, '[\"G01\",\"G010\",\"G011\",\"G012\",\"G03\",\"G05\"]', 'Jantung Koroner', 0.000000972209, 'P03', '2025-09-29 04:00:07'),
(108, 25, '[\"G08\",\"G09\"]', 'Pankreatitis', 0.00291545, 'P05', '2025-09-29 04:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','pakar') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `role`) VALUES
(24, 'rafif', 'rafif', '$2y$10$k1BlSP4iwTZFKgKxSu/3xuCWJx8/nRIwmTyvu0Z.U8qQq9EzFDN1W', 'user'),
(25, 'rapip surya', 'rapip', '$2y$10$X9RxCSjU3daJwusfie8fNu75qC.rlGvHuk1QCdKkqPUEeIugqUz6C', 'user'),
(26, 'admin', 'admin', '$2y$10$nCyYInTj7YUMPUG3tj2yY.bstESB5BCSeWxgGA/yIW4w4TM3FArC.', 'admin'),
(27, 'pakar', 'pakar', '$2y$10$qkG9w8Bx1/AkHtQl.jApM.zn94kF89e6wQ.GdWZij3MJPeGq7Ryh6', 'pakar');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gejala`
--
ALTER TABLE `gejala`
  ADD PRIMARY KEY (`kode_gejala`);

--
-- Indexes for table `penyakit`
--
ALTER TABLE `penyakit`
  ADD PRIMARY KEY (`kode_penyakit`);

--
-- Indexes for table `penyakit_gejala`
--
ALTER TABLE `penyakit_gejala`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_penyakit` (`kode_penyakit`),
  ADD KEY `kode_gejala` (`kode_gejala`);

--
-- Indexes for table `riwayat_konsultasi`
--
ALTER TABLE `riwayat_konsultasi`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `riwayat_konsultasi_ibfk_1` (`id_user`),
  ADD KEY `fk_riwayat_penyakit` (`kode_penyakit`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penyakit_gejala`
--
ALTER TABLE `penyakit_gejala`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `riwayat_konsultasi`
--
ALTER TABLE `riwayat_konsultasi`
  MODIFY `id_riwayat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penyakit_gejala`
--
ALTER TABLE `penyakit_gejala`
  ADD CONSTRAINT `penyakit_gejala_ibfk_1` FOREIGN KEY (`kode_penyakit`) REFERENCES `penyakit` (`kode_penyakit`),
  ADD CONSTRAINT `penyakit_gejala_ibfk_2` FOREIGN KEY (`kode_gejala`) REFERENCES `gejala` (`kode_gejala`);

--
-- Constraints for table `riwayat_konsultasi`
--
ALTER TABLE `riwayat_konsultasi`
  ADD CONSTRAINT `fk_riwayat_penyakit` FOREIGN KEY (`kode_penyakit`) REFERENCES `penyakit` (`kode_penyakit`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `riwayat_konsultasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
