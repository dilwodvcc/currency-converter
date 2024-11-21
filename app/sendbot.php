<?php
require 'Bot.php';
require '../vendor/autoload.php'; // Guzzle uchun autoload
require '../view/Currency.php';
require '../user_tg/saveUser.php';
use GuzzleHttp\Client;

$botToken = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';
$bot = new Bot($botToken);

$currency = new Currency();
$currencies = $currency->getCurrencies();

$client = new Client(); // Guzzle mijozini yaratish

$update = json_decode(file_get_contents('php://input'));
$message = $update->message ?? null;

if ($message) {
    $chatId = $message->chat->id;
    $firstName = $message->chat->first_name ?? null;
    $lastName = $message->chat->last_name ?? null;
    $username = $message->chat->username ?? null;

    saveUser($chatId, $firstName, $lastName, $username);

    $text = $message->text;

    switch (true) {
        case $text == '/start':
            handleStart($bot, $chatId);
            break;

        case $text == '/currency':
            handleCurrencyInstruction($bot, $chatId);
            break;

        case preg_match('/\d+\.?\d*\s+[A-Z]{3}\s+to\s+[A-Z]{3}/i', $text):
            handleCurrencyConversion($bot, $currency, $message, $currencies);
            break;

        case $text == '/weather':
            handleWeather($bot, $client, $chatId);
            break;

        case $text == '/namozvaqti':
            handlePrayerTimes($bot, $client, $chatId);
            break;

        default:
            $bot->makeRequest('sendMessage', [
                'chat_id' => $chatId,
                'text' => 'Kechirasiz, buyruq noto‘g‘ri. Quyidagilardan birini sinab ko‘ring:\n"/currency", "/weather", "/namozvaqti".',
            ]);
    }
}

if (isset($update->callback_query)) {
    $callbackQuery = $update->callback_query;
    handleCallbackQuery($bot, $callbackQuery, $currencies);
}


// Funksiyalar

function handleStart($bot, $chatId) {
    $text = <<<EOT
Xush kelibsiz! Bu Abdullohning Telegram botidir. Buyruqlar:
- "/currency" - Valyuta kurslari va konvertatsiya qilish bo‘yicha ma’lumot uchun.
- "/weather" - Ob-havo ma'lumotlari uchun.
- "/namozvaqti" - Namoz vaqtlari uchun.
EOT;

    $bot->makeRequest('sendMessage', [
        'chat_id' => $chatId,
        'text' => $text,
    ]);
}

function handleCurrencyInstruction($bot, $chatId) {
    $text = <<<EOT
Valyuta konvertatsiyasi uchun quyidagicha foydalaning:
Misol: 100 USD to UZS
Bu formatda valyuta miqdorini, qaysi valyutadan qaysi valyutaga o‘tkazish kerakligini yozing.
EOT;

    $bot->makeRequest('sendMessage', [
        'chat_id' => $chatId,
        'text' => $text,
    ]);
}

function handleCurrencyConversion($bot, $currency, $message, $currencies) {
    $text = $message->text;
    $pattern = '/(\d+\.?\d*)\s+([A-Z]{3})\s+to\s+([A-Z]{3})/i';
    if (preg_match($pattern, $text, $matches)) {
        $amount = floatval($matches[1]);
        $fromCurrency = strtoupper($matches[2]);
        $toCurrency = strtoupper($matches[3]);

        if (!isset($currencies[$fromCurrency]) || !isset($currencies[$toCurrency])) {
            $responseText = "Kiritilgan valyutalar noto‘g‘ri. Iltimos, to‘g‘ri formatda kiriting.";
        } else {
            $convertedAmount = $currency->exchange($amount, $fromCurrency, $toCurrency);
            $responseText = "$amount $fromCurrency -> $convertedAmount $toCurrency";
        }
    } else {
        $responseText = "Iltimos, quyidagi formatdan foydalaning:\n100 USD to UZS";
    }

    $bot->makeRequest('sendMessage', [
        'chat_id' => $message->chat->id,
        'text' => $responseText,
    ]);
}

function handleWeather($bot, $client, $chatId) {
    $city = "Tashkent";
    $apiKey = '570d6bcba80a484fabbf080f31f3f185';
    $weatherUrl = "https://api.openweathermap.org/data/2.5/weather";

    try {
        $response = $client->get($weatherUrl, [
            'query' => [
                'q' => $city,
                'units' => 'metric',
                'appid' => $apiKey,
            ],
        ]);
        $weatherData = json_decode($response->getBody(), true);

        if ($weatherData['cod'] == 200) {
            $temp = $weatherData['main']['temp'];
            $description = ucfirst($weatherData['weather'][0]['description']);
            $humidity = $weatherData['main']['humidity'];
            $pressure = $weatherData['main']['pressure'];
            $windSpeed = $weatherData['wind']['speed'];
            $windDeg = $weatherData['wind']['deg'];

            $text = <<<EOT
Shahar: $city
Harorat: $temp °C
Holati: $description
Namlik: $humidity%
Bosim: $pressure hPa
Shamol Tezligi: $windSpeed m/s
Shamol Yo'nalishi: $windDeg °
EOT;
        } else {
            $text = "Ob-havo ma'lumotlarini olib bo'lmadi.";
        }
    } catch (\Exception $e) {
        $text = "Xato yuz berdi: " . $e->getMessage();
    }

    $bot->makeRequest('sendMessage', [
        'chat_id' => $chatId,
        'text' => $text,
    ]);
}

function handlePrayerTimes($bot, $client, $chatId) {
    $city = "Tashkent";
    $country = "Uzbekistan";
    $apiUrl = "https://api.aladhan.com/v1/timingsByCity";

    try {
        $response = $client->get($apiUrl, [
            'query' => [
                'city' => $city,
                'country' => $country,
                'method' => 2,
            ],
        ]);
        $prayerData = json_decode($response->getBody(), true);

        if ($prayerData['code'] == 200) {
            $timings = $prayerData['data']['timings'];
            $text = <<<EOT
Shahar: $city
Bomdod: {$timings['Fajr']}
Peshin: {$timings['Dhuhr']}
Asr: {$timings['Asr']}
Shom: {$timings['Maghrib']}
Xufton: {$timings['Isha']}
EOT;
        } else {
            $text = "Namoz vaqtlarini olishda xato yuz berdi.";
        }
    } catch (\Exception $e) {
        $text = "Xato yuz berdi: " . $e->getMessage();
    }

    $bot->makeRequest('sendMessage', [
        'chat_id' => $chatId,
        'text' => $text,
    ]);
}

function handleCallbackQuery($bot, $callbackQuery, $currencies) {
    $data = $callbackQuery->data;
    $chatId = $callbackQuery->message->chat->id;

    if (strpos($data, 'currency_') === 0) {
        $selectedCurrency = str_replace('currency_', '', $data);
        if (isset($currencies[$selectedCurrency])) {
            $rate = $currencies[$selectedCurrency];
            $bot->makeRequest('sendMessage', [
                'chat_id' => $chatId,
                'text' => "$selectedCurrency: $rate ",
            ]);
        } else {
            $bot->makeRequest('sendMessage', [
                'chat_id' => $chatId,
                'text' => "Kechirasiz, valyuta topilmadi.",
            ]);
        }
    }
}
