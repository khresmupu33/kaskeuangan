-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 03:51 PM
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
-- Database: `kaskeuangan`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun_pembayaran`
--

CREATE TABLE `akun_pembayaran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_akun` varchar(50) NOT NULL,
  `saldo_akhir` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hutang_piutang`
--

CREATE TABLE `hutang_piutang` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pihak_terkait` varchar(100) NOT NULL,
  `jenis` enum('HUTANG','PIUTANG') NOT NULL,
  `total_nominal` decimal(15,2) NOT NULL,
  `sisa_nominal` decimal(15,2) NOT NULL,
  `bunga_persen` decimal(5,2) DEFAULT NULL,
  `tenggat_waktu` date DEFAULT NULL,
  `status` enum('AKTIF','LUNAS') DEFAULT 'AKTIF',
  `akun_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `user_id`) VALUES
(1, 'Transfer', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `target`
--

CREATE TABLE `target` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `deskripsi` varchar(100) NOT NULL,
  `nominal_maksimal` decimal(15,2) NOT NULL,
  `tenggat_waktu` date NOT NULL,
  `tipe_target` enum('SEKALI','RUTIN') DEFAULT 'SEKALI',
  `status` enum('AKTIF','SELESAI') DEFAULT 'AKTIF',
  `periode_target` enum('BULANAN','TAHUNAN') DEFAULT 'BULANAN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `akun_id` int(11) NOT NULL,
  `tipe_transaksi` enum('NORMAL','TRANSFER') DEFAULT 'NORMAL',
  `pemasukan` decimal(15,2) DEFAULT 0.00,
  `pengeluaran` decimal(15,2) DEFAULT 0.00,
  `path_bukti` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun_pembayaran`
--
ALTER TABLE `akun_pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_akun_pembayaran_user` (`user_id`);

--
-- Indexes for table `hutang_piutang`
--
ALTER TABLE `hutang_piutang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hutang_piutang_user` (`user_id`),
  ADD KEY `fk_hutang_piutang_akun` (`akun_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_akun_user` (`user_id`);

--
-- Indexes for table `target`
--
ALTER TABLE `target`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_target_user` (`user_id`),
  ADD KEY `fk_target_kategori` (`kategori_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transaksi_user` (`user_id`),
  ADD KEY `fk_transaksi_kategori` (`kategori_id`),
  ADD KEY `fk_transaksi_akun` (`akun_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun_pembayaran`
--
ALTER TABLE `akun_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hutang_piutang`
--
ALTER TABLE `hutang_piutang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `target`
--
ALTER TABLE `target`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `akun_pembayaran`
--
ALTER TABLE `akun_pembayaran`
  ADD CONSTRAINT `fk_akun_pembayaran_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hutang_piutang`
--
ALTER TABLE `hutang_piutang`
  ADD CONSTRAINT `fk_hutang_piutang_akun` FOREIGN KEY (`akun_id`) REFERENCES `akun_pembayaran` (`id`),
  ADD CONSTRAINT `fk_hutang_piutang_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kategori`
--
ALTER TABLE `kategori`
  ADD CONSTRAINT `fk_akun_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `target`
--
ALTER TABLE `target`
  ADD CONSTRAINT `fk_target_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`),
  ADD CONSTRAINT `fk_target_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_akun` FOREIGN KEY (`akun_id`) REFERENCES `akun_pembayaran` (`id`),
  ADD CONSTRAINT `fk_transaksi_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`),
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
