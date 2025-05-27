-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 11:36 PM
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
-- Database: `carrentalsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `action_date`) VALUES
(1, 1, 'Logged in', '2025-05-14 19:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `booking_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `car_id`, `start_date`, `end_date`, `total_amount`, `status`, `payment_method`, `promo_code`, `booking_date`) VALUES
(5, 1, 2, '2025-05-23', '2025-05-21', 150.00, 'confirmed', 'PayPal', '', '2025-05-20 21:14:48'),
(11, 1, 3, '2025-05-21', '2025-05-21', 120.00, 'confirmed', 'PayPal', '', '2025-05-20 21:37:33'),
(12, 1, 2, '2025-05-21', '2025-05-22', 100.00, 'confirmed', 'PayPal', '', '2025-05-20 22:27:36'),
(13, 1, 1, '2025-05-29', '2025-05-28', 90.00, 'confirmed', 'Credit Card', '', '2025-05-24 15:40:59'),
(20, 4, 3, '2025-05-28', '2025-05-28', 120.00, 'confirmed', 'Credit Card', '', '2025-05-27 00:56:52'),
(21, 4, 5, '2025-05-29', '2025-05-30', 190.00, 'confirmed', 'Credit Card', '', '2025-05-27 00:57:02'),
(24, 4, 5, '2025-05-28', '2025-05-29', 190.00, 'confirmed', 'Credit Card', '', '2025-05-27 18:48:09'),
(29, 4, 9, '2025-05-28', '2025-05-29', 240.00, 'confirmed', 'Credit Card', '', '2025-05-27 21:32:07');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `color` varchar(30) DEFAULT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `status` enum('available','rented','maintenance') DEFAULT 'available',
  `license_plate` varchar(20) NOT NULL,
  `mileage` int(11) DEFAULT 0,
  `transmission` enum('automatic','manual') NOT NULL,
  `fuel_type` enum('petrol','diesel','electric','hybrid') NOT NULL,
  `seats` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `number_of_cars` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `brand`, `model`, `year`, `color`, `daily_rate`, `status`, `license_plate`, `mileage`, `transmission`, `fuel_type`, `seats`, `image_url`, `description`, `created_at`, `updated_at`, `number_of_cars`) VALUES
