-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 27-05-2026 a las 00:30:33
-- Versión del servidor: 8.0.45-cll-lve
-- Versión de PHP: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gocreative_seim`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accounting_accounts`
--

CREATE TABLE `accounting_accounts` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `code` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8mb3_unicode_ci NOT NULL,
  `level` int NOT NULL DEFAULT '1',
  `parent_id` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accounting_journals`
--

CREATE TABLE `accounting_journals` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `entry_number` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `entry_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `source` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'manual',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'borrador',
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accounting_journal_lines`
--

CREATE TABLE `accounting_journal_lines` (
  `id` int NOT NULL,
  `journal_id` int NOT NULL,
  `account_id` int NOT NULL,
  `line_description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `debit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accounting_periods`
--

CREATE TABLE `accounting_periods` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `period` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'abierto',
  `closed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `company_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `action` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `entity` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `entity_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `company_id`, `user_id`, `action`, `entity`, `entity_id`, `created_at`) VALUES
(1, 1, 1, 'update', 'settings', NULL, '2026-02-04 16:14:42'),
(2, 1, 1, 'create', 'companies', 2, '2026-02-04 17:11:58'),
(3, 1, 1, 'update', 'users_company', 1, '2026-02-04 17:12:10'),
(4, 1, 1, 'create', 'users', NULL, '2026-02-05 13:51:53'),
(5, 1, 2, 'create', 'product_families', NULL, '2026-02-05 14:12:39'),
(6, 1, 2, 'create', 'product_subfamilies', NULL, '2026-02-05 14:13:05'),
(7, 1, 2, 'create', 'suppliers', NULL, '2026-02-05 14:16:35'),
(8, 1, 2, 'create', 'competitor_companies', NULL, '2026-02-05 14:17:49'),
(9, 1, 2, 'create', 'products', NULL, '2026-02-05 14:19:05'),
(10, 1, 2, 'update', 'products', 1, '2026-02-05 14:23:05'),
(11, 1, 2, 'create', 'users', NULL, '2026-02-05 14:28:45'),
(12, 1, 2, 'create', 'roles', 2, '2026-02-05 14:29:48'),
(13, 1, 2, 'update', 'users', 3, '2026-02-05 14:31:37'),
(14, 1, 2, 'create', 'produced_products', NULL, '2026-02-05 14:36:30'),
(15, 1, 2, 'update', 'products', 1, '2026-02-05 14:38:12'),
(16, 1, 2, 'create', 'production_orders', 1, '2026-02-05 14:38:55'),
(17, 1, 1, 'update', 'role_permissions', 2, '2026-02-05 14:56:54'),
(18, 1, 1, 'update', 'users', 3, '2026-02-05 14:57:14'),
(19, 1, 1, 'update', 'users', 3, '2026-02-05 14:58:59'),
(20, 1, 1, 'update', 'users', 3, '2026-02-05 14:59:14'),
(21, 1, 1, 'update', 'role_permissions', 2, '2026-02-05 14:59:32'),
(22, 1, 2, 'update', 'role_permissions', 2, '2026-02-05 15:01:19'),
(23, 1, 2, 'update', 'users', 3, '2026-02-05 15:02:27'),
(24, 1, 1, 'create', 'purchase_orders', 1, '2026-03-02 14:15:06'),
(25, 1, 1, 'create', 'clients', NULL, '2026-03-02 21:36:04'),
(26, 1, 1, 'update', 'clients', 1, '2026-03-02 21:37:38'),
(27, 1, 1, 'update', 'clients', 1, '2026-03-02 21:38:20'),
(28, 1, 1, 'create', 'quotes', 1, '2026-03-02 21:48:11'),
(29, 2, 1, 'create', 'petty_cash_receipts', 1, '2026-03-03 23:39:01'),
(30, 2, 1, 'create', 'petty_cash_receipts', 2, '2026-03-03 23:49:20'),
(31, 2, 1, 'update', 'suppliers', 2, '2026-03-04 00:39:39'),
(32, 2, 1, 'create', 'purchase_invoice_records', 1, '2026-03-04 01:42:50'),
(33, 2, 1, 'create', 'petty_cash_receipts', 3, '2026-03-04 02:01:35'),
(34, 2, 1, 'create', 'purchase_invoice_records', 2, '2026-03-04 02:02:32'),
(35, 2, 1, 'update', 'petty_cash_receipts', 3, '2026-03-04 02:18:59'),
(36, 2, 1, 'update', 'purchase_invoice_records', 2, '2026-03-04 02:19:23'),
(37, 2, 1, 'update', 'purchase_invoice_records', 2, '2026-03-04 02:20:06'),
(38, 2, 1, 'update', 'purchase_invoice_records', 1, '2026-03-04 02:20:21'),
(39, 2, 1, 'update', 'purchase_invoice_records', 2, '2026-03-04 02:20:41'),
(40, 2, 3, 'create', 'clients', NULL, '2026-03-29 23:08:17'),
(41, 2, 3, 'create', 'clients', NULL, '2026-04-07 23:52:24'),
(42, 2, 3, 'create', 'quotes', 2, '2026-04-08 00:05:49'),
(43, 2, 3, 'update', 'quotes', 2, '2026-04-08 00:08:35'),
(44, 2, 3, 'delete', 'quotes', 2, '2026-04-08 00:11:00'),
(45, 2, 3, 'create', 'quotes', 3, '2026-04-08 00:12:27'),
(46, 2, 3, 'update', 'quotes', 3, '2026-04-08 00:18:23'),
(47, 2, 3, 'create', 'quotes', 4, '2026-04-14 11:08:14'),
(48, 2, 2, 'create', 'quotes', 5, '2026-04-21 21:03:57'),
(49, 2, 1, 'update', 'companies', 2, '2026-04-22 12:43:34'),
(50, 2, 1, 'update', 'companies', 2, '2026-04-22 12:50:29'),
(51, 2, 1, 'create', 'suppliers', NULL, '2026-04-22 13:54:33'),
(52, 2, 1, 'update', 'companies', 2, '2026-04-22 14:13:30'),
(53, 2, 3, 'delete', 'quotes', 5, '2026-04-22 14:17:15'),
(54, 1, 2, 'update', 'companies', 2, '2026-04-22 14:48:20'),
(55, 1, 2, 'update', 'companies', 2, '2026-04-22 14:48:38'),
(56, 1, 2, 'update', 'companies', 2, '2026-04-22 14:49:33'),
(57, 1, 2, 'update', 'companies', 2, '2026-04-22 14:51:28'),
(58, 2, 1, 'update', 'companies', 2, '2026-04-22 14:53:45'),
(59, 2, 2, 'update', 'companies', 2, '2026-04-22 14:54:28'),
(60, 2, 3, 'update', 'quotes', 4, '2026-04-22 15:03:23'),
(61, 1, 3, 'create', 'purchase_orders', 2, '2026-04-22 15:16:55'),
(62, 1, 2, 'update', 'products', 1, '2026-04-22 15:30:42'),
(63, 1, 2, 'update', 'products', 1, '2026-04-22 15:31:46'),
(64, 1, 1, 'update', 'quotes', 1, '2026-04-22 15:40:54'),
(65, 1, 3, 'update', 'quotes', 1, '2026-04-22 15:41:07'),
(66, 1, 1, 'update', 'quotes', 1, '2026-04-22 15:49:19'),
(67, 1, 1, 'update', 'quotes', 1, '2026-04-22 15:49:28'),
(68, 1, 2, 'update', 'quotes', 1, '2026-04-22 15:57:10'),
(69, 1, 2, 'update', 'quotes', 1, '2026-04-22 15:57:41'),
(70, 2, 1, 'delete', 'products', 4, '2026-04-22 16:25:05'),
(71, 2, 1, 'delete', 'products', 5, '2026-04-22 16:25:09'),
(72, 1, 1, 'update', 'users', 2, '2026-04-23 15:17:46'),
(73, 1, 1, 'update', 'users', 2, '2026-04-23 15:18:01'),
(74, 2, 3, 'create', 'quotes', 6, '2026-04-30 00:05:52'),
(75, 2, 3, 'update', 'quotes', 6, '2026-04-30 00:07:14'),
(76, 2, 3, 'update', 'quotes', 6, '2026-04-30 00:12:05'),
(77, 2, 3, 'update', 'quotes', 6, '2026-04-30 00:15:55'),
(78, 2, 2, 'create', 'suppliers', NULL, '2026-05-25 17:20:01'),
(79, 2, 2, 'update', 'suppliers', 5, '2026-05-25 17:20:49'),
(80, 2, 2, 'delete', 'suppliers', 4, '2026-05-25 17:20:57'),
(81, 2, 2, 'delete', 'suppliers', 3, '2026-05-25 17:21:00'),
(82, 2, 2, 'delete', 'suppliers', 2, '2026-05-25 17:21:06'),
(83, 2, 2, 'create', 'suppliers', NULL, '2026-05-25 17:28:23'),
(84, 2, 2, 'create', 'products', NULL, '2026-05-25 19:12:37'),
(85, 2, 2, 'create', 'product_families', NULL, '2026-05-25 19:13:14'),
(86, 2, 2, 'create', 'product_families', NULL, '2026-05-25 19:13:30'),
(87, 2, 2, 'create', 'product_families', NULL, '2026-05-25 19:13:55'),
(88, 2, 2, 'create', 'product_families', NULL, '2026-05-25 19:14:09'),
(89, 2, 2, 'delete', 'products', 6, '2026-05-26 11:17:10'),
(90, 1, 1, 'update', 'users', 2, '2026-05-26 11:42:01'),
(91, 2, 2, 'create', 'users', NULL, '2026-05-26 11:54:44'),
(92, 2, 2, 'update', 'roles', 1, '2026-05-26 11:59:47'),
(93, 2, 2, 'delete', 'competitor_companies', 3, '2026-05-26 12:02:26'),
(94, 2, 2, 'create', 'clients', NULL, '2026-05-26 12:19:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `bank_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `account_type` varchar(80) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `account_number` varchar(80) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `account_holder` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `account_holder_rut` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'CLP',
  `current_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `company_id`, `name`, `bank_name`, `account_type`, `account_number`, `account_holder`, `account_holder_rut`, `currency`, `current_balance`, `created_at`, `updated_at`) VALUES
(1, 2, 'Seim Cuenta Dolar', 'Banco Santander', 'Cuenta Corriente', '005106162168', 'Ricardo Zuñiga', '77.400.109-3', 'USD', 0.00, '2026-03-04 00:02:53', '2026-05-26 12:09:35'),
(2, 2, 'Seim Spa', 'Banco Estado', 'Chequera Electrónica', '90272342210', 'Ricardo Zuñiga', '77.400.109-3', 'CLP', 0.00, '2026-03-04 01:57:45', '2026-05-26 12:07:11'),
(3, 2, 'Seim Spa', 'Banco Santander', 'Cuenta Corriente', '000097874484', 'Ricardo Zuñiga', '77.400.109-3', 'CLP', 0.00, '2026-05-26 12:04:55', '2026-05-26 12:09:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bank_transactions`
--

CREATE TABLE `bank_transactions` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `bank_account_id` int NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'deposito',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reference` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `bank_transactions`
--

INSERT INTO `bank_transactions` (`id`, `company_id`, `bank_account_id`, `transaction_date`, `description`, `type`, `amount`, `balance`, `reference`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-03-04', '', 'retiro', 10000000.00, 90000000.00, 'Commpra maquinaria', '2026-03-04 00:03:35', '2026-03-04 00:03:35'),
(2, 2, 2, '2026-03-04', 'Descripcion devolucion (compras de aceites maquinas)', 'transferencia', 2000000.00, 91000000.00, 'Devolucion rendicion 102', '2026-03-04 01:58:46', '2026-03-04 01:58:46'),
(3, 2, 2, '2026-03-04', 'Devolucion rendicion 105', 'retiro', 15000000.00, 76000000.00, 'Devolucion rendicion 105', '2026-03-04 02:00:13', '2026-03-04 02:00:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendar_events`
--

