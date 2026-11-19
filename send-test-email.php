<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест отправки email - Прокат лодок</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🚤 Прокат лодок</h1>
            <nav>
                <a href="index.html">Главная</a>
                <a href="boats.html">Лодки</a>
                <a href="rental-form.html">Забронировать</a>
                <a href="send-test-email.php">Тест почты</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="test-email-container">
                <h2>Тестирование отправки email (имитация)</h2>
                
                <div class="info-box">
                    <h3>📧 Как работает тестовый режим:</h3>
                    <p>В режиме имитации письма не отправляются реально, а сохраняются в файл <strong>email_log.txt</strong></p>
                    <p>Вы можете просмотреть содержимое логов в этом файле.</p>
                </div>
                
                <form id="testEmailForm" class="test-form">
                    <div class="form-group">
                        <label for="test_email">Email получателя:</label>
                        <input type="email" id="test_email" name="test_email" placeholder="test@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="test_subject">Тема письма:</label>
                        <input type="text" id="test_subject" name="test_subject" value="Тестовое письмо" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="test_message">Сообщение:</label>
                        <textarea id="test_message" name="test_message" rows="5" required>Это тестовое письмо с сайта Прокат лодок.</textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">Отправить тестовое письмо</button>
                </form>
                
                <div id="testResult" style="margin-top: 20px;"></div>
                
                <div class="log-viewer">
                    <h3>📝 Последние отправленные письма (лог):</h3>
                    <div class="log-content" id="logContent">
                        <?php
                        $logFile = 'email_log.txt';
                        if (file_exists($logFile)) {
                            $logs = file($logFile);
                            $lastLogs = array_slice($logs, -10); // Показываем последние 10 записей
                            foreach ($lastLogs as $log) {
                                echo "<div class='log-entry'>" . htmlspecialchars($log) . "</div>";
                            }
                        } else {
                            echo "<p>Лог-файл еще не создан. Отправьте тестовое письмо.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Прокат лодок. Все права защищены.</p>
        </div>
    </footer>

    <script>
    document.getElementById('testEmailForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('test_email').value;
        const subject = document.getElementById('test_subject').value;
        const message = document.getElementById('test_message').value;
        const resultDiv = document.getElementById('testResult');
        
        try {
            const response = await fetch('send-test-email-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&subject=${encodeURIComponent(subject)}&message=${encodeURIComponent(message)}`
            });
            
            const result = await response.json();
            
            if (result.success) {
                resultDiv.innerHTML = `
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;">
                        ✅ ${result.message}<br>
                        <small>Письмо сохранено в файл email_log.txt</small>
                    </div>
                `;
                // Обновляем лог
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                resultDiv.innerHTML = `
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
                        ❌ ${result.message}
                    </div>
                `;
            }
        } catch (error) {
            resultDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
                    ❌ Ошибка при отправке
                </div>
            `;
        }
    });
    </script>
</body>
</html>