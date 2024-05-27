-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 27 May 2024, 15:27:31
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `basvuru_case`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bundle_sub_products`
--

CREATE TABLE `bundle_sub_products` (
  `id` int(11) NOT NULL,
  `bundle_id` int(11) NOT NULL COMMENT 'products table(type= ''bundle'')',
  `sub_product_id` int(11) NOT NULL COMMENT 'sub_products table',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `bundle_sub_products`
--

INSERT INTO `bundle_sub_products` (`id`, `bundle_id`, `sub_product_id`, `created_at`, `updated_at`, `status`) VALUES
(1, 4, 2, '2024-05-26 18:54:51', '2024-05-26 18:54:51', 1),
(2, 4, 3, '2024-05-26 18:54:51', '2024-05-26 18:54:51', 0),
(3, 4, 4, '2024-05-27 10:52:36', '2024-05-27 10:52:36', 1),
(4, 5, 3, '2024-05-27 11:02:52', '2024-05-27 11:02:52', 0),
(5, 4, 1, '2024-05-27 11:03:34', '2024-05-27 11:03:34', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `product_price` float NOT NULL,
  `product_type` enum('single','bundle') NOT NULL,
  `total_stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `product_name`, `product_price`, `product_type`, `total_stock`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Ayakkabı', 35, 'single', 0, '2024-05-25 12:06:12', '2024-05-25 12:06:12', 1),
(2, 'Mont', 45, 'single', 0, '2024-05-25 12:29:38', '2024-05-25 12:29:38', 1),
(3, 'T-shirt', 55, 'single', 0, '2024-05-25 13:26:26', '2024-05-25 13:26:26', 1),
(4, 'Örnek Bundle 1', 100, 'bundle', 0, '2024-05-26 12:08:14', '2024-05-26 12:08:14', 1),
(5, 'Örnek Bundle 2', 200, 'bundle', 1, '2024-05-26 15:14:59', '2024-05-26 15:14:59', 1),
(9, 'Pantolon', 23, 'single', 0, '2024-05-27 10:37:06', '2024-05-27 10:37:06', 1),
(10, 'Etek', 12, 'single', 0, '2024-05-27 10:37:59', '2024-05-27 10:37:59', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sub_products`
--

CREATE TABLE `sub_products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT 'products table (type = ''single'')',
  `variant_id` int(11) NOT NULL COMMENT 'variants table',
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `sub_products`
--

INSERT INTO `sub_products` (`id`, `product_id`, `variant_id`, `quantity`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, 1, 3, '2024-05-26 11:18:55', '2024-05-26 11:18:55', 1),
(2, 1, 3, 3, '2024-05-26 11:19:14', '2024-05-26 11:19:14', 1),
(3, 3, 18, 3, '2024-05-26 11:38:30', '2024-05-26 11:38:30', 1),
(4, 1, 10, 3, '2024-05-26 11:55:43', '2024-05-26 11:55:43', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `variants`
--

CREATE TABLE `variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT 'products table',
  `size` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `color` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `variants`
--

INSERT INTO `variants` (`id`, `product_id`, `size`, `color`, `stock`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, '40', 'Mavi', 0, '2024-05-25 12:33:41', '2024-05-25 12:33:41', 0),
(2, 1, '41', 'Mavi', 3, '2024-05-25 12:34:16', '2024-05-25 12:34:16', 1),
(3, 1, '42', 'Mavi', 3, '2024-05-25 12:34:49', '2024-05-25 12:34:49', 1),
(4, 1, '43', 'Mavi', 3, '2024-05-25 12:34:54', '2024-05-25 12:34:54', 1),
(5, 1, '44', 'Mavi', 3, '2024-05-25 12:35:00', '2024-05-25 12:35:00', 1),
(6, 1, '44', 'Kırmızı', 3, '2024-05-25 12:35:16', '2024-05-25 12:35:16', 1),
(7, 1, '43', 'Kırmızı', 6, '2024-05-25 12:35:22', '2024-05-25 12:35:22', 1),
(8, 1, '42', 'Kırmızı', 3, '2024-05-25 12:35:29', '2024-05-25 12:35:29', 1),
(9, 1, '41', 'Kırmızı', 3, '2024-05-25 12:35:35', '2024-05-25 12:35:35', 1),
(10, 1, '40', 'Kırmızı', 3, '2024-05-25 12:35:40', '2024-05-25 12:35:40', 0),
(11, 2, 'S', 'Siyah', 3, '2024-05-25 12:36:21', '2024-05-25 12:36:21', 1),
(12, 2, 'M', 'Siyah', 5, '2024-05-25 12:36:27', '2024-05-25 12:36:27', 1),
(13, 2, 'L', 'Siyah', 3, '2024-05-25 12:36:32', '2024-05-25 12:36:32', 1),
(14, 2, 'XL', 'Siyah', 3, '2024-05-25 12:36:42', '2024-05-25 12:36:42', 1),
(18, 3, 'S', 'Beyaz', 3, '2024-05-25 21:13:53', '2024-05-25 21:13:53', 1),
(19, 0, '', '', 0, '2024-05-27 10:40:19', '2024-05-27 10:40:19', 1),
(20, 2, '40', 'Beyaz', 2, '2024-05-27 11:08:15', '2024-05-27 11:08:15', 1);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `bundle_sub_products`
--
ALTER TABLE `bundle_sub_products`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `sub_products`
--
ALTER TABLE `sub_products`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `variants`
--
ALTER TABLE `variants`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `bundle_sub_products`
--
ALTER TABLE `bundle_sub_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Tablo için AUTO_INCREMENT değeri `sub_products`
--
ALTER TABLE `sub_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `variants`
--
ALTER TABLE `variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
