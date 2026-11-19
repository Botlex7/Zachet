<?php
// setup.php - Скрипт для установки базы данных
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Параметры подключения
$host = 'localhost';
$dbname = 'boat_rental';
$username = 'root';
$password = '';

try {
    // Подключаемся без выбора базы данных
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Читаем SQL файл
    $sql = file_get_contents('database.sql');
    
    // Разбиваем SQL на отдельные запросы
    $queries = explode(';', $sql);
    
    echo "<h1>Установка базы данных для проката лодок</h1>";
    echo "<div style='font-family: monospace; background: #f4f4f4; padding: 20px; border-radius: 5px;'>";
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query) && $query != 'SHOW TABLES') {
            try {
                $pdo->exec($query);
                echo "<div style='color: green; margin: 5px 0;'>✓ Выполнен запрос: " . substr($query, 0, 100) . "...</div>";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "<div style='color: red; margin: 5px 0;'>✗ Ошибка: " . $e->getMessage() . "</div>";
                }
            }
        }
    }
    
    echo "</div>";
    
    // Проверяем создание таблиц
    $pdo->exec("USE $dbname");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Проверка таблиц:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li style='color: green;'>✓ Таблица '$table' создана успешно</li>";
    }
    echo "</ul>";
    
    if (in_array('bookings', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        echo "<p style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'>✅ База данных успешно установлена! В таблице bookings {$count} записей.</p>";
    } else {
        echo "<p style='color: red; background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ Таблица 'bookings' не создана. Проверьте права доступа.</p>";
    }
    
    echo "<br><a href='rental-form.html' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Перейти к форме бронирования</a>";
    echo " <a href='index.html' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>На главную</a>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>Ошибка установки базы данных!</h1>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<h3>Возможные решения:</h3>";
    echo "<ol>";
    echo "<li>Убедитесь, что MySQL запущен в OpenServer</li>";
    echo "<li>Проверьте параметры подключения в файле config.php</li>";
    echo "<li>Зайдите в phpMyAdmin и создайте базу данных вручную</li>";
    echo "<li>Запустите этот скрипт снова после исправления ошибок</li>";
    echo "</ol>";
    
    echo "<h3>Ручная установка:</h3>";
    echo "<p>Если автоматическая установка не работает, выполните следующие шаги:</p>";
    echo "<ol>";
    echo "<li>Откройте phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Создайте базу данных с именем <strong>boat_rental</strong></li>";
    echo "<li>Выберите кодировку <strong>utf8mb4_unicode_ci</strong></li>";
    echo "<li>Откройте вкладку SQL и выполните запросы из файла <strong>database.sql</strong></li>";
    echo "<li>Нажмите кнопку \"Выполнить\"</li>";
    echo "</ol>";
}
?>