(1, 'Toyota', 'Corolla', 2020, 'White', 45.00, 'available', 'ABC-1234', 15000, 'automatic', 'petrol', 5, '..\\assets\\uploads\\toyota.jpg', 'Reliable and fuel-efficient.', '2025-05-20 07:13:31', '2025-05-26 19:10:49', 1),
(2, 'Honda', 'Civic', 2019, 'Black', 50.00, 'available', 'XYZ-5678', 22000, 'manual', 'diesel', 5, '..\\assets\\uploads\\honda.png', 'Sporty and responsive handling.', '2025-05-20 07:33:30', '2025-05-26 19:10:46', 1),
(3, 'Tesla', 'Model 3', 2022, 'Blue', 120.00, 'available', 'TES-9999', 5000, 'automatic', 'electric', 5, '..\\assets\\uploads\\telsa 3.png', 'Electric vehicle with autopilot features.', '2025-05-20 07:33:37', '2025-05-27 14:18:24', 1),
(5, 'Ford', 'Mustang', 2018, 'Red', 95.00, 'available', 'FOR-8721', 30000, 'manual', 'petrol', 4, '..\\assets\\uploads\\fordo.jpg', 'Classic muscle car with powerful performance.', '2025-05-20 16:31:46', '2025-05-27 14:18:28', 1),
(6, 'BMW', 'X5', 2021, 'Gray', 110.00, 'available', 'BMW-4456', 18000, 'automatic', 'diesel', 5, '..\\assets\\uploads\\bmw.jpg', 'Luxury SUV with advanced safety features.', '2025-05-20 16:31:46', '2025-05-27 14:18:32', 1),
(7, 'Hyundai', 'Elantra', 2020, 'Silver', 48.00, 'available', 'HYU-3398', 21000, 'automatic', 'petrol', 5, '..\\assets\\uploads\\hyundai.jpg', 'Economical sedan ideal for daily use.', '2025-05-20 16:31:46', '2025-05-27 14:18:35', 1),
(8, 'Kia', 'Niro', 2023, 'Green', 70.00, 'available', 'KIA-7882', 3000, 'automatic', 'hybrid', 5, '..\\assets\\uploads\\kia.jpg', 'Modern hybrid crossover with great fuel efficiency.', '2025-05-20 16:31:46', '2025-05-27 14:18:37', 1),
(9, 'Nissan', 'Civic', 2025, 'Red', 120.00, '', '', 3000, 'automatic', 'petrol', 5, '..\\assets\\uploads\\telsa 3.png', 'Fast car', '2025-05-27 15:31:25', '2025-05-27 15:32:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_program`
--

CREATE TABLE `loyalty_program` (
  `loyalty_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  `tier` varchar(50) DEFAULT 'Basic',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_program`
--

INSERT INTO `loyalty_program` (`loyalty_id`, `user_id`, `points`, `tier`, `last_updated`) VALUES
(1, 1, 100, 'Silver', '2025-05-14 19:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_records`
--

CREATE TABLE `maintenance_records` (
  `maintenance_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `service_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `odometer_reading` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_records`
--

INSERT INTO `maintenance_records` (`maintenance_id`, `vehicle_id`, `service_date`, `description`, `odometer_reading`) VALUES
(1, 1, '2025-05-01', 'Oil change', '15000 KM');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(7, 4, 'New booking from akid mahmud ricky for Model 3 from 2025-05-28 to 2025-05-28', '', 1, '2025-05-26 18:56:52'),
(8, 4, 'New booking from akid mahmud ricky for Mustang from 2025-05-29 to 2025-05-30', '', 1, '2025-05-26 18:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_locations`
--

CREATE TABLE `pickup_locations` (
  `location_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `hours` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `after_hours_procedure` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pickup_locations`
--

INSERT INTO `pickup_locations` (`location_id`, `city`, `address`, `hours`, `amenities`, `after_hours_procedure`) VALUES
(1, 'Dhaka', '123 Main St', '9 AM - 6 PM', 'Car Wash, Wi-Fi', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `canvas_image` varchar(255) DEFAULT NULL,
  `signature_image` varchar(255) DEFAULT NULL,
  `photo_images` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `timestamp`, `canvas_image`, `signature_image`, `photo_images`) VALUES
(2, 1, '2025-05-17 09:06:41', 'uploads/canvas/68283581594f5.png', 'uploads/signatures/6828358159632.png', '[\"uploads\\/photos\\/682835815973e_496925212_1067000105284799_5429185889170316466_n.jpg\"]');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `driver_license` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email_verified` tinyint(1) DEFAULT 0,
  `role` varchar(50) DEFAULT 'user',
  `license_image` varchar(255) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `first_name`, `last_name`, `profile_picture`, `driver_license`, `created_at`, `email_verified`, `role`, `license_image`, `mobile`, `address`) VALUES
(1, 'user1@example.com', '123', 'John', 'Doe', NULL, 'DL123456', '2025-05-14 19:53:40', 0, 'user', 'uploads/licenses/license_1_1747719511.jpeg', NULL, NULL),
(2, 'admin1@example.com', '123', 'Jane', 'Smith', NULL, 'DL789101', '2025-05-14 19:53:40', 0, 'admin', NULL, NULL, NULL),
(3, 'a@gamil.com', '@Akid2246211', 'AKid', 'Mahmud', NULL, NULL, '2025-05-20 05:00:50', 0, 'user', NULL, NULL, NULL),
(4, 'akid@gmail.com', '@Akid2246211', 'akid mahmud', 'ricky', NULL, NULL, '2025-05-20 05:07:13', 0, 'user', 'assets/uploads/license_4_1748356389.png', '01840193060', 'asdw'),
(5, 'nabil@gmail.com', '$2y$10$2ppAHcNTlVhL39hwSyLuwuUD0m.bCh7W1Ck1LdPHs6UCaEEqLCeKK', 'nabil', 'nabil', NULL, NULL, '2025-05-20 12:52:52', 0, 'user', NULL, NULL, NULL),
(6, 'user3@gmail.com', '123', 'user', 'admin', NULL, NULL, '2025-05-20 12:56:12', 0, 'admin', NULL, NULL, NULL),
(7, 'b@gmail.com', '$2y$10$MvRQbBz.N36BcxO1klnNK.I5gZBFs1kXdYQ2E/WjYZlvA345OZXb2', 'Akid', 'dsd', NULL, NULL, '2025-05-24 18:47:14', 0, 'user', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_id` int(11) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_id`, `mobile`, `country`, `address`, `date_of_birth`) VALUES
(2, '01840193060', NULL, 'dada', NULL),
(4, '01840193060', 'Bangladesh', '23', '2025-05-15');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seat_position` varchar(50) DEFAULT NULL,
  `mirror_position` varchar(50) DEFAULT NULL,
  `preferred_car_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `seat_position`, `mirror_position`, `preferred_car_type`, `created_at`, `updated_at`) VALUES
(1, 1, 'front', '45', 'sedan', '2025-05-20 05:32:46', '2025-05-20 05:32:46'),
(2, 4, 'front', 'high', 'suv', '2025-05-27 14:10:20', '2025-05-27 14:14:26');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `model` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `mileage` varchar(20) DEFAULT NULL,
  `fuel_type` varchar(50) DEFAULT NULL,
  `transmission` varchar(20) DEFAULT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `model`, `image`, `price_per_day`, `mileage`, `fuel_type`, `transmission`, `availability`, `created_at`) VALUES
(1, 'Toyota Camry', NULL, 50.00, '15000 KM', 'Petrol', 'Automatic', 1, '2025-05-14 19:53:40'),
(2, 'Honda Civic', NULL, 45.00, '12000 KM', 'Gas', 'Automatic', 1, '2025-05-14 19:53:40'),
(3, 'Toyota Camry', '/assests/images/camry.jpg', 5000.00, '30000', 'Petrol', 'Automatic', 1, '2025-05-14 20:12:57'),
(4, 'Honda Civic', '/assests/images/civic.jpg', 4500.00, '40000', 'Petrol', 'Manual', 1, '2025-05-14 20:12:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`),
  ADD UNIQUE KEY `license_plate` (`license_plate`);

--
-- Indexes for table `loyalty_program`
--
ALTER TABLE `loyalty_program`
  ADD PRIMARY KEY (`loyalty_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  ADD PRIMARY KEY (`maintenance_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pickup_locations`
--
ALTER TABLE `pickup_locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `loyalty_program`
--
ALTER TABLE `loyalty_program`
  MODIFY `loyalty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pickup_locations`
--
ALTER TABLE `pickup_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`);

--
-- Constraints for table `loyalty_program`
--
ALTER TABLE `loyalty_program`
  ADD CONSTRAINT `loyalty_program_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_records`
--
ALTER TABLE `maintenance_records`
  ADD CONSTRAINT `maintenance_records_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
