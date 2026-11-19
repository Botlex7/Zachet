<?php
// Конфигурация для OpenServer
$host = 'localhost';  // Хост базы данных
$dbname = 'boat_rental';  // Название базы данных
$username = 'root';  // Пользователь MySQL
$password = '';  // Пароль (по умолчанию в OpenServer пустой)

// Настройки для тестирования почты на OpenServer
// Используем встроенный SMTP сервер OpenServer или тестовый вывод
$smtp_config = [
    'use_smtp' => false,  // Для имитации ставим false
    'test_mode' => true   // Режим тестирования - письма не отправляются, а сохраняются
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для логирования email (имитация отправки)
function logEmail($to, $subject, $message) {
    $logFile = 'email_log.txt';
    $logEntry = date('Y-m-d H:i:s') . " | TO: $to | SUBJECT: $subject | MESSAGE: $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    return true;
}

// Функция отправки email (с поддержкой тестового режима)
function sendEmail($to, $subject, $message) {
    global $smtp_config;
    
    if ($smtp_config['test_mode']) {
        // Имитация отправки - логируем письмо
        logEmail($to, $subject, $message);
        return true;
    } else {
        // Реальная отправка через mail()
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: no-reply@boat-rental.ru\r\n";
        return mail($to, $subject, $message, $headers);
    }
}
?>