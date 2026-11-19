<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? 'Тестовое письмо';
    $message = $_POST['message'] ?? '';
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email не указан']);
        exit;
    }
    
    $fullMessage = "
        <html>
        <head><style>body{font-family:Arial,sans-serif;}</style></head>
        <body>
            <h3>Тестовое письмо с сайта Прокат лодок</h3>
            <p>$message</p>
            <hr>
            <small>Это тестовое письмо отправлено в режиме имитации.</small>
        </body>
        </html>
    ";
    
    $result = sendEmail($email, $subject, $fullMessage);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Тестовое письмо успешно сохранено в лог']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении письма']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}
?>