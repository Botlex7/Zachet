<?php
require_once 'config.php';

header('Content-Type: application/json');

// Проверяем существование таблицы
try {
    $pdo->query("SELECT 1 FROM bookings LIMIT 1");
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Таблица бронирований не найдена. Пожалуйста, запустите файл setup.php для установки базы данных.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $boat_name = $_POST['boat_name'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $hours = $_POST['hours'] ?? 0;
    $total_price = $_POST['total_price'] ?? 0;
    $comments = $_POST['comments'] ?? '';
    
    // Валидация
    $errors = [];
    if (empty($boat_name)) $errors[] = 'Выберите лодку';
    if (empty($customer_name)) $errors[] = 'Введите ваше имя';
    if (empty($email)) $errors[] = 'Введите email';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите корректный email';
    if (empty($phone)) $errors[] = 'Введите телефон';
    if (empty($start_date)) $errors[] = 'Выберите дату начала';
    if (empty($end_date)) $errors[] = 'Выберите дату окончания';
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    try {
        // Проверяем, не забронирована ли уже лодка на это время
        $check_sql = "SELECT COUNT(*) FROM bookings 
                      WHERE boat_name = ? 
                      AND status != 'cancelled'
                      AND ((start_date BETWEEN ? AND ?) OR (end_date BETWEEN ? AND ?))";
        
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$boat_name, $start_date, $end_date, $start_date, $end_date]);
        $conflict_count = $check_stmt->fetchColumn();
        
        if ($conflict_count > 0) {
            echo json_encode(['success' => false, 'message' => 'Эта лодка уже забронирована на выбранное время. Пожалуйста, выберите другое время.']);
            exit;
        }
        
        // Сохраняем бронирование в базу данных
        $sql = "INSERT INTO bookings (boat_name, customer_name, email, phone, start_date, end_date, hours, total_price, comments, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$boat_name, $customer_name, $email, $phone, $start_date, $end_date, $hours, $total_price, $comments]);
        
        $booking_id = $pdo->lastInsertId();
        
        // Отправляем email клиенту
        $subject = "Подтверждение бронирования лодки #$booking_id";
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #007bff; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; }
                    .booking-details { background: #f4f4f4; padding: 15px; margin: 20px 0; border-radius: 5px; }
                    .footer { background: #333; color: white; padding: 10px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Прокат лодок</h2>
                    </div>
                    <div class='content'>
                        <h3>Уважаемый(ая) $customer_name!</h3>
                        <p>Ваше бронирование успешно создано.</p>
                        
                        <div class='booking-details'>
                            <h4>Детали бронирования:</h4>
                            <p><strong>Номер бронирования:</strong> #$booking_id</p>
                            <p><strong>Лодка:</strong> $boat_name</p>
                            <p><strong>Дата и время начала:</strong> $start_date</p>
                            <p><strong>Дата и время окончания:</strong> $end_date</p>
                            <p><strong>Количество часов:</strong> $hours</p>
                            <p><strong>Итого к оплате:</strong> $total_price ₽</p>
                            " . (!empty($comments) ? "<p><strong>Комментарии:</strong> $comments</p>" : "") . "
                        </div>
                        
                        <p>Статус: <strong>Ожидает подтверждения</strong></p>
                        <p>Наш менеджер свяжется с вами в ближайшее время для подтверждения бронирования.</p>
                        
                        <p>С уважением,<br>Команда Прокат лодок</p>
                        <p><small>Это автоматическое сообщение, пожалуйста, не отвечайте на него.</small></p>
                    </div>
                    <div class='footer'>
                        <p>© 2026 Прокат лодок. Все права защищены.</p>
                        <p>Телефон: +7 (999) 123-45-67</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        sendEmail($email, $subject, $message);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Бронирование успешно создано! На ваш email отправлено подтверждение.',
            'booking_id' => $booking_id
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}
?>