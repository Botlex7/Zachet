-- Удаляем старую базу данных, если существует
DROP DATABASE IF EXISTS `boat_rental`;

-- Создаем новую базу данных
CREATE DATABASE IF NOT EXISTS `boat_rental` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Выбираем базу данных
USE `boat_rental`;

-- Таблица бронирований
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `boat_name` varchar(100) NOT NULL,
    `customer_name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `start_date` datetime NOT NULL,
    `end_date` datetime NOT NULL,
    `hours` int(11) NOT NULL,
    `total_price` decimal(10,2) NOT NULL,
    `comments` text,
    `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_status` (`status`),
    KEY `idx_start_date` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица лодок
CREATE TABLE IF NOT EXISTS `boats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text,
    `price_per_hour` decimal(10,2) NOT NULL,
    `capacity` int(11) DEFAULT NULL,
    `image_url` varchar(255) DEFAULT NULL,
    `is_available` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавляем тестовые лодки
INSERT INTO `boats` (`name`, `description`, `price_per_hour`, `capacity`, `image_url`, `is_available`) VALUES
('Моторная лодка 1', 'Отличная лодка для прогулок по озеру. Идеально подходит для семейного отдыха.', 2000, 4, 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=300', 1),
('Моторная лодка 2', 'Мощная лодка для активного отдыха и водных видов спорта.', 2500, 5, 'https://images.unsplash.com/photo-1579033461380-adb47c3eb938?w=300', 1),
('Катер', 'Комфортабельный катер для большой компании. Есть тент от солнца.', 3500, 8, 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?w=300', 1),
('Яхта', 'Роскошная яхта для VIP отдыха. Включает обслуживание.', 5000, 12, 'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?w=300', 1),
('Лодка для рыбалки', 'Специально оборудованная лодка для рыбалки. Есть эхолот.', 1800, 3, 'https://images.unsplash.com/photo-1531860431897-37e4aef1e762?w=300', 1);

-- Добавляем тестовое бронирование (необязательно)
INSERT INTO `bookings` (`boat_name`, `customer_name`, `email`, `phone`, `start_date`, `end_date`, `hours`, `total_price`, `comments`, `status`, `created_at`) VALUES
('Моторная лодка 1', 'Тестовый Клиент', 'test@example.com', '+79991234567', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 3 HOUR, 3, 6000, 'Тестовое бронирование', 'pending', NOW());

-- Выводим информацию о созданных таблицах
SHOW TABLES;