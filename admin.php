<?php
require_once 'config.php';

// Простая проверка пароля (для демонстрации)
$auth_file = 'admin_auth.txt';
if (!file_exists($auth_file)) {
    file_put_contents($auth_file, password_hash('admin123', PASSWORD_DEFAULT));
}

session_start();

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in']) && isset($_POST['password'])) {
    $stored_hash = file_get_contents($auth_file);
    if (password_verify($_POST['password'], $stored_hash)) {
        $_SESSION['admin_logged_in'] = true;
    }
}

if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Вход в админ-панель</title>
        <style>
            body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
            .login-form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
            input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
            button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Вход в админ-панель</h2>
            <form method="POST">
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit">Войти</button>
            </form>
            <p style="margin-top: 10px; font-size: 12px; color: #666;">Пароль по умолчанию: <strong>admin123</strong></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Получаем все бронирования
$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$bookings = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель - Бронирования</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; }
        .container { max-width: 1400px; margin: 20px auto; padding: 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card .number { font-size: 32px; font-weight: bold; color: #007bff; }
        .bookings-table { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f5f5f5; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #ffc107; color: #000; }
        .status-confirmed { background: #28a745; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        .status-completed { background: #17a2b8; color: white; }
        .logout { float: right; background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; }
        .logout:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Управление бронированиями
            <a href="?logout=1" class="logout" onclick="return confirm('Выйти?')">Выйти</a>
        </h1>
        
        <?php
        if (isset($_GET['logout'])) {
            session_destroy();
            header('Location: admin.php');
            exit;
        }
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Всего бронирований</h3>
                <div class="number"><?php echo count($bookings); ?></div>
            </div>
            <div class="stat-card">
                <h3>Ожидают подтверждения</h3>
                <div class="number">
                    <?php echo count(array_filter($bookings, function($b) { return $b['status'] == 'pending'; })); ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Подтверждено</h3>
                <div class="number">
                    <?php echo count(array_filter($bookings, function($b) { return $b['status'] == 'confirmed'; })); ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Общая выручка</h3>
                <div class="number">
                    <?php 
                    $total = array_sum(array_column($bookings, 'total_price'));
                    echo number_format($total, 0, '', ' ') . ' ₽';
                    ?>
                </div>
            </div>
        </div>
        
        <div class="bookings-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Лодка</th>
                        <th>Клиент</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Начало</th>
                        <th>Конец</th>
                        <th>Часов</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Дата брони</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="11" style="text-align: center;">Нет бронирований</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['boat_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                                <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                <td><?php echo $booking['start_date']; ?></td>
                                <td><?php echo $booking['end_date']; ?></td>
                                <td><?php echo $booking['hours']; ?></td>
                                <td><?php echo number_format($booking['total_price'], 0, '', ' '); ?> ₽</td>
                                <td>
                                    <span class="status status-<?php echo $booking['status']; ?>">
                                        <?php 
                                        $statuses = ['pending' => 'Ожидает', 'confirmed' => 'Подтверждено', 'cancelled' => 'Отменено', 'completed' => 'Завершено'];
                                        echo $statuses[$booking['status']];
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo $booking['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>