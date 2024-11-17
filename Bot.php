<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Bot Xabar Yuborish</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            resize: none;
            box-sizing: border-box;
        }

        textarea:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .response-message {
            margin-top: 15px;
            font-size: 14px;
            color: green;
        }
        .response-message.error {
            color: red;
        }
        .response-message.warning {
            color: #FAB12F;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Telegram Botga Xabar Yuborish</h1>
        <form method="POST" action="">
            <textarea name="customMessage" placeholder="Xabaringizni kiriting..." rows="5" cols="40"></textarea>
            <br><br>
            <button type="submit">Xabar yuborish</button>
        </form>

        <!-- Javob xabarini bu yerda ko'rsatamiz -->
        <?php
        $botToken = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';
        $chatId = "6177186948";

        function getCurrencyRates() {
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

        function sendMessageToTelegram($botToken, $chatId, $message) {
            $apiUrl = "https://api.telegram.org/bot$botToken/sendMessage";

            $data = [
                'chat_id' => $chatId,
                'text' => $message,
            ];

            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                ],
            ];

            $context = stream_context_create($options);
            $result = file_get_contents($apiUrl, false, $context);

            return $result ? true : false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customMessage'])) {
            $customMessage = htmlspecialchars($_POST['customMessage']);
            if (!empty($customMessage)) {
                $sendResult = sendMessageToTelegram($botToken, $chatId, $customMessage);
                if ($sendResult) {
                    echo '<div class="response-message">Xabar yuborildi!</div>';
                } else {
                    echo '<div class="response-message error">Xabar yuborishda xatolik yuz berdi.</div>';
                }
            } else {
                echo '<div class="response-message warning">Iltimos, xabarni to\'ldiring.</div>';
            }
        } else {
            $currencyMessage = getCurrencyRates();
            $sendResult = sendMessageToTelegram($botToken, $chatId, $currencyMessage);
            if ($sendResult) {
                echo '<div class="response-message">Valyuta kurslari yuborildi!</div>';
            } else {
                echo '<div class="response-message error">Valyuta kurslarini yuborishda xato yuz berdi.</div>';
            }
        }
        ?>
    </div>
</body>
</html>