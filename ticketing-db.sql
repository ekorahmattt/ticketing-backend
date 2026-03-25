/*
SQLyog Professional v13.1.1 (64 bit)
MySQL - 10.4.27-MariaDB : Database - ticketing-db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ticketing-db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `ticketing-db`;

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`name`) values 
(1,'Komputer'),
(2,'Jaringan'),
(3,'SIMRS'),
(4,'Display'),
(5,'Printer'),
(6,'Scanner');

/*Table structure for table `device_connections` */

DROP TABLE IF EXISTS `device_connections`;

CREATE TABLE `device_connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_device_id` int(11) NOT NULL,
  `child_device_id` int(11) NOT NULL,
  `connection_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_device_id` (`parent_device_id`),
  KEY `child_device_id` (`child_device_id`),
  CONSTRAINT `device_connections_ibfk_1` FOREIGN KEY (`parent_device_id`) REFERENCES `devices` (`id`),
  CONSTRAINT `device_connections_ibfk_2` FOREIGN KEY (`child_device_id`) REFERENCES `devices` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `device_connections` */

insert  into `device_connections`(`id`,`parent_device_id`,`child_device_id`,`connection_type`) values 
(2,5,6,'USB'),
(3,7,8,'USB'),
(4,9,10,'USB'),
(5,11,12,'USB');

/*Table structure for table `device_types` */

DROP TABLE IF EXISTS `device_types`;

CREATE TABLE `device_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `device_types` */

insert  into `device_types`(`id`,`name`) values 
(1,'Computer'),
(2,'Printer'),
(3,'Access Point'),
(4,'CCTV');

/*Table structure for table `device_user_assignments` */

DROP TABLE IF EXISTS `device_user_assignments`;

CREATE TABLE `device_user_assignments` (
  `device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`device_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `device_user_assignments_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `device_user_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `device_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `device_user_assignments` */

insert  into `device_user_assignments`(`device_id`,`user_id`) values 
(3,1),
(3,2),
(3,3),
(5,4),
(5,5),
(5,6),
(7,7),
(7,8),
(7,9),
(9,10),
(9,11),
(9,12),
(11,13),
(11,14),
(11,15),
(13,2),
(13,3),
(14,16);

/*Table structure for table `device_users` */

DROP TABLE IF EXISTS `device_users`;

CREATE TABLE `device_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_device_users_unit` (`unit_id`),
  CONSTRAINT `fk_device_users_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `device_users` */

insert  into `device_users`(`id`,`name`,`full_name`,`unit_id`,`phone`,`created_at`) values 
(1,'andi','Andi Saputra',1,'081200000001','2026-03-20 22:35:42'),
(2,'budi','Budi Santoso',1,'081200000002','2026-03-20 22:35:42'),
(3,'citra','Citra Lestari',1,'081200000003','2026-03-20 22:35:42'),
(4,'dedi','Dedi Kurniawan',2,'081200000004','2026-03-20 22:35:42'),
(5,'eka','Eka Putri',2,'081200000005','2026-03-20 22:35:42'),
(6,'fajar','Fajar Nugroho',2,'081200000006','2026-03-20 22:35:42'),
(7,'gina','Gina Maharani',3,'081200000007','2026-03-20 22:35:42'),
(8,'hadi','Hadi Prasetyo',3,'081200000008','2026-03-20 22:35:42'),
(9,'indah','Indah Permata',3,'081200000009','2026-03-20 22:35:42'),
(10,'joko','Joko Susilo',4,'081200000010','2026-03-20 22:35:42'),
(11,'kiki','Kiki Amelia',4,'081200000011','2026-03-20 22:35:42'),
(12,'lukas','Lukas Wijaya',4,'081200000012','2026-03-20 22:35:42'),
(13,'maya','Maya Sari',5,'081200000013','2026-03-20 22:35:42'),
(14,'nina','Nina Oktaviani',5,'081200000014','2026-03-20 22:35:42'),
(15,'oscar','Oscar Gunawan',5,'081200000015','2026-03-20 22:35:42'),
(16,'Eko','Eko Rahmat',2,'08081023123','2026-03-25 01:29:44');

/*Table structure for table `devices` */

DROP TABLE IF EXISTS `devices`;

CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `device_type_id` int(11) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(100) DEFAULT NULL,
  `remote_address` varchar(50) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Aktif',
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_device_ip` (`ip_address`),
  KEY `idx_device_mac` (`mac_address`),
  KEY `fk_device_type` (`device_type_id`),
  KEY `fk_devices_created_by` (`created_by`),
  KEY `fk_devices_updated_by` (`updated_by`),
  KEY `fk_devices_unit` (`unit_id`),
  CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`),
  CONSTRAINT `fk_device_type` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`),
  CONSTRAINT `fk_devices_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_devices_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  CONSTRAINT `fk_devices_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `devices` */

