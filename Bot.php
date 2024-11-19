<!--<!DOCTYPE html>-->
<!--<html lang="uz">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>Telegram Bot Xabar Yuborish</title>-->
<!--    <style>-->
<!--        body {-->
<!--            font-family: Arial, sans-serif;-->
<!--            background-color: #f4f4f9;-->
<!--            margin: 0;-->
<!--            padding: 0;-->
<!--            display: flex;-->
<!--            justify-content: center;-->
<!--            align-items: center;-->
<!--            min-height: 100vh;-->
<!--        }-->
<!---->
<!--        .container {-->
<!--            background: #fff;-->
<!--            padding: 20px;-->
<!--            border-radius: 10px;-->
<!--            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);-->
<!--            max-width: 400px;-->
<!--            width: 100%;-->
<!--            text-align: center;-->
<!--        }-->
<!---->
<!--        h1 {-->
<!--            font-size: 24px;-->
<!--            margin-bottom: 20px;-->
<!--            color: #333;-->
<!--        }-->
<!---->
<!--        textarea {-->
<!--            width: 100%;-->
<!--            padding: 10px;-->
<!--            border: 1px solid #ddd;-->
<!--            border-radius: 5px;-->
<!--            font-size: 16px;-->
<!--            resize: none;-->
<!--            box-sizing: border-box;-->
<!--        }-->
<!---->
<!--        textarea:focus {-->
<!--            outline: none;-->
<!--            border-color: #007BFF;-->
<!--            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);-->
<!--        }-->
<!---->
<!--        button {-->
<!--            background-color: #007BFF;-->
<!--            color: #fff;-->
<!--            border: none;-->
<!--            padding: 10px 20px;-->
<!--            font-size: 16px;-->
<!--            border-radius: 5px;-->
<!--            cursor: pointer;-->
<!--            margin-top: 10px;-->
<!--        }-->
<!---->
<!--        button:hover {-->
<!--            background-color: #0056b3;-->
<!--        }-->
<!---->
<!--        .response-message {-->
<!--            margin-top: 15px;-->
<!--            font-size: 14px;-->
<!--            color: green;-->
<!--        }-->
<!--        .response-message.error {-->
<!--            color: red;-->
<!--        }-->
<!--        .response-message.warning {-->
<!--            color: #FAB12F;-->
<!--        }-->
<!--    </style>-->
<!--</head>-->
<!--<body>-->
<!--    <div class="container">-->
<!--        <h1>Telegram Botga Xabar Yuborish</h1>-->
<!--        <form method="POST" action="">-->
<!--            <textarea name="customMessage" placeholder="Xabaringizni kiriting..." rows="5" cols="40"></textarea>-->
<!--            <br><br>-->
<!--            <button type="submit">Xabar yuborish</button>-->
<!--        </form>-->
<!--        -->
<!--    </div>-->
<!--</body>-->
<!--</html>-->

<?php

$botToken = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';

// Bot klassi
class Bot
{
    private $token;
    private $apiUrl;

    public function __construct($token)
    {
        $this->token = $token;
        $this->apiUrl = "https://api.telegram.org/bot$this->token/";
    }

    public function makeRequest($method, $params = [])
    {
        $url = $this->apiUrl . $method;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
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
function getCurrencyRates()
{
    $currencyData = file_get_contents("https://cbu.uz/uz/arkhiv-kursov-valyut/json/");
    $currency = json_decode($currencyData, true);

    if (!$currency) {
        return "Valyuta kurslarini olishda xato yuz berdi.";
    }

    $message = "Bugungi valyuta kurslari:\n";
    foreach ($currency as $rate) {
        if ($rate['Ccy'] === 'USD' || $rate['Ccy'] === 'EUR') {
            $message .= "1 " . $rate['Ccy'] . " = " . $rate['Rate'] . " UZS\n";
        }
    }
    return $message;
}

?>
