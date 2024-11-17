<?php
class Bot
{
    const API_URL = 'https://api.telegram.org/bot';
    private $token = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';

    public function makeRequest($method, $data=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $this->token . '/' . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
        var_dump($response);
    }
}

$bot = new Bot();
$bot->makeRequest('sendMessage', ['chat_id' => 6177186948, 'text'=>'hello I am bot']);
$bot->makeRequest('sendVideo', ['chat_id' => 6177186948, 'video'=>'https://www.w3schools.com/html/mov_bbb.mp4']);