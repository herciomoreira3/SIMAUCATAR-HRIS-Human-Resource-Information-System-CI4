-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2026 at 04:54 PM
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
-- Database: `starterpanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_settings`
--

CREATE TABLE `attendance_settings` (
  `id` int(11) UNSIGNED NOT NULL,
  `tama_hahu` time DEFAULT '08:00:00',
  `tama_remata` time DEFAULT '09:00:00',
  `sai_hahu` time DEFAULT '17:00:00',
  `sai_remata` time DEFAULT '18:00:00',
  `toleransia_minutu` int(11) DEFAULT 15,
  `updated_at` datetime DEFAULT NULL,
  `sabadu` tinyint(1) DEFAULT 0,
  `domingu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_settings`
--

INSERT INTO `attendance_settings` (`id`, `tama_hahu`, `tama_remata`, `sai_hahu`, `sai_remata`, `toleransia_minutu`, `updated_at`, `sabadu`, `domingu`) VALUES
(1, '14:20:00', '15:00:00', '14:29:00', '15:00:00', 0, '2026-05-15 23:43:52', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `avizu`
--

CREATE TABLE `avizu` (
  `id` int(11) UNSIGNED NOT NULL,
  `titulu` varchar(255) DEFAULT NULL,
  `konteudu` text DEFAULT NULL,
  `data_publikasaun` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `data_remata` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `avizu`
--

INSERT INTO `avizu` (`id`, `titulu`, `konteudu`, `data_publikasaun`, `created_at`, `updated_at`, `data_remata`) VALUES
(17, 'Enkontru Fulan Fulan', 'Favor marka prezensa iha enkontru.', '2026-05-15', NULL, NULL, '2026-05-22 14:47:36'),
(18, 'Avisu Feriadu', 'Aban loron feriadu nasionál.', '2026-05-14', NULL, NULL, '2026-05-16 14:47:36'),
(19, 'Konfigurasaun Absénsia Foun', 'Absénsia TAMA loke husi 14:20:00 to\'o 15:00:00. Absénsia SAI loke husi 14:29:00 to\'o 15:00:00. Favor absénte iha tempu ne\'ebé konese!', '2026-05-15', '2026-05-15 23:43:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departamentu`
--

CREATE TABLE `departamentu` (
  `id` int(11) UNSIGNED NOT NULL,
  `naran_departamentu` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departamentu`
--

INSERT INTO `departamentu` (`id`, `naran_departamentu`) VALUES
(5, 'Administrasaun'),
(6, 'Finansas'),
(7, 'Lojístika'),
(8, 'Rekursu Umanu'),
(9, 'TI');

-- --------------------------------------------------------

--
-- Table structure for table `funsionariu`
--

CREATE TABLE `funsionariu` (
  `id` int(11) UNSIGNED NOT NULL,
  `utilizador_id` int(11) UNSIGNED DEFAULT NULL,
  `nid` varchar(50) DEFAULT NULL,
  `naran_kompletu` varchar(150) DEFAULT NULL,
  `seksu` enum('Mane','Feto') DEFAULT NULL,
  `fatin_moris` varchar(100) DEFAULT NULL,
  `data_moris` date DEFAULT NULL,
  `hela_fatin` text DEFAULT NULL,
  `nu_telefone` varchar(20) DEFAULT NULL,
  `estadu_sivil` enum('Solteiru','Kaben Nain','Divorsiadu') DEFAULT NULL,
  `departamentu_id` int(11) UNSIGNED DEFAULT NULL,
  `pozisaun_id` int(11) UNSIGNED DEFAULT NULL,
  `kategoria_id` int(11) UNSIGNED DEFAULT NULL,
  `data_hahu_servisu` date DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funsionariu`
--

INSERT INTO `funsionariu` (`id`, `utilizador_id`, `nid`, `naran_kompletu`, `seksu`, `fatin_moris`, `data_moris`, `hela_fatin`, `nu_telefone`, `estadu_sivil`, `departamentu_id`, `pozisaun_id`, `kategoria_id`, `data_hahu_servisu`, `foto_perfil`, `created_at`, `updated_at`) VALUES
(5, 7, '2024001', 'Joao dos Santos', 'Mane', 'Dili', '1994-07-25', 'Dili', '71333625', 'Solteiru', 5, 7, 3, '2022-12-22', '1778850050_d4901467fd98de645f24.jpg', NULL, '2026-05-15 22:00:50'),
(6, 8, '2024002', 'Maria Soares', '', NULL, '1980-01-21', 'Dili', '75666753', '', 6, 7, 5, '2023-12-02', NULL, NULL, NULL),
(7, 9, '2024003', 'Antonio da Costa', '', NULL, '1983-10-11', 'Dili', '74734649', '', 9, 5, 4, '2021-08-26', NULL, NULL, NULL),
(8, 10, '2024004', 'Lucia Pires', '', NULL, '1989-08-19', 'Dili', '78652420', '', 9, 5, 4, '2020-09-28', NULL, NULL, NULL),
(9, 11, '2024005', 'Jose Ramos', '', NULL, '1984-11-24', 'Dili', '73022141', '', 6, 5, 3, '2021-10-08', NULL, NULL, NULL),
(10, 12, '2024006', 'Filomena Ximenes', '', NULL, '1990-08-04', 'Dili', '74669774', '', 5, 5, 3, '2022-04-20', NULL, NULL, NULL),
(11, 13, '2024007', 'Agostinho Belo', '', NULL, '1994-02-22', 'Dili', '72056264', '', 6, 8, 4, '2022-03-04', NULL, NULL, NULL),
(12, 14, '2024008', 'Rosa de Jesus', '', NULL, '1989-08-15', 'Dili', '77524378', '', 9, 7, 5, '2020-08-06', NULL, NULL, NULL),
(13, 15, '2024009', 'Bernardino Guterres', '', NULL, '1985-03-01', 'Dili', '71557391', '', 5, 8, 4, '2023-04-10', NULL, NULL, NULL),
(14, 16, '2024010', 'Teresa Amaral', '', NULL, '1992-06-24', 'Dili', '73717195', '', 5, 6, 3, '2022-05-13', NULL, NULL, NULL),
(15, 17, '2024011', 'Domingos Ferreira', '', NULL, '1982-03-17', 'Dili', '76588605', '', 8, 7, 4, '2020-03-06', NULL, NULL, NULL),
(16, 18, '2024012', 'Isabel Lopes', '', NULL, '1992-11-16', 'Dili', '77952075', '', 9, 8, 4, '2020-05-16', NULL, NULL, NULL),
(17, 19, '2024013', 'Francisco Mendonca', '', NULL, '1987-10-18', 'Dili', '77979968', '', 6, 6, 4, '2022-06-20', NULL, NULL, NULL),
(18, 20, '2024014', 'Ana Maria Silva', '', NULL, '1993-04-12', 'Dili', '74075275', '', 5, 5, 3, '2020-11-14', NULL, NULL, NULL),
(19, 21, '2024015', 'Mateus Oliveira', '', NULL, '1985-08-18', 'Dili', '73401938', '', 5, 7, 3, '2023-01-12', NULL, NULL, NULL),
(20, 22, '2024016', 'Jacinta Pereira', '', NULL, '1989-08-27', 'Dili', '77714525', '', 9, 7, 5, '2020-11-24', NULL, NULL, NULL),
(21, 23, '2024017', 'Gabriel de Araujo', '', NULL, '1992-03-14', 'Dili', '79869421', '', 9, 7, 4, '2021-07-06', NULL, NULL, NULL),
(22, 24, '2024018', 'Sofia Magno', '', NULL, '1994-08-17', 'Dili', '74700194', '', 7, 7, 4, '2021-03-17', NULL, NULL, NULL),
(23, 25, '2024019', 'Henrique Martins', '', NULL, '1987-12-26', 'Dili', '73360807', '', 8, 6, 4, '2022-05-17', NULL, NULL, NULL),
(24, 26, '2024020', 'Beatriz Gusmao', '', NULL, '1994-09-03', 'Dili', '74547750', '', 7, 7, 4, '2021-08-14', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategoria`
--

CREATE TABLE `kategoria` (
  `id` int(11) UNSIGNED NOT NULL,
  `naran_kategoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategoria`
--

INSERT INTO `kategoria` (`id`, `naran_kategoria`) VALUES
(3, 'Permanente'),
(4, 'Kontratadu'),
(5, 'Estajiáriu');

-- --------------------------------------------------------

--
-- Table structure for table `lisensa`
--

CREATE TABLE `lisensa` (
  `id` int(11) UNSIGNED NOT NULL,
  `funsionariu_id` int(11) UNSIGNED DEFAULT NULL,
  `tipu_lisensa` enum('Moras','Anual','Maternidade','Lutu','Seluk') DEFAULT NULL,
  `data_hahu` date DEFAULT NULL,
  `data_remata` date DEFAULT NULL,
  `razaun` text DEFAULT NULL,
  `dokumentu_suporta` varchar(255) DEFAULT NULL,
  `estadu_lisensa` enum('Pendente','Aprovadu','Rezeitadu') DEFAULT 'Pendente',
  `komentariu_admin` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lisensa`
--

INSERT INTO `lisensa` (`id`, `funsionariu_id`, `tipu_lisensa`, `data_hahu`, `data_remata`, `razaun`, `dokumentu_suporta`, `estadu_lisensa`, `komentariu_admin`, `created_at`, `updated_at`) VALUES
(6, 5, 'Moras', '2026-04-19', '2026-04-22', 'Asuntu familia (Mock)', NULL, 'Aprovadu', NULL, '2026-05-15 14:47:36', NULL),
(7, 6, 'Moras', '2026-04-11', '2026-04-12', 'Asuntu familia (Mock)', NULL, 'Aprovadu', NULL, '2026-05-15 14:47:36', NULL),
(8, 7, 'Moras', '2026-04-12', '2026-04-15', 'Asuntu familia (Mock)', NULL, 'Aprovadu', NULL, '2026-05-15 14:47:36', NULL),
(9, 8, '', '2026-04-01', '2026-04-04', 'Asuntu familia (Mock)', NULL, 'Aprovadu', NULL, '2026-05-15 14:47:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-06-15-113925', 'App\\Database\\Migrations\\Session', 'default', 'App', 1778805031, 1),
(2, '2025-06-15-114014', 'App\\Database\\Migrations\\UserManagement', 'default', 'App', 1778805031, 1);

-- --------------------------------------------------------

--
-- Table structure for table `papel`
--

CREATE TABLE `papel` (
  `id` int(11) UNSIGNED NOT NULL,
  `naran_papel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `papel`
--

INSERT INTO `papel` (`id`, `naran_papel`) VALUES
(1, 'administrador'),
(2, 'funsionariu');

-- --------------------------------------------------------

--
-- Table structure for table `pozisaun`
--

CREATE TABLE `pozisaun` (
  `id` int(11) UNSIGNED NOT NULL,
  `naran_pozisaun` varchar(100) DEFAULT NULL,
  `salariu_baziku` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pozisaun`
--

INSERT INTO `pozisaun` (`id`, `naran_pozisaun`, `salariu_baziku`) VALUES
(5, 'Diretor', 800.00),
(6, 'Xefe Seksaun', 500.00),
(7, 'Staff', 300.00),
(8, 'Asistente', 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `prezensa`
--

CREATE TABLE `prezensa` (
  `id` int(11) UNSIGNED NOT NULL,
  `funsionariu_id` int(11) UNSIGNED DEFAULT NULL,
  `data_prezensa` date DEFAULT NULL,
  `oras_tama` time DEFAULT NULL,
  `oras_sai` time DEFAULT NULL,
  `estadu_prezensa` enum('Prezente','Tardi','Falta','Lisensa') DEFAULT NULL,
  `foto_tama` varchar(255) DEFAULT NULL,
  `kordenada` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prezensa`
--

INSERT INTO `prezensa` (`id`, `funsionariu_id`, `data_prezensa`, `oras_tama`, `oras_sai`, `estadu_prezensa`, `foto_tama`, `kordenada`, `created_at`, `updated_at`) VALUES
(19, 5, '2026-04-01', '08:27:35', '17:11:16', 'Prezente', NULL, NULL, NULL, NULL),
(20, 5, '2026-04-02', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(21, 5, '2026-04-03', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(22, 5, '2026-04-06', '08:08:46', '17:12:37', 'Prezente', NULL, NULL, NULL, NULL),
(23, 5, '2026-04-07', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(24, 5, '2026-04-08', '08:12:47', '17:00:24', 'Prezente', NULL, NULL, NULL, NULL),
(25, 5, '2026-04-09', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(26, 5, '2026-04-10', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(27, 5, '2026-04-13', '08:24:11', '17:15:29', 'Prezente', NULL, NULL, NULL, NULL),
(28, 5, '2026-04-14', '08:27:34', '17:07:48', 'Prezente', NULL, NULL, NULL, NULL),
(29, 5, '2026-04-15', '08:20:31', '17:08:54', 'Prezente', NULL, NULL, NULL, NULL),
(30, 5, '2026-04-16', '08:14:43', '17:06:34', 'Prezente', NULL, NULL, NULL, NULL),
(31, 5, '2026-04-17', '08:17:13', '17:05:18', 'Prezente', NULL, NULL, NULL, NULL),
(32, 5, '2026-04-20', '08:29:54', '17:06:33', 'Prezente', NULL, NULL, NULL, NULL),
(33, 5, '2026-04-21', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(34, 5, '2026-04-22', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(35, 5, '2026-04-23', '08:18:17', '17:02:22', 'Prezente', NULL, NULL, NULL, NULL),
(36, 5, '2026-04-24', '08:22:35', '17:05:36', 'Prezente', NULL, NULL, NULL, NULL),
(37, 5, '2026-04-27', '08:19:49', '17:08:43', 'Prezente', NULL, NULL, NULL, NULL),
(38, 5, '2026-04-28', '08:02:15', '17:05:58', 'Prezente', NULL, NULL, NULL, NULL),
(39, 5, '2026-04-29', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(40, 5, '2026-04-30', '08:19:44', '17:08:10', 'Prezente', NULL, NULL, NULL, NULL),
(41, 6, '2026-04-01', '08:02:42', '17:04:48', 'Prezente', NULL, NULL, NULL, NULL),
(42, 6, '2026-04-02', '08:19:50', '17:07:49', 'Prezente', NULL, NULL, NULL, NULL),
(43, 6, '2026-04-03', '08:13:59', '17:13:16', 'Prezente', NULL, NULL, NULL, NULL),
(44, 6, '2026-04-06', '08:03:30', '17:01:52', 'Prezente', NULL, NULL, NULL, NULL),
(45, 6, '2026-04-07', '08:22:51', '17:14:47', 'Prezente', NULL, NULL, NULL, NULL),
(46, 6, '2026-04-08', '08:06:31', '17:12:47', 'Prezente', NULL, NULL, NULL, NULL),
(47, 6, '2026-04-09', '08:20:22', '17:10:58', 'Prezente', NULL, NULL, NULL, NULL),
(48, 6, '2026-04-10', '08:13:44', '17:08:59', 'Prezente', NULL, NULL, NULL, NULL),
(49, 6, '2026-04-13', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(50, 6, '2026-04-14', '08:21:46', '17:03:28', 'Prezente', NULL, NULL, NULL, NULL),
(51, 6, '2026-04-15', '08:21:14', '17:09:21', 'Prezente', NULL, NULL, NULL, NULL),
(52, 6, '2026-04-16', '08:04:38', '17:06:32', 'Prezente', NULL, NULL, NULL, NULL),
(53, 6, '2026-04-17', '08:11:56', '17:08:18', 'Prezente', NULL, NULL, NULL, NULL),
(54, 6, '2026-04-20', '08:17:30', '17:03:49', 'Prezente', NULL, NULL, NULL, NULL),
(55, 6, '2026-04-21', '08:08:45', '17:08:17', 'Prezente', NULL, NULL, NULL, NULL),
(56, 6, '2026-04-22', '08:09:44', '17:09:10', 'Prezente', NULL, NULL, NULL, NULL),
(57, 6, '2026-04-23', '08:26:18', '17:12:14', 'Prezente', NULL, NULL, NULL, NULL),
(58, 6, '2026-04-24', '08:21:28', '17:11:38', 'Prezente', NULL, NULL, NULL, NULL),
(59, 6, '2026-04-27', '08:18:23', '17:12:41', 'Prezente', NULL, NULL, NULL, NULL),
(60, 6, '2026-04-28', '08:27:13', '17:11:45', 'Prezente', NULL, NULL, NULL, NULL),
(61, 6, '2026-04-29', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(62, 6, '2026-04-30', '08:06:30', '17:05:53', 'Prezente', NULL, NULL, NULL, NULL),
(63, 7, '2026-04-01', '08:16:18', '17:03:54', 'Prezente', NULL, NULL, NULL, NULL),
(64, 7, '2026-04-02', '08:12:22', '17:00:41', 'Prezente', NULL, NULL, NULL, NULL),
(65, 7, '2026-04-03', '08:15:35', '17:04:52', 'Prezente', NULL, NULL, NULL, NULL),
(66, 7, '2026-04-06', '08:25:59', '17:15:22', 'Prezente', NULL, NULL, NULL, NULL),
(67, 7, '2026-04-07', '08:00:24', '17:11:45', 'Prezente', NULL, NULL, NULL, NULL),
(68, 7, '2026-04-08', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(69, 7, '2026-04-09', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(70, 7, '2026-04-10', '08:00:44', '17:00:45', 'Prezente', NULL, NULL, NULL, NULL),
(71, 7, '2026-04-13', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(72, 7, '2026-04-14', '08:30:49', '17:11:40', 'Prezente', NULL, NULL, NULL, NULL),
(73, 7, '2026-04-15', '08:19:11', '17:13:17', 'Prezente', NULL, NULL, NULL, NULL),
(74, 7, '2026-04-16', '08:04:13', '17:11:20', 'Prezente', NULL, NULL, NULL, NULL),
(75, 7, '2026-04-17', '08:20:27', '17:09:52', 'Prezente', NULL, NULL, NULL, NULL),
(76, 7, '2026-04-20', '08:15:49', '17:00:19', 'Prezente', NULL, NULL, NULL, NULL),
(77, 7, '2026-04-21', '08:05:10', '17:09:51', 'Prezente', NULL, NULL, NULL, NULL),
(78, 7, '2026-04-22', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(79, 7, '2026-04-23', '08:05:19', '17:03:51', 'Prezente', NULL, NULL, NULL, NULL),
(80, 7, '2026-04-24', '08:26:49', '17:03:51', 'Prezente', NULL, NULL, NULL, NULL),
(81, 7, '2026-04-27', '08:12:34', '17:09:54', 'Prezente', NULL, NULL, NULL, NULL),
(82, 7, '2026-04-28', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(83, 7, '2026-04-29', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(84, 7, '2026-04-30', '08:09:52', '17:09:24', 'Prezente', NULL, NULL, NULL, NULL),
(85, 8, '2026-04-01', '08:20:43', '17:11:13', 'Prezente', NULL, NULL, NULL, NULL),
(86, 8, '2026-04-02', '08:19:20', '17:10:42', 'Prezente', NULL, NULL, NULL, NULL),
(87, 8, '2026-04-03', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(88, 8, '2026-04-06', '08:19:53', '17:03:18', 'Prezente', NULL, NULL, NULL, NULL),
(89, 8, '2026-04-07', '08:01:12', '17:11:31', 'Prezente', NULL, NULL, NULL, NULL),
(90, 8, '2026-04-08', '08:22:44', '17:02:19', 'Prezente', NULL, NULL, NULL, NULL),
(91, 8, '2026-04-09', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(92, 8, '2026-04-10', '08:30:29', '17:02:54', 'Prezente', NULL, NULL, NULL, NULL),
(93, 8, '2026-04-13', '08:16:49', '17:11:16', 'Prezente', NULL, NULL, NULL, NULL),
(94, 8, '2026-04-14', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(95, 8, '2026-04-15', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(96, 8, '2026-04-16', '08:30:14', '17:04:11', 'Prezente', NULL, NULL, NULL, NULL),
(97, 8, '2026-04-17', '08:15:19', '17:11:38', 'Prezente', NULL, NULL, NULL, NULL),
(98, 8, '2026-04-20', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(99, 8, '2026-04-21', '08:23:41', '17:10:48', 'Prezente', NULL, NULL, NULL, NULL),
(100, 8, '2026-04-22', '08:13:10', '17:05:49', 'Prezente', NULL, NULL, NULL, NULL),
(101, 8, '2026-04-23', '08:03:34', '17:03:38', 'Prezente', NULL, NULL, NULL, NULL),
(102, 8, '2026-04-24', '08:10:22', '17:00:33', 'Prezente', NULL, NULL, NULL, NULL),
(103, 8, '2026-04-27', '08:20:42', '17:10:49', 'Prezente', NULL, NULL, NULL, NULL),
(104, 8, '2026-04-28', '08:02:41', '17:01:53', 'Prezente', NULL, NULL, NULL, NULL),
(105, 8, '2026-04-29', '08:15:44', '17:00:26', 'Prezente', NULL, NULL, NULL, NULL),
(106, 8, '2026-04-30', '08:00:30', '17:07:37', 'Prezente', NULL, NULL, NULL, NULL),
(107, 9, '2026-04-01', '08:05:37', '17:00:44', 'Prezente', NULL, NULL, NULL, NULL),
(108, 9, '2026-04-02', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(109, 9, '2026-04-03', '08:22:26', '17:06:25', 'Prezente', NULL, NULL, NULL, NULL),
(110, 9, '2026-04-06', '08:24:58', '17:11:50', 'Prezente', NULL, NULL, NULL, NULL),
(111, 9, '2026-04-07', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(112, 9, '2026-04-08', '08:29:58', '17:09:22', 'Prezente', NULL, NULL, NULL, NULL),
(113, 9, '2026-04-09', '08:29:43', '17:13:17', 'Prezente', NULL, NULL, NULL, NULL),
(114, 9, '2026-04-10', '08:12:31', '17:11:14', 'Prezente', NULL, NULL, NULL, NULL),
(115, 9, '2026-04-13', '08:17:45', '17:07:54', 'Prezente', NULL, NULL, NULL, NULL),
(116, 9, '2026-04-14', '08:23:28', '17:15:25', 'Prezente', NULL, NULL, NULL, NULL),
(117, 9, '2026-04-15', '08:14:54', '17:00:18', 'Prezente', NULL, NULL, NULL, NULL),
(118, 9, '2026-04-16', '08:23:22', '17:05:10', 'Prezente', NULL, NULL, NULL, NULL),
(119, 9, '2026-04-17', '08:01:37', '17:03:41', 'Prezente', NULL, NULL, NULL, NULL),
(120, 9, '2026-04-20', '08:02:42', '17:01:10', 'Prezente', NULL, NULL, NULL, NULL),
(121, 9, '2026-04-21', '08:21:50', '17:06:46', 'Prezente', NULL, NULL, NULL, NULL),
(122, 9, '2026-04-22', '08:15:42', '17:07:34', 'Prezente', NULL, NULL, NULL, NULL),
(123, 9, '2026-04-23', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(124, 9, '2026-04-24', '08:08:41', '17:01:41', 'Prezente', NULL, NULL, NULL, NULL),
(125, 9, '2026-04-27', '08:19:27', '17:08:12', 'Prezente', NULL, NULL, NULL, NULL),
(126, 9, '2026-04-28', '08:12:14', '17:12:25', 'Prezente', NULL, NULL, NULL, NULL),
(127, 9, '2026-04-29', '08:09:27', '17:08:19', 'Prezente', NULL, NULL, NULL, NULL),
(128, 9, '2026-04-30', '08:15:30', '17:03:53', 'Prezente', NULL, NULL, NULL, NULL),
(129, 10, '2026-04-01', '08:26:51', '17:10:53', 'Prezente', NULL, NULL, NULL, NULL),
(130, 10, '2026-04-02', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(131, 10, '2026-04-03', '08:26:31', '17:07:50', 'Prezente', NULL, NULL, NULL, NULL),
(132, 10, '2026-04-06', '08:16:14', '17:15:40', 'Prezente', NULL, NULL, NULL, NULL),
(133, 10, '2026-04-07', '08:13:56', '17:14:23', 'Prezente', NULL, NULL, NULL, NULL),
(134, 10, '2026-04-08', '08:00:54', '17:03:27', 'Prezente', NULL, NULL, NULL, NULL),
(135, 10, '2026-04-09', '08:24:35', '17:15:48', 'Prezente', NULL, NULL, NULL, NULL),
(136, 10, '2026-04-10', '08:27:13', '17:15:55', 'Prezente', NULL, NULL, NULL, NULL),
(137, 10, '2026-04-13', '08:09:50', '17:02:49', 'Prezente', NULL, NULL, NULL, NULL),
(138, 10, '2026-04-14', '08:04:51', '17:07:59', 'Prezente', NULL, NULL, NULL, NULL),
(139, 10, '2026-04-15', '08:10:35', '17:02:35', 'Prezente', NULL, NULL, NULL, NULL),
(140, 10, '2026-04-16', '08:18:18', '17:14:36', 'Prezente', NULL, NULL, NULL, NULL),
(141, 10, '2026-04-17', '08:07:52', '17:00:15', 'Prezente', NULL, NULL, NULL, NULL),
(142, 10, '2026-04-20', '08:20:36', '17:13:17', 'Prezente', NULL, NULL, NULL, NULL),
(143, 10, '2026-04-21', '08:23:32', '17:15:28', 'Prezente', NULL, NULL, NULL, NULL),
(144, 10, '2026-04-22', '08:29:13', '17:04:20', 'Prezente', NULL, NULL, NULL, NULL),
(145, 10, '2026-04-23', '08:03:48', '17:12:28', 'Prezente', NULL, NULL, NULL, NULL),
(146, 10, '2026-04-24', '08:30:32', '17:11:21', 'Prezente', NULL, NULL, NULL, NULL),
(147, 10, '2026-04-27', '08:01:25', '17:05:34', 'Prezente', NULL, NULL, NULL, NULL),
(148, 10, '2026-04-28', '08:29:44', '17:03:31', 'Prezente', NULL, NULL, NULL, NULL),
(149, 10, '2026-04-29', '08:04:56', '17:10:28', 'Prezente', NULL, NULL, NULL, NULL),
(150, 10, '2026-04-30', '08:30:50', '17:00:36', 'Prezente', NULL, NULL, NULL, NULL),
(151, 11, '2026-04-01', '08:26:36', '17:02:28', 'Prezente', NULL, NULL, NULL, NULL),
(152, 11, '2026-04-02', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(153, 11, '2026-04-03', '08:11:59', '17:06:22', 'Prezente', NULL, NULL, NULL, NULL),
(154, 11, '2026-04-06', '08:11:11', '17:11:17', 'Prezente', NULL, NULL, NULL, NULL),
(155, 11, '2026-04-07', '08:01:10', '17:06:36', 'Prezente', NULL, NULL, NULL, NULL),
(156, 11, '2026-04-08', '08:24:14', '17:13:37', 'Prezente', NULL, NULL, NULL, NULL),
(157, 11, '2026-04-09', '08:14:21', '17:05:19', 'Prezente', NULL, NULL, NULL, NULL),
(158, 11, '2026-04-10', '08:08:35', '17:05:46', 'Prezente', NULL, NULL, NULL, NULL),
(159, 11, '2026-04-13', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(160, 11, '2026-04-14', '08:27:16', '17:09:26', 'Prezente', NULL, NULL, NULL, NULL),
(161, 11, '2026-04-15', '08:11:51', '17:10:39', 'Prezente', NULL, NULL, NULL, NULL),
(162, 11, '2026-04-16', '08:27:18', '17:09:14', 'Prezente', NULL, NULL, NULL, NULL),
(163, 11, '2026-04-17', '08:30:19', '17:15:18', 'Prezente', NULL, NULL, NULL, NULL),
(164, 11, '2026-04-20', '08:24:10', '17:05:24', 'Prezente', NULL, NULL, NULL, NULL),
(165, 11, '2026-04-21', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(166, 11, '2026-04-22', '08:22:53', '17:02:15', 'Prezente', NULL, NULL, NULL, NULL),
(167, 11, '2026-04-23', '08:30:48', '17:10:52', 'Prezente', NULL, NULL, NULL, NULL),
(168, 11, '2026-04-24', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(169, 11, '2026-04-27', '08:20:37', '17:14:20', 'Prezente', NULL, NULL, NULL, NULL),
(170, 11, '2026-04-28', '08:01:14', '17:01:22', 'Prezente', NULL, NULL, NULL, NULL),
(171, 11, '2026-04-29', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(172, 11, '2026-04-30', '08:25:22', '17:09:20', 'Prezente', NULL, NULL, NULL, NULL),
(173, 12, '2026-04-01', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(174, 12, '2026-04-02', '08:00:59', '17:15:16', 'Prezente', NULL, NULL, NULL, NULL),
(175, 12, '2026-04-03', '08:25:31', '17:00:44', 'Prezente', NULL, NULL, NULL, NULL),
(176, 12, '2026-04-06', '08:19:32', '17:12:32', 'Prezente', NULL, NULL, NULL, NULL),
(177, 12, '2026-04-07', '08:11:41', '17:00:16', 'Prezente', NULL, NULL, NULL, NULL),
(178, 12, '2026-04-08', '08:19:48', '17:03:14', 'Prezente', NULL, NULL, NULL, NULL),
(179, 12, '2026-04-09', '08:25:16', '17:15:49', 'Prezente', NULL, NULL, NULL, NULL),
(180, 12, '2026-04-10', '08:06:24', '17:00:54', 'Prezente', NULL, NULL, NULL, NULL),
(181, 12, '2026-04-13', '08:08:21', '17:00:39', 'Prezente', NULL, NULL, NULL, NULL),
(182, 12, '2026-04-14', '08:27:28', '17:04:30', 'Prezente', NULL, NULL, NULL, NULL),
(183, 12, '2026-04-15', '08:03:29', '17:01:47', 'Prezente', NULL, NULL, NULL, NULL),
(184, 12, '2026-04-16', '08:02:10', '17:00:57', 'Prezente', NULL, NULL, NULL, NULL),
(185, 12, '2026-04-17', '08:12:14', '17:10:33', 'Prezente', NULL, NULL, NULL, NULL),
(186, 12, '2026-04-20', '08:07:19', '17:11:41', 'Prezente', NULL, NULL, NULL, NULL),
(187, 12, '2026-04-21', '08:04:50', '17:12:35', 'Prezente', NULL, NULL, NULL, NULL),
(188, 12, '2026-04-22', '08:22:36', '17:05:58', 'Prezente', NULL, NULL, NULL, NULL),
(189, 12, '2026-04-23', '08:14:52', '17:05:52', 'Prezente', NULL, NULL, NULL, NULL),
(190, 12, '2026-04-24', '08:07:49', '17:00:50', 'Prezente', NULL, NULL, NULL, NULL),
(191, 12, '2026-04-27', '08:00:26', '17:11:27', 'Prezente', NULL, NULL, NULL, NULL),
(192, 12, '2026-04-28', '08:29:47', '17:09:22', 'Prezente', NULL, NULL, NULL, NULL),
(193, 12, '2026-04-29', '08:26:57', '17:07:44', 'Prezente', NULL, NULL, NULL, NULL),
(194, 12, '2026-04-30', '08:05:36', '17:03:32', 'Prezente', NULL, NULL, NULL, NULL),
(195, 13, '2026-04-01', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(196, 13, '2026-04-02', '08:18:48', '17:14:37', 'Prezente', NULL, NULL, NULL, NULL),
(197, 13, '2026-04-03', '08:21:29', '17:13:47', 'Prezente', NULL, NULL, NULL, NULL),
(198, 13, '2026-04-06', '08:20:37', '17:12:31', 'Prezente', NULL, NULL, NULL, NULL),
(199, 13, '2026-04-07', '08:00:37', '17:04:28', 'Prezente', NULL, NULL, NULL, NULL),
(200, 13, '2026-04-08', '08:10:59', '17:05:56', 'Prezente', NULL, NULL, NULL, NULL),
(201, 13, '2026-04-09', '08:08:54', '17:13:27', 'Prezente', NULL, NULL, NULL, NULL),
(202, 13, '2026-04-10', '08:02:26', '17:13:12', 'Prezente', NULL, NULL, NULL, NULL),
(203, 13, '2026-04-13', '08:00:48', '17:05:11', 'Prezente', NULL, NULL, NULL, NULL),
(204, 13, '2026-04-14', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(205, 13, '2026-04-15', '08:16:39', '17:12:15', 'Prezente', NULL, NULL, NULL, NULL),
(206, 13, '2026-04-16', '08:16:25', '17:02:47', 'Prezente', NULL, NULL, NULL, NULL),
(207, 13, '2026-04-17', '08:28:52', '17:14:57', 'Prezente', NULL, NULL, NULL, NULL),
(208, 13, '2026-04-20', '08:20:50', '17:04:56', 'Prezente', NULL, NULL, NULL, NULL),
(209, 13, '2026-04-21', '08:23:37', '17:06:12', 'Prezente', NULL, NULL, NULL, NULL),
(210, 13, '2026-04-22', '08:00:49', '17:02:57', 'Prezente', NULL, NULL, NULL, NULL),
(211, 13, '2026-04-23', '08:24:10', '17:14:56', 'Prezente', NULL, NULL, NULL, NULL),
(212, 13, '2026-04-24', '08:29:59', '17:12:34', 'Prezente', NULL, NULL, NULL, NULL),
(213, 13, '2026-04-27', '08:26:57', '17:15:19', 'Prezente', NULL, NULL, NULL, NULL),
(214, 13, '2026-04-28', '08:16:31', '17:04:50', 'Prezente', NULL, NULL, NULL, NULL),
(215, 13, '2026-04-29', '08:14:46', '17:12:58', 'Prezente', NULL, NULL, NULL, NULL),
(216, 13, '2026-04-30', '08:18:12', '17:08:49', 'Prezente', NULL, NULL, NULL, NULL),
(217, 14, '2026-04-01', '08:06:17', '17:03:48', 'Prezente', NULL, NULL, NULL, NULL),
(218, 14, '2026-04-02', '08:14:44', '17:14:13', 'Prezente', NULL, NULL, NULL, NULL),
(219, 14, '2026-04-03', '08:24:32', '17:15:37', 'Prezente', NULL, NULL, NULL, NULL),
(220, 14, '2026-04-06', '08:14:17', '17:10:52', 'Prezente', NULL, NULL, NULL, NULL),
(221, 14, '2026-04-07', '08:21:51', '17:00:42', 'Prezente', NULL, NULL, NULL, NULL),
(222, 14, '2026-04-08', '08:02:47', '17:06:43', 'Prezente', NULL, NULL, NULL, NULL),
(223, 14, '2026-04-09', '08:00:55', '17:10:46', 'Prezente', NULL, NULL, NULL, NULL),
(224, 14, '2026-04-10', '08:15:54', '17:09:19', 'Prezente', NULL, NULL, NULL, NULL),
(225, 14, '2026-04-13', '08:25:58', '17:13:48', 'Prezente', NULL, NULL, NULL, NULL),
(226, 14, '2026-04-14', '08:18:53', '17:10:43', 'Prezente', NULL, NULL, NULL, NULL),
(227, 14, '2026-04-15', '08:10:37', '17:05:11', 'Prezente', NULL, NULL, NULL, NULL),
(228, 14, '2026-04-16', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(229, 14, '2026-04-17', '08:25:59', '17:01:10', 'Prezente', NULL, NULL, NULL, NULL),
(230, 14, '2026-04-20', '08:15:56', '17:12:42', 'Prezente', NULL, NULL, NULL, NULL),
(231, 14, '2026-04-21', '08:13:18', '17:08:21', 'Prezente', NULL, NULL, NULL, NULL),
(232, 14, '2026-04-22', '08:15:33', '17:00:45', 'Prezente', NULL, NULL, NULL, NULL),
(233, 14, '2026-04-23', '08:00:46', '17:02:58', 'Prezente', NULL, NULL, NULL, NULL),
(234, 14, '2026-04-24', '08:23:26', '17:02:28', 'Prezente', NULL, NULL, NULL, NULL),
(235, 14, '2026-04-27', '08:07:26', '17:08:33', 'Prezente', NULL, NULL, NULL, NULL),
(236, 14, '2026-04-28', '08:27:49', '17:07:12', 'Prezente', NULL, NULL, NULL, NULL),
(237, 14, '2026-04-29', '08:20:56', '17:12:31', 'Prezente', NULL, NULL, NULL, NULL),
(238, 14, '2026-04-30', '08:05:23', '17:15:29', 'Prezente', NULL, NULL, NULL, NULL),
(239, 15, '2026-04-01', '08:13:41', '17:12:16', 'Prezente', NULL, NULL, NULL, NULL),
(240, 15, '2026-04-02', '08:20:42', '17:14:56', 'Prezente', NULL, NULL, NULL, NULL),
(241, 15, '2026-04-03', '08:06:21', '17:10:46', 'Prezente', NULL, NULL, NULL, NULL),
(242, 15, '2026-04-06', '08:23:13', '17:12:15', 'Prezente', NULL, NULL, NULL, NULL),
(243, 15, '2026-04-07', '08:22:24', '17:02:52', 'Prezente', NULL, NULL, NULL, NULL),
(244, 15, '2026-04-08', '08:01:34', '17:10:50', 'Prezente', NULL, NULL, NULL, NULL),
(245, 15, '2026-04-09', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(246, 15, '2026-04-10', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(247, 15, '2026-04-13', '08:08:39', '17:14:22', 'Prezente', NULL, NULL, NULL, NULL),
(248, 15, '2026-04-14', '08:25:29', '17:04:44', 'Prezente', NULL, NULL, NULL, NULL),
(249, 15, '2026-04-15', '08:12:10', '17:12:20', 'Prezente', NULL, NULL, NULL, NULL),
(250, 15, '2026-04-16', '08:21:58', '17:14:33', 'Prezente', NULL, NULL, NULL, NULL),
(251, 15, '2026-04-17', '08:12:53', '17:14:11', 'Prezente', NULL, NULL, NULL, NULL),
(252, 15, '2026-04-20', '08:28:25', '17:03:11', 'Prezente', NULL, NULL, NULL, NULL),
(253, 15, '2026-04-21', '08:28:16', '17:05:29', 'Prezente', NULL, NULL, NULL, NULL),
(254, 15, '2026-04-22', '08:09:17', '17:06:24', 'Prezente', NULL, NULL, NULL, NULL),
(255, 15, '2026-04-23', '08:08:21', '17:04:28', 'Prezente', NULL, NULL, NULL, NULL),
(256, 15, '2026-04-24', '08:24:33', '17:11:17', 'Prezente', NULL, NULL, NULL, NULL),
(257, 15, '2026-04-27', '08:30:50', '17:02:56', 'Prezente', NULL, NULL, NULL, NULL),
(258, 15, '2026-04-28', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(259, 15, '2026-04-29', '08:27:10', '17:00:47', 'Prezente', NULL, NULL, NULL, NULL),
(260, 15, '2026-04-30', '08:25:26', '17:15:24', 'Prezente', NULL, NULL, NULL, NULL),
(261, 16, '2026-04-01', '08:25:35', '17:11:49', 'Prezente', NULL, NULL, NULL, NULL),
(262, 16, '2026-04-02', '08:20:24', '17:07:37', 'Prezente', NULL, NULL, NULL, NULL),
(263, 16, '2026-04-03', '08:04:58', '17:13:34', 'Prezente', NULL, NULL, NULL, NULL),
(264, 16, '2026-04-06', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(265, 16, '2026-04-07', '08:28:10', '17:01:15', 'Prezente', NULL, NULL, NULL, NULL),
(266, 16, '2026-04-08', '08:09:22', '17:05:30', 'Prezente', NULL, NULL, NULL, NULL),
(267, 16, '2026-04-09', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(268, 16, '2026-04-10', '08:17:44', '17:12:20', 'Prezente', NULL, NULL, NULL, NULL),
(269, 16, '2026-04-13', '08:01:19', '17:08:43', 'Prezente', NULL, NULL, NULL, NULL),
(270, 16, '2026-04-14', '08:11:36', '17:14:23', 'Prezente', NULL, NULL, NULL, NULL),
(271, 16, '2026-04-15', '08:28:52', '17:03:12', 'Prezente', NULL, NULL, NULL, NULL),
(272, 16, '2026-04-16', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(273, 16, '2026-04-17', '08:28:25', '17:11:33', 'Prezente', NULL, NULL, NULL, NULL),
(274, 16, '2026-04-20', '08:19:34', '17:01:54', 'Prezente', NULL, NULL, NULL, NULL),
(275, 16, '2026-04-21', '08:19:32', '17:01:48', 'Prezente', NULL, NULL, NULL, NULL),
(276, 16, '2026-04-22', '08:26:37', '17:02:53', 'Prezente', NULL, NULL, NULL, NULL),
(277, 16, '2026-04-23', '08:16:10', '17:13:55', 'Prezente', NULL, NULL, NULL, NULL),
(278, 16, '2026-04-24', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(279, 16, '2026-04-27', '08:23:47', '17:05:51', 'Prezente', NULL, NULL, NULL, NULL),
(280, 16, '2026-04-28', '08:06:20', '17:14:52', 'Prezente', NULL, NULL, NULL, NULL),
(281, 16, '2026-04-29', '08:03:11', '17:13:46', 'Prezente', NULL, NULL, NULL, NULL),
(282, 16, '2026-04-30', '08:18:15', '17:01:25', 'Prezente', NULL, NULL, NULL, NULL),
(283, 17, '2026-04-01', '08:30:44', '17:10:11', 'Prezente', NULL, NULL, NULL, NULL),
(284, 17, '2026-04-02', '08:09:23', '17:13:12', 'Prezente', NULL, NULL, NULL, NULL),
(285, 17, '2026-04-03', '08:12:14', '17:02:33', 'Prezente', NULL, NULL, NULL, NULL),
(286, 17, '2026-04-06', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(287, 17, '2026-04-07', '08:13:49', '17:11:19', 'Prezente', NULL, NULL, NULL, NULL),
(288, 17, '2026-04-08', '08:09:20', '17:04:33', 'Prezente', NULL, NULL, NULL, NULL),
(289, 17, '2026-04-09', '08:29:42', '17:09:52', 'Prezente', NULL, NULL, NULL, NULL),
(290, 17, '2026-04-10', '08:28:53', '17:11:52', 'Prezente', NULL, NULL, NULL, NULL),
(291, 17, '2026-04-13', '08:07:55', '17:00:41', 'Prezente', NULL, NULL, NULL, NULL),
(292, 17, '2026-04-14', '08:04:19', '17:15:27', 'Prezente', NULL, NULL, NULL, NULL),
(293, 17, '2026-04-15', '08:00:55', '17:02:35', 'Prezente', NULL, NULL, NULL, NULL),
(294, 17, '2026-04-16', '08:00:20', '17:01:16', 'Prezente', NULL, NULL, NULL, NULL),
(295, 17, '2026-04-17', '08:24:43', '17:11:34', 'Prezente', NULL, NULL, NULL, NULL),
(296, 17, '2026-04-20', '08:12:41', '17:13:44', 'Prezente', NULL, NULL, NULL, NULL),
(297, 17, '2026-04-21', '08:05:13', '17:05:14', 'Prezente', NULL, NULL, NULL, NULL),
(298, 17, '2026-04-22', '08:17:56', '17:10:54', 'Prezente', NULL, NULL, NULL, NULL),
(299, 17, '2026-04-23', '08:07:59', '17:01:42', 'Prezente', NULL, NULL, NULL, NULL),
(300, 17, '2026-04-24', '08:16:55', '17:11:58', 'Prezente', NULL, NULL, NULL, NULL),
(301, 17, '2026-04-27', '08:03:51', '17:07:50', 'Prezente', NULL, NULL, NULL, NULL),
(302, 17, '2026-04-28', '08:02:44', '17:07:33', 'Prezente', NULL, NULL, NULL, NULL),
(303, 17, '2026-04-29', '08:27:45', '17:11:25', 'Prezente', NULL, NULL, NULL, NULL),
(304, 17, '2026-04-30', '08:18:48', '17:12:51', 'Prezente', NULL, NULL, NULL, NULL),
(305, 18, '2026-04-01', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(306, 18, '2026-04-02', '08:28:18', '17:15:44', 'Prezente', NULL, NULL, NULL, NULL),
(307, 18, '2026-04-03', '08:16:25', '17:05:14', 'Prezente', NULL, NULL, NULL, NULL),
(308, 18, '2026-04-06', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(309, 18, '2026-04-07', '08:05:56', '17:05:24', 'Prezente', NULL, NULL, NULL, NULL),
(310, 18, '2026-04-08', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(311, 18, '2026-04-09', '08:14:20', '17:03:52', 'Prezente', NULL, NULL, NULL, NULL),
(312, 18, '2026-04-10', '08:16:37', '17:14:56', 'Prezente', NULL, NULL, NULL, NULL),
(313, 18, '2026-04-13', '08:13:38', '17:05:42', 'Prezente', NULL, NULL, NULL, NULL),
(314, 18, '2026-04-14', '08:25:43', '17:15:43', 'Prezente', NULL, NULL, NULL, NULL),
(315, 18, '2026-04-15', '08:07:27', '17:01:11', 'Prezente', NULL, NULL, NULL, NULL),
(316, 18, '2026-04-16', '08:25:21', '17:13:10', 'Prezente', NULL, NULL, NULL, NULL),
(317, 18, '2026-04-17', '08:27:10', '17:15:25', 'Prezente', NULL, NULL, NULL, NULL),
(318, 18, '2026-04-20', '08:09:14', '17:12:59', 'Prezente', NULL, NULL, NULL, NULL),
(319, 18, '2026-04-21', '08:10:49', '17:02:51', 'Prezente', NULL, NULL, NULL, NULL),
(320, 18, '2026-04-22', '08:28:34', '17:00:21', 'Prezente', NULL, NULL, NULL, NULL),
(321, 18, '2026-04-23', '08:27:48', '17:14:29', 'Prezente', NULL, NULL, NULL, NULL),
(322, 18, '2026-04-24', '08:27:43', '17:05:35', 'Prezente', NULL, NULL, NULL, NULL),
(323, 18, '2026-04-27', '08:26:49', '17:05:20', 'Prezente', NULL, NULL, NULL, NULL),
(324, 18, '2026-04-28', '08:03:11', '17:10:10', 'Prezente', NULL, NULL, NULL, NULL),
(325, 18, '2026-04-29', '08:13:54', '17:08:50', 'Prezente', NULL, NULL, NULL, NULL),
(326, 18, '2026-04-30', '08:25:11', '17:05:53', 'Prezente', NULL, NULL, NULL, NULL),
(327, 19, '2026-04-01', '08:22:33', '17:06:42', 'Prezente', NULL, NULL, NULL, NULL),
(328, 19, '2026-04-02', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(329, 19, '2026-04-03', '08:15:47', '17:12:12', 'Prezente', NULL, NULL, NULL, NULL),
(330, 19, '2026-04-06', '08:12:24', '17:14:58', 'Prezente', NULL, NULL, NULL, NULL),
(331, 19, '2026-04-07', '08:14:27', '17:01:34', 'Prezente', NULL, NULL, NULL, NULL),
(332, 19, '2026-04-08', '08:00:43', '17:09:32', 'Prezente', NULL, NULL, NULL, NULL),
(333, 19, '2026-04-09', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(334, 19, '2026-04-10', '08:22:41', '17:08:18', 'Prezente', NULL, NULL, NULL, NULL),
(335, 19, '2026-04-13', '08:15:32', '17:04:43', 'Prezente', NULL, NULL, NULL, NULL),
(336, 19, '2026-04-14', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(337, 19, '2026-04-15', '08:06:44', '17:12:23', 'Prezente', NULL, NULL, NULL, NULL),
(338, 19, '2026-04-16', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(339, 19, '2026-04-17', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(340, 19, '2026-04-20', '08:22:37', '17:12:16', 'Prezente', NULL, NULL, NULL, NULL),
(341, 19, '2026-04-21', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(342, 19, '2026-04-22', '08:29:37', '17:00:29', 'Prezente', NULL, NULL, NULL, NULL),
(343, 19, '2026-04-23', '08:16:30', '17:02:52', 'Prezente', NULL, NULL, NULL, NULL),
(344, 19, '2026-04-24', '08:10:13', '17:13:55', 'Prezente', NULL, NULL, NULL, NULL),
(345, 19, '2026-04-27', '08:24:24', '17:15:38', 'Prezente', NULL, NULL, NULL, NULL),
(346, 19, '2026-04-28', '08:06:14', '17:15:29', 'Prezente', NULL, NULL, NULL, NULL),
(347, 19, '2026-04-29', '08:22:27', '17:08:28', 'Prezente', NULL, NULL, NULL, NULL),
(348, 19, '2026-04-30', '08:08:47', '17:01:59', 'Prezente', NULL, NULL, NULL, NULL),
(349, 20, '2026-04-01', '08:19:53', '17:10:48', 'Prezente', NULL, NULL, NULL, NULL),
(350, 20, '2026-04-02', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(351, 20, '2026-04-03', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(352, 20, '2026-04-06', '08:15:30', '17:04:36', 'Prezente', NULL, NULL, NULL, NULL),
(353, 20, '2026-04-07', '08:19:56', '17:10:51', 'Prezente', NULL, NULL, NULL, NULL),
(354, 20, '2026-04-08', '08:20:32', '17:13:28', 'Prezente', NULL, NULL, NULL, NULL),
(355, 20, '2026-04-09', '08:21:46', '17:13:32', 'Prezente', NULL, NULL, NULL, NULL),
(356, 20, '2026-04-10', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(357, 20, '2026-04-13', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(358, 20, '2026-04-14', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(359, 20, '2026-04-15', '08:19:27', '17:13:54', 'Prezente', NULL, NULL, NULL, NULL),
(360, 20, '2026-04-16', '08:04:14', '17:13:10', 'Prezente', NULL, NULL, NULL, NULL),
(361, 20, '2026-04-17', '08:03:29', '17:12:11', 'Prezente', NULL, NULL, NULL, NULL),
(362, 20, '2026-04-20', '08:27:30', '17:09:41', 'Prezente', NULL, NULL, NULL, NULL),
(363, 20, '2026-04-21', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(364, 20, '2026-04-22', '08:28:35', '17:06:45', 'Prezente', NULL, NULL, NULL, NULL),
(365, 20, '2026-04-23', '08:05:39', '17:08:27', 'Prezente', NULL, NULL, NULL, NULL),
(366, 20, '2026-04-24', '08:29:43', '17:12:41', 'Prezente', NULL, NULL, NULL, NULL),
(367, 20, '2026-04-27', '08:12:19', '17:09:50', 'Prezente', NULL, NULL, NULL, NULL),
(368, 20, '2026-04-28', '08:14:47', '17:05:21', 'Prezente', NULL, NULL, NULL, NULL),
(369, 20, '2026-04-29', '08:09:21', '17:11:30', 'Prezente', NULL, NULL, NULL, NULL),
(370, 20, '2026-04-30', '08:22:18', '17:15:28', 'Prezente', NULL, NULL, NULL, NULL),
(371, 21, '2026-04-01', '08:14:11', '17:11:16', 'Prezente', NULL, NULL, NULL, NULL),
(372, 21, '2026-04-02', '08:10:14', '17:10:13', 'Prezente', NULL, NULL, NULL, NULL),
(373, 21, '2026-04-03', '08:30:50', '17:03:51', 'Prezente', NULL, NULL, NULL, NULL),
(374, 21, '2026-04-06', '08:10:56', '17:06:41', 'Prezente', NULL, NULL, NULL, NULL),
(375, 21, '2026-04-07', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(376, 21, '2026-04-08', '08:08:11', '17:12:28', 'Prezente', NULL, NULL, NULL, NULL),
(377, 21, '2026-04-09', '08:00:35', '17:07:45', 'Prezente', NULL, NULL, NULL, NULL),
(378, 21, '2026-04-10', '08:25:57', '17:07:13', 'Prezente', NULL, NULL, NULL, NULL),
(379, 21, '2026-04-13', '08:16:30', '17:14:28', 'Prezente', NULL, NULL, NULL, NULL),
(380, 21, '2026-04-14', '08:25:11', '17:08:23', 'Prezente', NULL, NULL, NULL, NULL),
(381, 21, '2026-04-15', '08:08:46', '17:05:47', 'Prezente', NULL, NULL, NULL, NULL),
(382, 21, '2026-04-16', '08:09:29', '17:01:19', 'Prezente', NULL, NULL, NULL, NULL),
(383, 21, '2026-04-17', '08:02:45', '17:07:48', 'Prezente', NULL, NULL, NULL, NULL),
(384, 21, '2026-04-20', '08:06:29', '17:02:38', 'Prezente', NULL, NULL, NULL, NULL),
(385, 21, '2026-04-21', '08:12:28', '17:10:15', 'Prezente', NULL, NULL, NULL, NULL),
(386, 21, '2026-04-22', '08:13:42', '17:03:23', 'Prezente', NULL, NULL, NULL, NULL),
(387, 21, '2026-04-23', '08:10:59', '17:12:55', 'Prezente', NULL, NULL, NULL, NULL),
(388, 21, '2026-04-24', '08:20:19', '17:09:31', 'Prezente', NULL, NULL, NULL, NULL),
(389, 21, '2026-04-27', '08:11:58', '17:02:18', 'Prezente', NULL, NULL, NULL, NULL),
(390, 21, '2026-04-28', '08:24:35', '17:14:24', 'Prezente', NULL, NULL, NULL, NULL),
(391, 21, '2026-04-29', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(392, 21, '2026-04-30', '08:28:12', '17:01:53', 'Prezente', NULL, NULL, NULL, NULL),
(393, 22, '2026-04-01', '08:08:41', '17:10:47', 'Prezente', NULL, NULL, NULL, NULL),
(394, 22, '2026-04-02', '08:12:21', '17:15:18', 'Prezente', NULL, NULL, NULL, NULL),
(395, 22, '2026-04-03', '08:06:52', '17:00:40', 'Prezente', NULL, NULL, NULL, NULL),
(396, 22, '2026-04-06', '08:18:55', '17:14:39', 'Prezente', NULL, NULL, NULL, NULL),
(397, 22, '2026-04-07', '08:30:10', '17:01:21', 'Prezente', NULL, NULL, NULL, NULL),
(398, 22, '2026-04-08', '08:10:13', '17:04:11', 'Prezente', NULL, NULL, NULL, NULL),
(399, 22, '2026-04-09', '08:14:26', '17:03:25', 'Prezente', NULL, NULL, NULL, NULL),
(400, 22, '2026-04-10', '08:05:21', '17:12:22', 'Prezente', NULL, NULL, NULL, NULL),
(401, 22, '2026-04-13', '08:25:58', '17:02:51', 'Prezente', NULL, NULL, NULL, NULL),
(402, 22, '2026-04-14', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(403, 22, '2026-04-15', '08:11:50', '17:09:24', 'Prezente', NULL, NULL, NULL, NULL),
(404, 22, '2026-04-16', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(405, 22, '2026-04-17', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(406, 22, '2026-04-20', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(407, 22, '2026-04-21', '08:04:49', '17:11:26', 'Prezente', NULL, NULL, NULL, NULL),
(408, 22, '2026-04-22', '08:27:16', '17:07:23', 'Prezente', NULL, NULL, NULL, NULL),
(409, 22, '2026-04-23', '08:27:58', '17:15:35', 'Prezente', NULL, NULL, NULL, NULL),
(410, 22, '2026-04-24', '08:06:47', '17:00:47', 'Prezente', NULL, NULL, NULL, NULL),
(411, 22, '2026-04-27', '08:03:10', '17:10:55', 'Prezente', NULL, NULL, NULL, NULL),
(412, 22, '2026-04-28', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(413, 22, '2026-04-29', '08:30:45', '17:09:13', 'Prezente', NULL, NULL, NULL, NULL),
(414, 22, '2026-04-30', '08:08:10', '17:06:42', 'Prezente', NULL, NULL, NULL, NULL),
(415, 23, '2026-04-01', '08:12:14', '17:04:22', 'Prezente', NULL, NULL, NULL, NULL),
(416, 23, '2026-04-02', '08:30:37', '17:13:24', 'Prezente', NULL, NULL, NULL, NULL),
(417, 23, '2026-04-03', '08:26:22', '17:09:34', 'Prezente', NULL, NULL, NULL, NULL),
(418, 23, '2026-04-06', '08:25:41', '17:05:20', 'Prezente', NULL, NULL, NULL, NULL),
(419, 23, '2026-04-07', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(420, 23, '2026-04-08', '08:29:37', '17:07:54', 'Prezente', NULL, NULL, NULL, NULL),
(421, 23, '2026-04-09', '08:19:15', '17:15:28', 'Prezente', NULL, NULL, NULL, NULL),
(422, 23, '2026-04-10', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(423, 23, '2026-04-13', '08:10:36', '17:13:54', 'Prezente', NULL, NULL, NULL, NULL),
(424, 23, '2026-04-14', '08:25:28', '17:07:52', 'Prezente', NULL, NULL, NULL, NULL),
(425, 23, '2026-04-15', '08:14:52', '17:15:45', 'Prezente', NULL, NULL, NULL, NULL),
(426, 23, '2026-04-16', '08:10:57', '17:07:33', 'Prezente', NULL, NULL, NULL, NULL),
(427, 23, '2026-04-17', '08:03:14', '17:15:12', 'Prezente', NULL, NULL, NULL, NULL),
(428, 23, '2026-04-20', '08:01:30', '17:05:25', 'Prezente', NULL, NULL, NULL, NULL),
(429, 23, '2026-04-21', NULL, NULL, 'Lisensa', NULL, NULL, NULL, NULL),
(430, 23, '2026-04-22', '08:30:47', '17:07:47', 'Prezente', NULL, NULL, NULL, NULL),
(431, 23, '2026-04-23', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(432, 23, '2026-04-24', '08:24:43', '17:13:35', 'Prezente', NULL, NULL, NULL, NULL),
(433, 23, '2026-04-27', '08:07:25', '17:13:34', 'Prezente', NULL, NULL, NULL, NULL),
(434, 23, '2026-04-28', '08:28:11', '17:05:22', 'Prezente', NULL, NULL, NULL, NULL),
(435, 23, '2026-04-29', '08:26:43', '17:08:13', 'Prezente', NULL, NULL, NULL, NULL),
(436, 23, '2026-04-30', '08:04:12', '17:04:59', 'Prezente', NULL, NULL, NULL, NULL),
(437, 24, '2026-04-01', '08:07:47', '17:06:40', 'Prezente', NULL, NULL, NULL, NULL),
(438, 24, '2026-04-02', '08:07:53', '17:08:11', 'Prezente', NULL, NULL, NULL, NULL),
(439, 24, '2026-04-03', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(440, 24, '2026-04-06', '08:26:59', '17:14:55', 'Prezente', NULL, NULL, NULL, NULL),
(441, 24, '2026-04-07', '08:11:12', '17:12:22', 'Prezente', NULL, NULL, NULL, NULL),
(442, 24, '2026-04-08', '08:22:29', '17:00:20', 'Prezente', NULL, NULL, NULL, NULL),
(443, 24, '2026-04-09', '08:09:25', '17:10:23', 'Prezente', NULL, NULL, NULL, NULL),
(444, 24, '2026-04-10', '08:17:33', '17:07:23', 'Prezente', NULL, NULL, NULL, NULL),
(445, 24, '2026-04-13', '08:26:12', '17:11:24', 'Prezente', NULL, NULL, NULL, NULL),
(446, 24, '2026-04-14', '08:02:24', '17:02:28', 'Prezente', NULL, NULL, NULL, NULL),
(447, 24, '2026-04-15', '08:21:54', '17:05:17', 'Prezente', NULL, NULL, NULL, NULL),
(448, 24, '2026-04-16', '08:29:34', '17:00:43', 'Prezente', NULL, NULL, NULL, NULL),
(449, 24, '2026-04-17', '08:13:38', '17:08:16', 'Prezente', NULL, NULL, NULL, NULL),
(450, 24, '2026-04-20', '08:29:11', '17:05:29', 'Prezente', NULL, NULL, NULL, NULL),
(451, 24, '2026-04-21', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(452, 24, '2026-04-22', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(453, 24, '2026-04-23', '08:28:38', '17:04:25', 'Prezente', NULL, NULL, NULL, NULL),
(454, 24, '2026-04-24', '08:18:55', '17:10:48', 'Prezente', NULL, NULL, NULL, NULL),
(455, 24, '2026-04-27', '08:29:58', '17:11:41', 'Prezente', NULL, NULL, NULL, NULL),
(456, 24, '2026-04-28', '08:18:52', '17:05:23', 'Prezente', NULL, NULL, NULL, NULL),
(457, 24, '2026-04-29', '08:15:57', '17:08:51', 'Prezente', NULL, NULL, NULL, NULL),
(458, 24, '2026-04-30', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(459, 5, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(460, 9, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(461, 10, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(462, 14, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(463, 18, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(464, 19, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(465, 7, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(466, 8, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(467, 11, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(468, 13, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(469, 15, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(470, 16, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(471, 17, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(472, 21, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(473, 22, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(474, 23, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(475, 24, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(476, 6, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(477, 12, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(478, 20, '2026-05-15', NULL, NULL, 'Falta', NULL, NULL, '2026-05-15 21:48:09', NULL),
(479, 5, '2026-05-14', '08:00:26', '17:06:15', 'Prezente', NULL, NULL, NULL, NULL),
(480, 6, '2026-05-14', '08:15:50', '17:06:28', 'Prezente', NULL, NULL, NULL, NULL),
(481, 7, '2026-05-14', '08:09:47', '17:12:40', 'Prezente', NULL, NULL, NULL, NULL),
(482, 8, '2026-05-14', '08:16:22', '17:10:39', 'Prezente', NULL, NULL, NULL, NULL),
(483, 9, '2026-05-14', '08:28:48', '17:15:28', 'Prezente', NULL, NULL, NULL, NULL),
(484, 10, '2026-05-14', '08:15:36', '17:03:50', 'Prezente', NULL, NULL, NULL, NULL),
(485, 11, '2026-05-14', '08:19:25', '17:04:17', 'Prezente', NULL, NULL, NULL, NULL),
(486, 12, '2026-05-14', '08:01:23', '17:10:47', 'Prezente', NULL, NULL, NULL, NULL),
(487, 13, '2026-05-14', '08:14:34', '17:13:23', 'Prezente', NULL, NULL, NULL, NULL),
(488, 14, '2026-05-14', '08:13:20', '17:06:11', 'Prezente', NULL, NULL, NULL, NULL),
(489, 15, '2026-05-14', '08:13:56', '17:15:44', 'Prezente', NULL, NULL, NULL, NULL),
(490, 16, '2026-05-14', '08:25:22', '17:01:46', 'Prezente', NULL, NULL, NULL, NULL),
(491, 17, '2026-05-14', '08:03:11', '17:15:43', 'Prezente', NULL, NULL, NULL, NULL),
(492, 18, '2026-05-14', '08:26:59', '17:10:20', 'Prezente', NULL, NULL, NULL, NULL),
(493, 19, '2026-05-14', '08:12:52', '17:04:26', 'Prezente', NULL, NULL, NULL, NULL),
(494, 20, '2026-05-14', '08:07:22', '17:00:59', 'Prezente', NULL, NULL, NULL, NULL),
(495, 21, '2026-05-14', '08:16:31', '17:13:25', 'Prezente', NULL, NULL, NULL, NULL),
(496, 22, '2026-05-14', '08:18:14', '17:02:55', 'Prezente', NULL, NULL, NULL, NULL),
(497, 23, '2026-05-14', '08:18:18', '17:06:51', 'Prezente', NULL, NULL, NULL, NULL),
(498, 24, '2026-05-14', '08:00:48', '17:04:48', 'Prezente', NULL, NULL, NULL, NULL),
(499, 5, '2026-05-13', '08:20:29', '17:10:56', 'Prezente', NULL, NULL, NULL, NULL),
(500, 6, '2026-05-13', '08:11:23', '17:04:48', 'Prezente', NULL, NULL, NULL, NULL),
(501, 7, '2026-05-13', '08:03:48', '17:01:34', 'Prezente', NULL, NULL, NULL, NULL),
(502, 8, '2026-05-13', '08:11:56', '17:15:29', 'Prezente', NULL, NULL, NULL, NULL),
(503, 9, '2026-05-13', '08:12:14', '17:11:20', 'Prezente', NULL, NULL, NULL, NULL),
(504, 10, '2026-05-13', '08:20:45', '17:08:13', 'Prezente', NULL, NULL, NULL, NULL),
(505, 11, '2026-05-13', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(506, 12, '2026-05-13', '08:26:40', '17:07:52', 'Prezente', NULL, NULL, NULL, NULL),
(507, 13, '2026-05-13', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(508, 14, '2026-05-13', '08:23:14', '17:07:16', 'Prezente', NULL, NULL, NULL, NULL),
(509, 15, '2026-05-13', '08:11:54', '17:01:49', 'Prezente', NULL, NULL, NULL, NULL),
(510, 16, '2026-05-13', '08:06:48', '17:10:35', 'Prezente', NULL, NULL, NULL, NULL),
(511, 17, '2026-05-13', '08:09:22', '17:13:20', 'Prezente', NULL, NULL, NULL, NULL),
(512, 18, '2026-05-13', '08:27:15', '17:13:40', 'Prezente', NULL, NULL, NULL, NULL),
(513, 19, '2026-05-13', '08:01:39', '17:14:41', 'Prezente', NULL, NULL, NULL, NULL),
(514, 20, '2026-05-13', '08:19:59', '17:13:12', 'Prezente', NULL, NULL, NULL, NULL),
(515, 21, '2026-05-13', '08:02:19', '17:09:31', 'Prezente', NULL, NULL, NULL, NULL),
(516, 22, '2026-05-13', '08:07:24', '17:02:10', 'Prezente', NULL, NULL, NULL, NULL),
(517, 23, '2026-05-13', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(518, 24, '2026-05-13', '08:09:49', '17:09:28', 'Prezente', NULL, NULL, NULL, NULL),
(519, 5, '2026-05-12', '08:11:37', '17:14:22', 'Prezente', NULL, NULL, NULL, NULL),
(520, 6, '2026-05-12', '08:08:57', '17:12:35', 'Prezente', NULL, NULL, NULL, NULL),
(521, 7, '2026-05-12', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(522, 8, '2026-05-12', '08:05:22', '17:04:50', 'Prezente', NULL, NULL, NULL, NULL),
(523, 9, '2026-05-12', '08:17:53', '17:00:10', 'Prezente', NULL, NULL, NULL, NULL),
(524, 10, '2026-05-12', '08:09:39', '17:01:51', 'Prezente', NULL, NULL, NULL, NULL),
(525, 11, '2026-05-12', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(526, 12, '2026-05-12', '08:09:21', '17:05:57', 'Prezente', NULL, NULL, NULL, NULL),
(527, 13, '2026-05-12', '08:05:53', '17:09:25', 'Prezente', NULL, NULL, NULL, NULL),
(528, 14, '2026-05-12', '08:11:51', '17:01:25', 'Prezente', NULL, NULL, NULL, NULL),
(529, 15, '2026-05-12', '08:19:14', '17:05:53', 'Prezente', NULL, NULL, NULL, NULL),
(530, 16, '2026-05-12', '08:17:50', '17:11:33', 'Prezente', NULL, NULL, NULL, NULL),
(531, 17, '2026-05-12', '08:30:59', '17:13:58', 'Prezente', NULL, NULL, NULL, NULL),
(532, 18, '2026-05-12', '08:19:53', '17:10:50', 'Prezente', NULL, NULL, NULL, NULL),
(533, 19, '2026-05-12', '08:22:44', '17:04:29', 'Prezente', NULL, NULL, NULL, NULL),
(534, 20, '2026-05-12', '08:16:36', '17:12:49', 'Prezente', NULL, NULL, NULL, NULL),
(535, 21, '2026-05-12', '08:07:33', '17:00:49', 'Prezente', NULL, NULL, NULL, NULL),
(536, 22, '2026-05-12', '08:15:12', '17:09:38', 'Prezente', NULL, NULL, NULL, NULL),
(537, 23, '2026-05-12', '08:02:22', '17:06:27', 'Prezente', NULL, NULL, NULL, NULL),
(538, 24, '2026-05-12', '08:07:58', '17:08:55', 'Prezente', NULL, NULL, NULL, NULL),
(539, 5, '2026-05-11', '08:30:45', '17:07:47', 'Prezente', NULL, NULL, NULL, NULL),
(540, 6, '2026-05-11', '08:24:31', '17:12:21', 'Prezente', NULL, NULL, NULL, NULL),
(541, 7, '2026-05-11', '08:19:43', '17:08:30', 'Prezente', NULL, NULL, NULL, NULL),
(542, 8, '2026-05-11', '08:00:17', '17:09:42', 'Prezente', NULL, NULL, NULL, NULL),
(543, 9, '2026-05-11', '08:07:11', '17:07:32', 'Prezente', NULL, NULL, NULL, NULL),
(544, 10, '2026-05-11', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(545, 11, '2026-05-11', '08:09:59', '17:07:45', 'Prezente', NULL, NULL, NULL, NULL),
(546, 12, '2026-05-11', '08:29:30', '17:06:13', 'Prezente', NULL, NULL, NULL, NULL),
(547, 13, '2026-05-11', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(548, 14, '2026-05-11', '08:00:10', '17:06:22', 'Prezente', NULL, NULL, NULL, NULL),
(549, 15, '2026-05-11', '08:19:39', '17:08:22', 'Prezente', NULL, NULL, NULL, NULL),
(550, 16, '2026-05-11', '08:12:13', '17:09:44', 'Prezente', NULL, NULL, NULL, NULL),
(551, 17, '2026-05-11', '08:23:14', '17:06:35', 'Prezente', NULL, NULL, NULL, NULL),
(552, 18, '2026-05-11', '08:23:19', '17:11:16', 'Prezente', NULL, NULL, NULL, NULL),
(553, 19, '2026-05-11', '08:05:44', '17:12:29', 'Prezente', NULL, NULL, NULL, NULL),
(554, 20, '2026-05-11', '08:22:34', '17:04:49', 'Prezente', NULL, NULL, NULL, NULL),
(555, 21, '2026-05-11', '08:07:41', '17:13:41', 'Prezente', NULL, NULL, NULL, NULL),
(556, 22, '2026-05-11', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(557, 23, '2026-05-11', '08:14:27', '17:15:15', 'Prezente', NULL, NULL, NULL, NULL),
(558, 24, '2026-05-11', '08:30:22', '17:11:40', 'Prezente', NULL, NULL, NULL, NULL),
(559, 5, '2026-05-08', '08:06:15', '17:01:18', 'Prezente', NULL, NULL, NULL, NULL),
(560, 6, '2026-05-08', '08:06:50', '17:11:58', 'Prezente', NULL, NULL, NULL, NULL),
(561, 7, '2026-05-08', '08:12:11', '17:04:11', 'Prezente', NULL, NULL, NULL, NULL),
(562, 8, '2026-05-08', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(563, 9, '2026-05-08', '08:25:11', '17:13:37', 'Prezente', NULL, NULL, NULL, NULL),
(564, 10, '2026-05-08', '08:12:28', '17:09:59', 'Prezente', NULL, NULL, NULL, NULL),
(565, 11, '2026-05-08', '08:25:34', '17:13:15', 'Prezente', NULL, NULL, NULL, NULL),
(566, 12, '2026-05-08', '08:19:58', '17:06:13', 'Prezente', NULL, NULL, NULL, NULL),
(567, 13, '2026-05-08', '08:30:30', '17:06:24', 'Prezente', NULL, NULL, NULL, NULL),
(568, 14, '2026-05-08', '08:27:59', '17:14:44', 'Prezente', NULL, NULL, NULL, NULL),
(569, 15, '2026-05-08', '08:03:45', '17:03:28', 'Prezente', NULL, NULL, NULL, NULL),
(570, 16, '2026-05-08', '08:11:44', '17:05:43', 'Prezente', NULL, NULL, NULL, NULL),
(571, 17, '2026-05-08', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(572, 18, '2026-05-08', '08:07:47', '17:06:11', 'Prezente', NULL, NULL, NULL, NULL),
(573, 19, '2026-05-08', '08:15:19', '17:04:18', 'Prezente', NULL, NULL, NULL, NULL),
(574, 20, '2026-05-08', '08:04:57', '17:09:54', 'Prezente', NULL, NULL, NULL, NULL),
(575, 21, '2026-05-08', '08:06:22', '17:05:19', 'Prezente', NULL, NULL, NULL, NULL),
(576, 22, '2026-05-08', '08:05:20', '17:15:40', 'Prezente', NULL, NULL, NULL, NULL),
(577, 23, '2026-05-08', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(578, 24, '2026-05-08', '08:00:24', '17:15:36', 'Prezente', NULL, NULL, NULL, NULL),
(579, 5, '2026-05-07', '08:13:21', '17:15:30', 'Prezente', NULL, NULL, NULL, NULL),
(580, 6, '2026-05-07', '08:22:27', '17:03:33', 'Prezente', NULL, NULL, NULL, NULL),
(581, 7, '2026-05-07', '08:17:32', '17:14:40', 'Prezente', NULL, NULL, NULL, NULL),
(582, 8, '2026-05-07', '08:30:29', '17:06:40', 'Prezente', NULL, NULL, NULL, NULL),
(583, 9, '2026-05-07', '08:04:26', '17:05:37', 'Prezente', NULL, NULL, NULL, NULL),
(584, 10, '2026-05-07', '08:24:31', '17:05:26', 'Prezente', NULL, NULL, NULL, NULL),
(585, 11, '2026-05-07', '08:23:59', '17:11:45', 'Prezente', NULL, NULL, NULL, NULL),
(586, 12, '2026-05-07', '08:14:37', '17:13:50', 'Prezente', NULL, NULL, NULL, NULL),
(587, 13, '2026-05-07', '08:15:31', '17:08:57', 'Prezente', NULL, NULL, NULL, NULL),
(588, 14, '2026-05-07', '08:00:51', '17:10:15', 'Prezente', NULL, NULL, NULL, NULL),
(589, 15, '2026-05-07', '08:20:29', '17:09:36', 'Prezente', NULL, NULL, NULL, NULL),
(590, 16, '2026-05-07', '08:20:31', '17:05:56', 'Prezente', NULL, NULL, NULL, NULL),
(591, 17, '2026-05-07', '08:13:10', '17:04:49', 'Prezente', NULL, NULL, NULL, NULL),
(592, 18, '2026-05-07', '08:04:27', '17:08:26', 'Prezente', NULL, NULL, NULL, NULL),
(593, 19, '2026-05-07', '08:16:31', '17:04:56', 'Prezente', NULL, NULL, NULL, NULL),
(594, 20, '2026-05-07', '08:18:16', '17:12:17', 'Prezente', NULL, NULL, NULL, NULL),
(595, 21, '2026-05-07', '08:25:30', '17:08:52', 'Prezente', NULL, NULL, NULL, NULL),
(596, 22, '2026-05-07', '08:23:43', '17:14:43', 'Prezente', NULL, NULL, NULL, NULL),
(597, 23, '2026-05-07', '08:00:50', '17:08:48', 'Prezente', NULL, NULL, NULL, NULL),
(598, 24, '2026-05-07', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(599, 5, '2026-05-06', '08:03:10', '17:12:19', 'Prezente', NULL, NULL, NULL, NULL),
(600, 6, '2026-05-06', '08:30:50', '17:08:46', 'Prezente', NULL, NULL, NULL, NULL),
(601, 7, '2026-05-06', '08:26:47', '17:02:22', 'Prezente', NULL, NULL, NULL, NULL),
(602, 8, '2026-05-06', '08:02:51', '17:03:37', 'Prezente', NULL, NULL, NULL, NULL),
(603, 9, '2026-05-06', '08:26:26', '17:10:49', 'Prezente', NULL, NULL, NULL, NULL),
(604, 10, '2026-05-06', '08:16:32', '17:13:16', 'Prezente', NULL, NULL, NULL, NULL),
(605, 11, '2026-05-06', '08:18:52', '17:11:16', 'Prezente', NULL, NULL, NULL, NULL),
(606, 12, '2026-05-06', '08:17:46', '17:05:22', 'Prezente', NULL, NULL, NULL, NULL),
(607, 13, '2026-05-06', '08:12:31', '17:02:57', 'Prezente', NULL, NULL, NULL, NULL),
(608, 14, '2026-05-06', '08:04:37', '17:12:27', 'Prezente', NULL, NULL, NULL, NULL),
(609, 15, '2026-05-06', '08:03:25', '17:06:23', 'Prezente', NULL, NULL, NULL, NULL),
(610, 16, '2026-05-06', '08:08:54', '17:08:25', 'Prezente', NULL, NULL, NULL, NULL),
(611, 17, '2026-05-06', '08:00:49', '17:14:50', 'Prezente', NULL, NULL, NULL, NULL),
(612, 18, '2026-05-06', '08:13:19', '17:05:34', 'Prezente', NULL, NULL, NULL, NULL),
(613, 19, '2026-05-06', '08:03:39', '17:03:27', 'Prezente', NULL, NULL, NULL, NULL),
(614, 20, '2026-05-06', '08:00:46', '17:15:31', 'Prezente', NULL, NULL, NULL, NULL),
(615, 21, '2026-05-06', '08:06:36', '17:13:24', 'Prezente', NULL, NULL, NULL, NULL),
(616, 22, '2026-05-06', '08:21:49', '17:06:16', 'Prezente', NULL, NULL, NULL, NULL),
(617, 23, '2026-05-06', '08:05:38', '17:05:54', 'Prezente', NULL, NULL, NULL, NULL),
(618, 24, '2026-05-06', '08:03:28', '17:04:49', 'Prezente', NULL, NULL, NULL, NULL),
(619, 5, '2026-05-05', '08:25:15', '17:04:57', 'Prezente', NULL, NULL, NULL, NULL),
(620, 6, '2026-05-05', '08:29:53', '17:09:16', 'Prezente', NULL, NULL, NULL, NULL),
(621, 7, '2026-05-05', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(622, 8, '2026-05-05', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(623, 9, '2026-05-05', '08:05:31', '17:15:38', 'Prezente', NULL, NULL, NULL, NULL),
(624, 10, '2026-05-05', '08:06:23', '17:15:28', 'Prezente', NULL, NULL, NULL, NULL),
(625, 11, '2026-05-05', '08:19:24', '17:05:25', 'Prezente', NULL, NULL, NULL, NULL),
(626, 12, '2026-05-05', '08:17:19', '17:02:55', 'Prezente', NULL, NULL, NULL, NULL),
(627, 13, '2026-05-05', '08:17:27', '17:10:43', 'Prezente', NULL, NULL, NULL, NULL),
(628, 14, '2026-05-05', '08:21:16', '17:15:36', 'Prezente', NULL, NULL, NULL, NULL),
(629, 15, '2026-05-05', '08:25:40', '17:15:17', 'Prezente', NULL, NULL, NULL, NULL),
(630, 16, '2026-05-05', '08:21:58', '17:12:27', 'Prezente', NULL, NULL, NULL, NULL),
(631, 17, '2026-05-05', '08:06:15', '17:00:23', 'Prezente', NULL, NULL, NULL, NULL),
(632, 18, '2026-05-05', '08:06:25', '17:06:44', 'Prezente', NULL, NULL, NULL, NULL),
(633, 19, '2026-05-05', '08:03:26', '17:04:51', 'Prezente', NULL, NULL, NULL, NULL),
(634, 20, '2026-05-05', '08:16:42', '17:07:31', 'Prezente', NULL, NULL, NULL, NULL),
(635, 21, '2026-05-05', '08:30:51', '17:11:27', 'Prezente', NULL, NULL, NULL, NULL);
INSERT INTO `prezensa` (`id`, `funsionariu_id`, `data_prezensa`, `oras_tama`, `oras_sai`, `estadu_prezensa`, `foto_tama`, `kordenada`, `created_at`, `updated_at`) VALUES
(636, 22, '2026-05-05', '08:07:31', '17:02:11', 'Prezente', NULL, NULL, NULL, NULL),
(637, 23, '2026-05-05', '08:02:36', '17:14:19', 'Prezente', NULL, NULL, NULL, NULL),
(638, 24, '2026-05-05', '08:01:30', '17:07:22', 'Prezente', NULL, NULL, NULL, NULL),
(639, 5, '2026-05-04', '08:01:23', '17:07:53', 'Prezente', NULL, NULL, NULL, NULL),
(640, 6, '2026-05-04', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(641, 7, '2026-05-04', '08:05:30', '17:13:47', 'Prezente', NULL, NULL, NULL, NULL),
(642, 8, '2026-05-04', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(643, 9, '2026-05-04', '08:27:16', '17:11:20', 'Prezente', NULL, NULL, NULL, NULL),
(644, 10, '2026-05-04', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(645, 11, '2026-05-04', '08:10:49', '17:14:33', 'Prezente', NULL, NULL, NULL, NULL),
(646, 12, '2026-05-04', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(647, 13, '2026-05-04', '08:24:18', '17:14:46', 'Prezente', NULL, NULL, NULL, NULL),
(648, 14, '2026-05-04', '08:25:52', '17:08:33', 'Prezente', NULL, NULL, NULL, NULL),
(649, 15, '2026-05-04', '08:24:49', '17:13:26', 'Prezente', NULL, NULL, NULL, NULL),
(650, 16, '2026-05-04', '08:27:29', '17:03:19', 'Prezente', NULL, NULL, NULL, NULL),
(651, 17, '2026-05-04', '08:05:15', '17:15:55', 'Prezente', NULL, NULL, NULL, NULL),
(652, 18, '2026-05-04', '08:21:24', '17:05:40', 'Prezente', NULL, NULL, NULL, NULL),
(653, 19, '2026-05-04', '08:21:43', '17:12:14', 'Prezente', NULL, NULL, NULL, NULL),
(654, 20, '2026-05-04', '08:15:30', '17:07:52', 'Prezente', NULL, NULL, NULL, NULL),
(655, 21, '2026-05-04', '08:08:29', '17:13:31', 'Prezente', NULL, NULL, NULL, NULL),
(656, 22, '2026-05-04', '08:00:34', '17:03:48', 'Prezente', NULL, NULL, NULL, NULL),
(657, 23, '2026-05-04', '08:04:51', '17:15:40', 'Prezente', NULL, NULL, NULL, NULL),
(658, 24, '2026-05-04', NULL, NULL, 'Falta', NULL, NULL, NULL, NULL),
(659, 5, '2026-05-01', '08:20:27', '17:04:34', 'Prezente', NULL, NULL, NULL, NULL),
(660, 6, '2026-05-01', '08:09:13', '17:05:55', 'Prezente', NULL, NULL, NULL, NULL),
(661, 7, '2026-05-01', '08:18:22', '17:04:35', 'Prezente', NULL, NULL, NULL, NULL),
(662, 8, '2026-05-01', '08:18:39', '17:13:11', 'Prezente', NULL, NULL, NULL, NULL),
(663, 9, '2026-05-01', '08:14:17', '17:01:23', 'Prezente', NULL, NULL, NULL, NULL),
(664, 10, '2026-05-01', '08:04:17', '17:05:29', 'Prezente', NULL, NULL, NULL, NULL),
(665, 11, '2026-05-01', '08:07:45', '17:03:54', 'Prezente', NULL, NULL, NULL, NULL),
(666, 12, '2026-05-01', '08:25:29', '17:04:28', 'Prezente', NULL, NULL, NULL, NULL),
(667, 13, '2026-05-01', '08:26:27', '17:13:39', 'Prezente', NULL, NULL, NULL, NULL),
(668, 14, '2026-05-01', '08:21:29', '17:09:37', 'Prezente', NULL, NULL, NULL, NULL),
(669, 15, '2026-05-01', '08:17:36', '17:01:41', 'Prezente', NULL, NULL, NULL, NULL),
(670, 16, '2026-05-01', '08:25:10', '17:12:29', 'Prezente', NULL, NULL, NULL, NULL),
(671, 17, '2026-05-01', '08:06:44', '17:00:59', 'Prezente', NULL, NULL, NULL, NULL),
(672, 18, '2026-05-01', '08:25:53', '17:02:42', 'Prezente', NULL, NULL, NULL, NULL),
(673, 19, '2026-05-01', '08:30:54', '17:03:33', 'Prezente', NULL, NULL, NULL, NULL),
(674, 20, '2026-05-01', '08:24:47', '17:03:35', 'Prezente', NULL, NULL, NULL, NULL),
(675, 21, '2026-05-01', '08:14:35', '17:01:21', 'Prezente', NULL, NULL, NULL, NULL),
(676, 22, '2026-05-01', '08:11:39', '17:00:17', 'Prezente', NULL, NULL, NULL, NULL),
(677, 23, '2026-05-01', '08:25:41', '17:03:51', 'Prezente', NULL, NULL, NULL, NULL),
(678, 24, '2026-05-01', '08:14:31', '17:10:11', 'Prezente', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salariu`
--

CREATE TABLE `salariu` (
  `id` int(11) UNSIGNED NOT NULL,
  `funsionariu_id` int(11) UNSIGNED DEFAULT NULL,
  `fulan` int(2) DEFAULT NULL,
  `tinan` year(4) DEFAULT NULL,
  `salariu_baziku` decimal(10,2) DEFAULT NULL,
  `total_subsidiu` decimal(10,2) DEFAULT 0.00,
  `total_deskontu` decimal(10,2) DEFAULT 0.00,
  `salariu_liquidu` decimal(10,2) DEFAULT NULL,
  `estadu_pagamentu` enum('Seidauk Selu','Selu Ona') DEFAULT 'Seidauk Selu',
  `data_pagamentu` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salariu`
--

INSERT INTO `salariu` (`id`, `funsionariu_id`, `fulan`, `tinan`, `salariu_baziku`, `total_subsidiu`, `total_deskontu`, `salariu_liquidu`, `estadu_pagamentu`, `data_pagamentu`, `created_at`, `updated_at`) VALUES
(6, 5, 4, '2026', 300.00, 50.00, 30.00, 320.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(7, 6, 4, '2026', 300.00, 22.00, 0.00, 322.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(8, 7, 4, '2026', 800.00, 38.00, 20.00, 818.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(9, 8, 4, '2026', 800.00, 22.00, 15.00, 807.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(10, 9, 4, '2026', 800.00, 31.00, 10.00, 821.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(11, 10, 4, '2026', 800.00, 30.00, 0.00, 830.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(12, 11, 4, '2026', 250.00, 45.00, 20.00, 275.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(13, 12, 4, '2026', 300.00, 40.00, 5.00, 335.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(14, 13, 4, '2026', 250.00, 33.00, 10.00, 273.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(15, 14, 4, '2026', 500.00, 50.00, 5.00, 545.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(16, 15, 4, '2026', 300.00, 39.00, 10.00, 329.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(17, 16, 4, '2026', 250.00, 39.00, 15.00, 274.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(18, 17, 4, '2026', 500.00, 31.00, 0.00, 531.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(19, 18, 4, '2026', 800.00, 40.00, 10.00, 830.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(20, 19, 4, '2026', 300.00, 29.00, 25.00, 304.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(21, 20, 4, '2026', 300.00, 31.00, 10.00, 321.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(22, 21, 4, '2026', 300.00, 36.00, 0.00, 336.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(23, 22, 4, '2026', 300.00, 22.00, 15.00, 307.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(24, 23, 4, '2026', 500.00, 24.00, 10.00, 514.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL),
(25, 24, 4, '2026', 300.00, 36.00, 20.00, 316.00, 'Selu Ona', '2026-04-30', '2026-05-15 14:47:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salariu_detallu`
--

CREATE TABLE `salariu_detallu` (
  `id` int(11) UNSIGNED NOT NULL,
  `salariu_id` int(11) UNSIGNED DEFAULT NULL,
  `naran_komponente` varchar(100) DEFAULT NULL,
  `valór` decimal(10,2) DEFAULT NULL,
  `tipu` enum('Subsidiu','Deskontu') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sansaun`
--

CREATE TABLE `sansaun` (
  `id` int(11) UNSIGNED NOT NULL,
  `funsionariu_id` int(11) UNSIGNED DEFAULT NULL,
  `tipu_sansaun` enum('Avisu Lisan','Avisu Eskritu 1','Avisu Eskritu 2','Suspensaun') DEFAULT NULL,
  `motivu` text DEFAULT NULL,
  `data_sansaun` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tipu_sansaun_id` int(11) UNSIGNED DEFAULT NULL,
  `estadu_sansaun` enum('Ativu','Retira','Konkluidu') DEFAULT 'Ativu',
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `valor_pagadu` decimal(10,2) DEFAULT 0.00,
  `pozisaun_anterior_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sansaun`
--

INSERT INTO `sansaun` (`id`, `funsionariu_id`, `tipu_sansaun`, `motivu`, `data_sansaun`, `created_at`, `updated_at`, `tipu_sansaun_id`, `estadu_sansaun`, `valor_total`, `valor_pagadu`, `pozisaun_anterior_id`) VALUES
(9, 4, NULL, 'bosok ten  (Hatun pozisaun husi Koko ba Koko1)', '2026-05-15', '2026-05-15 19:58:34', '2026-05-15 19:59:01', 6, 'Retira', 0.00, 0.00, 3),
(10, 5, NULL, 'Violasaun regra (Mock)', '2026-04-28', '2026-05-15 14:47:36', NULL, 7, 'Ativu', 16.00, 0.00, NULL),
(11, 6, NULL, 'Violasaun regra (Mock)', '2026-04-23', '2026-05-15 14:47:36', NULL, 7, 'Ativu', 8.00, 0.00, NULL),
(12, 5, NULL, 'Sansaun Absénsia: Falta dala 6 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 5.40, 0.00, NULL),
(13, 19, NULL, 'Sansaun Absénsia: Falta dala 5 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 2.70, 0.00, NULL),
(14, 7, NULL, 'Sansaun Absénsia: Falta dala 4 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 7.20, 0.00, NULL),
(15, 8, NULL, 'Sansaun Absénsia: Falta dala 3 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 7.20, 0.00, NULL),
(16, 11, NULL, 'Sansaun Absénsia: Falta dala 4 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 2.25, 0.00, NULL),
(17, 16, NULL, 'Sansaun Absénsia: Falta dala 3 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 2.25, 0.00, NULL),
(18, 22, NULL, 'Sansaun Absénsia: Falta dala 3 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 2.70, 0.00, NULL),
(19, 24, NULL, 'Sansaun Absénsia: Falta dala 4 iha 4/2026', '2026-05-15', '2026-05-15 21:49:58', NULL, 10, 'Ativu', 2.70, 0.00, NULL),
(20, 10, NULL, 'Sansaun Absénsia: Falta dala 3 iha 5/2026', '2026-05-15', '2026-05-15 23:47:45', NULL, 10, 'Ativu', 7.20, 0.00, NULL),
(21, 7, NULL, 'Sansaun Absénsia: Falta dala 3 iha 5/2026', '2026-05-15', '2026-05-15 23:47:45', NULL, 10, 'Ativu', 7.20, 0.00, NULL),
(22, 8, NULL, 'Sansaun Absénsia: Falta dala 4 iha 5/2026', '2026-05-15', '2026-05-15 23:47:45', NULL, 10, 'Ativu', 7.20, 0.00, NULL),
(23, 11, NULL, 'Sansaun Absénsia: Falta dala 3 iha 5/2026', '2026-05-15', '2026-05-15 23:47:45', NULL, 10, 'Ativu', 2.25, 0.00, NULL),
(24, 13, NULL, 'Sansaun Absénsia: Falta dala 3 iha 5/2026', '2026-05-15', '2026-05-15 23:47:45', NULL, 10, 'Ativu', 2.25, 0.00, NULL),
(25, 23, NULL, 'Sansaun Absénsia: Falta dala 3 iha 5/2026', '2026-05-15', '2026-05-15 23:47:45', NULL, 10, 'Ativu', 4.50, 0.00, NULL),
(26, 24, NULL, 'Sansaun Absénsia: Falta dala 3 iha 5/2026', '2026-05-15', '2026-05-15 23:47:45', NULL, 10, 'Ativu', 2.70, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subsidiu`
--

CREATE TABLE `subsidiu` (
  `id` int(11) UNSIGNED NOT NULL,
  `naran_subsidiu` varchar(100) NOT NULL,
  `valor_padrao` decimal(10,2) DEFAULT 0.00,
  `deskrisaun` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipu_sansaun`
--

CREATE TABLE `tipu_sansaun` (
  `id` int(11) UNSIGNED NOT NULL,
  `naran_tipu` varchar(100) NOT NULL,
  `kategoria` enum('Jeral','Korta Saláriu','Hatun Pozisaun') DEFAULT 'Jeral',
  `valor_dedusaun` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipu_sansaun`
--

INSERT INTO `tipu_sansaun` (`id`, `naran_tipu`, `kategoria`, `valor_dedusaun`, `created_at`, `updated_at`) VALUES
(6, 'Miss Management', 'Hatun Pozisaun', 0.00, '2026-05-15 19:57:44', NULL),
(7, 'Atraso Frequente', 'Korta Saláriu', 5.00, NULL, NULL),
(8, 'Insubordinasaun', 'Jeral', 0.00, NULL, NULL),
(9, 'Falta la fó hatene', 'Korta Saláriu', 10.00, NULL, NULL),
(10, 'Falta Absénsia', 'Korta Saláriu', 0.00, '2026-05-15 21:49:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(5) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Developer', 'developer@mail.io', '$2y$10$L5azAZuEwhJXoaFfcgE8se65QhaToz1X0ZZaLTBGZVTboSge8ylyC', 1, '2026-05-15 12:30:46', '0000-00-00 00:00:00'),
(2, 'Hercio Moreira Gusmao', 'admin@gmail.com', '$2y$10$jAii8s/KKA32zxGHCZ72MOhHFJcAzB7A8l9ca8QVnxsPX/OsZOC8C', 2, '2026-05-15 01:48:32', '0000-00-00 00:00:00'),
(7, 'Joao dos Santos', 'walosi@gmail.com', '$2y$10$W5pmukP1g8GZOk8krlAwo.iWPyOfjQDQes3LQcxjBw1XQi0QTPrDy', 3, '2026-05-15 14:47:34', '2026-05-15 22:00:50'),
(8, 'Maria Soares', '2024002', '$2y$10$as3KxhSqiI/2HsWhhjrzCeTH7l411SFAg2cy/sWTiAyVNmOfbqxoS', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(9, 'Antonio da Costa', '2024003', '$2y$10$XMC9Ud8SgM4h6m9EZF3ruuyDBI2svk.xHnWEhfz8br3wlGYUj3b2q', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(10, 'Lucia Pires', '2024004', '$2y$10$5Gbq3Cf1ZaF3Iy3KrsO9ReMyypgvSNSxsDXXxC5y7YA5nr1FsmNme', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(11, 'Jose Ramos', '2024005', '$2y$10$F8r.WH4TjHhUU60WD1.Qz.FdnkIk2hsui/92qtsUAS0ShiwsKLvJ6', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(12, 'Filomena Ximenes', '2024006', '$2y$10$ino2v.vDznSPa0stLEVdseKfmEQMGMQhUSljaf/Mqg1WwMh32qsbe', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(13, 'Agostinho Belo', '2024007', '$2y$10$sgCC9nhMU9p5PzBMF/dqgOASrCuiN0snsbX3Up3K4Z1oTMANbsMEm', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(14, 'Rosa de Jesus', '2024008', '$2y$10$Eb1WK1482v6jogOowGZBWuTvEkQRAiMIvv3dMjzli1Sj8Y/Vfixwi', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(15, 'Bernardino Guterres', '2024009', '$2y$10$BgBOSirLo3iXVmKuInHgT.HNZF53dUhuOST7RVKc15caFHsBeJgEq', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(16, 'Teresa Amaral', '2024010', '$2y$10$cwdU2K6ZED5TFskJiJWP5OYOljGplC6VeMf0xiV/qCob4V6I5RhaW', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(17, 'Domingos Ferreira', '2024011', '$2y$10$GaQsvhRjFsu1rgx3fU17DOhfqW1vYLWIeWbwGNoj7MQn5O6G/7OAa', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(18, 'Isabel Lopes', '2024012', '$2y$10$6NTLBnb8i0pF5h.sd8wUv.Icus/NEr65Gn3qlYLDwzWGsHLinx2NC', 2, '2026-05-15 14:47:34', '0000-00-00 00:00:00'),
(19, 'Francisco Mendonca', '2024013', '$2y$10$efZvfTL57am30sooN7e3Vedd1acg3y7SnLH0TG1Ma76Q99Sckn7BK', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00'),
(20, 'Ana Maria Silva', '2024014', '$2y$10$bodeQnQPc57uOtBbGhYPmOvmThPs3x8pU8OYUMIlg0fLSBdV7ZmFK', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00'),
(21, 'Mateus Oliveira', '2024015', '$2y$10$Mdt01enz8ZmFona0HU6P..DgP0xt.ZTC0vthsfevoFWasTpzhtkdO', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00'),
(22, 'Jacinta Pereira', '2024016', '$2y$10$elkI6tA9Rj2wt4e.iWOv6eP1fLvlkU4TLCiJy1PdDdgOqyRLitaQK', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00'),
(23, 'Gabriel de Araujo', '2024017', '$2y$10$4EK6i64Jh4BI40BgQ2Fh0O/4HY0gvyxgS3SIazVgQ9sBH7dlnN/ku', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00'),
(24, 'Sofia Magno', '2024018', '$2y$10$ErbH4jxtIWiXLX5xz3JO2Om.RbWUOt8dCXjlIzcL4tmlBgzE3B52.', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00'),
(25, 'Henrique Martins', '2024019', '$2y$10$wOdc2RS6Zrn4I2/SUfm2Te/8tlKb9kKXJitor9ieqxf.RG2ndRmqC', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00'),
(26, 'Beatriz Gusmao', '2024020', '$2y$10$WQrWEFbUiHqpjxaFaQlfBOGCSW5mX5Y529dbDh627rz.tvnt4pIFW', 2, '2026-05-15 14:47:35', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_access`
--

CREATE TABLE `user_access` (
  `id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL,
  `menu_category_id` int(11) UNSIGNED NOT NULL,
  `menu_id` int(11) UNSIGNED NOT NULL,
  `submenu_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_access`
--

INSERT INTO `user_access` (`id`, `role_id`, `menu_category_id`, `menu_id`, `submenu_id`) VALUES
(1, 1, 1, 0, 0),
(2, 1, 0, 1, 0),
(3, 1, 2, 0, 0),
(4, 1, 0, 2, 0),
(5, 1, 0, 3, 0),
(6, 2, 3, 0, 0),
(7, 2, 4, 0, 0),
(8, 2, 5, 0, 0),
(9, 2, 6, 0, 0),
(10, 2, 0, 4, 0),
(11, 2, 0, 5, 0),
(12, 2, 0, 6, 0),
(13, 2, 0, 7, 0),
(14, 2, 0, 8, 0),
(15, 2, 0, 9, 0),
(16, 2, 0, 10, 0),
(17, 2, 0, 11, 0),
(18, 2, 0, 12, 0),
(19, 2, 0, 13, 0),
(20, 3, 3, 0, 0),
(21, 3, 6, 0, 0),
(22, 3, 0, 14, 0),
(23, 3, 0, 15, 0),
(24, 3, 0, 16, 0),
(25, 3, 0, 17, 0),
(26, 3, 0, 18, 0),
(27, 2, 0, 1, 0),
(28, 3, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_menu`
--

CREATE TABLE `user_menu` (
  `id` int(11) UNSIGNED NOT NULL,
  `menu_category` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` text NOT NULL,
  `parent` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_menu`
--

INSERT INTO `user_menu` (`id`, `menu_category`, `title`, `url`, `icon`, `parent`) VALUES
(1, 1, 'Dashboard', 'dashboard', 'home', 0),
(2, 2, 'Users', 'users', 'user', 0),
(3, 2, 'Menu Management', 'menu-management', 'command', 0),
(4, 3, 'Admin Dashboard', 'administrador/dashboard', 'grid', 0),
(5, 4, 'Departamentu', 'administrador/departamentu', 'layers', 0),
(6, 4, 'Pozisaun', 'administrador/pozisaun', 'briefcase', 0),
(7, 4, 'Kategoria', 'administrador/kategoria', 'tag', 0),
(8, 5, 'Funsionáriu', 'administrador/funsionariu', 'users', 0),
(9, 5, 'Prezensa', 'administrador/prezensa', 'calendar', 0),
(10, 5, 'Lisensa', 'administrador/lisensa', 'file-text', 0),
(11, 5, 'Saláriu', 'administrador/salariu', 'dollar-sign', 0),
(12, 5, 'Avizu', 'administrador/avizu', 'bell', 0),
(13, 5, 'Sansaun', 'administrador/sansaun', 'alert-triangle', 0),
(14, 3, 'Dashboard', 'funsionariu/dashboard', 'home', 0),
(15, 6, 'Prezensa', 'funsionariu/prezensa', 'clock', 0),
(16, 6, 'Lisensa', 'funsionariu/lisensa', 'send', 0),
(17, 6, 'Saláriu', 'funsionariu/salariu', 'file', 0),
(18, 6, 'Perfil', 'funsionariu/perfil', 'user', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_menu_category`
--

CREATE TABLE `user_menu_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `menu_category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_menu_category`
--

INSERT INTO `user_menu_category` (`id`, `menu_category`) VALUES
(1, 'Common Page'),
(2, 'Settings'),
(3, 'DASHBOARD'),
(4, 'MASTER DATA'),
(5, 'HR MANAGEMENT'),
(6, 'SELF SERVICE');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) UNSIGNED NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `role_name`) VALUES
(1, 'Developer'),
(2, 'administrador'),
(3, 'funsionariu');

-- --------------------------------------------------------

--
-- Table structure for table `user_submenu`
--

CREATE TABLE `user_submenu` (
  `id` int(11) UNSIGNED NOT NULL,
  `menu` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilizador`
--

CREATE TABLE `utilizador` (
  `id` int(11) UNSIGNED NOT NULL,
  `naran_utilizador` varchar(100) DEFAULT NULL,
  `xave_secreta` varchar(255) DEFAULT NULL,
  `papel_id` int(11) UNSIGNED DEFAULT NULL,
  `estadu_kontu` enum('Ativu','Inativu') DEFAULT 'Ativu',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilizador`
--

INSERT INTO `utilizador` (`id`, `naran_utilizador`, `xave_secreta`, `papel_id`, `estadu_kontu`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$MtHrfIviqOnvcJCWBmO/V.7UoqRZttQxo4DYetfeFq2HtEzwVr2je', 1, 'Ativu', '2026-05-15 10:41:58', NULL),
(2, 'hezron@gmail.com', '$2y$10$pMNabouoaYcV4gSH39JAse3NTdHwz06vbnEGtdAWqZaVVskn2ovIu', 2, 'Ativu', '2026-05-15 02:57:14', NULL),
(5, 'walosi@gmail.com', '$2y$10$foGul7a7g/z3ZrjeyWfND.Yjfo9TlHv24Gm4VrtehdMiIcsylHV4K', 2, 'Ativu', '2026-05-15 02:59:00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `avizu`
--
ALTER TABLE `avizu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departamentu`
--
ALTER TABLE `departamentu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `funsionariu`
--
ALTER TABLE `funsionariu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utilizador_id` (`utilizador_id`),
  ADD UNIQUE KEY `nid` (`nid`);

--
-- Indexes for table `kategoria`
--
ALTER TABLE `kategoria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lisensa`
--
ALTER TABLE `lisensa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `papel`
--
ALTER TABLE `papel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `naran_papel` (`naran_papel`);

--
-- Indexes for table `pozisaun`
--
ALTER TABLE `pozisaun`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prezensa`
--
ALTER TABLE `prezensa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salariu`
--
ALTER TABLE `salariu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salariu_detallu`
--
ALTER TABLE `salariu_detallu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sansaun`
--
ALTER TABLE `sansaun`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`timestamp`);

--
-- Indexes for table `subsidiu`
--
ALTER TABLE `subsidiu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tipu_sansaun`
--
ALTER TABLE `tipu_sansaun`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_access`
--
ALTER TABLE `user_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_menu`
--
ALTER TABLE `user_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_menu_category`
--
ALTER TABLE `user_menu_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_submenu`
--
ALTER TABLE `user_submenu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilizador`
--
ALTER TABLE `utilizador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `naran_utilizador` (`naran_utilizador`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `avizu`
--
ALTER TABLE `avizu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `departamentu`
--
ALTER TABLE `departamentu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `funsionariu`
--
ALTER TABLE `funsionariu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `kategoria`
--
ALTER TABLE `kategoria`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lisensa`
--
ALTER TABLE `lisensa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `papel`
--
ALTER TABLE `papel`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pozisaun`
--
ALTER TABLE `pozisaun`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `prezensa`
--
ALTER TABLE `prezensa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=679;

--
-- AUTO_INCREMENT for table `salariu`
--
ALTER TABLE `salariu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `salariu_detallu`
--
ALTER TABLE `salariu_detallu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sansaun`
--
ALTER TABLE `sansaun`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `subsidiu`
--
ALTER TABLE `subsidiu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tipu_sansaun`
--
ALTER TABLE `tipu_sansaun`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user_access`
--
ALTER TABLE `user_access`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user_menu`
--
ALTER TABLE `user_menu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_menu_category`
--
ALTER TABLE `user_menu_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_submenu`
--
ALTER TABLE `user_submenu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilizador`
--
ALTER TABLE `utilizador`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