CREATE TABLE `calendar_events` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `created_by_user_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `event_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'meeting',
  `location` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime DEFAULT NULL,
  `all_day` tinyint(1) NOT NULL DEFAULT '0',
  `reminder_minutes` int DEFAULT NULL,
  `class_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'bg-primary-subtle text-primary',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendar_event_attendees`
--

CREATE TABLE `calendar_event_attendees` (
  `id` int NOT NULL,
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendar_event_documents`
--

CREATE TABLE `calendar_event_documents` (
  `id` int NOT NULL,
  `event_id` int NOT NULL,
  `document_id` int NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int NOT NULL,
  `thread_id` int NOT NULL,
  `sender_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sender_id` int NOT NULL,
  `message` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_threads`
--

CREATE TABLE `chat_threads` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `subject` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'abierto',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `billing_email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `giro` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `commune` varchar(120) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `contact` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mandante_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mandante_rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mandante_phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mandante_email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `portal_token` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `portal_password` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb3_unicode_ci,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `company_id`, `name`, `rut`, `email`, `billing_email`, `phone`, `address`, `giro`, `commune`, `contact`, `mandante_name`, `mandante_rut`, `mandante_phone`, `mandante_email`, `avatar_path`, `portal_token`, `portal_password`, `notes`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Prueba', '15626773-2', 'erwin.2785@gmail.com', 'erwin.2785@gmail.com', '+56944627287', 'Parma', 'Venta de botellas', 'Santiago de Chile (Santiago, Chile)', 'Erwin Isla S', 'Erwin Isla S', '15626773-2', '+56944627287', 'erwin.2785@gmail.com', NULL, 'ee9474bce07ff572d10477a70b4efd64', '$2y$10$xJ0SWE/dSZ/hN2Jvfzuhe.lm/WzlCPci0E565PS3hNUTTV/tmGQue', NULL, 'activo', '2026-03-02 21:36:04', '2026-03-02 21:38:20', NULL),
(2, 2, 'Claudio Tapia- Francisco Ojeda', '', 'francisco.ojeda@beumer.com', 'francisco.ojeda@beumer.com', NULL, NULL, 'Centros de Vigilancia', NULL, NULL, NULL, NULL, NULL, 'francisco.ojeda@beumer.com', NULL, 'c68f0f9c6e6f5862dad968eb15cbb2e8', '$2y$10$.5qsDrS4cqAYaTjyyc12Oevxa7tyZXw6uvwC/HDnhFEqK1SDlI01G', 'CENTRO DE VIGILANCIA', 'activo', '2026-03-29 23:08:17', '2026-03-29 23:08:17', NULL),
(3, 2, 'INVERSIONES EL BELLOTO TRES LIMITADA', '76042517-6', 'claudio.yevenes@myeh.cl', 'claudio.yevenes@myeh.cl', NULL, 'CERRO EL PLOMO 5680. OF 604 PS 6. LAS CONDES', 'SOCIEDAD INVERSIONES-ARRIENDO INMUEBLES, MAQUINARIA MINERA', 'LAS CONDES. REGIÓN METROPOLITANA', 'Claudio Yevenes', NULL, NULL, NULL, NULL, NULL, '70d938105ade2b5da0f232b759dce7e8', '$2y$10$/1hWbgD3i94bBn92Fanfg.xhoiEFz1lHCG3eWgHxvP.AaZtwROJbS', NULL, 'activo', '2026-04-07 23:52:24', '2026-04-07 23:52:24', NULL),
(4, 2, 'Minera Centinela', '76727040-2', 'rguzman@mineracentinela.cl', 'rguzman@mineracentinela.cl', NULL, 'Apoquindo 4001 1802, Santiago', 'Extraccion y procesamiento de Cobre', 'Las Condes', NULL, NULL, NULL, NULL, NULL, NULL, 'f78db8283c0355674257cb7c27a6ec15', '$2y$10$pGZPmnanYKpp30wvCin/N.JB4K9LfKvBzcFHMFskcHZAPtVchoNV.', NULL, 'activo', '2026-05-26 12:19:16', '2026-05-26 12:19:16', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `commercial_briefs`
--

CREATE TABLE `commercial_briefs` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `contact_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `contact_email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `service_summary` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `expected_budget` decimal(12,2) DEFAULT NULL,
  `desired_start_date` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'nuevo',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

CREATE TABLE `companies` (
  `id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `giro` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `commune` varchar(120) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_color` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_black` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `companies`
--

INSERT INTO `companies` (`id`, `name`, `rut`, `email`, `phone`, `address`, `giro`, `commune`, `logo_color`, `logo_black`, `created_at`, `updated_at`) VALUES
(1, 'Acquaperla SPA', '', '', '', '', '', '', 'storage/uploads/logos/logo-color-5eb8ac6515f537bb.png', NULL, '2026-02-04 13:20:55', '2026-02-04 16:14:42'),
(2, 'Seim Energia', '77.400.109-3', 'seim@seimenergia.com', '+56990961266', 'El Roble N°7479, Antofagasta', 'Comercializadora y Venta de Insumos Electricos', 'ANTOFAGASTA', 'storage/uploads/logos/logo-color-d53b7dc015073ee7.png', '', '2026-02-04 17:11:58', '2026-04-22 14:54:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competitor_companies`
--

CREATE TABLE `competitor_companies` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `competitor_companies`
--

INSERT INTO `competitor_companies` (`id`, `company_id`, `name`, `code`, `rut`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 1, 'No aplica', 'NA', '', '', '', '2026-02-05 14:17:49', '2026-02-05 14:17:49'),
(2, 1, 'Seim Energia', 'SE', '77.400.109-3', 'seim@seimenergia.com', 'El Roble N°7479, Antofagasta', '2026-04-22 15:59:28', '2026-04-22 15:59:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documents`
--

CREATE TABLE `documents` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_size` int NOT NULL DEFAULT '0',
  `is_favorite` tinyint(1) NOT NULL DEFAULT '0',
  `download_count` int NOT NULL DEFAULT '0',
  `last_downloaded_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_categories`
--

CREATE TABLE `document_categories` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(120) COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '#6c757d',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_shares`
--

CREATE TABLE `document_shares` (
  `id` int NOT NULL,
  `document_id` int NOT NULL,
  `user_id` int NOT NULL,
  `shared_by_user_id` int NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `subject` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `body_html` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `error` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_queue`
--

CREATE TABLE `email_queue` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int DEFAULT NULL,
  `template_id` int DEFAULT NULL,
  `subject` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `body_html` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'cobranza',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pending',
  `scheduled_at` datetime NOT NULL,
  `tries` int NOT NULL DEFAULT '0',
  `last_error` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `subject` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `body_html` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'cobranza',
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `acquisition_date` date NOT NULL,
  `acquisition_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `depreciation_method` varchar(30) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'linea_recta',
  `useful_life_months` int NOT NULL DEFAULT '0',
  `accumulated_depreciation` decimal(12,2) NOT NULL DEFAULT '0.00',
  `book_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `honorarios_documents`
--

CREATE TABLE `honorarios_documents` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `provider_name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `provider_rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `document_number` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `issue_date` date NOT NULL,
  `gross_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `retention_rate` decimal(5,2) NOT NULL DEFAULT '13.00',
  `retention_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pendiente',
  `paid_at` date DEFAULT NULL,
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_attendance`
--

CREATE TABLE `hr_attendance` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `worked_hours` decimal(5,2) DEFAULT NULL,
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `absence_type` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_contracts`
--

CREATE TABLE `hr_contracts` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `contract_type_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `position_id` int DEFAULT NULL,
  `schedule_id` int DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `salary` decimal(12,2) NOT NULL,
  `weekly_hours` int NOT NULL DEFAULT '45',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'vigente',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_contract_types`
--

CREATE TABLE `hr_contract_types` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `max_duration_months` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_departments`
--

CREATE TABLE `hr_departments` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_employees`
--

CREATE TABLE `hr_employees` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `position_id` int DEFAULT NULL,
  `health_provider_id` int DEFAULT NULL,
  `pension_fund_id` int DEFAULT NULL,
  `rut` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `nationality` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `civil_status` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hire_date` date NOT NULL,
  `termination_date` date DEFAULT NULL,
  `health_provider` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `health_plan` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pension_fund` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pension_rate` decimal(5,2) NOT NULL DEFAULT '10.00',
  `health_rate` decimal(5,2) NOT NULL DEFAULT '7.00',
  `unemployment_rate` decimal(5,2) NOT NULL DEFAULT '0.60',
  `dependents_count` int NOT NULL DEFAULT '0',
  `payment_method` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bank_account_type` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `qr_token` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `face_descriptor` text COLLATE utf8mb3_unicode_ci,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_health_providers`
--

CREATE TABLE `hr_health_providers` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `provider_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'fonasa',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_payrolls`
--

CREATE TABLE `hr_payrolls` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `base_salary` decimal(12,2) NOT NULL,
  `bonuses` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_earnings` decimal(12,2) NOT NULL DEFAULT '0.00',
  `other_deductions` decimal(12,2) NOT NULL DEFAULT '0.00',
  `taxable_income` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pension_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `health_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unemployment_deduction` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_pay` decimal(12,2) NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'borrador',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_payroll_items`
--

CREATE TABLE `hr_payroll_items` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `item_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'haber',
  `taxable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_payroll_lines`
--

CREATE TABLE `hr_payroll_lines` (
  `id` int NOT NULL,
  `payroll_id` int NOT NULL,
  `payroll_item_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_pension_funds`
--

CREATE TABLE `hr_pension_funds` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_positions`
--

CREATE TABLE `hr_positions` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hr_work_schedules`
--

CREATE TABLE `hr_work_schedules` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `weekly_hours` int NOT NULL DEFAULT '45',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `lunch_break_minutes` int NOT NULL DEFAULT '60',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventory_movements`
--

CREATE TABLE `inventory_movements` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `produced_product_id` int DEFAULT NULL,
  `movement_date` date NOT NULL,
  `movement_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reference_type` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `inventory_movements`
--

INSERT INTO `inventory_movements` (`id`, `company_id`, `product_id`, `produced_product_id`, `movement_date`, `movement_type`, `quantity`, `unit_cost`, `reference_type`, `reference_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, '2026-02-05', 'salida', 500, 1000.00, 'production', 1, 'Consumo producción', '2026-02-05 14:38:55', '2026-02-05 14:38:55'),
(2, 1, NULL, 1, '2026-02-05', 'entrada', 500, 2040.00, 'production', 1, 'Ingreso producción', '2026-02-05 14:38:55', '2026-02-05 14:38:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoices`
--

CREATE TABLE `invoices` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `service_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `numero` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `impuestos` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `sii_document_type` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_document_number` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_giro` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_commune` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_tax_rate` decimal(5,2) NOT NULL DEFAULT '19.00',
  `sii_exempt_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notas` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `company_id` int DEFAULT NULL,
  `title` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `company_id`, `title`, `message`, `type`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nuevo usuario', 'Se creó el usuario \"Yaritza Barreda\".', 'success', NULL, '2026-02-05 13:51:53', '2026-02-05 13:51:53'),
(2, 2, 'Nuevo usuario', 'Se creó el usuario \"Yaritza Barreda\".', 'success', NULL, '2026-02-05 13:51:53', '2026-02-05 13:51:53'),
(3, 1, 'Nuevo usuario', 'Se creó el usuario \"Erik Rojas\".', 'success', NULL, '2026-02-05 14:28:45', '2026-02-05 14:28:45'),
(4, 2, 'Nuevo usuario', 'Se creó el usuario \"Erik Rojas\".', 'success', NULL, '2026-02-05 14:28:45', '2026-02-05 14:28:45'),
(5, 1, 'Nuevo cliente', 'Se creó el cliente \"Prueba\".', 'success', NULL, '2026-03-02 21:36:04', '2026-03-02 21:36:04'),
(6, 1, 'Nueva cotización', 'Se creó la cotización COT-000001.', 'success', NULL, '2026-03-02 21:48:11', '2026-03-02 21:48:11'),
(7, 2, 'Nuevo cliente', 'Se creó el cliente \"Claudio Tapia- Francisco Ojeda\".', 'success', NULL, '2026-03-29 23:08:17', '2026-03-29 23:08:17'),
(8, 2, 'Nuevo cliente', 'Se creó el cliente \"INVERSIONES EL BELLOTO TRES LIMITADA\".', 'success', NULL, '2026-04-07 23:52:24', '2026-04-07 23:52:24'),
(9, 2, 'Nueva cotización', 'Se creó la cotización COT-000001.', 'success', NULL, '2026-04-08 00:05:49', '2026-04-08 00:05:49'),
(10, 2, 'Nueva cotización', 'Se creó la cotización COT-000001.', 'success', NULL, '2026-04-08 00:12:27', '2026-04-08 00:12:27'),
(11, 2, 'Cotización no enviada', 'No se pudo enviar la cotización.', 'danger', NULL, '2026-04-08 09:02:10', '2026-04-08 09:02:10'),
(12, 2, 'Nueva cotización', 'Se creó la cotización COT-000004.', 'success', NULL, '2026-04-14 11:08:14', '2026-04-14 11:08:14'),
(13, 2, 'Cotización no enviada', 'No se pudo enviar la cotización.', 'danger', NULL, '2026-04-14 11:09:02', '2026-04-14 11:09:02'),
(14, 2, 'Nueva cotización', 'Se creó la cotización COT-000005.', 'success', NULL, '2026-04-21 21:03:57', '2026-04-21 21:03:57'),
(15, 2, 'Nueva cotización', 'Se creó la cotización COT-000005.', 'success', NULL, '2026-04-30 00:05:52', '2026-04-30 00:05:52'),
(16, 1, 'Nuevo usuario', 'Se creó el usuario \"Ricardo Zuñiga P.\".', 'success', NULL, '2026-05-26 11:54:44', '2026-05-26 11:54:44'),
(17, 2, 'Nuevo usuario', 'Se creó el usuario \"Ricardo Zuñiga P.\".', 'success', NULL, '2026-05-26 11:54:44', '2026-05-26 11:54:44'),
(18, 2, 'Nuevo cliente', 'Se creó el cliente \"Minera Centinela\".', 'success', NULL, '2026-05-26 12:19:16', '2026-05-26 12:19:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `referencia` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `comprobante` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `petty_cash_products`
--

CREATE TABLE `petty_cash_products` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(180) COLLATE utf8mb3_unicode_ci NOT NULL,
  `classification` enum('producto','servicio') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'servicio',
  `category` varchar(120) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `suggested_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit_measure` varchar(30) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Unidad',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `petty_cash_products`
--

INSERT INTO `petty_cash_products` (`id`, `company_id`, `name`, `classification`, `category`, `suggested_price`, `unit_measure`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Pan Marraqueta', 'servicio', 'Pan', 2000.00, 'Unidad', '2026-03-03 23:37:47', '2026-03-03 23:37:47', NULL),
(2, 2, 'Jamon Sandwish', 'servicio', 'Jamon', 5000.00, 'Unidad', '2026-03-03 23:38:27', '2026-03-03 23:38:27', NULL),
(3, 2, 'Arrollado', 'servicio', 'Jamon', 4000.00, 'Unidad', '2026-03-03 23:48:25', '2026-03-03 23:48:25', NULL),
(4, 2, 'Cocoacola 3lt', 'servicio', 'Bebidas', 3200.00, 'Unidad', '2026-03-03 23:48:39', '2026-03-03 23:48:39', NULL),
(5, 2, 'Cafe 300g', 'servicio', 'Cafe', 4500.00, 'Unidad', '2026-03-03 23:58:26', '2026-03-03 23:58:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `petty_cash_receipts`
--

CREATE TABLE `petty_cash_receipts` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `receipt_number` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `receipt_date` date NOT NULL,
  `supplier_name` varchar(180) COLLATE utf8mb3_unicode_ci NOT NULL,
  `currency` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'CLP',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `petty_cash_receipts`
--

INSERT INTO `petty_cash_receipts` (`id`, `company_id`, `receipt_number`, `receipt_date`, `supplier_name`, `currency`, `total_amount`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, '10001', '2026-03-03', 'Provedor prueba', 'CLP', 25000.00, '', 1, '2026-03-03 23:39:01', '2026-03-03 23:39:01', NULL),
(2, 2, '1002', '2026-03-03', 'Provedor prueba', 'CLP', 21200.00, '', 1, '2026-03-03 23:49:20', '2026-03-03 23:49:20', NULL),
(3, 2, '10032', '2026-03-04', 'Provedor prueba', 'CLP', 5600.00, '', 1, '2026-03-04 02:01:35', '2026-03-04 02:18:59', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `petty_cash_receipt_items`
--

CREATE TABLE `petty_cash_receipt_items` (
  `id` int NOT NULL,
  `receipt_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `observation` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `petty_cash_receipt_items`
--

INSERT INTO `petty_cash_receipt_items` (`id`, `receipt_id`, `product_id`, `description`, `quantity`, `unit_price`, `subtotal`, `observation`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Pan Marraqueta', 10.00, 2000.00, 20000.00, 'Para desayuno', '2026-03-03 23:39:01', '2026-03-03 23:39:01'),
(2, 1, 2, 'Jamon Sandwish', 1.00, 5000.00, 5000.00, 'Para desayuno', '2026-03-03 23:39:01', '2026-03-03 23:39:01'),
(3, 2, 3, 'Arrollado', 3.00, 4000.00, 12000.00, '', '2026-03-03 23:49:20', '2026-03-03 23:49:20'),
(4, 2, 4, 'Cocoacola 3lt', 1.00, 3200.00, 3200.00, '', '2026-03-03 23:49:20', '2026-03-03 23:49:20'),
(5, 2, 1, 'Pan Marraqueta', 3.00, 2000.00, 6000.00, '', '2026-03-03 23:49:20', '2026-03-03 23:49:20'),
(8, 3, 3, 'Arrollado', 1.30, 4000.00, 5200.00, 'Para desayuno', '2026-03-04 02:18:59', '2026-03-04 02:18:59'),
(9, 3, 1, 'Pan Marraqueta', 0.20, 2000.00, 400.00, '', '2026-03-04 02:18:59', '2026-03-04 02:18:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_sessions`
--

CREATE TABLE `pos_sessions` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `user_id` int NOT NULL,
  `opening_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sale_context` enum('local','camion') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'local',
  `closing_amount` decimal(12,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'abierto',
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `pos_sessions`
--

INSERT INTO `pos_sessions` (`id`, `company_id`, `user_id`, `opening_amount`, `sale_context`, `closing_amount`, `status`, `opened_at`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1500.00, 'local', 100000.00, 'cerrado', '2026-02-05 14:24:16', '2026-02-05 23:01:53', '2026-02-05 14:24:16', '2026-02-05 23:01:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_session_withdrawals`
--

CREATE TABLE `pos_session_withdrawals` (
  `id` int NOT NULL,
  `pos_session_id` int NOT NULL,
  `company_id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produced_products`
--

CREATE TABLE `produced_products` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_min` int NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `produced_products`
--

INSERT INTO `produced_products` (`id`, `company_id`, `name`, `sku`, `description`, `price`, `cost`, `stock`, `stock_min`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Bidon 20lts', '12345', '', 2500.00, 2040.00, 1500, 100, 'activo', '2026-02-05 14:36:29', '2026-02-05 14:38:55', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produced_product_materials`
--

CREATE TABLE `produced_product_materials` (
  `id` int NOT NULL,
  `produced_product_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `produced_product_materials`
--

INSERT INTO `produced_product_materials` (`id`, `produced_product_id`, `product_id`, `quantity`, `unit_cost`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1.00, 1000.00, 1000.00, '2026-02-05 14:36:29', '2026-02-05 14:36:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `production_expenses`
--

CREATE TABLE `production_expenses` (
  `id` int NOT NULL,
  `production_id` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `production_expenses`
--

INSERT INTO `production_expenses` (`id`, `production_id`, `description`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'Agua', 500000.00, '2026-02-05 14:38:55', '2026-02-05 14:38:55'),
(2, 1, 'Luz', 20000.00, '2026-02-05 14:38:55', '2026-02-05 14:38:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `production_inputs`
--

CREATE TABLE `production_inputs` (
  `id` int NOT NULL,
  `production_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `production_inputs`
--

INSERT INTO `production_inputs` (`id`, `production_id`, `product_id`, `quantity`, `unit_cost`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 500, 1000.00, 500000.00, '2026-02-05 14:38:55', '2026-02-05 14:38:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `production_orders`
--

CREATE TABLE `production_orders` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `production_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'completada',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `production_orders`
--

INSERT INTO `production_orders` (`id`, `company_id`, `production_date`, `status`, `total_cost`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-02-05', 'completada', 1020000.00, 'Pedido Hospital', '2026-02-05 14:38:55', '2026-02-05 14:38:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `production_outputs`
--

CREATE TABLE `production_outputs` (
  `id` int NOT NULL,
  `production_id` int NOT NULL,
  `produced_product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `production_outputs`
--

INSERT INTO `production_outputs` (`id`, `production_id`, `produced_product_id`, `quantity`, `unit_cost`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 500, 2040.00, 1020000.00, '2026-02-05 14:38:55', '2026-02-05 14:38:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `competitor_company_id` int DEFAULT NULL,
  `family_id` int DEFAULT NULL,
  `subfamily_id` int DEFAULT NULL,
  `competition_code` varchar(30) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `supplier_code` varchar(30) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `supplier_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `competition_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `photo_1` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `photo_2` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_min` int NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `company_id`, `supplier_id`, `competitor_company_id`, `family_id`, `subfamily_id`, `competition_code`, `supplier_code`, `supplier_price`, `competition_price`, `name`, `sku`, `description`, `photo_1`, `photo_2`, `price`, `cost`, `stock`, `stock_min`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 1, 'NA-BID-BID-0001', 'DL-BID-BID-0001', 0.00, 0.00, 'Bidon 20lts', '1234hjfk', '', 'storage/uploads/products/product-photo-1-56f69262f8396933.jpg', NULL, 1500.00, 1000.00, 1, 100, 'activo', '2026-02-05 14:19:05', '2026-04-22 15:31:46', NULL),
(2, 1, NULL, 2, 2, 2, 'SE-SHR-MAN-0001', NULL, 1.53, 0.00, 'SHRINKMARK-1000-2-9 c/u manguito 2\" largo /embalaje 250 unidades', '858589-000', 'c/u manguito 2\" largo /embalaje 250 unidades', NULL, NULL, 1.53, 1.53, 0, 0, 'activo', '2026-04-22 16:07:58', '2026-04-22 16:07:58', NULL),
(3, 1, NULL, 2, 3, 3, 'SE-FAM-MAN-0001', NULL, 0.57, 0.00, 'SHRINKMARK-10-2-9 c/u manguito 2\" largo /embalaje 250 unidades', '415673-000', 'c/u manguito 2\" largo /embalaje 250 unidades', NULL, NULL, 0.57, 0.57, 0, 0, 'activo', '2026-04-22 16:07:58', '2026-04-22 16:07:58', NULL),
(4, 2, 4, 3, 4, 4, 'SE-SHR-POR-0001', 'TYCO-SHR-POR-0001', 0.11, 0.00, '11310000 c/u portador 95 mm largo /500 unidades', 'EC4942-000', 'c/u portador 95 mm largo /500 unidades', NULL, NULL, 0.11, 0.11, 0, 0, 'activo', '2026-04-22 16:12:43', '2026-04-22 16:12:43', '2026-04-22 16:25:05'),
(5, 2, 4, 3, 5, 5, 'SE-FAM-POR-0001', 'TYCO-FAM-POR-0001', 0.09, 0.00, '11320000 c/u portador 65 mm largo /500 unidades', 'EC4943-000', 'c/u portador 65 mm largo /500 unidades', NULL, NULL, 0.09, 0.09, 0, 0, 'activo', '2026-04-22 16:12:43', '2026-04-22 16:12:43', '2026-04-22 16:25:09'),
(6, 2, 5, 3, 4, 4, 'SE-SHR-POR-0002', 'SPR01-SHR-POR-0001', 30994.00, 0.00, 'Mantas Termocontraibles WRS10030.0915F, clase 1000 V', 'SE4923225', '', NULL, NULL, 128890.00, 0.00, 0, 0, 'activo', '2026-05-25 19:12:37', '2026-05-25 19:12:37', '2026-05-26 11:17:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_families`
--

CREATE TABLE `product_families` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `product_families`
--

INSERT INTO `product_families` (`id`, `company_id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bidon plastico', 'BID', '2026-02-05 14:12:39', '2026-02-05 14:12:39'),
(2, 1, 'Familia SHRINKMARK', 'SHR', '2026-04-22 16:07:58', '2026-04-22 16:07:58'),
(3, 1, 'Familia SHRINKMARK', 'FAM', '2026-04-22 16:07:58', '2026-04-22 16:07:58'),
(4, 2, 'Familia SHRINKMARK_CARRIER', 'SHR', '2026-04-22 16:12:43', '2026-04-22 16:12:43'),
(5, 2, 'Familia SHRINKMARK_CARRIER', 'FAM', '2026-04-22 16:12:43', '2026-04-22 16:12:43'),
(6, 2, 'Mantas Termocontraibles WRS10030.0915F, clase 1000 V. 0.750cm.', 'MAN', '2026-05-25 19:13:14', '2026-05-25 19:13:14'),
(7, 2, 'Mantas Termocontraibles WRS10030.0915F, clase 1000 V. 0.915cm.', 'MAN', '2026-05-25 19:13:30', '2026-05-25 19:13:30'),
(8, 2, 'Mantas Termocontraibles WRS10030.0915F, clase 1000 V. 1Mt.', 'MAN', '2026-05-25 19:13:55', '2026-05-25 19:13:55'),
(9, 2, 'Mantas Termocontraibles WRS10030.0915F, clase 1000 V. 1.2Mts.', 'MAN', '2026-05-25 19:14:09', '2026-05-25 19:14:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_subfamilies`
--

CREATE TABLE `product_subfamilies` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `family_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `product_subfamilies`
--

INSERT INTO `product_subfamilies` (`id`, `company_id`, `family_id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Bidon 20lts', 'BID', '2026-02-05 14:13:05', '2026-02-05 14:13:05'),
(2, 1, 2, 'Subfamilia MANGUITOS_TERMOCONTRAIBLES', 'MAN', '2026-04-22 16:07:58', '2026-04-22 16:07:58'),
(3, 1, 3, 'Subfamilia MANGUITOS_TERMOCONTRAIBLES', 'MAN', '2026-04-22 16:07:58', '2026-04-22 16:07:58'),
(4, 2, 4, 'Subfamilia PORTADOR_PARA_MANGUITOS_SHRINKMARK', 'POR', '2026-04-22 16:12:43', '2026-04-22 16:12:43'),
(5, 2, 5, 'Subfamilia PORTADOR_PARA_MANGUITOS_SHRINKMARK', 'POR', '2026-04-22 16:12:43', '2026-04-22 16:12:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `status` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `value` decimal(12,2) DEFAULT NULL,
  `mandante_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mandante_rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mandante_phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mandante_email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project_tasks`
--

CREATE TABLE `project_tasks` (
  `id` int NOT NULL,
  `project_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `progress_percent` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchases`
--

CREATE TABLE `purchases` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `reference` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sii_document_type` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_document_number` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_giro` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_commune` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_tax_rate` decimal(5,2) NOT NULL DEFAULT '19.00',
  `sii_exempt_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_invoice_records`
--

CREATE TABLE `purchase_invoice_records` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `document_type` enum('factura','boleta','servicio') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'factura',
  `invoice_number` varchar(120) COLLATE utf8mb3_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `supplier_name` varchar(180) COLLATE utf8mb3_unicode_ci NOT NULL,
  `supplier_tax_id` varchar(30) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'CLP',
  `net_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_invoice_records`
--

INSERT INTO `purchase_invoice_records` (`id`, `company_id`, `document_type`, `invoice_number`, `invoice_date`, `due_date`, `supplier_name`, `supplier_tax_id`, `currency`, `net_amount`, `tax_amount`, `total_amount`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'factura', '1001', '2026-03-04', NULL, 'Provedor prueba', '99999999-9', 'CLP', 800.00, 152.00, 952.00, '', 1, '2026-03-04 01:42:50', '2026-03-04 02:20:21', NULL),
(2, 2, 'factura', '1003', '2026-03-04', NULL, 'Provedor prueba dos', '99999999-9', 'CLP', 110000.00, 20900.00, 130900.00, '', 1, '2026-03-04 02:02:32', '2026-03-04 02:20:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_invoice_record_items`
--

CREATE TABLE `purchase_invoice_record_items` (
  `id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `item_type` enum('producto','servicio') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'producto',
  `description` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `observation` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_invoice_record_items`
--

INSERT INTO `purchase_invoice_record_items` (`id`, `invoice_id`, `item_type`, `description`, `quantity`, `unit_price`, `subtotal`, `observation`, `created_at`, `updated_at`) VALUES
(6, 1, 'producto', 'Pan Marraqueta', 1.00, 800.00, 800.00, 'Para desayuno', '2026-03-04 02:20:21', '2026-03-04 02:20:21'),
(7, 2, 'producto', 'Jamon Sandwish', 20.00, 5000.00, 100000.00, '', '2026-03-04 02:20:41', '2026-03-04 02:20:41'),
(8, 2, 'servicio', 'Pan Marraqueta', 5.00, 2000.00, 10000.00, '', '2026-03-04 02:20:41', '2026-03-04 02:20:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int NOT NULL,
  `purchase_id` int NOT NULL,
  `product_id` int NOT NULL,
  `petty_cash_product_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `reference` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `order_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `company_id`, `supplier_id`, `reference`, `order_date`, `status`, `subtotal`, `total`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '', '2026-03-02', 'pendiente', 1000.00, 1000.00, '', '2026-03-02 14:15:06', '2026-03-02 14:15:06', NULL),
(2, 1, 1, '123', '2026-04-22', 'pendiente', 1000.00, 1000.00, 'Referencia cotización: 1234\n\nCondiciones de la orden:\nPago a 30 días contra factura.\r\nEntrega sujeta a confirmación de stock.\r\nValidez de precios: 7 días corridos.', '2026-04-22 15:16:55', '2026-04-22 15:16:55', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int NOT NULL,
  `purchase_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `product_id`, `quantity`, `unit_cost`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1000.00, 1000.00, '2026-03-02 14:15:06', '2026-03-02 14:15:06'),
(2, 2, 1, 1, 1000.00, 1000.00, '2026-04-22 15:16:55', '2026-04-22 15:16:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quotes`
--

CREATE TABLE `quotes` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `service_id` int DEFAULT NULL,
  `system_service_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `numero` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_emision` date NOT NULL,
  `estado` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'creada',
  `next_action_date` date DEFAULT NULL,
  `next_action_note` text COLLATE utf8mb3_unicode_ci,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `closed_at` datetime DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `discount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'amount',
  `impuestos` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `sii_document_type` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_document_number` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_giro` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_commune` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_tax_rate` decimal(5,2) NOT NULL DEFAULT '19.00',
  `sii_exempt_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notas` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `quotes`
--

INSERT INTO `quotes` (`id`, `company_id`, `client_id`, `service_id`, `system_service_id`, `project_id`, `numero`, `fecha_emision`, `estado`, `next_action_date`, `next_action_note`, `is_closed`, `closed_at`, `subtotal`, `discount_total`, `discount_total_type`, `impuestos`, `total`, `sii_document_type`, `sii_document_number`, `sii_receiver_rut`, `sii_receiver_name`, `sii_receiver_giro`, `sii_receiver_address`, `sii_receiver_commune`, `sii_tax_rate`, `sii_exempt_amount`, `notas`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL, NULL, 'COT-000001', '2026-03-02', 'enviada', '2026-04-26', 'llamar cliente', 1, '2026-04-22 15:57:41', 1500.00, 0.00, 'amount', 285.00, 1785.00, 'factura_electronica', '', '15626773-2', 'Prueba', 'Venta de botellas', 'Parma', 'Santiago de Chile (Santiago, Chile)', 19.00, 0.00, '', '2026-03-02 21:48:11', '2026-04-22 15:57:41'),
(3, 2, 3, NULL, NULL, NULL, 'COT-000001', '2026-04-08', 'creada', NULL, NULL, 0, NULL, 12670517.00, 0.00, 'amount', 2407398.23, 15077915.23, 'factura_electronica', '', '76042517-6', 'INVERSIONES EL BELLOTO TRES LIMITADA', 'SOCIEDAD INVERSIONES-ARRIENDO INMUEBLES, MAQUINARIA MINERA', 'CERRO EL PLOMO 5680. OF 604 PS 6. LAS CONDES', 'LAS CONDES. REGIÓN METROPOLITANA', 19.00, 0.00, '-. Se establece al inicio pago inicial del 30% del valor señalado.\r\n-. Al final del trabajo se entregaran planos asvil con certificacion SEC.\r\n-. Tiempo de ejecución del trabajo ocho dias contados desde la respectiva orden de compra.', '2026-04-08 00:12:27', '2026-04-08 00:18:23'),
(4, 2, 3, NULL, NULL, NULL, 'COT-000004', '2026-04-14', 'aprobada', NULL, NULL, 0, NULL, 11475000.00, 0.00, 'amount', 2180250.00, 13655250.00, 'factura_electronica', '', '76042517-6', 'INVERSIONES EL BELLOTO TRES LIMITADA', 'SOCIEDAD INVERSIONES-ARRIENDO INMUEBLES, MAQUINARIA MINERA', 'CERRO EL PLOMO 5680. OF 604 PS 6. LAS CONDES', 'LAS CONDES. REGIÓN METROPOLITANA', 19.00, 0.00, 'Se solicita 30% de adelanto al inicio del proyecto.\r\nCliente proporcionara materiales e insumos para realización de proyecto.\r\nTiempo de Trabajo, once dias desde aprovada la orden de trabajo y realizado el adelanto solicitado.', '2026-04-14 11:08:14', '2026-04-22 15:03:23'),
(6, 2, 3, NULL, NULL, NULL, 'COT-000005', '2026-04-30', 'creada', NULL, NULL, 0, NULL, 3894500.00, 0.00, 'amount', 739955.00, 4634455.00, 'factura_electronica', '', '76042517-6', 'INVERSIONES EL BELLOTO TRES LIMITADA', 'SOCIEDAD INVERSIONES-ARRIENDO INMUEBLES, MAQUINARIA MINERA', 'CERRO EL PLOMO 5680. OF 604 PS 6. LAS CONDES', 'LAS CONDES. REGIÓN METROPOLITANA', 19.00, 0.00, 'Se solicita 30% de adelanto al início del proyecto. \r\nCliente proporcionara materiales e insumos para realización de proyecto. \r\nTiempo de Trabajo, once dias desde aprobada la orden de trabajo y realizado el adelanto solicitado.', '2026-04-30 00:05:52', '2026-04-30 00:15:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int NOT NULL,
  `quote_id` int NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'amount',
  `total` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `quote_items`
--

INSERT INTO `quote_items` (`id`, `quote_id`, `descripcion`, `cantidad`, `precio_unitario`, `descuento`, `discount_type`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bidon 20lts', 1, 1500.00, 0.00, 'amount', 1500.00, '2026-03-02 21:48:11', '2026-03-02 21:48:11'),
(5, 3, 'Instalación alumbrado interior-exteriro galpon N° 1', 1, 12670517.00, 0.00, 'amount', 12670517.00, '2026-04-08 00:18:23', '2026-04-08 00:18:23'),
(8, 4, 'Instalacion 135 metros de bandeja portaconductores e instalación de soportes ', 135, 85000.00, 0.00, 'amount', 11475000.00, '2026-04-22 15:03:23', '2026-04-22 15:03:23'),
(15, 6, 'Trabajos no comprendidos cotización galpones 01 y 02. ', 1, 3894500.00, 0.00, 'amount', 3894500.00, '2026-04-30 00:15:55', '2026-04-30 00:15:55'),
(16, 6, 'Alimentación eléctrica 02 oficinas ', 1, 0.00, 0.00, 'amount', 0.00, '2026-04-30 00:15:55', '2026-04-30 00:15:55'),
(17, 6, 'Alimentación de generador a galpones 01 y 02.  ', 1, 0.00, 0.00, 'amount', 0.00, '2026-04-30 00:15:55', '2026-04-30 00:15:55'),
(18, 6, 'Insumos eléctricos ', 1, 0.00, 0.00, 'amount', 0.00, '2026-04-30 00:15:55', '2026-04-30 00:15:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin.', '2026-02-04 13:20:55', '2026-05-26 11:59:47'),
(2, 'Vendedor', '2026-02-05 14:29:48', '2026-02-05 14:29:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int NOT NULL,
  `role_id` int NOT NULL,
  `permission_key` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_key`, `created_at`) VALUES
(8, 2, 'dashboard_view', '2026-02-05 15:01:19'),
(9, 2, 'clients_view', '2026-02-05 15:01:19'),
(10, 2, 'clients_edit', '2026-02-05 15:01:19'),
(11, 2, 'purchases_view', '2026-02-05 15:01:19'),
(12, 2, 'purchases_edit', '2026-02-05 15:01:19'),
(13, 2, 'sales_view', '2026-02-05 15:01:19'),
(14, 2, 'sales_edit', '2026-02-05 15:01:19'),
(15, 2, 'petty_cash_view', '2026-03-03 23:33:46'),
(16, 2, 'petty_cash_edit', '2026-03-03 23:33:46'),
(1001, 1, 'dashboard_view', '2026-05-27 00:00:00'),
(1002, 1, 'crm_view', '2026-05-27 00:00:00'),
(1003, 1, 'clients_view', '2026-05-27 00:00:00'),
(1004, 1, 'clients_edit', '2026-05-27 00:00:00'),
(1005, 1, 'tickets_view', '2026-05-27 00:00:00'),
(1006, 1, 'tickets_edit', '2026-05-27 00:00:00'),
(1007, 1, 'projects_view', '2026-05-27 00:00:00'),
(1008, 1, 'projects_edit', '2026-05-27 00:00:00'),
(1009, 1, 'documents_view', '2026-05-27 00:00:00'),
(1010, 1, 'documents_edit', '2026-05-27 00:00:00'),
(1011, 1, 'products_view', '2026-05-27 00:00:00'),
(1012, 1, 'products_edit', '2026-05-27 00:00:00'),
(1013, 1, 'produced_products_view', '2026-05-27 00:00:00'),
(1014, 1, 'produced_products_edit', '2026-05-27 00:00:00'),
(1015, 1, 'suppliers_view', '2026-05-27 00:00:00'),
(1016, 1, 'suppliers_edit', '2026-05-27 00:00:00'),
(1017, 1, 'purchases_view', '2026-05-27 00:00:00'),
(1018, 1, 'purchases_edit', '2026-05-27 00:00:00'),
(1019, 1, 'purchase_orders_view', '2026-05-27 00:00:00'),
(1020, 1, 'purchase_orders_edit', '2026-05-27 00:00:00'),
(1021, 1, 'production_view', '2026-05-27 00:00:00'),
(1022, 1, 'production_edit', '2026-05-27 00:00:00'),
(1023, 1, 'sales_view', '2026-05-27 00:00:00'),
(1024, 1, 'sales_edit', '2026-05-27 00:00:00'),
(1025, 1, 'sales_dispatches_view', '2026-05-27 00:00:00'),
(1026, 1, 'sales_dispatches_edit', '2026-05-27 00:00:00'),
(1027, 1, 'product_families_view', '2026-05-27 00:00:00'),
(1028, 1, 'product_families_edit', '2026-05-27 00:00:00'),
(1029, 1, 'product_subfamilies_view', '2026-05-27 00:00:00'),
(1030, 1, 'product_subfamilies_edit', '2026-05-27 00:00:00'),
(1031, 1, 'competitor_companies_view', '2026-05-27 00:00:00'),
(1032, 1, 'competitor_companies_edit', '2026-05-27 00:00:00'),
(1033, 1, 'services_view', '2026-05-27 00:00:00'),
(1034, 1, 'services_edit', '2026-05-27 00:00:00'),
(1035, 1, 'system_services_view', '2026-05-27 00:00:00'),
(1036, 1, 'system_services_edit', '2026-05-27 00:00:00'),
(1037, 1, 'service_types_view', '2026-05-27 00:00:00'),
(1038, 1, 'service_types_edit', '2026-05-27 00:00:00'),
(1039, 1, 'chile_regions_view', '2026-05-27 00:00:00'),
(1040, 1, 'chile_regions_edit', '2026-05-27 00:00:00'),
(1041, 1, 'quotes_view', '2026-05-27 00:00:00'),
(1042, 1, 'quotes_edit', '2026-05-27 00:00:00'),
(1043, 1, 'invoices_view', '2026-05-27 00:00:00'),
(1044, 1, 'invoices_edit', '2026-05-27 00:00:00'),
(1045, 1, 'payments_view', '2026-05-27 00:00:00'),
(1046, 1, 'hr_employees_view', '2026-05-27 00:00:00'),
(1047, 1, 'hr_employees_edit', '2026-05-27 00:00:00'),
(1048, 1, 'hr_contracts_view', '2026-05-27 00:00:00'),
(1049, 1, 'hr_contracts_edit', '2026-05-27 00:00:00'),
(1050, 1, 'hr_attendance_view', '2026-05-27 00:00:00'),
(1051, 1, 'hr_attendance_edit', '2026-05-27 00:00:00'),
(1052, 1, 'hr_payrolls_view', '2026-05-27 00:00:00'),
(1053, 1, 'hr_payrolls_edit', '2026-05-27 00:00:00'),
(1054, 1, 'hr_maintainers_view', '2026-05-27 00:00:00'),
(1055, 1, 'hr_maintainers_edit', '2026-05-27 00:00:00'),
(1056, 1, 'email_templates_view', '2026-05-27 00:00:00'),
(1057, 1, 'email_templates_edit', '2026-05-27 00:00:00'),
(1058, 1, 'email_queue_view', '2026-05-27 00:00:00'),
(1059, 1, 'email_queue_edit', '2026-05-27 00:00:00'),
(1060, 1, 'settings_view', '2026-05-27 00:00:00'),
(1061, 1, 'settings_edit', '2026-05-27 00:00:00'),
(1062, 1, 'email_config_view', '2026-05-27 00:00:00'),
(1063, 1, 'email_config_edit', '2026-05-27 00:00:00'),
(1064, 1, 'online_payments_config_view', '2026-05-27 00:00:00'),
(1065, 1, 'online_payments_config_edit', '2026-05-27 00:00:00'),
(1066, 1, 'accounting_view', '2026-05-27 00:00:00'),
(1067, 1, 'accounting_edit', '2026-05-27 00:00:00'),
(1068, 1, 'taxes_view', '2026-05-27 00:00:00'),
(1069, 1, 'taxes_edit', '2026-05-27 00:00:00'),
(1070, 1, 'honorarios_view', '2026-05-27 00:00:00'),
(1071, 1, 'honorarios_edit', '2026-05-27 00:00:00'),
(1072, 1, 'fixed_assets_view', '2026-05-27 00:00:00'),
(1073, 1, 'fixed_assets_edit', '2026-05-27 00:00:00'),
(1074, 1, 'treasury_view', '2026-05-27 00:00:00'),
(1075, 1, 'treasury_edit', '2026-05-27 00:00:00'),
(1076, 1, 'petty_cash_view', '2026-05-27 00:00:00'),
(1077, 1, 'petty_cash_edit', '2026-05-27 00:00:00'),
(1078, 1, 'invoice_register_view', '2026-05-27 00:00:00'),
(1079, 1, 'invoice_register_edit', '2026-05-27 00:00:00'),
(1080, 1, 'inventory_view', '2026-05-27 00:00:00'),
(1081, 1, 'inventory_edit', '2026-05-27 00:00:00'),
(1082, 1, 'companies_view', '2026-05-27 00:00:00'),
(1083, 1, 'companies_edit', '2026-05-27 00:00:00'),
(1084, 1, 'users_view', '2026-05-27 00:00:00'),
(1085, 1, 'users_edit', '2026-05-27 00:00:00'),
(1086, 1, 'roles_view', '2026-05-27 00:00:00'),
(1087, 1, 'roles_edit', '2026-05-27 00:00:00'),
(1088, 1, 'users_companies_view', '2026-05-27 00:00:00'),
(1089, 1, 'users_companies_edit', '2026-05-27 00:00:00'),
(1090, 1, 'users_permissions_view', '2026-05-27 00:00:00'),
(1091, 1, 'users_permissions_edit', '2026-05-27 00:00:00'),
(1092, 1, 'calendar_view', '2026-05-27 00:00:00'),
(1093, 1, 'calendar_edit', '2026-05-27 00:00:00'),
(1094, 1, 'company_switch_view', '2026-05-27 00:00:00'),
(1095, 1, 'company_switch_edit', '2026-05-27 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int DEFAULT NULL,
  `pos_session_id` int DEFAULT NULL,
  `channel` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'venta',
  `numero` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sale_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pagado',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'amount',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sii_document_type` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_document_number` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_rut` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_giro` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_receiver_commune` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sii_tax_rate` decimal(5,2) NOT NULL DEFAULT '19.00',
  `sii_exempt_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_dispatches`
--

CREATE TABLE `sales_dispatches` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `truck_code` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `seller_name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `seller_user_id` int DEFAULT NULL,
  `dispatch_date` date NOT NULL,
  `pos_session_id` int DEFAULT NULL,
  `status` enum('abierto','cerrado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'abierto',
  `notes` text COLLATE utf8mb4_general_ci,
  `cash_delivered` decimal(14,2) NOT NULL DEFAULT '0.00',
  `closed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sales_dispatches`
--

INSERT INTO `sales_dispatches` (`id`, `company_id`, `truck_code`, `seller_name`, `seller_user_id`, `dispatch_date`, `pos_session_id`, `status`, `notes`, `cash_delivered`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'BDHS66', 'Prueba', NULL, '2026-02-05', 1, 'abierto', '', 0.00, NULL, '2026-02-05 21:02:14', '2026-02-05 21:02:14'),
(2, 1, 'BDHS66', 'Prueba', NULL, '2026-02-05', 1, 'abierto', '', 0.00, NULL, '2026-02-05 21:02:40', '2026-02-05 21:02:40'),
(3, 1, 'BDHS66', 'Prueba', NULL, '2026-02-05', 1, 'abierto', '', 0.00, NULL, '2026-02-05 21:13:12', '2026-02-05 21:13:12'),
(4, 1, 'BDHS66', 'Prueba', NULL, '2026-02-05', 1, 'abierto', '', 0.00, NULL, '2026-02-05 21:24:49', '2026-02-05 21:24:49'),
(5, 1, 'BDHS66', 'Prueba', NULL, '2026-02-05', 1, 'abierto', '', 0.00, NULL, '2026-02-05 21:33:59', '2026-02-05 21:33:59'),
(6, 1, 'BDHS66', 'Prueba', NULL, '2026-02-05', 1, 'abierto', '', 0.00, NULL, '2026-02-05 21:40:00', '2026-02-05 21:40:00'),
(7, 1, 'BDHS66', 'Prueba', NULL, '2026-02-05', NULL, 'abierto', '', 0.00, NULL, '2026-02-05 21:45:57', '2026-02-05 21:45:57'),
(8, 1, 'BDHS66', 'Yaritza Barreda', 2, '2026-02-05', 1, 'cerrado', '', 90000.00, '2026-02-05 22:59:19', '2026-02-05 22:06:12', '2026-02-05 22:59:19'),
(9, 1, 'BDHS66', 'Yaritza Barreda', 2, '2026-02-06', NULL, 'cerrado', '', 100000.00, '2026-02-06 00:08:05', '2026-02-06 00:06:58', '2026-02-06 00:08:05'),
(10, 1, 'sxm152s', 'Yaritza Barreda', 2, '2026-02-23', NULL, 'cerrado', '', 0.00, '2026-02-23 12:56:07', '2026-02-23 12:54:13', '2026-02-23 12:56:07'),
(11, 1, 'sxm152s', 'Yaritza Barreda', 2, '2026-02-23', NULL, 'cerrado', '', 0.00, '2026-02-23 12:59:35', '2026-02-23 12:58:24', '2026-02-23 12:59:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_dispatch_items`
--

CREATE TABLE `sales_dispatch_items` (
  `id` int NOT NULL,
  `dispatch_id` int NOT NULL,
  `produced_product_id` int NOT NULL,
  `quantity_dispatched` int NOT NULL DEFAULT '0',
  `empty_returned_total` int NOT NULL DEFAULT '0',
  `empty_muy_bueno` int NOT NULL DEFAULT '0',
  `empty_bueno` int NOT NULL DEFAULT '0',
  `empty_aceptable` int NOT NULL DEFAULT '0',
  `empty_malo` int NOT NULL DEFAULT '0',
  `empty_merma` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sales_dispatch_items`
--

INSERT INTO `sales_dispatch_items` (`id`, `dispatch_id`, `produced_product_id`, `quantity_dispatched`, `empty_returned_total`, `empty_muy_bueno`, `empty_bueno`, `empty_aceptable`, `empty_malo`, `empty_merma`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1000, 0, 0, 0, 0, 0, 0, '2026-02-05 21:02:14', '2026-02-05 21:02:14'),
(2, 2, 1, 11, 0, 0, 0, 0, 0, 0, '2026-02-05 21:02:40', '2026-02-05 21:02:40'),
(3, 3, 1, 100, 0, 0, 0, 0, 0, 0, '2026-02-05 21:13:12', '2026-02-05 21:13:12'),
(4, 4, 1, 12, 0, 0, 0, 0, 0, 0, '2026-02-05 21:24:49', '2026-02-05 21:24:49'),
(5, 5, 1, 10, 0, 0, 0, 0, 0, 0, '2026-02-05 21:33:59', '2026-02-05 21:33:59'),
(6, 6, 1, 10, 0, 0, 0, 0, 0, 0, '2026-02-05 21:40:00', '2026-02-05 21:40:00'),
(7, 7, 1, 1, 0, 0, 0, 0, 0, 0, '2026-02-05 21:45:57', '2026-02-05 21:45:57'),
(8, 8, 1, 100, 99, 0, 0, 98, 0, 1, '2026-02-05 22:06:12', '2026-02-05 22:59:19'),
(9, 9, 1, 100, 98, 96, 0, 1, 0, 1, '2026-02-06 00:06:58', '2026-02-06 00:08:05'),
(10, 10, 1, 115, 50, 10, 10, 10, 10, 10, '2026-02-23 12:54:13', '2026-02-23 12:56:07'),
(11, 11, 1, 200, 100, 25, 25, 25, 25, 0, '2026-02-23 12:58:24', '2026-02-23 12:59:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `brief_id` int DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pendiente',
  `total` decimal(12,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'CLP',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` int NOT NULL,
  `sales_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int NOT NULL,
  `sale_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `produced_product_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'amount',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_payments`
--

CREATE TABLE `sale_payments` (
  `id` int NOT NULL,
  `sale_id` int NOT NULL,
  `method` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `service_type` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'CLP',
  `billing_cycle` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'mensual',
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `delete_date` date DEFAULT NULL,
  `notice_days_1` int NOT NULL DEFAULT '15',
  `notice_days_2` int NOT NULL DEFAULT '5',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'activo',
  `auto_invoice` tinyint(1) NOT NULL DEFAULT '1',
  `auto_email` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_renewals`
--

CREATE TABLE `service_renewals` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `service_id` int DEFAULT NULL,
  `renewal_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pendiente',
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'CLP',
  `reminder_days` int NOT NULL DEFAULT '15',
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_types`
--

CREATE TABLE `service_types` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `company_id` int DEFAULT NULL,
  `key` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`id`, `company_id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 'company', '{\"name\":\"Acquaperla SPA\",\"rut\":\"\",\"bank\":\"\",\"account_type\":\"\",\"account_number\":\"\",\"email\":\"\",\"phone\":\"\",\"address\":\"\",\"giro\":\"\",\"commune\":\"\",\"signature\":\"\",\"logo_color\":\"storage\\/uploads\\/logos\\/logo-color-5eb8ac6515f537bb.png\",\"logo_black\":null,\"login_logo\":null}', '2026-02-04 16:14:42', '2026-02-04 16:14:42'),
(2, 2, 'company', '{\"rut\":\"77.400.109-3\",\"giro\":\"Comercializadora y Venta de Insumos Electricos\",\"name\":\"Seim Energia\",\"email\":\"seim@seimenergia.com\",\"phone\":\"+56990961266\",\"address\":\"El Roble N\\u00b07479, Antofagasta\",\"commune\":\"ANTOFAGASTA\",\"logo_black\":\"\",\"logo_color\":\"storage\\/uploads\\/logos\\/logo-color-d53b7dc015073ee7.png\"}', '2026-04-22 12:37:05', '2026-04-22 14:54:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `contact_name` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tax_id` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `giro` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `commune` varchar(120) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_id`, `name`, `code`, `contact_name`, `tax_id`, `email`, `phone`, `address`, `giro`, `commune`, `website`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Dali', 'DL', 'Dali', '763015068', 'dali@importadoradali.cl', '+56944457602', '', '', '', '', '', '2026-02-05 14:16:35', '2026-02-05 14:16:35', NULL),
(2, 2, 'Provedor prueba', 'PRO0001', 'Erwin Isla S', '99999999-9', 'erwin.2785@gmail.com', '944627287', 'Parma', 'Venta de botellas', 'Santiago de Chile (Santiago, Chile)', '', '', '2026-03-04 00:39:17', '2026-03-04 00:39:39', '2026-05-25 17:21:06'),
(3, 2, 'Provedor prueba dos', 'PRO0004', 'Erwin Isla S', '99999999-9', 'erwin.2785@gmail.com', '944627287', 'Parma', 'Giro prueba', 'Santiago de Chile (Santiago, Chile)', '', '', '2026-03-04 01:54:43', '2026-03-04 01:54:43', '2026-05-25 17:21:00'),
(4, 2, 'Proveedor de prueba', 'TYCO', '', '', '', '', '', '', '', '', '', '2026-04-22 13:54:33', '2026-04-22 13:54:33', '2026-05-25 17:20:57'),
(5, 2, 'Sparkline Spa', 'SPR01', 'Renzo Biso Allendes', '76.995.060-5', 'r.biso@rpl-chile.cl', '+56967008564', '13 Norte 853 803-Viña del Mar', 'Comercializacion de productos electricos, ropa y articulos de seguridad', 'Viña del Mar', '', '', '2026-05-25 17:20:01', '2026-05-25 17:20:49', NULL),
(6, 2, 'Xiamen GBS Adhesive Tape Co.,Ltd', 'GBS002', 'Mandy Chen', '', 'mandy@gbstape.com', '+8618259291096', '11th Floor, The Center, 99 Queen\'s Road Central, Central, Hong Kong.', '', 'Xiamen, Hong Kong', 'https://www.gbstape.com/', '', '2026-05-25 17:28:23', '2026-05-25 17:28:23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `subject` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'abierto',
  `priority` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'media',
  `assigned_user_id` int DEFAULT NULL,
  `created_by_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'client',
  `created_by_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `support_ticket_messages`
--

CREATE TABLE `support_ticket_messages` (
  `id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `sender_type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sender_id` int NOT NULL,
  `message` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `system_services`
--

CREATE TABLE `system_services` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `service_type_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `cost` decimal(12,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'CLP',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tax_periods`
--

CREATE TABLE `tax_periods` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `period` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `iva_debito` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva_credito` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remanente` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_retenciones` decimal(12,2) NOT NULL DEFAULT '0.00',
  `impuesto_unico` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pendiente',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tax_withholdings`
--

CREATE TABLE `tax_withholdings` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `period_id` int DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `base_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `role_id` int NOT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `signature` text COLLATE utf8mb3_unicode_ci,
  `signature_image_path` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `password`, `role_id`, `avatar_path`, `signature`, `signature_image_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'E Isla', 'eisla@gocreative.cl', '$2y$12$Aa7Lucu.iaa3mUMBZjxAyO96KI0d6yNaKuOD/Rdru1FsOhn9Kmtga', 1, NULL, NULL, NULL, '2026-02-04 13:20:55', '2026-02-04 17:12:10', NULL),
(2, 1, 'Yaritza Barreda', 'Ventas@seimenergia.com', '$2y$10$xQfZ.xkiGc3bCBLldPoIJeFi5W1w1sHONbbiIOieD3lWJ7JHI1HkK', 1, 'storage/uploads/avatars/user-b49e6973031b3886.png', '', 'storage/uploads/signatures/signature-e04c503f93131c49.png', '2026-02-05 13:51:53', '2026-05-26 11:42:01', NULL),
(3, 1, 'Erik Rojas', 'erik.rojas@seimenergia.com', '$2y$10$zgRA2qJ2iNZZHN5QLvAsEux3FX34VifAhjfKFhGekZT2MPyQB75IG', 1, NULL, 'Erik Rojas V.', 'storage/uploads/signatures/signature-721476bca907cf5e.png', '2026-02-05 14:28:44', '2026-02-05 15:02:27', NULL),
(12, 1, 'Ricardo Zuñiga P.', 'ricardo.zuniga@seimenergia.com', '$2y$10$VhkdTmY0BxHxIA1MGvpwHeEQRRvtDl/1MpuBS2ONxf8rm57L3wMai', 1, NULL, 'Ricardo Zuñiga P.', 'storage/uploads/signatures/signature-4867b119d2f97c1b.png', '2026-05-26 11:54:44', '2026-05-26 11:54:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_companies`
--

CREATE TABLE `user_companies` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `company_id` int NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `user_companies`
--

INSERT INTO `user_companies` (`id`, `user_id`, `company_id`, `created_at`) VALUES
(2, 1, 1, '2026-02-04 17:12:10'),
(3, 1, 2, '2026-02-04 17:12:10'),
(16, 3, 1, '2026-02-05 15:02:27'),
(17, 3, 2, '2026-02-05 15:02:27'),
(22, 2, 1, '2026-05-26 11:42:01'),
(23, 2, 2, '2026-05-26 11:42:01'),
(24, 12, 1, '2026-05-26 11:54:44'),
(25, 12, 2, '2026-05-26 11:54:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accounting_accounts`
--
ALTER TABLE `accounting_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indices de la tabla `accounting_journals`
--
ALTER TABLE `accounting_journals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `accounting_journal_lines`
--
ALTER TABLE `accounting_journal_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_id` (`journal_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indices de la tabla `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `bank_transactions`
--
ALTER TABLE `bank_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `bank_account_id` (`bank_account_id`);

--
-- Indices de la tabla `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_calendar_events_company` (`company_id`),
  ADD KEY `idx_calendar_events_start` (`start_at`),
  ADD KEY `fk_calendar_events_user` (`created_by_user_id`);

--
-- Indices de la tabla `calendar_event_attendees`
--
ALTER TABLE `calendar_event_attendees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_calendar_event_attendee_unique` (`event_id`,`user_id`),
  ADD KEY `idx_calendar_event_attendees_event` (`event_id`),
  ADD KEY `idx_calendar_event_attendees_user` (`user_id`);

--
-- Indices de la tabla `calendar_event_documents`
--
ALTER TABLE `calendar_event_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_calendar_event_document_unique` (`event_id`,`document_id`),
  ADD KEY `idx_calendar_event_documents_event` (`event_id`),
  ADD KEY `idx_calendar_event_documents_document` (`document_id`);

--
-- Indices de la tabla `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread_id` (`thread_id`);

--
-- Indices de la tabla `chat_threads`
--
ALTER TABLE `chat_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_clients_portal_token` (`portal_token`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `idx_clients_status` (`status`);

--
-- Indices de la tabla `commercial_briefs`
--
ALTER TABLE `commercial_briefs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `competitor_companies`
--
ALTER TABLE `competitor_companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_competitor_companies_company` (`company_id`);

--
-- Indices de la tabla `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_documents_company` (`company_id`),
  ADD KEY `idx_documents_category` (`category_id`);

--
-- Indices de la tabla `document_categories`
--
ALTER TABLE `document_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_document_categories_company` (`company_id`);

--
-- Indices de la tabla `document_shares`
--
ALTER TABLE `document_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_document_shares_document` (`document_id`),
  ADD KEY `idx_document_shares_user` (`user_id`),
  ADD KEY `fk_document_shares_shared_by` (`shared_by_user_id`);

--
-- Indices de la tabla `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `email_queue`
--
ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `idx_email_queue_status` (`status`);

--
-- Indices de la tabla `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `honorarios_documents`
--
ALTER TABLE `honorarios_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `hr_attendance`
--
ALTER TABLE `hr_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indices de la tabla `hr_contracts`
--
ALTER TABLE `hr_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `contract_type_id` (`contract_type_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indices de la tabla `hr_contract_types`
--
ALTER TABLE `hr_contract_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `hr_departments`
--
ALTER TABLE `hr_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `health_provider_id` (`health_provider_id`),
  ADD KEY `pension_fund_id` (`pension_fund_id`);

--
-- Indices de la tabla `hr_health_providers`
--
ALTER TABLE `hr_health_providers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indices de la tabla `hr_payroll_items`
--
ALTER TABLE `hr_payroll_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `hr_payroll_lines`
--
ALTER TABLE `hr_payroll_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_id` (`payroll_id`),
  ADD KEY `payroll_item_id` (`payroll_item_id`);

--
-- Indices de la tabla `hr_pension_funds`
--
ALTER TABLE `hr_pension_funds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `hr_positions`
--
ALTER TABLE `hr_positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `hr_work_schedules`
--
ALTER TABLE `hr_work_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `produced_product_id` (`produced_product_id`);

--
-- Indices de la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `idx_invoices_estado` (`estado`),
  ADD KEY `idx_invoices_numero` (`numero`);

--
-- Indices de la tabla `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indices de la tabla `petty_cash_products`
--
ALTER TABLE `petty_cash_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_petty_cash_products_company` (`company_id`);

--
-- Indices de la tabla `petty_cash_receipts`
--
ALTER TABLE `petty_cash_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_petty_cash_receipts_user` (`created_by`),
  ADD KEY `idx_petty_cash_receipts_company_date` (`company_id`,`receipt_date`),
  ADD KEY `idx_petty_cash_receipts_supplier` (`supplier_name`);

--
-- Indices de la tabla `petty_cash_receipt_items`
--
ALTER TABLE `petty_cash_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_petty_cash_items_product` (`product_id`),
  ADD KEY `idx_petty_cash_items_receipt` (`receipt_id`);

--
-- Indices de la tabla `pos_sessions`
--
ALTER TABLE `pos_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_pos_sessions_company_user` (`company_id`,`user_id`);

--
-- Indices de la tabla `pos_session_withdrawals`
--
ALTER TABLE `pos_session_withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pos_session_id` (`pos_session_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `produced_products`
--
ALTER TABLE `produced_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `produced_product_materials`
--
ALTER TABLE `produced_product_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produced_product_id` (`produced_product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `production_expenses`
--
ALTER TABLE `production_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_expenses_production` (`production_id`);

--
-- Indices de la tabla `production_inputs`
--
ALTER TABLE `production_inputs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_production_inputs_production` (`production_id`);

--
-- Indices de la tabla `production_orders`
--
ALTER TABLE `production_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_orders_company` (`company_id`);

--
-- Indices de la tabla `production_outputs`
--
ALTER TABLE `production_outputs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produced_product_id` (`produced_product_id`),
  ADD KEY `idx_production_outputs_production` (`production_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `subfamily_id` (`subfamily_id`),
  ADD KEY `idx_products_company` (`company_id`),
  ADD KEY `idx_products_supplier` (`supplier_id`);

--
-- Indices de la tabla `product_families`
--
ALTER TABLE `product_families`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_families_company` (`company_id`);

--
-- Indices de la tabla `product_subfamilies`
--
ALTER TABLE `product_subfamilies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `idx_product_subfamilies_company` (`company_id`);

--
-- Indices de la tabla `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indices de la tabla `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indices de la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `idx_purchases_company` (`company_id`);

--
-- Indices de la tabla `purchase_invoice_records`
--
ALTER TABLE `purchase_invoice_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_purchase_invoice_records_company_date` (`company_id`,`invoice_date`),
  ADD KEY `idx_purchase_invoice_records_supplier` (`supplier_name`);

--
-- Indices de la tabla `purchase_invoice_record_items`
--
ALTER TABLE `purchase_invoice_record_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indices de la tabla `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_purchase_items_petty_cash_product` (`petty_cash_product_id`);

--
-- Indices de la tabla `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `idx_purchase_orders_company` (`company_id`);

--
-- Indices de la tabla `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_purchase_order_items_order` (`purchase_order_id`);

--
-- Indices de la tabla `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `system_service_id` (`system_service_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `idx_quotes_estado` (`estado`),
  ADD KEY `idx_quotes_next_action_date` (`next_action_date`),
  ADD KEY `idx_quotes_is_closed` (`is_closed`);

--
-- Indices de la tabla `quote_items`
--
ALTER TABLE `quote_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quote_id` (`quote_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_role_permission_unique` (`role_id`,`permission_key`),
  ADD KEY `idx_role_permissions_role` (`role_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `pos_session_id` (`pos_session_id`),
  ADD KEY `idx_sales_company` (`company_id`);

--
-- Indices de la tabla `sales_dispatches`
--
ALTER TABLE `sales_dispatches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_dispatches_company_date` (`company_id`,`dispatch_date`),
  ADD KEY `idx_sales_dispatches_session` (`pos_session_id`),
  ADD KEY `idx_sales_dispatches_seller_user` (`seller_user_id`);

--
-- Indices de la tabla `sales_dispatch_items`
--
ALTER TABLE `sales_dispatch_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_dispatch_items_dispatch` (`dispatch_id`),
  ADD KEY `idx_sales_dispatch_items_product` (`produced_product_id`);

--
-- Indices de la tabla `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `brief_id` (`brief_id`);

--
-- Indices de la tabla `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_sales_order_items_order` (`sales_order_id`);

--
-- Indices de la tabla `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `produced_product_id` (`produced_product_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indices de la tabla `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_services_status` (`status`),
  ADD KEY `idx_services_due_date` (`due_date`);

--
-- Indices de la tabla `service_renewals`
--
ALTER TABLE `service_renewals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indices de la tabla `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_types_company` (`company_id`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_settings_key_company` (`company_id`,`key`);

--
-- Indices de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `assigned_user_id` (`assigned_user_id`);

--
-- Indices de la tabla `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indices de la tabla `system_services`
--
ALTER TABLE `system_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_type_id` (`service_type_id`),
  ADD KEY `fk_system_services_company` (`company_id`);

--
-- Indices de la tabla `tax_periods`
--
ALTER TABLE `tax_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indices de la tabla `tax_withholdings`
--
ALTER TABLE `tax_withholdings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indices de la tabla `user_companies`
--
ALTER TABLE `user_companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_user_companies_unique` (`user_id`,`company_id`),
  ADD KEY `company_id` (`company_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accounting_accounts`
--
ALTER TABLE `accounting_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `accounting_journals`
--
ALTER TABLE `accounting_journals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `accounting_journal_lines`
--
ALTER TABLE `accounting_journal_lines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `accounting_periods`
--
ALTER TABLE `accounting_periods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `bank_transactions`
--
ALTER TABLE `bank_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `calendar_events`
--
ALTER TABLE `calendar_events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calendar_event_attendees`
--
ALTER TABLE `calendar_event_attendees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calendar_event_documents`
--
ALTER TABLE `calendar_event_documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_threads`
--
ALTER TABLE `chat_threads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `commercial_briefs`
--
ALTER TABLE `commercial_briefs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `competitor_companies`
--
ALTER TABLE `competitor_companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `document_categories`
--
ALTER TABLE `document_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `document_shares`
--
ALTER TABLE `document_shares`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_queue`
--
ALTER TABLE `email_queue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `honorarios_documents`
--
ALTER TABLE `honorarios_documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_attendance`
--
ALTER TABLE `hr_attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_contracts`
--
ALTER TABLE `hr_contracts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_contract_types`
--
ALTER TABLE `hr_contract_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_departments`
--
ALTER TABLE `hr_departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_employees`
--
ALTER TABLE `hr_employees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_health_providers`
--
ALTER TABLE `hr_health_providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_payroll_items`
--
ALTER TABLE `hr_payroll_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_payroll_lines`
--
ALTER TABLE `hr_payroll_lines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_pension_funds`
--
ALTER TABLE `hr_pension_funds`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_positions`
--
ALTER TABLE `hr_positions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hr_work_schedules`
--
ALTER TABLE `hr_work_schedules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `petty_cash_products`
--
ALTER TABLE `petty_cash_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `petty_cash_receipts`
--
ALTER TABLE `petty_cash_receipts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `petty_cash_receipt_items`
--
ALTER TABLE `petty_cash_receipt_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pos_sessions`
--
ALTER TABLE `pos_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pos_session_withdrawals`
--
ALTER TABLE `pos_session_withdrawals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `produced_products`
--
ALTER TABLE `produced_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `produced_product_materials`
--
ALTER TABLE `produced_product_materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `production_expenses`
--
ALTER TABLE `production_expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `production_inputs`
--
ALTER TABLE `production_inputs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `production_orders`
--
ALTER TABLE `production_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `production_outputs`
--
ALTER TABLE `production_outputs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `product_families`
--
ALTER TABLE `product_families`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `product_subfamilies`
--
ALTER TABLE `product_subfamilies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `project_tasks`
--
ALTER TABLE `project_tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `purchase_invoice_records`
--
ALTER TABLE `purchase_invoice_records`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `purchase_invoice_record_items`
--
ALTER TABLE `purchase_invoice_record_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `quote_items`
--
ALTER TABLE `quote_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sales_dispatches`
--
ALTER TABLE `sales_dispatches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `sales_dispatch_items`
--
ALTER TABLE `sales_dispatch_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sale_payments`
--
ALTER TABLE `sale_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `service_renewals`
--
ALTER TABLE `service_renewals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `service_types`
--
ALTER TABLE `service_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `system_services`
--
ALTER TABLE `system_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tax_periods`
--
ALTER TABLE `tax_periods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tax_withholdings`
--
ALTER TABLE `tax_withholdings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `user_companies`
--
ALTER TABLE `user_companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accounting_accounts`
--
ALTER TABLE `accounting_accounts`
  ADD CONSTRAINT `accounting_accounts_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `accounting_accounts_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `accounting_accounts` (`id`);

--
-- Filtros para la tabla `accounting_journals`
--
ALTER TABLE `accounting_journals`
  ADD CONSTRAINT `accounting_journals_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `accounting_journals_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `accounting_journal_lines`
--
ALTER TABLE `accounting_journal_lines`
  ADD CONSTRAINT `accounting_journal_lines_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `accounting_journals` (`id`),
  ADD CONSTRAINT `accounting_journal_lines_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounting_accounts` (`id`);

--
-- Filtros para la tabla `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD CONSTRAINT `accounting_periods_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `bank_accounts_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `bank_transactions`
--
ALTER TABLE `bank_transactions`
  ADD CONSTRAINT `bank_transactions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `bank_transactions_ibfk_2` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`);

--
-- Filtros para la tabla `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD CONSTRAINT `fk_calendar_events_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_calendar_events_user` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calendar_event_attendees`
--
ALTER TABLE `calendar_event_attendees`
  ADD CONSTRAINT `fk_calendar_event_attendees_event` FOREIGN KEY (`event_id`) REFERENCES `calendar_events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_calendar_event_attendees_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calendar_event_documents`
--
ALTER TABLE `calendar_event_documents`
  ADD CONSTRAINT `fk_calendar_event_documents_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_calendar_event_documents_event` FOREIGN KEY (`event_id`) REFERENCES `calendar_events` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `chat_threads` (`id`);

--
-- Filtros para la tabla `chat_threads`
--
ALTER TABLE `chat_threads`
  ADD CONSTRAINT `chat_threads_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `chat_threads_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Filtros para la tabla `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `commercial_briefs`
--
ALTER TABLE `commercial_briefs`
  ADD CONSTRAINT `commercial_briefs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `commercial_briefs_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Filtros para la tabla `competitor_companies`
--
ALTER TABLE `competitor_companies`
  ADD CONSTRAINT `competitor_companies_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_documents_category` FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_documents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `document_categories`
--
ALTER TABLE `document_categories`
  ADD CONSTRAINT `fk_document_categories_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `document_shares`
--
ALTER TABLE `document_shares`
  ADD CONSTRAINT `fk_document_shares_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_document_shares_shared_by` FOREIGN KEY (`shared_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_document_shares_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `email_queue`
--
ALTER TABLE `email_queue`
  ADD CONSTRAINT `email_queue_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `email_queue_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `email_queue_ibfk_3` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`id`);

--
-- Filtros para la tabla `email_templates`
--
ALTER TABLE `email_templates`
  ADD CONSTRAINT `email_templates_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD CONSTRAINT `fixed_assets_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `honorarios_documents`
--
ALTER TABLE `honorarios_documents`
  ADD CONSTRAINT `honorarios_documents_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `hr_attendance`
--
ALTER TABLE `hr_attendance`
  ADD CONSTRAINT `hr_attendance_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `hr_attendance_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`);

--
-- Filtros para la tabla `hr_contracts`
--
ALTER TABLE `hr_contracts`
  ADD CONSTRAINT `hr_contracts_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `hr_contracts_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`),
  ADD CONSTRAINT `hr_contracts_ibfk_3` FOREIGN KEY (`contract_type_id`) REFERENCES `hr_contract_types` (`id`),
  ADD CONSTRAINT `hr_contracts_ibfk_4` FOREIGN KEY (`department_id`) REFERENCES `hr_departments` (`id`),
  ADD CONSTRAINT `hr_contracts_ibfk_5` FOREIGN KEY (`position_id`) REFERENCES `hr_positions` (`id`),
  ADD CONSTRAINT `hr_contracts_ibfk_6` FOREIGN KEY (`schedule_id`) REFERENCES `hr_work_schedules` (`id`);

--
-- Filtros para la tabla `hr_contract_types`
--
ALTER TABLE `hr_contract_types`
  ADD CONSTRAINT `hr_contract_types_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `hr_departments`
--
ALTER TABLE `hr_departments`
  ADD CONSTRAINT `hr_departments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD CONSTRAINT `hr_employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `hr_employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `hr_departments` (`id`),
  ADD CONSTRAINT `hr_employees_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `hr_positions` (`id`),
  ADD CONSTRAINT `hr_employees_ibfk_4` FOREIGN KEY (`health_provider_id`) REFERENCES `hr_health_providers` (`id`),
  ADD CONSTRAINT `hr_employees_ibfk_5` FOREIGN KEY (`pension_fund_id`) REFERENCES `hr_pension_funds` (`id`);

--
-- Filtros para la tabla `hr_health_providers`
--
ALTER TABLE `hr_health_providers`
  ADD CONSTRAINT `hr_health_providers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD CONSTRAINT `hr_payrolls_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `hr_payrolls_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`);

--
-- Filtros para la tabla `hr_payroll_items`
--
ALTER TABLE `hr_payroll_items`
  ADD CONSTRAINT `hr_payroll_items_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `hr_payroll_lines`
--
ALTER TABLE `hr_payroll_lines`
  ADD CONSTRAINT `hr_payroll_lines_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `hr_payrolls` (`id`),
  ADD CONSTRAINT `hr_payroll_lines_ibfk_2` FOREIGN KEY (`payroll_item_id`) REFERENCES `hr_payroll_items` (`id`);

--
-- Filtros para la tabla `hr_pension_funds`
--
ALTER TABLE `hr_pension_funds`
  ADD CONSTRAINT `hr_pension_funds_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `hr_positions`
--
ALTER TABLE `hr_positions`
  ADD CONSTRAINT `hr_positions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `hr_work_schedules`
--
ALTER TABLE `hr_work_schedules`
  ADD CONSTRAINT `hr_work_schedules_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD CONSTRAINT `inventory_movements_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `inventory_movements_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_movements_ibfk_3` FOREIGN KEY (`produced_product_id`) REFERENCES `produced_products` (`id`);

--
-- Filtros para la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `invoices_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Filtros para la tabla `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Filtros para la tabla `petty_cash_products`
--
ALTER TABLE `petty_cash_products`
  ADD CONSTRAINT `fk_petty_cash_products_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `petty_cash_receipts`
--
ALTER TABLE `petty_cash_receipts`
  ADD CONSTRAINT `fk_petty_cash_receipts_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `fk_petty_cash_receipts_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `petty_cash_receipt_items`
--
ALTER TABLE `petty_cash_receipt_items`
  ADD CONSTRAINT `fk_petty_cash_items_product` FOREIGN KEY (`product_id`) REFERENCES `petty_cash_products` (`id`),
  ADD CONSTRAINT `fk_petty_cash_items_receipt` FOREIGN KEY (`receipt_id`) REFERENCES `petty_cash_receipts` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pos_sessions`
--
ALTER TABLE `pos_sessions`
  ADD CONSTRAINT `pos_sessions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `pos_sessions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pos_session_withdrawals`
--
ALTER TABLE `pos_session_withdrawals`
  ADD CONSTRAINT `pos_session_withdrawals_ibfk_1` FOREIGN KEY (`pos_session_id`) REFERENCES `pos_sessions` (`id`),
  ADD CONSTRAINT `pos_session_withdrawals_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `pos_session_withdrawals_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `produced_products`
--
ALTER TABLE `produced_products`
  ADD CONSTRAINT `produced_products_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `produced_product_materials`
--
ALTER TABLE `produced_product_materials`
  ADD CONSTRAINT `produced_product_materials_ibfk_1` FOREIGN KEY (`produced_product_id`) REFERENCES `produced_products` (`id`),
  ADD CONSTRAINT `produced_product_materials_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `production_expenses`
--
ALTER TABLE `production_expenses`
  ADD CONSTRAINT `production_expenses_ibfk_1` FOREIGN KEY (`production_id`) REFERENCES `production_orders` (`id`);

--
-- Filtros para la tabla `production_inputs`
--
ALTER TABLE `production_inputs`
  ADD CONSTRAINT `production_inputs_ibfk_1` FOREIGN KEY (`production_id`) REFERENCES `production_orders` (`id`),
  ADD CONSTRAINT `production_inputs_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `production_orders`
--
ALTER TABLE `production_orders`
  ADD CONSTRAINT `production_orders_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `production_outputs`
--
ALTER TABLE `production_outputs`
  ADD CONSTRAINT `production_outputs_ibfk_1` FOREIGN KEY (`production_id`) REFERENCES `production_orders` (`id`),
  ADD CONSTRAINT `production_outputs_ibfk_2` FOREIGN KEY (`produced_product_id`) REFERENCES `produced_products` (`id`);

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`family_id`) REFERENCES `product_families` (`id`),
  ADD CONSTRAINT `products_ibfk_4` FOREIGN KEY (`subfamily_id`) REFERENCES `product_subfamilies` (`id`);

--
-- Filtros para la tabla `product_families`
--
ALTER TABLE `product_families`
  ADD CONSTRAINT `product_families_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `product_subfamilies`
--
ALTER TABLE `product_subfamilies`
  ADD CONSTRAINT `product_subfamilies_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `product_subfamilies_ibfk_2` FOREIGN KEY (`family_id`) REFERENCES `product_families` (`id`);

--
-- Filtros para la tabla `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Filtros para la tabla `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Filtros para la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Filtros para la tabla `purchase_invoice_records`
--
ALTER TABLE `purchase_invoice_records`
  ADD CONSTRAINT `purchase_invoice_records_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `purchase_invoice_records_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `purchase_invoice_record_items`
--
ALTER TABLE `purchase_invoice_record_items`
  ADD CONSTRAINT `purchase_invoice_record_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `purchase_invoice_records` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `fk_purchase_items_petty_cash_product` FOREIGN KEY (`petty_cash_product_id`) REFERENCES `petty_cash_products` (`id`),
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  ADD CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Filtros para la tabla `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `quotes`
--
ALTER TABLE `quotes`
  ADD CONSTRAINT `quotes_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `quotes_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `quotes_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `quotes_ibfk_4` FOREIGN KEY (`system_service_id`) REFERENCES `system_services` (`id`),
  ADD CONSTRAINT `quotes_ibfk_5` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Filtros para la tabla `quote_items`
--
ALTER TABLE `quote_items`
  ADD CONSTRAINT `quote_items_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`);

--
-- Filtros para la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`pos_session_id`) REFERENCES `pos_sessions` (`id`);

--
-- Filtros para la tabla `sales_dispatches`
--
ALTER TABLE `sales_dispatches`
  ADD CONSTRAINT `fk_sales_dispatches_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sales_dispatches_pos_session` FOREIGN KEY (`pos_session_id`) REFERENCES `pos_sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sales_dispatches_seller_user` FOREIGN KEY (`seller_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sales_dispatch_items`
--
ALTER TABLE `sales_dispatch_items`
  ADD CONSTRAINT `fk_sales_dispatch_items_dispatch` FOREIGN KEY (`dispatch_id`) REFERENCES `sales_dispatches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sales_dispatch_items_product` FOREIGN KEY (`produced_product_id`) REFERENCES `produced_products` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `sales_orders_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `sales_orders_ibfk_3` FOREIGN KEY (`brief_id`) REFERENCES `commercial_briefs` (`id`);

--
-- Filtros para la tabla `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`),
  ADD CONSTRAINT `sales_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_3` FOREIGN KEY (`produced_product_id`) REFERENCES `produced_products` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_4` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Filtros para la tabla `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD CONSTRAINT `sale_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);

--
-- Filtros para la tabla `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `services_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Filtros para la tabla `service_renewals`
--
ALTER TABLE `service_renewals`
  ADD CONSTRAINT `service_renewals_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `service_renewals_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `service_renewals_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Filtros para la tabla `service_types`
--
ALTER TABLE `service_types`
  ADD CONSTRAINT `fk_service_types_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `service_types_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `support_tickets_ibfk_3` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD CONSTRAINT `support_ticket_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`);

--
-- Filtros para la tabla `system_services`
--
ALTER TABLE `system_services`
  ADD CONSTRAINT `fk_system_services_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `system_services_ibfk_1` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`),
  ADD CONSTRAINT `system_services_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `tax_periods`
--
ALTER TABLE `tax_periods`
  ADD CONSTRAINT `tax_periods_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Filtros para la tabla `tax_withholdings`
--
ALTER TABLE `tax_withholdings`
  ADD CONSTRAINT `tax_withholdings_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `tax_withholdings_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `tax_periods` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `user_companies`
--
ALTER TABLE `user_companies`
  ADD CONSTRAINT `user_companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_companies_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