insert  into `devices`(`id`,`device_name`,`brand`,`model`,`serial_number`,`device_type_id`,`ip_address`,`mac_address`,`remote_address`,`os`,`unit_id`,`coord_x`,`coord_y`,`last_seen`,`created_at`,`created_by`,`updated_by`,`updated_at`,`status`,`keterangan`) values 
(3,'PC-RJ-01','Dell','OptiPlex 3080','SN-RJ-PC01',1,'192.168.10.11','AA:BB:CC:DD:01','-','Windows 10',NULL,100,100,NULL,'2026-03-20 22:35:51',NULL,1,'2026-03-20 16:40:18','Aktif',NULL),
(4,'PR-RJ-01','HP','LaserJet 1020','SN-RJ-PR01',2,'192.168.10.21','AA:BB:CC:DD:02',NULL,NULL,1,120,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(5,'PC-RI-01','Lenovo','ThinkCentre M720','SN-RI-PC01',1,'192.168.20.11','AA:BB:CC:DD:03',NULL,'Windows 10',2,200,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(6,'PR-RI-01','Canon','LBP 2900','SN-RI-PR01',2,'192.168.20.21','AA:BB:CC:DD:04',NULL,NULL,2,220,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(7,'PC-IGD-01','HP','ProDesk 400','SN-IGD-PC01',1,'192.168.30.11','AA:BB:CC:DD:05',NULL,'Windows 11',3,300,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(8,'PR-IGD-01','Epson','L3110','SN-IGD-PR01',2,'192.168.30.21','AA:BB:CC:DD:06',NULL,NULL,3,320,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(9,'PC-RAD-01','Dell','Vostro 3681','SN-RAD-PC01',1,'192.168.40.11','AA:BB:CC:DD:07',NULL,'Windows 10',4,400,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(10,'PR-RAD-01','Brother','HL-L2321D','SN-RAD-PR01',2,'192.168.40.21','AA:BB:CC:DD:08',NULL,NULL,4,420,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(11,'PC-LAB-01','Acer','Veriton X','SN-LAB-PC01',1,'192.168.50.11','AA:BB:CC:DD:09',NULL,'Windows 10',5,500,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(12,'PR-LAB-01','HP','DeskJet 2336','SN-LAB-PR01',2,'192.168.50.21','AA:BB:CC:DD:10',NULL,NULL,5,520,100,NULL,'2026-03-20 22:35:51',NULL,NULL,NULL,'Aktif',NULL),
(13,'TEST-PC-01','Lenovo','Thinkpad','',1,'192.168.20.13','','-','',NULL,NULL,NULL,NULL,'2026-03-20 16:41:18',1,1,'2026-03-20 16:43:03','Aktif',NULL),
(14,'SIMRS-EKO','Acer','','',1,'::1','','','',2,NULL,NULL,NULL,'2026-03-25 01:41:49',1,NULL,NULL,'Aktif','');

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_type` enum('admin','device') NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_messages_ticket` (`ticket_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `messages` */

insert  into `messages`(`id`,`ticket_id`,`sender_type`,`sender_id`,`message`,`created_at`) values 
(3,10,'admin',1,'Ditunggu ya mbak','2026-03-25 01:09:45'),
(4,12,'admin',1,'ditunggu bro','2026-03-25 01:24:33'),
(5,12,'device',NULL,'oke aman','2026-03-25 01:24:43'),
(6,14,'admin',1,'ditunggu mak','2026-03-25 02:01:11'),
(7,14,'device',NULL,'oke mas','2026-03-25 02:01:17'),
(8,14,'admin',1,'password 12345','2026-03-25 02:01:31');

/*Table structure for table `subcategories` */

DROP TABLE IF EXISTS `subcategories`;

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `sla_minutes` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `subcategories` */

insert  into `subcategories`(`id`,`category_id`,`name`,`sla_minutes`) values 
(1,1,'Komputer tidak menyala',60),
(2,1,'Komputer sangat lambat',60),
(3,1,'Blue screen / error sistem',60),
(4,1,'Keyboard atau mouse tidak berfungsi',60),
(5,2,'Tidak bisa terhubung ke jaringan',30),
(6,2,'Internet sangat lambat',30),
(7,2,'Wifi sering terputus',30),
(8,2,'Tidak bisa akses server',30),
(9,3,'SIMRS tidak bisa login',15),
(10,3,'SIMRS error saat input data',15),
(11,3,'SIMRS tidak bisa mencetak',15),
(12,3,'SIMRS sangat lambat',15),
(13,4,'Monitor tidak menyala',60),
(14,4,'Tampilan layar tidak normal',60),
(15,4,'Resolusi layar bermasalah',60),
(16,5,'Printer tidak bisa mencetak',45),
(17,5,'Printer offline',45),
(18,5,'Hasil cetakan tidak jelas',45),
(19,5,'Kertas sering macet',45),
(20,6,'Scanner tidak terdeteksi',60),
(21,6,'Scanner tidak bisa scan',60),
(22,6,'Hasil scan tidak muncul',60),
(23,3,'INACBGs Tidak Bisa Diakses',NULL);

/*Table structure for table `ticket_attachments` */

DROP TABLE IF EXISTS `ticket_attachments`;

CREATE TABLE `ticket_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `ticket_attachments` */

insert  into `ticket_attachments`(`id`,`ticket_id`,`file_name`,`file_path`,`uploaded_at`) values 
(1,16,'ticket_16_1774406992.png','/uploads/tickets/ticket_16_1774406992.png','2026-03-25 10:49:52');

/*Table structure for table `tickets` */

DROP TABLE IF EXISTS `tickets`;

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` int(11) DEFAULT NULL,
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
  `subcategory_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `handled_by` (`handled_by`),
  KEY `idx_ticket_status` (`status`),
  KEY `idx_ticket_device` (`device_id`),
  KEY `fk_ticket_category` (`category_id`),
  KEY `fk_ticket_subcategory` (`subcategory_id`),
  CONSTRAINT `fk_ticket_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `fk_ticket_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tickets` */

insert  into `tickets`(`id`,`device_id`,`reporter_name`,`reporter_unit`,`reporter_contact`,`report_hostname`,`report_ip`,`report_device_brand`,`report_device_model`,`report_user_agent`,`title`,`description`,`action_taken`,`handling_notes`,`status`,`priority`,`sla_response_minutes`,`first_response_at`,`handled_by`,`created_at`,`updated_at`,`resolved_at`,`category_id`,`subcategory_id`) values 
(9,NULL,'Andi Saputra','Rawat Jalan','','SIMRS-EKO','::1','','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','SIMRS - SIMRS tidak bisa login','','Restart printer',NULL,'process','medium',NULL,'2026-03-25 01:21:11',NULL,'2026-03-25 08:07:56','2026-03-25 01:21:11',NULL,3,9),
(10,NULL,'Andi Saputra','IGD','','SIMRS-EKO','::1','','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','Printer - Printer tidak bisa mencetak','',NULL,NULL,'done','medium',NULL,'2026-03-25 01:09:57',1,'2026-03-25 08:09:12','2026-03-25 01:10:26',NULL,5,16),
(11,NULL,'Gina Maharani','Rawat Jalan','','SIMRS-EKO','::1','','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','SIMRS - SIMRS tidak bisa mencetak','',NULL,NULL,'open','medium',NULL,NULL,NULL,'2026-03-25 08:22:58',NULL,NULL,3,11),
(12,NULL,'Andi Saputra','Rawat Inap','','SIMRS-EKO','::1','','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','SIMRS - SIMRS tidak bisa login','',NULL,NULL,'open','medium',NULL,NULL,NULL,'2026-03-25 08:24:13',NULL,NULL,3,9),
(13,NULL,'Andi Saputra','Rawat Inap','','SIMRS-EKO','::1','','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','Komputer - Keyboard atau mouse tidak berfungsi','',NULL,NULL,'done','medium',NULL,'2026-03-25 01:25:49',NULL,'2026-03-25 08:25:29','2026-03-25 01:25:53',NULL,1,4),
(14,14,'Eko','Rawat Inap','','SIMRS-EKO','::1','Acer','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','SIMRS - SIMRS tidak bisa login','',NULL,NULL,'done','medium',NULL,'2026-03-25 02:01:56',NULL,'2026-03-25 09:00:51','2026-03-25 03:38:23',NULL,3,9),
(15,14,'Eko','Rawat Inap','','SIMRS-EKO','::1','Acer','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','Display - Monitor tidak menyala','',NULL,NULL,'open','medium',NULL,NULL,NULL,'2026-03-25 10:49:33',NULL,NULL,4,13),
(16,14,'Eko','Rawat Inap','','SIMRS-EKO','::1','Acer','','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','Printer - Hasil cetakan tidak jelas','',NULL,NULL,'open','medium',NULL,NULL,NULL,'2026-03-25 10:49:52',NULL,NULL,5,18);

/*Table structure for table `units` */

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `units` */

insert  into `units`(`id`,`name`,`code`,`created_at`) values 
(1,'Rawat Jalan','RJ','2026-03-20 23:51:16'),
(2,'Rawat Inap','RI','2026-03-20 23:51:16'),
(3,'IGD','IGD','2026-03-20 23:51:16'),
(4,'Radiologi','RAD','2026-03-20 23:51:16'),
(5,'Laboratorium','LAB','2026-03-20 23:51:16');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','superadmin') DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`username`,`password`,`role`,`last_login`,`created_at`,`updated_at`) values 
(1,'Eko Rahmat','Eko','$2y$10$F6aqWguvk8xKQ5u7a29QLetDjxleYbl8sBMJVOv.Ro6TsF8e/TjRa','superadmin','2026-03-25 01:08:18','2026-03-10 05:22:50','2026-03-19 15:29:47'),
(4,'Adrian Ronaldy','Ronal','$2y$10$jGIC6eduWvV6mqtDYJfBne6gpFEz3klu0czpR6jkrqg8FF55AIANG','admin','2026-03-19 15:28:39','2026-03-19 15:27:38',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
