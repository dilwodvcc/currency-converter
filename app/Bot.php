<?php
require "vendor/autoload.php";
require "app/DB.php";
use GuzzleHttp\Client;

class Bot {
    const API_URL = 'https://api.telegram.org/bot';
    private $token = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';
    public $client;
    public function makeRequest($method, $data = []) {
        $this->client = new Client([
            'base_uri' => self::API_URL . $this->token . '/',
            'timeout'  => 2.0,
        ]);

        $request = $this->client->request('POST', $method, ['json' => $data]);

        return json_decode($request->getBody()->getContents());
    }
    public function saveUser($chat_id, $username): bool {
        var_dump($this->getUser($chat_id));
        if($this->getUser($chat_id)) {
            return false;
        }
        $query = "INSERT INTO user (chat_id, username) VALUES (:chat_id, :username)";
        $db = new DB();
        return $db->conn->prepare($query)->execute([
            ':chat_id' => $chat_id,
            ':username' => $username,
        ]);
    }
    public function getUser($chat_id):bool|array {
        $query = "SELECT * FROM user WHERE chat_id = :chat_id";
        $db = new DB();
        $stmt= $db->conn->prepare($query);
        $stmt->execute([
            ':chat_id' => $chat_id,
        ]);
        return $stmt->fetch();
    }
}
