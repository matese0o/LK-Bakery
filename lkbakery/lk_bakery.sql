-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 06:03 PM
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
-- Database: `lk_bakery`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

--
-- Table structure for table `main_categories`
--

CREATE TABLE `main_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `main_categories`
--

INSERT INTO `main_categories` (`id`, `name`, `created_at`) VALUES
(19, 'Food', '2025-04-26 08:01:39'),
(20, 'Drinks', '2025-04-26 08:01:41'),
(21, 'Box Options', '2025-04-26 08:01:43');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `main_category_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `item_name`, `main_category_id`, `sub_category_id`, `description`, `price`, `image_path`, `is_available`, `created_at`) VALUES
(28, 'SOURDOUGH', 19, 16, 'Best used for Toast, Grilled Cheese, and open sandwiches. Great eaten with Olive Oil & Balsamic Vinegar.', 200.00, '68220ec543d99_Sourdough.png', 1, '2025-04-28 15:25:51'),
(29, 'CHOCOLATE CHIP & SEA SALT', 19, 20, 'A classic chocolate chip cookie with walnuts to balance the sweet and the salty.', 99.00, '682211284a6ff_ChocolateChipNSeaSalt.png', 1, '2025-04-28 15:26:34'),
(30, 'ESPRESSO', 20, 17, '30 ml\r\nHot', 70.00, '680f9e62ae94a_Espresso.png', 1, '2025-04-28 15:27:30'),
(31, 'PURE MATCHA TEA', 20, 21, 'Unsweetened', 170.00, '680f9e99b82a7_PureMatchaTea.png', 1, '2025-04-28 15:28:25'),
(36, 'PAN AU CHOCOLAT', 19, 23, 'Butter croissant base with chocolate sticks inside. Best paired with black coffee, cafe latte, or hot chocolate.', 99.00, '682210e563120_PanAuChocolat.png', 1, '2025-05-12 15:16:53'),
(37, 'LE CROOKIE', 19, 23, 'Contains: Nuts, Dairy, Egg, Gluten', 149.00, '682211650ef9c_LeCrookie.png', 1, '2025-05-12 15:19:01'),
(38, 'CARAMEL MACCHIATO', 20, 17, '', 160.00, '6822119b84228_CaramelMacchiato.png', 1, '2025-05-12 15:19:55'),
(39, 'SPANISH MATCHA LATTE', 20, 21, '', 195.00, '682211db3814c_SpanishMatchaLatte.png', 1, '2025-05-12 15:20:59'),
(44, 'BOX OF SIX CLASSIC PASTRIES', 21, 26, 'Choice of 6', 594.00, '682552eed3a46_ClassicPastries.png', 1, '2025-05-15 02:35:26'),
(45, 'BOX OF SIX ASSORTED CLASSIC', 21, 26, 'Choice of 3 Classic and 3 Premium Pastries', 744.00, '68255309894f0_AssortedClassic.png', 1, '2025-05-15 02:35:53'),
(46, 'BOX OF SIX ASSORTED ELITES', 21, 26, 'Choice of 2 Classic, 2 Premium, and 2 Elite Pastries', 894.00, '68255329dbf4c_AssortedElites.png', 1, '2025-05-15 02:36:25'),
(47, 'BOX OF SIX ASSORTED PREMIUM', 21, 26, 'Choice of 3 Premium and 3 Elite Pastries', 894.00, '68255342647d2_AssortedPremium.png', 1, '2025-05-15 02:36:50');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` int(11) NOT NULL,
  `main_category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `main_category_id`, `name`, `created_at`) VALUES
(16, 19, 'Breads', '2025-04-26 08:01:59'),
(17, 20, 'Coffee Series', '2025-04-26 08:02:10'),
(20, 19, 'Cookies', '2025-04-28 15:16:57'),
(21, 20, 'Matcha Series', '2025-04-28 15:17:36'),
(23, 19, 'Laminated Pastry', '2025-05-12 15:14:49'),
(24, 20, 'Non-Caffeine', '2025-05-12 15:16:16'),
(26, 21, 'Box of Six', '2025-05-15 02:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'admin', 'admin@lkbakery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-04-25 18:08:08', '2025-05-12 17:45:33', 1),
(3, 'zeloiwow', 'cuteko@gmail.com', '$2y$10$q1.mxAXc4V/1ecfIf59/KOwEM.GTcT7iarjunyD.UmuNMabnM0W0q', 'customer', '2025-04-26 08:21:33', '2025-04-26 08:21:33', 1),
(4, 'matese', 'matese@gmail.com', '$2y$10$v3nE6vzXzjdOvQHSNE9iZ.Gi6GObv7tZopOI1YAwEWSRD43OljvpO', 'customer', '2025-04-28 15:01:28', '2025-04-28 15:01:28', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `main_categories`
--
ALTER TABLE `main_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_menu_items_main_category` (`main_category_id`),
  ADD KEY `fk_menu_items_sub_category` (`sub_category_id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `main_category_id` (`main_category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `main_categories`
--
ALTER TABLE `main_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `fk_menu_items_main_category` FOREIGN KEY (`main_category_id`) REFERENCES `main_categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_menu_items_sub_category` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_ibfk_1` FOREIGN KEY (`main_category_id`) REFERENCES `main_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
