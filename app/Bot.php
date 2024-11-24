<?php
require "vendor/autoload.php";
require "app/DB.php";
use GuzzleHttp\Client;

class Bot {
    const API_URL = 'https://api.telegram.org/bot';
    private $token = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';
    private $client;

    public function __construct() {
        $this->client = new Client([
            'base_uri' => self::API_URL . $this->token . '/',
            'timeout' => 2.0,
        ]);
    }

    public function makeRequest($method, $data = []) {
        $response = $this->client->request('POST', $method, ['json' => $data]);
        return json_decode($response->getBody(), true);
    }

    public function saveUser($chat_id, $username): bool {
        $db = new DB();
        if ($this->getUser($chat_id)) {
            return false;
        }
        $query = "INSERT INTO user (chat_id, username) VALUES (:chat_id, :username)";
        return $db->conn->prepare($query)->execute([':chat_id' => $chat_id, ':username' => $username]);
    }

    public function getUser($chat_id): bool|array {
        $db = new DB();
        $query = "SELECT * FROM user WHERE chat_id = :chat_id";
        $stmt = $db->conn->prepare($query);
        $stmt->execute([':chat_id' => $chat_id]);
        return $stmt->fetch();
    }
}

// Botdan kelgan yangilanishni qayta ishlash
$bot = new Bot();
$update = json_decode(file_get_contents('php://input'));

if (isset($update->message)) {
    $message = $update->message;
    $from_id = $message->from->id;
    $username = $message->from->username ?? 'Foydalanuvchi';
    $text = $message->text;

    $bot->saveUser($from_id, $username);

    if ($text === '/start') {
        $bot->makeRequest('sendMessage', [
            'chat_id' => $from_id,
            'text' => "Xush kelibsiz! Botga ulanganingiz uchun rahmat!",
        ]);
    }
}

// Veb-sahifa
$db = new DB();
$users = $db->conn->query("SELECT * FROM user")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Bot Ma'lumotlari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Telegram Bot Ma'lumotlari</h1>
    <table class="table table-bordered mt-4">
        <thead>
        <tr>
            <th>ID</th>
            <th>Chat ID</th>
            <th>Foydalanuvchi nomi</th>
            <th>Qo'shilgan vaqti</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['chat_id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center mt-4">
        <a href="https://t.me/SOFTWER_BOT" class="btn btn-primary btn-lg" target="_blank">Botga o'tish</a>
    </div>
</div>
</body>
</html>
