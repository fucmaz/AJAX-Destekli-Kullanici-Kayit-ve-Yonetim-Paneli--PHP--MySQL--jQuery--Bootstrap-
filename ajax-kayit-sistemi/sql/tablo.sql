-- AJAX Kayıt Sistemi şema
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `ajax_kayit` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE `ajax_kayit`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ad` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
  `soyad` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
  `sifre` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dogrulama_kodu` varchar(64) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `dogrulama_expires_at` datetime DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 0,
  `rol` enum('admin','editor','user') COLLATE utf8mb4_turkish_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_dogrulama_kodu` (`dogrulama_kodu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Demo admin: info@deneme.com.tr / ChangeMe!123  (kurulumdan sonra mutlaka değiştirin)
INSERT INTO `users` (`id`, `ad`, `soyad`, `email`, `sifre`, `created_at`, `dogrulama_kodu`, `dogrulama_expires_at`, `aktif`, `rol`) VALUES
(1, 'Ferat', 'Ucmaz', 'info@deneme.com.tr', '$2y$10$.W/JxRgozp9RYmICA0LY6uaVirRJJ1JwY7Xz/1gRP130oQkSgauha', CURRENT_TIMESTAMP, NULL, NULL, 1, 'admin');

COMMIT;
