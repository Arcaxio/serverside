-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2024 at 10:40 PM
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
-- Database: `assignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `product_id`, `user_id`, `quantity`) VALUES
(109, 14, 7, 1),
(110, 15, 7, 1),
(111, 16, 7, 1),
(112, 20, 7, 1),
(113, 18, 7, 1),
(114, 19, 7, 1),
(115, 3, 7, 1),
(116, 17, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Desktop & Laptop'),
(2, 'Smartphones'),
(3, 'Accessories');

-- --------------------------------------------------------

--
-- Table structure for table `ordered_items`
--

CREATE TABLE `ordered_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `item_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordered_items`
--

INSERT INTO `ordered_items` (`order_item_id`, `order_id`, `product_id`, `item_quantity`) VALUES
(73, 69, 14, 1),
(74, 69, 20, 1),
(75, 70, 16, 1),
(76, 71, 18, 1),
(77, 72, 19, 1),
(78, 73, 3, 1),
(79, 73, 17, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL,
  `payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `order_status`, `payment_id`) VALUES
(69, 7, '2024-03-28 04:39:47', 1249.74, 'pending', 111128),
(70, 7, '2024-03-28 04:41:04', 116.60, 'processing', 403026),
(71, 7, '2024-03-28 04:41:54', 47.70, 'shipped', 266836),
(72, 7, '2024-03-28 04:43:01', 106.00, 'delivered', 998644),
(73, 7, '2024-03-28 04:44:09', 127.19, 'cancelled', 240259);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` int(5) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `phone_number` varchar(11) NOT NULL,
  `payment_datetime` datetime NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `total_payment_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `product_id`, `user_id`, `fullname`, `address`, `zipcode`, `city`, `state`, `phone_number`, `payment_datetime`, `payment_method`, `total_payment_amount`) VALUES
(111128, 14, 7, 'customer full name', 'No 123, Jalan 123, Taman 123', 31900, 'Kampar', 'Perak', '0123456789', '2024-03-27 21:39:47', 'Debit/Credit Card', 1249.74),
(240259, 3, 7, 'buyer4', 'buyer4 address', 87000, 'Labuan', 'Wilayah Persekutuan Labuan', '12345678910', '2024-03-27 21:44:09', 'E-Wallet', 127.19),
(266836, 18, 7, 'buyer2', 'buyer2 address', 79100, 'Johor Bahru', 'Johor', '0198558294', '2024-03-27 21:41:54', 'E-Wallet', 47.70),
(403026, 16, 7, 'buyer1 name', 'buyer1 address', 98000, 'Miri', 'Sarawak', '01111248294', '2024-03-27 21:41:04', 'PayPal', 116.60),
(998644, 19, 7, 'buyer3', 'buyer3 address', 88000, 'Kota Kinabalu', 'Sabah', '0987654321', '2024-03-27 21:43:01', 'Debit/Credit Card', 106.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `image_path`, `category_id`) VALUES
(3, 'Premium Phone Case', 'Sleek and protective case for your phone', 19.99, 'premium_case.jpg', 3),
(14, 'MX Master 3S', 'Meet MX Master 3S – an iconic mouse remastered.\r\nFeel every moment of your workflow with even more precision, tactility, and performance, thanks to Quiet Clicks and an 8,000 DPI track-on-glass 134 mm minimum glass thickness track on glass sensor.', 569.00, 'https://resource.logitech.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/logitech/en/products/mice/mx-master-3s/gallery/mx-master-3s-mouse-top-view-graphite.png?v=1', 1),
(15, 'PEBBLE MOUSE 2 M350S', 'Slim, compact Bluetooth® mouse with a customizable button.', 119.00, 'https://resource.logitech.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/logitech/en/products/mice/pebble-mouse-2-m350s/gallery/pebble-mouse-2-m350s-top-tonal-rose-gallery.png?v=1', 1),
(16, 'ZONE VIBE 100', 'Lightweight, wireless headphones — professional enough for the office, perfect for working from home.', 100.00, 'https://resource.logitech.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/logitech/en/products/headsets/zone-vibe-100/gallery/zone-vibe-100-gallery-white-1.png?v=1', 1),
(17, 'Anker Prime 67W GaN Wall Charger (3 Ports)', 'Fast Charging for 3 Devices: With 2 USB-C ports and 1 USB-A port, effortlessly charge your phone, tablet, and notebook all at once from a single charger. Connect a single device to charge up to 67W.', 90.00, 'https://cdn.shopify.com/s/files/1/0493/9834/9974/files/Rectangle8.png?v=1690893875', 3),
(18, 'Anker 765 USB-C to USB-C Cable (3 ft / 6 ft)', 'A maximum output of 240W and compatibility with USB Power Delivery 3.1 allows for high-speed charging to a wide range of devices including phones, tablets, and laptops.', 35.00, 'https://cdn.shopify.com/s/files/1/0493/9834/9974/files/6ft-01.jpg?v=1697681538', 2),
(19, 'Anker 737 Power Bank (PowerCore 24K)', 'Equipped with the latest Power Delivery 3.1 and bi-directional technology to quickly recharge the portable charger or get a 140W ultra-powerful charge. (Note: This power bank does not support wireless recharge using a charging base)', 90.00, 'https://cdn.shopify.com/s/files/1/0493/9834/9974/products/A1289011-Anker_737_Power_Bank_PowerCore_24K_1.png?v=1672388225', 2),
(20, 'PRO X', 'The tournament-proven PRO design, now with your choice of swappable pro-grade GX mechanical switches: Clicky, Tactile and Linear. Built to win in collaboration with the world’s top esports athletes.', 600.00, 'https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-keyboard/pro-x-keyboard-gallery-1.png?v=1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager') DEFAULT 'manager'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `username`, `password`, `role`) VALUES
(1, 'username123', '$2y$10$esmN8eAHe/Hr3NSV4VJIue1gQ3EcqsFok.Kld52EPMF39gTK2OzZC', 'admin'),
(2, '123username', '$2y$10$zsnTyVs4MPw7ILW0P4yxbOFcFOENTujjOHqJKIhOPixnx3FSnu/au', 'manager');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user') DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `zipcode` int(5) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `name`, `email`, `address`, `phone_number`, `zipcode`, `city`, `state`) VALUES
(7, 'customer123', '$2y$10$5FACw9mvCWfKbDZWlSK6kuq2s1d1UMtIBdfkzy0bWm.0pivkAPdf2', NULL, 'customer full name', 'customer123@mail.com', 'No 123, Jalan 123, Taman 123', '0123456789', 31900, 'Kampar', 'Perak');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `cart_product_id` (`product_id`),
  ADD KEY `cart_user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `ordered_items`
--
ALTER TABLE `ordered_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `ordered_order_id` (`order_id`),
  ADD KEY `ordered_product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payment_user_id` (`user_id`),
  ADD KEY `payment_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `product_category_id` (`category_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ordered_items`
--
ALTER TABLE `ordered_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=998645;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `cart_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ordered_items`
--
ALTER TABLE `ordered_items`
  ADD CONSTRAINT `ordered_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `ordered_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `payment_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `product_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
