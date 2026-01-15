-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 15, 2026 at 02:31 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cektani`
--

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` bigint UNSIGNED NOT NULL,
  `sector_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `width` double DEFAULT NULL,
  `length` double DEFAULT NULL,
  `max_capacity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`id`, `sector_id`, `name`, `width`, `length`, `max_capacity`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bed C-01-1', 1.2, 10, 48, '2026-01-10 22:25:44', '2026-01-10 22:25:44'),
(2, 1, 'Bed C-01-2', 1.2, 10, 33, '2026-01-12 02:09:04', '2026-01-12 02:09:04'),
(3, 1, 'Bed C-01-3', 1.2, 10, 33, '2026-01-12 02:14:06', '2026-01-12 02:14:06');

-- --------------------------------------------------------

--
-- Table structure for table `commodities`
--

CREATE TABLE `commodities` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variety` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harvest_duration_days` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commodities`
--

INSERT INTO `commodities` (`id`, `name`, `variety`, `harvest_duration_days`, `created_at`, `updated_at`) VALUES
(1, 'Cabai Roket', 'Wiji Tani', 90, '2026-01-10 22:04:08', '2026-01-10 22:04:08'),
(2, 'Sawit', NULL, 800, '2026-01-10 22:04:32', '2026-01-10 22:04:32'),
(3, 'Genjer', NULL, 90, '2026-01-10 22:05:01', '2026-01-10 22:05:01');

-- --------------------------------------------------------

--
-- Table structure for table `cycle_logs`
--

CREATE TABLE `cycle_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `planting_cycle_id` bigint UNSIGNED NOT NULL,
  `log_date` date NOT NULL,
  `phase` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cycle_logs`
--

INSERT INTO `cycle_logs` (`id`, `planting_cycle_id`, `log_date`, `phase`, `activity`, `notes`, `photo_path`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-01-12', 'Pemeliharaan', 'Pengerjaan terjadwal: Tanam cabe. alright berhasil', NULL, NULL, '2026-01-12 14:33:17', '2026-01-12 14:33:17'),
(2, 1, '2026-01-12', 'Vegetatif', 'Pengerjaan terjadwal: Semprot. done', NULL, NULL, '2026-01-12 14:35:49', '2026-01-12 14:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` decimal(10,2) NOT NULL DEFAULT '0.00',
  `avg_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `min_stock_alert` int NOT NULL DEFAULT '5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `name`, `category`, `unit`, `stock`, `avg_price`, `min_stock_alert`, `created_at`, `updated_at`) VALUES
(1, 'NPK 20:15:40', 'Pupuk', 'kg', '10.00', '10000.00', 5, '2026-01-12 15:29:37', '2026-01-13 20:21:57'),
(2, 'Pupuk sp-32', 'Umum', 'kg', '50.00', '5000.00', 5, '2026-01-13 23:20:07', '2026-01-13 23:20:07');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `inventory_id` bigint UNSIGNED NOT NULL,
  `type` enum('in','out','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price_per_unit` decimal(15,2) DEFAULT NULL,
  `total_price` decimal(15,2) DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `inventory_id`, `type`, `quantity`, `price_per_unit`, `total_price`, `reference_type`, `reference_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'in', '20.00', '10000.00', '200000.00', 'transaction', 5, 'Pembelian Stok Baru', '2026-01-12 15:33:45', '2026-01-12 15:33:45'),
(2, 1, 'out', '2.00', '10000.00', '20000.00', 'schedule', 6, 'Pemakaian untuk jadwal: Pemupukan', '2026-01-12 15:48:48', '2026-01-12 15:48:48'),
(3, 1, 'out', '12.00', '10000.00', '120000.00', 'schedule', 7, 'Pemakaian untuk jadwal: pupuk', '2026-01-12 15:53:32', '2026-01-12 15:53:32'),
(4, 1, 'out', '1.00', '10000.00', '10000.00', 'schedule', 8, 'Pemakaian untuk jadwal: pupuk lagi', '2026-01-12 15:57:40', '2026-01-12 15:57:40'),
(5, 1, 'out', '1.00', '10000.00', '10000.00', 'schedule', 9, 'Pemakaian untuk jadwal: pupuk1', '2026-01-12 16:10:10', '2026-01-12 16:10:10'),
(6, 1, 'out', '1.00', '10000.00', '10000.00', 'schedule', 10, 'Pemakaian untuk jadwal: xxxxxxxxxx', '2026-01-13 20:06:19', '2026-01-13 20:06:19'),
(7, 1, 'out', '1.00', '10000.00', '10000.00', 'schedule', 10, 'Pemakaian untuk jadwal: xxxxxxxxxx', '2026-01-13 20:07:23', '2026-01-13 20:07:23'),
(8, 1, 'out', '1.00', '10000.00', '10000.00', 'schedule', 10, 'Pemakaian untuk jadwal: xxxxxxxxxx', '2026-01-13 20:07:43', '2026-01-13 20:07:43'),
(9, 1, 'out', '1.00', '10000.00', '10000.00', 'schedule', 10, 'Pemakaian untuk jadwal: xxxxxxxxxx', '2026-01-13 20:10:57', '2026-01-13 20:10:57'),
(10, 1, 'in', '20.00', '10000.00', '200000.00', 'transaction', 7, 'Pembelian Stok Baru', '2026-01-13 20:21:38', '2026-01-13 20:21:38'),
(11, 1, 'out', '10.00', '10000.00', '100000.00', 'schedule', 11, 'Pemakaian untuk jadwal: asdfsda', '2026-01-13 20:21:57', '2026-01-13 20:21:57'),
(12, 2, 'in', '50.00', '5000.00', '250000.00', 'manual', NULL, 'Pembelian via Sesi: cabai Roket', '2026-01-13 23:20:07', '2026-01-13 23:20:07');

-- --------------------------------------------------------

--
-- Table structure for table `lands`
--

CREATE TABLE `lands` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `geojson_data` json DEFAULT NULL,
  `area_size` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lands`
