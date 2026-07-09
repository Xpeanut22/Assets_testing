-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2023 at 03:00 PM
-- Server version: 10.4.16-MariaDB
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assets`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplierid` int(11) NOT NULL,
  `typeid` int(11) NOT NULL,
  `brandid` int(11) NOT NULL,
  `assettag` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchasedate` date NOT NULL,
  `cost` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warranty` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locationid` int(11) NOT NULL,
  `checkstatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `supplierid`, `typeid`, `brandid`, `assettag`, `name`, `serial`, `quantity`, `purchasedate`, `cost`, `warranty`, `status`, `picture`, `description`, `created_at`, `updated_at`, `locationid`, `checkstatus`) VALUES
(1, 1, 1, 1, 'AST2302131', 'Toyota Pick-up Truck (Response Vehicle', '123456789', 'undefined', '2018-03-06', '1400000', '36', '1', '0213202306190763e9d65b690252022 almera.jpg', 'Vehicle for emergency respponse', '2023-02-12 22:19:07', '2023-02-12 22:45:34', 2, 2),
(2, 1, 1, 1, 'AST2302132', 'Toyota Innova', '123456789', 'undefined', '2018-02-13', '1250000', '36', '1', '0213202307404263e9e97a1f9bctoyota innova.jpg', 'Toyota Innova 2.0 Gasoline Automatic', '2023-02-12 23:38:11', '2023-02-12 23:40:42', 4, 2),
(3, 1, 1, 1, 'AST2302133', 'Toyota Avanza', '12345678997', 'undefined', '2017-01-03', '675000', '36', '1', '0213202307531763e9ec6d53b30avanza.jpg', 'Toyota Avanza 1.5', '2023-02-12 23:53:17', '2023-02-12 23:53:17', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `asset_history`
--

CREATE TABLE `asset_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetid` bigint(20) UNSIGNED NOT NULL,
  `employeeid` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_history`
--

INSERT INTO `asset_history` (`id`, `assetid`, `employeeid`, `date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2023-02-13', '1', '2023-02-12 22:45:34', '2023-02-12 22:45:34'),
(2, 2, 2, '2018-02-14', '1', '2023-02-12 23:39:06', '2023-02-12 23:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `asset_type`
--

CREATE TABLE `asset_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_type`
--

INSERT INTO `asset_type` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Vehicle', 'Service Vehicle', '2023-02-12 22:11:51', '2023-02-12 22:11:51'),
(2, 'Computer', 'Computer Hardware', '2023-02-12 22:12:16', '2023-02-12 22:12:16');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Toyota', 'Toyota Motor', '2023-02-12 22:12:36', '2023-02-12 22:12:36'),
(2, 'Acer', 'Acer Computer', '2023-02-12 22:12:54', '2023-02-12 22:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `component`
--

CREATE TABLE `component` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplierid` int(11) NOT NULL,
  `typeid` int(11) NOT NULL,
  `brandid` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchasedate` date NOT NULL,
  `cost` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warranty` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locationid` int(11) NOT NULL,
  `checkstatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `component`
--

INSERT INTO `component` (`id`, `supplierid`, `typeid`, `brandid`, `name`, `serial`, `quantity`, `purchasedate`, `cost`, `warranty`, `status`, `picture`, `description`, `created_at`, `updated_at`, `locationid`, `checkstatus`) VALUES
(1, 1, 1, 1, 'Tire', 'tire101', '4', '2023-02-13', '5600', '12', '1', 'pic.png', 'for vehicle', '2023-02-12 22:34:00', '2023-02-12 22:36:14', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `component_assets`
--

CREATE TABLE `component_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetid` bigint(20) UNSIGNED NOT NULL,
  `componentid` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `component_assets`
--

INSERT INTO `component_assets` (`id`, `assetid`, `componentid`, `quantity`, `created_at`, `updated_at`, `status`, `date`) VALUES
(1, 1, 1, 2, '2023-02-12 22:36:14', '2023-02-12 22:36:14', '1', '2023-02-13');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'MDDRMO', 'MDRRM Office', '2023-02-12 22:16:10', '2023-02-12 22:16:10'),
(2, 'I.T. Unit', 'I.T. Unit, Mayor\'s Officer', '2023-02-12 23:35:36', '2023-02-12 23:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `depreciation`
--

CREATE TABLE `depreciation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetid` int(11) DEFAULT NULL,
  `componentid` int(11) DEFAULT NULL,
  `period` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assetvalue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `depreciation`
--

INSERT INTO `depreciation` (`id`, `assetid`, `componentid`, `period`, `assetvalue`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '60', '300000', '2023-02-12 22:31:33', '2023-02-12 22:31:33');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `departmentid` int(11) NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jobrole` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `departmentid`, `fullname`, `email`, `jobrole`, `city`, `country`, `address`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kazmir Capunihan', 'mdrrmo@gmail.com', 'LDRRMO', 'Carmona', 'Philippines', NULL, '2023-02-12 22:16:56', '2023-02-12 22:16:56'),
(2, 2, 'Angelo Macha', 'angelo@gmail.com', 'I.T. Officer III', 'Carmona', 'Philippines', NULL, '2023-02-12 23:35:11', '2023-02-12 23:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetid` int(11) DEFAULT NULL,
  `componentid` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Motorpool', 'Motorpool Area, Maduya', '2023-02-12 22:14:07', '2023-02-12 22:14:07'),
(2, 'MDRRMO', 'MDRRM Office', '2023-02-12 22:14:27', '2023-02-12 22:14:27'),
(3, 'Barangay 1', 'Barangay 1, Poblacion', '2023-02-12 22:15:08', '2023-02-12 22:15:08'),
(4, 'IT Office', 'IT Office', '2023-02-12 23:34:36', '2023-02-12 23:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetid` int(11) NOT NULL,
  `supplierid` int(11) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `assetid`, `supplierid`, `startdate`, `enddate`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2023-02-14', '2023-02-14', 'Maintenance', '2023-02-12 22:37:01', '2023-02-12 22:37:01'),
(2, 2, 1, '2023-02-14', '2023-02-14', 'Maintenance', '2023-02-12 23:41:49', '2023-02-12 23:41:49');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phonenumber` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` char(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `formatdate` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `currency` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` char(5) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company`, `address`, `email`, `phonenumber`, `country`, `logo`, `formatdate`, `created_at`, `updated_at`, `currency`, `language`) VALUES
(1, 'Carmona Asset Management System', 'Carmona, Cavite', 'mo@carmona.gov.ph', '4300817', 'Philippines', 'carmonalogo10cm.png', 'm-d-Y', '2020-09-09 17:00:00', '2020-09-09 17:00:00', 'P', 'en');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `name`, `email`, `city`, `country`, `zip`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Toyota Motors Phils., Inc.', 'toyota@gmail.com', 'Silang, Cavite', 'Philippines', '4117', '4302675', 'Silang', '2023-02-12 22:13:35', '2023-02-12 22:13:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `status`, `city`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@example.com', '$2y$10$UC70weIRUWjYxfl4n9N3DuytohYU3LDsLTsBuVWFyas29kslhtnGa', '1', 'Carmona', '4302675', '1', '2020-09-13 00:10:45', '2023-02-13 00:03:50'),
(2, 'Angelo Macha', 'angelomacha1974@gmail.com', '$2y$10$diepZnDrM2KLa5QyobObRORKrArEIayBxuXYtLsvvf7N0QynIH0Kq', '1', 'Carmona', '4302675', '1', '2023-02-12 22:47:12', '2023-02-12 22:47:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_history`
--
ALTER TABLE `asset_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_history_assetid_foreign` (`assetid`);

--
-- Indexes for table `asset_type`
--
ALTER TABLE `asset_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `component`
--
ALTER TABLE `component`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `component_assets`
--
ALTER TABLE `component_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `component_assets_assetid_foreign` (`assetid`),
  ADD KEY `component_assets_componentid_foreign` (`componentid`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `depreciation`
--
ALTER TABLE `depreciation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `asset_history`
--
ALTER TABLE `asset_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `asset_type`
--
ALTER TABLE `asset_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `component`
--
ALTER TABLE `component`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `component_assets`
--
ALTER TABLE `component_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `depreciation`
--
ALTER TABLE `depreciation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asset_history`
--
ALTER TABLE `asset_history`
  ADD CONSTRAINT `asset_history_assetid_foreign` FOREIGN KEY (`assetid`) REFERENCES `assets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `component_assets`
--
ALTER TABLE `component_assets`
  ADD CONSTRAINT `component_assets_assetid_foreign` FOREIGN KEY (`assetid`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `component_assets_componentid_foreign` FOREIGN KEY (`componentid`) REFERENCES `component` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
