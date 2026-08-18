-- Mevcut kurulumları güvenlik şemasına yükseltmek için
-- phpMyAdmin veya mysql istemcisinde ajax_kayit veritabanını seçip çalıştırın.

SET NAMES utf8mb4;

-- Doğrulama süresi sütunu (varsa hata alırsanız bu satırı atlayın)
ALTER TABLE `users`
  ADD COLUMN `dogrulama_expires_at` datetime DEFAULT NULL AFTER `dogrulama_kodu`;

-- ENUM aktif değerini güvenli şekilde TINYINT'e çevir
ALTER TABLE `users`
  ADD COLUMN `aktif_tmp` tinyint(1) NOT NULL DEFAULT 0;

UPDATE `users`
SET `aktif_tmp` = CASE
  WHEN CAST(`aktif` AS CHAR) IN ('1', 'true') THEN 1
  ELSE 0
END;

ALTER TABLE `users` DROP COLUMN `aktif`;
ALTER TABLE `users` CHANGE `aktif_tmp` `aktif` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `users`
  MODIFY `ad` varchar(50) NOT NULL,
  MODIFY `soyad` varchar(50) NOT NULL,
  MODIFY `email` varchar(100) NOT NULL,
  MODIFY `sifre` varchar(255) NOT NULL,
  MODIFY `rol` enum('admin','editor','user') NOT NULL DEFAULT 'user',
  ENGINE=InnoDB;

ALTER TABLE `users` ADD INDEX `idx_dogrulama_kodu` (`dogrulama_kodu`);
