-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 07 Haz 2025, 17:19:52
-- Sunucu sürümü: 8.3.0
-- PHP Sürümü: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `ajax_kayit`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ad` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `soyad` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `sifre` varchar(255) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dogrulama_kodu` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `aktif` enum('0','1') COLLATE utf8mb4_turkish_ci DEFAULT '0',
  `rol` enum('admin','editor','user') COLLATE utf8mb4_turkish_ci DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `ad`, `soyad`, `email`, `sifre`, `created_at`, `dogrulama_kodu`, `aktif`, `rol`) VALUES
(9, 'Ferat', 'Ucmaz', 'info@deneme.com.tr', '$2y$10$28FVFCQNxpCe3vDZ7kb77O/gANmZ.2nN/CRxSeNC1ysN4dqAkWf5C', '2025-06-07 16:37:23', NULL, '1', 'admin');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
