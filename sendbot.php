<?php
require 'Bot.php';
require 'vendor/autoload.php'; // Guzzle uchun autoload
require 'view/Currency.php';

use GuzzleHttp\Client;

$botToken = '7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng';
$bot = new Bot($botToken);

$currency = new Currency();
$currencies = $currency->getCurrencies();

$client = new Client(); // Guzzle mijozini yaratish

$update = json_decode(file_get_contents('php://input'));
$message = $update->message ?? null;

if (isset($message->text)) {
    $text = $message->text;

    if ($text == '/currency') {
        $keyboard = [];
        foreach ($currencies as $currency => $rate) {
            $keyboard[] = [['text' => $currency, 'callback_data' => "currency_$currency"]];
        }

        $bot->makeRequest('sendMessage', [
            'chat_id' => $message->chat->id,
            'text' => "Valyutani tanlang:",
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ]),
        ]);
    } elseif ($text == '/start') {
        $bot->makeRequest('sendMessage', [
            'chat_id' => $message->chat->id,
            'text' => 'Xush kelibsiz! Bu Abdullohning Telegram botidir. Buyruqlar:\n"/currency" - Valyuta kurslari uchun\n"/weather" - Ob-havo ma\'lumotlari uchun\n"/namozvaqti" - Namoz vaqtlari uchun.',
        ]);
    }
}

if (isset($update->callback_query)) {
    $callbackQuery = $update->callback_query;
    $data = $callbackQuery->data;
    $chatId = $callbackQuery->message->chat->id;

    if (strpos($data, 'currency_') === 0) {
        // Tanlangan valyutani aniqlash
        $selectedCurrency = str_replace('currency_', '', $data);
        if (isset($currencies[$selectedCurrency])) {
            $rate = $currencies[$selectedCurrency];
            $bot->makeRequest('sendMessage', [
                'chat_id' => $chatId,
                'text' => "$selectedCurrency: $rate UZS",
            ]);
        } else {
            $bot->makeRequest('sendMessage', [
                'chat_id' => $chatId,
                'text' => "Kechirasiz, valyuta topilmadi.",
            ]);
        }
    }
}

if ($message && $message->text == '/weather') {
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

            $formattedWeather = "Shahar: $city\n";
            $formattedWeather .= "Harorat: $temp °C\n";
            $formattedWeather .= "Holati: $description\n";
            $formattedWeather .= "Namlik: $humidity%\n";
            $formattedWeather .= "Bosim: $pressure hPa\n";
            $formattedWeather .= "Shamol Tezligi: $windSpeed m/s\n";
            $formattedWeather .= "Shamol Yo'nalishi: $windDeg °\n";
        } else {
            $formattedWeather = "Ob-havo ma'lumotlarini olib bo'lmadi.";
        }
    } catch (\Exception $e) {
        $formattedWeather = "Xato yuz berdi: " . $e->getMessage();
    }

    $bot->makeRequest('sendMessage', [
        'chat_id' => $message->chat->id,
        'text' => $formattedWeather,
    ]);
}

if ($message && $message->text == '/namozvaqti') {
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
            $formattedPrayerTimes = "Shahar: $city\n";
            $formattedPrayerTimes .= "Bomdod: {$timings['Fajr']}\n";
            $formattedPrayerTimes .= "Peshin: {$timings['Dhuhr']}\n";
            $formattedPrayerTimes .= "Asr   : {$timings['Asr']}\n";
            $formattedPrayerTimes .= "Shom  : {$timings['Maghrib']}\n";
            $formattedPrayerTimes .= "Xufton: {$timings['Isha']}\n";
        } else {
            $formattedPrayerTimes = "Namoz vaqtlarini olishda xato yuz berdi.";
        }
    } catch (\Exception $e) {
        $formattedPrayerTimes = "Xato yuz berdi: " . $e->getMessage();
    }

    $bot->makeRequest('sendMessage', [
        'chat_id' => $message->chat->id,
        'text' => $formattedPrayerTimes,
    ]);
}