--

INSERT INTO `lands` (`id`, `user_id`, `name`, `address`, `geojson_data`, `area_size`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lahan S', 'Desa Sumber Rejo, belakang SMA', '{\"type\": \"Feature\", \"geometry\": {\"type\": \"Polygon\", \"coordinates\": [[[102.269418, -3.437943], [102.269456, -3.43752], [102.269665, -3.437494], [102.269842, -3.437456], [102.269922, -3.437419], [102.270002, -3.437445], [102.270131, -3.437461], [102.270217, -3.437477], [102.270297, -3.437477], [102.270383, -3.437467], [102.270372, -3.437595], [102.270372, -3.437713], [102.270335, -3.437788], [102.27026, -3.437884], [102.270244, -3.437938], [102.269938, -3.437933], [102.269552, -3.437965], [102.269418, -3.437943]]]}, \"properties\": {}}', 5272.86, '2026-01-10 20:58:21', '2026-01-10 20:58:21'),
(3, 1, 'Lahan C', 'Desa Sumber Rejo, belakang SMA', '{\"type\": \"Feature\", \"geometry\": {\"type\": \"Polygon\", \"coordinates\": [[[102.26861, -3.43663], [102.26861, -3.436731], [102.268557, -3.436822], [102.268535, -3.436897], [102.26853, -3.436967], [102.268783, -3.437087], [102.268966, -3.437087], [102.269089, -3.437097], [102.269218, -3.437092], [102.269473, -3.437085], [102.269441, -3.436656], [102.268857, -3.436635], [102.26861, -3.43663]]]}, \"properties\": {}}', 4772.92, '2026-01-10 21:07:57', '2026-01-10 21:07:57');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_01_09_082330_create_lands_table', 1),
(6, '2026_01_09_082344_create_sectors_table', 1),
(7, '2026_01_09_082410_create_beds_table', 1),
(8, '2026_01_09_082512_create_commodities_table', 1),
(9, '2026_01_09_082535_create_planting_cycles_table', 1),
(10, '2026_01_09_082609_create_cycle_logs_table', 1),
(11, '2026_01_09_082627_create_transactions_table', 1),
(12, '2026_01_09_123854_add_area_size_to_sectors_table', 1),
(13, '2026_01_12_211654_create_schedules_table', 2),
(14, '2026_01_12_221522_create_inventories_table', 3),
(15, '2026_01_12_221531_create_inventory_logs_table', 3),
(16, '2026_01_14_031914_change_transaction_type_to_string', 4),
(17, '2026_01_14_053756_create_shopping_items_table', 5),
(18, '2026_01_14_054322_create_shopping_sessions_table', 6),
(19, '2026_01_14_054335_add_session_id_to_shopping_items', 6),
(20, '2026_01_14_062735_add_location_to_shopping_items', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planting_cycles`
--

CREATE TABLE `planting_cycles` (
  `id` bigint UNSIGNED NOT NULL,
  `bed_id` bigint UNSIGNED NOT NULL,
  `commodity_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `estimated_harvest_date` date DEFAULT NULL,
  `initial_plant_count` int NOT NULL,
  `current_plant_count` int NOT NULL,
  `status` enum('active','harvested','failed','planned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `planting_cycles`
--

INSERT INTO `planting_cycles` (`id`, `bed_id`, `commodity_id`, `start_date`, `estimated_harvest_date`, `initial_plant_count`, `current_plant_count`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-01-11', '2026-04-11', 48, 48, 'active', '2026-01-10 22:26:13', '2026-01-10 22:26:13'),
(2, 2, 1, '2026-01-12', '2026-04-12', 33, 33, 'active', '2026-01-12 02:09:31', '2026-01-12 02:09:31'),
(3, 3, 1, '2026-01-12', '2026-04-12', 33, 33, 'active', '2026-01-12 02:14:30', '2026-01-12 02:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `land_id` bigint UNSIGNED NOT NULL,
  `sector_id` bigint UNSIGNED DEFAULT NULL,
  `bed_id` bigint UNSIGNED DEFAULT NULL,
  `planting_cycle_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `status` enum('pending','completed','missed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `land_id`, `sector_id`, `bed_id`, `planting_cycle_id`, `title`, `due_date`, `type`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, NULL, 2, 'Tanam cabe', '2026-01-14', 'general', 'completed', 'testing', '2026-01-12 14:30:43', '2026-01-12 14:31:07'),
(2, 3, NULL, NULL, 1, 'Semprot', '2026-01-16', 'fertilizing', 'completed', 'testing 50', '2026-01-12 14:35:40', '2026-01-12 14:35:49'),
(3, 3, 1, NULL, NULL, 'Penggemburan', '2026-01-17', 'fertilizing', 'completed', 'testing new modal\n[Penyelesaian]: selesai dengan baik', '2026-01-12 14:58:17', '2026-01-12 14:58:31'),
(4, 3, NULL, NULL, NULL, 'alfa', '2026-01-23', 'pest_control', 'pending', 'for all lahan', '2026-01-12 14:59:48', '2026-01-12 14:59:48'),
(5, 3, 1, 1, 1, 'mancing', '2026-01-23', 'irrigation', 'pending', 'all happy', '2026-01-12 15:00:58', '2026-01-12 15:00:58'),
(6, 3, NULL, NULL, NULL, 'Pemupukan', '2026-01-17', 'fertilizing', 'completed', 'lahan\n[Selesai]: selesai di tebar', '2026-01-12 15:48:29', '2026-01-12 15:48:48'),
(7, 3, NULL, NULL, NULL, 'pupuk', '2026-01-13', 'fertilizing', 'completed', 'testing\n[Selesai]: pupuk banyak', '2026-01-12 15:53:06', '2026-01-12 15:53:32'),
(8, 3, NULL, NULL, NULL, 'pupuk lagi', '2026-01-12', 'fertilizing', 'completed', 'coba uji\n[Selesai]: tettttt', '2026-01-12 15:57:24', '2026-01-12 15:57:40'),
(9, 3, NULL, NULL, NULL, 'pupuk1', '2026-01-12', 'fertilizing', 'completed', 'pupuk\n[Selesai]: ', '2026-01-12 16:09:59', '2026-01-12 16:10:10'),
(10, 3, NULL, NULL, NULL, 'xxxxxxxxxx', '2026-01-14', 'fertilizing', 'completed', '\n[Selesai]: ', '2026-01-13 20:06:09', '2026-01-13 20:12:12'),
(11, 3, NULL, NULL, NULL, 'asdfsda', '2026-01-14', 'fertilizing', 'completed', 'd\n[Selesai]: ', '2026-01-13 20:12:51', '2026-01-13 20:21:57');

-- --------------------------------------------------------

--
-- Table structure for table `sectors`
--

CREATE TABLE `sectors` (
  `id` bigint UNSIGNED NOT NULL,
  `land_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_size` double NOT NULL DEFAULT '0',
  `geojson_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sectors`
--

INSERT INTO `sectors` (`id`, `land_id`, `name`, `area_size`, `geojson_data`, `created_at`, `updated_at`) VALUES
(1, 3, 'Sektor 1', 307.65, '{\"type\": \"Feature\", \"geometry\": {\"type\": \"Polygon\", \"coordinates\": [[[102.268708, -3.436691], [102.268739, -3.436695], [102.268746, -3.436778], [102.268762, -3.436816], [102.268773, -3.436853], [102.268782, -3.436883], [102.268794, -3.436927], [102.268811, -3.436985], [102.268817, -3.437018], [102.268787, -3.437026], [102.268774, -3.437003], [102.26875, -3.436993], [102.268716, -3.436963], [102.268695, -3.436931], [102.268659, -3.43688], [102.268672, -3.436844], [102.268685, -3.436797], [102.268696, -3.436757], [102.268708, -3.436691]]]}, \"properties\": {}}', '2026-01-10 21:12:12', '2026-01-10 21:12:12'),
(2, 3, 'Sektor 2', 280.87, '{\"type\": \"Feature\", \"geometry\": {\"type\": \"Polygon\", \"coordinates\": [[[102.268742, -3.436696], [102.268763, -3.43677], [102.268781, -3.436809], [102.268786, -3.436861], [102.268795, -3.436901], [102.268806, -3.436942], [102.268819, -3.436991], [102.268848, -3.436977], [102.268844, -3.436944], [102.268841, -3.436923], [102.268838, -3.436874], [102.268848, -3.436855], [102.268854, -3.43681], [102.268877, -3.436774], [102.268881, -3.436744], [102.268893, -3.436711], [102.268896, -3.436689], [102.268848, -3.436692], [102.26878, -3.436692], [102.268742, -3.436696]]]}, \"properties\": {}}', '2026-01-10 21:17:20', '2026-01-10 21:17:20'),
(3, 3, 'Sektor 3', 60.62, '{\"type\": \"Feature\", \"geometry\": {\"type\": \"Polygon\", \"coordinates\": [[[102.26865, -3.43675], [102.268668, -3.436754], [102.268691, -3.436765], [102.26866, -3.436851], [102.268606, -3.436819], [102.268621, -3.436782], [102.26865, -3.43675]]]}, \"properties\": {}}', '2026-01-10 21:19:28', '2026-01-10 21:19:28'),
(4, 3, 'Sektor 4', 125.85, '{\"type\": \"Feature\", \"geometry\": {\"type\": \"Polygon\", \"coordinates\": [[[102.268615, -3.436841], [102.268644, -3.436852], [102.268659, -3.43686], [102.268655, -3.436881], [102.268672, -3.436934], [102.268688, -3.436948], [102.268708, -3.436971], [102.268734, -3.436997], [102.268769, -3.437021], [102.268787, -3.437038], [102.268813, -3.437033], [102.268824, -3.437057], [102.268795, -3.437064], [102.268763, -3.437058], [102.268734, -3.437041], [102.2687, -3.43701], [102.268669, -3.436975], [102.268647, -3.436954], [102.268629, -3.436916], [102.268615, -3.436891], [102.268609, -3.436863], [102.268615, -3.436841]]]}, \"properties\": {}}', '2026-01-10 21:22:06', '2026-01-10 21:22:06');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_items`
--

CREATE TABLE `shopping_items` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `type` enum('direct','stock') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stock',
  `inventory_id` bigint UNSIGNED DEFAULT NULL,
  `is_purchased` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shopping_session_id` bigint UNSIGNED DEFAULT NULL,
  `land_id` bigint UNSIGNED DEFAULT NULL,
  `sector_id` bigint UNSIGNED DEFAULT NULL,
  `bed_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shopping_items`
--

INSERT INTO `shopping_items` (`id`, `name`, `url`, `estimated_price`, `quantity`, `unit`, `type`, `inventory_id`, `is_purchased`, `created_at`, `updated_at`, `shopping_session_id`, `land_id`, `sector_id`, `bed_id`) VALUES
(5, 'Mulsa 490m x 120cm', 'https://shopee.co.id/Plastik-Mulsa-Hitam-Cap-Badak-18kg-Kuat-Lentur-i.262256315.22459425888', '1275.00', '490.00', 'm', 'direct', NULL, 0, '2026-01-14 19:27:31', '2026-01-14 19:27:31', 3, NULL, NULL, NULL),
(6, 'Benih Cabai Rawit Roket 10gr Wijitani', 'https://shopee.co.id/BENIH-CABAI-RAWIT-ROKET-10GR-WIJITANI-i.561077408.20232807838', '76800.00', '4.00', 'bks', 'stock', NULL, 0, '2026-01-14 19:29:20', '2026-01-14 19:29:20', 3, NULL, NULL, NULL),
(7, 'Kapur Dolomit', 'Toko Jautani', '4170.00', '30.00', 'kg', 'stock', NULL, 0, '2026-01-14 19:32:11', '2026-01-14 19:32:11', 3, NULL, NULL, NULL),
(8, 'NPK 16;16;16', 'Toko Jautani', '16500.00', '50.00', 'kg', 'stock', NULL, 0, '2026-01-14 19:41:28', '2026-01-14 19:41:28', 3, NULL, NULL, NULL),
(9, 'SP-36', 'offline', '5000.00', '50.00', 'kg', 'stock', NULL, 0, '2026-01-14 19:45:45', '2026-01-14 19:45:45', 3, NULL, NULL, NULL),
(10, 'PH meter', NULL, '50000.00', '1.00', 'buah', 'direct', NULL, 0, '2026-01-15 06:07:48', '2026-01-15 06:07:48', 3, NULL, NULL, NULL),
(11, 'Mulsa 490m x 120cm', NULL, '1275.00', '490.00', 'm', 'direct', NULL, 0, '2026-01-15 06:10:18', '2026-01-15 06:10:18', 3, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_sessions`
--

CREATE TABLE `shopping_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planning_date` date DEFAULT NULL,
  `status` enum('active','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shopping_sessions`
--

INSERT INTO `shopping_sessions` (`id`, `name`, `planning_date`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Awal', '2026-01-15', 'active', '2026-01-14 19:17:44', '2026-01-14 19:17:44');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `land_id` bigint UNSIGNED DEFAULT NULL,
  `sector_id` bigint UNSIGNED DEFAULT NULL,
  `bed_id` bigint UNSIGNED DEFAULT NULL,
  `planting_cycle_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `land_id`, `sector_id`, `bed_id`, `planting_cycle_id`, `type`, `category`, `amount`, `transaction_date`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, NULL, NULL, 'expense', 'Racun Rumput', '70000.00', '2026-01-11', 'Racun bablas x 2', '2026-01-10 21:26:16', '2026-01-10 21:26:16'),
(2, 1, 3, 1, NULL, NULL, 'expense', 'Tukang', '80000.00', '2026-01-11', 'Gemburin tanah', '2026-01-10 21:30:59', '2026-01-10 21:30:59'),
(3, 1, 3, NULL, NULL, NULL, 'expense', 'Tadah air', '15000.00', '2026-01-01', 'Terpal Rp5000 x 3', '2026-01-12 02:36:01', '2026-01-12 02:36:01'),
(4, 1, 3, NULL, NULL, NULL, 'income', 'Jual Rumput', '20000.00', '2026-01-12', 'Jual alang alang untuk ternak', '2026-01-12 02:36:53', '2026-01-12 02:36:53'),
(5, 1, NULL, NULL, NULL, NULL, 'expense', 'Belanja Stok', '200000.00', '2026-01-12', 'Beli Stok: NPK 20:15:40 (20 kg)', '2026-01-12 15:33:45', '2026-01-12 15:33:45'),
(6, 1, 3, NULL, NULL, NULL, 'expense', 'Pemakaian Stok', '10000.00', '2026-01-12', 'Auto-log material: NPK 20:15:40 (1 kg)', '2026-01-12 16:10:10', '2026-01-12 16:10:10'),
(7, 1, NULL, NULL, NULL, NULL, 'expense', 'Belanja Stok', '200000.00', '2026-01-14', 'Beli Stok: NPK 20:15:40 (20 kg)', '2026-01-13 20:21:38', '2026-01-13 20:21:38'),
(8, 1, 3, NULL, NULL, NULL, 'cost_allocation', 'Pemakaian Stok', '100000.00', '2026-01-14', 'Auto-log material: NPK 20:15:40 (10 kg)', '2026-01-13 20:21:57', '2026-01-13 20:21:57'),
(9, 1, NULL, NULL, NULL, NULL, 'expense', 'Belanja Stok', '250000.00', '2026-01-14', 'Beli: Pupuk sp-32 (50 kg) - Sesi: cabai Roket', '2026-01-13 23:20:07', '2026-01-13 23:20:07'),
(10, 1, NULL, NULL, NULL, NULL, 'expense', 'Operasional/Jasa', '35000.00', '2026-01-14', 'Beli: Obat semprot (1 Botol) - Sesi: cabai Roket', '2026-01-13 23:20:54', '2026-01-13 23:20:54'),
(11, 1, 3, 1, NULL, NULL, 'income', 'testing', '200000.00', '2026-01-14', 'fdsfadfads', '2026-01-14 01:15:19', '2026-01-14 01:15:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'ojik', 'admin@gmail.com', NULL, '$2y$12$k9rtGL51HuQWYcRw74cWOuZ7linxCuSdtBwzRZh4jYmeBjpuNblZ2', 'SWQ6yHBwxJPsabGcfxbtSEPv93PhhhLXMTdgRlCfLauuF3iib57gJxx3J8Ka', '2026-01-10 20:55:04', '2026-01-10 20:55:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beds_sector_id_foreign` (`sector_id`);

--
-- Indexes for table `commodities`
--
ALTER TABLE `commodities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cycle_logs`
--
ALTER TABLE `cycle_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cycle_logs_planting_cycle_id_foreign` (`planting_cycle_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_logs_inventory_id_foreign` (`inventory_id`);

--
-- Indexes for table `lands`
--
ALTER TABLE `lands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lands_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `planting_cycles`
--
ALTER TABLE `planting_cycles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `planting_cycles_bed_id_foreign` (`bed_id`),
  ADD KEY `planting_cycles_commodity_id_foreign` (`commodity_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_land_id_foreign` (`land_id`),
  ADD KEY `schedules_sector_id_foreign` (`sector_id`),
  ADD KEY `schedules_bed_id_foreign` (`bed_id`),
  ADD KEY `schedules_planting_cycle_id_foreign` (`planting_cycle_id`);

--
-- Indexes for table `sectors`
--
ALTER TABLE `sectors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sectors_land_id_foreign` (`land_id`);

--
-- Indexes for table `shopping_items`
--
ALTER TABLE `shopping_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shopping_items_inventory_id_foreign` (`inventory_id`),
  ADD KEY `shopping_items_shopping_session_id_foreign` (`shopping_session_id`),
  ADD KEY `shopping_items_land_id_foreign` (`land_id`),
  ADD KEY `shopping_items_sector_id_foreign` (`sector_id`),
  ADD KEY `shopping_items_bed_id_foreign` (`bed_id`);

--
-- Indexes for table `shopping_sessions`
--
ALTER TABLE `shopping_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_land_id_foreign` (`land_id`),
  ADD KEY `transactions_sector_id_foreign` (`sector_id`),
  ADD KEY `transactions_bed_id_foreign` (`bed_id`),
  ADD KEY `transactions_planting_cycle_id_foreign` (`planting_cycle_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `commodities`
--
ALTER TABLE `commodities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cycle_logs`
--
ALTER TABLE `cycle_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lands`
--
ALTER TABLE `lands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planting_cycles`
--
ALTER TABLE `planting_cycles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sectors`
--
ALTER TABLE `sectors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shopping_items`
--
ALTER TABLE `shopping_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shopping_sessions`
--
ALTER TABLE `shopping_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `beds_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cycle_logs`
--
ALTER TABLE `cycle_logs`
  ADD CONSTRAINT `cycle_logs_planting_cycle_id_foreign` FOREIGN KEY (`planting_cycle_id`) REFERENCES `planting_cycles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lands`
--
ALTER TABLE `lands`
  ADD CONSTRAINT `lands_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `planting_cycles`
--
ALTER TABLE `planting_cycles`
  ADD CONSTRAINT `planting_cycles_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`),
  ADD CONSTRAINT `planting_cycles_commodity_id_foreign` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_planting_cycle_id_foreign` FOREIGN KEY (`planting_cycle_id`) REFERENCES `planting_cycles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sectors`
--
ALTER TABLE `sectors`
  ADD CONSTRAINT `sectors_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_items`
--
ALTER TABLE `shopping_items`
  ADD CONSTRAINT `shopping_items_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shopping_items_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shopping_items_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shopping_items_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shopping_items_shopping_session_id_foreign` FOREIGN KEY (`shopping_session_id`) REFERENCES `shopping_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_planting_cycle_id_foreign` FOREIGN KEY (`planting_cycle_id`) REFERENCES `planting_cycles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
