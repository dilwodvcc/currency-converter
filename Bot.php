<?php

require 'vendor/autoload.php'; // Guzzle'ni yuklash uchun

use GuzzleHttp\Client;

// Telegram Bot klassi
class Bot
{
    private $token;
    private $apiUrl;
    private $client;

    public function __construct($token)
    {
        $this->token = $token;
        $this->apiUrl = "https://api.telegram.org/bot$this->token/";
        $this->client = new Client();
    }

    public function makeRequest($method, $params = [])
    {
        $url = $this->apiUrl . $method;

        try {
            $response = $this->client->post($url, [
                'form_params' => $params
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendMessage($chatId, $message)
    {
        return $this->makeRequest('sendMessage', [
            'chat_id' => $chatId,
            'text' => $message
        ]);
    }

    public function getUpdates()
    {
        return $this->makeRequest('getUpdates');
    }
}

// Valyuta kurslarini olish funksiyasi
function getCurrencyRates()
{
    $client = new Client();
    $apiUrl = "https://cbu.uz/uz/arkhiv-kursov-valyut/json/";

    try {
        $response = $client->get($apiUrl);
        $currencyData = json_decode($response->getBody(), true);

        if (!$currencyData) {
            return "Valyuta kurslarini olishda xato yuz berdi.";
        }

        $message = "Bugungi valyuta kurslari:\n";
        foreach ($currencyData as $rate) {
            if ($rate['Ccy'] === 'USD' || $rate['Ccy'] === 'EUR') {
                $message .= "1 " . $rate['Ccy'] . " = " . $rate['Rate'] . " UZS\n";
            }
        }
        return $message;
    } catch (Exception $e) {
        return "Xato yuz berdi: " . $e->getMessage();
    }
}

// Xabar yuborish logikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $botToken = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';
    $bot = new Bot($botToken);

    $customMessage = htmlspecialchars($_POST['customMessage']);
    $currencyRates = getCurrencyRates();

    // Xabar tayyorlash
    $fullMessage = $customMessage . "\n\n" . $currencyRates;

    // Chat ID-ni kiritishingiz kerak
    $chatId = '<YOUR_CHAT_ID>'; // Bu yerga haqiqiy chat_id ni kiriting

    $result = $bot->sendMessage($chatId, $fullMessage);

    if ($result['ok']) {
        echo "<p class='response-message'>Xabar muvaffaqiyatli yuborildi!</p>";
    } else {
        echo "<p class='response-message error'>Xabar yuborishda xato: " . $result['error'] . "</p>";
    }
}

?>
