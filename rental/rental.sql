-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2024 at 10:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bayar`
--

CREATE TABLE `tbl_bayar` (
  `id_bayar` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `id_kembali` int(11) DEFAULT NULL,
  `nopol` int(10) NOT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `total_bayar` decimal(10,2) DEFAULT NULL,
  `status` enum('lunas','belum lunas') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_bayar`
--

INSERT INTO `tbl_bayar` (`id_bayar`, `nik`, `id_kembali`, `nopol`, `tgl_bayar`, `total_bayar`, `status`) VALUES
(42, 0, 33, 0, '2024-10-28', 200000.00, 'lunas'),
(43, 0, 34, 0, '2024-10-28', 200000.00, 'lunas'),
(44, 0, 35, 0, '2024-10-28', 700000.00, 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kembali`
--

CREATE TABLE `tbl_kembali` (
  `id_kembali` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `kondisi_mobil` text DEFAULT NULL,
  `denda` decimal(10,2) DEFAULT NULL,
  `biaya_tambahan` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_kembali`
--

INSERT INTO `tbl_kembali` (`id_kembali`, `id_transaksi`, `tgl_kembali`, `kondisi_mobil`, `denda`, `biaya_tambahan`) VALUES
(33, 54, '2024-10-31', 'rusak\r\n', 100000.00, 100000.00),
(34, 55, '2024-10-31', 'bodol', 100000.00, 100000.00),
(35, 56, '2024-10-31', 'bauik\r\n', 100000.00, 600000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_member`
--

CREATE TABLE `tbl_member` (
  `nik` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jk` enum('L','P') DEFAULT NULL,
  `telp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `role` enum('user','','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_member`
--

INSERT INTO `tbl_member` (`nik`, `nama`, `jk`, `telp`, `alamat`, `user`, `pass`, `role`) VALUES
(2222, 'user', 'P', '0888', 'tul', 'user', '$2y$10$xuB2J25opQDuTj8aTvQaEuly3Rxfkvb7eKLm.wrix6LfwBEoRysoK', 'user'),
(321321, 'Bima', 'L', '9876543', 'Tulung', 'bima', '$2y$10$o2ekFd4upjxQftVHTUGFC.yBPj//MUAwhwwlyWascCPlbHKgSN9XW', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mobil`
--

CREATE TABLE `tbl_mobil` (
  `nopol` varchar(10) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `status` enum('tersedia','tidak') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_mobil`
--

INSERT INTO `tbl_mobil` (`nopol`, `brand`, `type`, `tahun`, `harga`, `foto`, `status`) VALUES
('Makar', 'Honda', 'SPort', '2000', 255000.00, 'upload/tabea-schimpf-O7WzqmeYoqc-unsplash.jpg', 'tersedia'),
('N1GG4', 'Toyota', 'PickuP', '2000', 300000.00, 'upload/toyota-tacoma_100751176_m.jpg', 'tersedia'),
('Webp', 'Honda', 'SUV', '2018', 150000.00, 'upload/tabea-schimpf-O7WzqmeYoqc-unsplash.jpg', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi`
--

CREATE TABLE `tbl_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `nik` int(11) DEFAULT NULL,
  `nopol` varchar(10) DEFAULT NULL,
  `tgl_booking` date DEFAULT NULL,
  `tgl_ambil` date DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `supir` tinyint(1) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `downpayment` decimal(10,2) DEFAULT NULL,
  `kekurangan` decimal(10,2) DEFAULT NULL,
  `status` enum('booking','approved','ambil','kembali') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transaksi`
--

INSERT INTO `tbl_transaksi` (`id_transaksi`, `nik`, `nopol`, `tgl_booking`, `tgl_ambil`, `tgl_kembali`, `supir`, `total`, `downpayment`, `kekurangan`, `status`) VALUES
(54, 2222, 'Webp', '2024-10-28', '2024-10-28', '2024-10-29', 0, 150000.00, 150000.00, 0.00, ''),
(55, 321321, 'N1GG4', '2024-10-28', '2024-10-28', '2024-10-29', 1, 400000.00, 400000.00, 0.00, ''),
(56, 2222, 'Webp', '2024-10-28', '2024-10-28', '2024-10-29', 1, 250000.00, 250000.00, 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int(11) NOT NULL,
  `user` varchar(50) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `lvl` enum('admin','petugas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `user`, `pass`, `lvl`) VALUES
(3, 'admin', '$2y$10$IaL2469mB9uLX5aFelDjVeO7UgtC2ICeQAVr9WgeZvc7nFS8n2Bd.', 'admin'),
(4, 'petugas', '$2y$10$u0rM2zCCkqt51ueRBQwdBepjZyNNVlpk5/WShmi.U7mKp10.lZxKq', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_bayar`
--
ALTER TABLE `tbl_bayar`
  ADD PRIMARY KEY (`id_bayar`),
  ADD KEY `id_kembali` (`id_kembali`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `tbl_kembali`
--
ALTER TABLE `tbl_kembali`
  ADD PRIMARY KEY (`id_kembali`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `tbl_member`
--
ALTER TABLE `tbl_member`
  ADD PRIMARY KEY (`nik`);

--
-- Indexes for table `tbl_mobil`
--
ALTER TABLE `tbl_mobil`
  ADD PRIMARY KEY (`nopol`);

--
-- Indexes for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `nik` (`nik`),
  ADD KEY `nopol` (`nopol`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_bayar`
--
ALTER TABLE `tbl_bayar`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tbl_kembali`
--
ALTER TABLE `tbl_kembali`
  MODIFY `id_kembali` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_bayar`
--
ALTER TABLE `tbl_bayar`
  ADD CONSTRAINT `tbl_bayar_ibfk_1` FOREIGN KEY (`id_kembali`) REFERENCES `tbl_kembali` (`id_kembali`);

--
-- Constraints for table `tbl_kembali`
--
ALTER TABLE `tbl_kembali`
  ADD CONSTRAINT `tbl_kembali_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `tbl_transaksi` (`id_transaksi`);

--
-- Constraints for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  ADD CONSTRAINT `tbl_transaksi_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `tbl_member` (`nik`),
  ADD CONSTRAINT `tbl_transaksi_ibfk_2` FOREIGN KEY (`nopol`) REFERENCES `tbl_mobil` (`nopol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
