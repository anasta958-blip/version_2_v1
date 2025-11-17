<?php


header('Content-Type: application/json');


$BOT_TOKEN = '8525977303:AAH8rQJkmf5OOQoiCR13rVYIFRAdGk3WnTI';  
$CHAT_ID   = '-1003416825607';                            



$name      = trim($_POST['name'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$messenger = $_POST['messenger'] ?? '';


if (!$name || !$phone || !$messenger) {
    echo json_encode(['ok' => false, 'error' => 'Заполните все поля']);
    exit;
}

$name      = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$phone     = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$messenger = $messenger === 'whatsapp' ? 'WhatsApp' : 'Telegram';


$source = trim($_POST['source'] ?? '');

$message = "Новая заявка!\n\n" .
           "Имя: <b>$name</b>\n" .
           "Телефон: <code>$phone</code>\n" .
           "Мессенджер: <b>$messenger</b>\n";

if ($source == '1') $message .= "Дополнительно: скидка 10%\n";

$message .= "\nДата: " . date('d.m.Y H:i');


$url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'chat_id'    => $CHAT_ID,
        'text'       => $message,
        'parse_mode' => 'HTML'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


if ($httpCode == 200 && $response) {
    $result = json_decode($response, true);
    if ($result['ok'] ?? false) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Ошибка Telegram: ' . ($result['description'] ?? 'Unknown')]);
    }
} else {
    echo json_encode(['ok' => false, 'error' => 'Сервер недоступен']);
}

?>
