-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2026 at 02:59 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ticketing-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Komputer'),
(2, 'Jaringan'),
(3, 'SIMRS'),
(4, 'Display'),
(5, 'Printer'),
(6, 'Scanner');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `device_type_id` int(11) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(100) DEFAULT NULL,
  `remote_address` varchar(50) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `device_name`, `brand`, `model`, `serial_number`, `device_type_id`, `ip_address`, `mac_address`, `remote_address`, `os`, `unit`, `coord_x`, `coord_y`, `last_seen`, `created_at`, `updated_at`) VALUES
(1, 'IGD-PC-01', 'Acer', 'Aspire C227-1751', 'ACER-IGD-001', 1, '192.168.1.20', '00:1B:44:11:3A:B7', '192.168.1.20:3389', 'Windows 10 Pro', 'IGD', 340, 210, NULL, '2026-03-11 04:20:36', NULL),
(2, 'Printer-IGD-01', 'Epson', 'L3210', 'EPSON-IGD-PR01', 2, '192.168.1.30', 'A4:5E:60:11:22:33', NULL, NULL, 'IGD', 380, 215, NULL, '2026-03-11 04:21:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `device_connections`
--

CREATE TABLE `device_connections` (
  `id` int(11) NOT NULL,
  `parent_device_id` int(11) NOT NULL,
  `child_device_id` int(11) NOT NULL,
  `connection_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_types`
--

CREATE TABLE `device_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device_types`
--

INSERT INTO `device_types` (`id`, `name`) VALUES
(1, 'Computer'),
(2, 'Printer'),
(3, 'Access Point'),
(4, 'CCTV');

-- --------------------------------------------------------

--
-- Table structure for table `device_users`
--

CREATE TABLE `device_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_user_assignments`
--

CREATE TABLE `device_user_assignments` (
  `device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender_type` enum('admin','device') NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `ticket_id`, `sender_type`, `sender_id`, `message`, `created_at`) VALUES
(1, 8, 'admin', 1, 'Baik pak, akan segera kami cek komputernya.', '2026-03-11 08:46:43'),
(2, 8, 'device', NULL, 'Terima kasih pak, ditunggu kedatangannya.', '2026-03-11 08:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `sla_minutes` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`, `sla_minutes`) VALUES
(1, 1, 'Komputer tidak menyala', NULL),
(2, 1, 'Komputer sangat lambat', NULL),
(3, 1, 'Blue screen / error sistem', NULL),
(4, 1, 'Keyboard atau mouse tidak berfungsi', NULL),
(5, 2, 'Tidak bisa terhubung ke jaringan', NULL),
(6, 2, 'Internet sangat lambat', NULL),
(7, 2, 'Wifi sering terputus', NULL),
(8, 2, 'Tidak bisa akses server', NULL),
(9, 3, 'SIMRS tidak bisa login', NULL),
(10, 3, 'SIMRS error saat input data', NULL),
(11, 3, 'SIMRS tidak bisa mencetak', NULL),
(12, 3, 'SIMRS sangat lambat', NULL),
(13, 4, 'Monitor tidak menyala', NULL),
(14, 4, 'Tampilan layar tidak normal', NULL),
(15, 4, 'Resolusi layar bermasalah', NULL),
(16, 5, 'Printer tidak bisa mencetak', NULL),
(17, 5, 'Printer offline', NULL),
(18, 5, 'Hasil cetakan tidak jelas', NULL),
(19, 5, 'Kertas sering macet', NULL),
(20, 6, 'Scanner tidak terdeteksi', NULL),
(21, 6, 'Scanner tidak bisa scan', NULL),
(22, 6, 'Hasil scan tidak muncul', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `reporter_name` varchar(100) DEFAULT NULL,
  `reporter_unit` varchar(100) DEFAULT NULL,
  `reporter_contact` varchar(100) DEFAULT NULL,
  `report_hostname` varchar(100) DEFAULT NULL,
  `report_ip` varchar(50) DEFAULT NULL,
  `report_device_brand` varchar(100) DEFAULT NULL,
  `report_device_model` varchar(100) DEFAULT NULL,
  `report_user_agent` text DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `handling_notes` text DEFAULT NULL,
  `status` enum('open','process','pending','on_hold','done','cancelled') NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `sla_response_minutes` int(11) DEFAULT NULL,
  `first_response_at` datetime DEFAULT NULL,
  `handled_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `subcategory_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `device_id`, `reporter_name`, `reporter_unit`, `reporter_contact`, `report_hostname`, `report_ip`, `report_device_brand`, `report_device_model`, `report_user_agent`, `title`, `description`, `action_taken`, `handling_notes`, `status`, `priority`, `sla_response_minutes`, `first_response_at`, `handled_by`, `created_at`, `updated_at`, `resolved_at`, `category_id`, `subcategory_id`) VALUES
(8, 1, 'Budi Santoso', 'Poli Umum', '081234567890', 'SIMRS-EKO', '::1', 'Lenovo', 'ThinkCentre M720q', 'PostmanRuntime/7.51.1', '[UPDATE] Komputer Poli Umum mati sendiri', 'Admin update: Komputer Poli Umum-PC01 tipe Lenovo sering mati sendiri saat digunakan. Teknisi agar membawa sparepart PSU cadangan karena kemungkinan kerusakan daya.', NULL, NULL, '', 'medium', NULL, '2026-03-11 07:34:09', 1, '2026-03-11 04:31:40', '2026-03-11 07:36:51', NULL, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_attachments`
--

CREATE TABLE `ticket_attachments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','superadmin') DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Eko Rahmat', 'eko', '$2y$10$HSbXHem83TUxaPkn.8Ip6.LdhapOVPBSRL5a8ZBRsPRxdKcj.OEq6', 'superadmin', NULL, '2026-03-10 05:22:50', NULL),
(2, 'Rosmiati', 'rosmiati', '$2y$10$C5TmUmKey9i0v7cbHVGHhu1c1NeSM/AF5ZEioiQrOt9P1JnPuh6BO', 'admin', '2026-03-11 03:54:31', '2026-03-10 05:24:35', NULL),
(3, 'Ronal', 'ronal', '$2y$10$m9TWOuKQAFK4EYU3bH./gun3xrfSqY8Uq7OaKZ69h7BplZ0giP3Wi', 'admin', NULL, '2026-03-10 07:29:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_device_ip` (`ip_address`),
  ADD KEY `idx_device_mac` (`mac_address`),
  ADD KEY `fk_device_type` (`device_type_id`);

--
-- Indexes for table `device_connections`
--
ALTER TABLE `device_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_device_id` (`parent_device_id`),
  ADD KEY `child_device_id` (`child_device_id`);

--
-- Indexes for table `device_types`
--
ALTER TABLE `device_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_users`
--
ALTER TABLE `device_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_user_assignments`
--
ALTER TABLE `device_user_assignments`
  ADD PRIMARY KEY (`device_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_ticket` (`ticket_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `handled_by` (`handled_by`),
  ADD KEY `idx_ticket_status` (`status`),
  ADD KEY `idx_ticket_device` (`device_id`),
  ADD KEY `fk_ticket_category` (`category_id`),
  ADD KEY `fk_ticket_subcategory` (`subcategory_id`);

--
-- Indexes for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `device_connections`
--
ALTER TABLE `device_connections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_types`
--
ALTER TABLE `device_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `device_users`
--
ALTER TABLE `device_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`),
  ADD CONSTRAINT `fk_device_type` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`);

--
-- Constraints for table `device_connections`
--
ALTER TABLE `device_connections`
  ADD CONSTRAINT `device_connections_ibfk_1` FOREIGN KEY (`parent_device_id`) REFERENCES `devices` (`id`),
  ADD CONSTRAINT `device_connections_ibfk_2` FOREIGN KEY (`child_device_id`) REFERENCES `devices` (`id`);

--
-- Constraints for table `device_user_assignments`
--
ALTER TABLE `device_user_assignments`
  ADD CONSTRAINT `device_user_assignments_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `device_user_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `device_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_ticket_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_ticket_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`),
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD CONSTRAINT `ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
