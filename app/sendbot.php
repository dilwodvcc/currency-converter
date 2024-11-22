<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require 'Bot.php';
require 'vendor/autoload.php';
require 'view/Currency.php';

use GuzzleHttp\Client;

$bot = new Bot("7534341779:AAE5jziTYpkfDnbMxL4XSCvl8bW373JJCng");
$currency = new Currency();
$currencies = $currency->getCurrencies();

$client = new Client();

$update = json_decode(file_get_contents('php://input'));

if (isset($update)) {
    $message = $update->message;

    if ($message) {
        $from_id = $message->from->id;
        $username = $message->chat->username ?? '';
        $firstName = $message->from->first_name ?? 'Foydalanuvchi';

        $bot->saveUser($from_id, $firstName);
        $text = $message->text;

        // Handle different commands with a switch statement
        switch (true) {
            case ($text == '/start'):
                handleStart($bot, $from_id, $username);
                break;
            case ($text == '/currency' || $text == 'Valyuta ayrboshlash'):
                handleCurrencyInstruction($bot, $from_id);
                break;
            case (preg_match('/\d+\.?\d*\s+[A-Z]{3}\s+to\s+[A-Z]{3}/i', $text)):
                handleCurrencyConversion($bot, $currency, $message, $currencies);
                break;
            case ($text == '/weather'):
            case ($text == 'Ob havo malumoti'):
                handleWeather($bot, $client, $from_id);
                break;
            case ($text == '/namozvaqti'):
            case ($text == 'Namoz vaqtlari'):
                handlePrayerTimes($bot, $client, $from_id);
                break;
            default:
                $bot->makeRequest('sendMessage', [
                    'chat_id' => $from_id,
                    'text' => "Kechirasiz, buyruq noto‘g‘ri. Quyidagilardan birini sinab ko‘ring:\n\"/currency\", \"/weather\", \"/namozvaqti\".",
                ]);
        }
    }

    if (isset($update->callback_query)) {
        $callbackQuery = $update->callback_query;
        handleCallbackQuery($bot, $callbackQuery, $currencies);
    }
}

// Functions

function handleStart($bot, $from_id, $username) {
    error_log("Starting with chat_id: " . $from_id); // chat_id ni log qiling

    $bot->saveUser($from_id, $username);
    $reply_keyboard = [
        'keyboard' => [
            [
                ['text' => 'Ob havo malumoti'],
                ['text' => 'Valyuta ayrboshlash'],
                ['text' => 'Namoz vaqtlari'],
            ]
        ],
        'resize_keyboard' => true,
    ];

    $bot->makeRequest('sendMessage', [
        'chat_id' => $from_id,
        'text' => <<<EOT
Xush kelibsiz! Bu Abdullohning Telegram botidir. Buyruqlar:
- "/currency" - Valyuta kurslari va konvertatsiya qilish bo‘yicha ma’lumot uchun.
- "/weather" - Ob-havo ma'lumotlari uchun.
- "/namozvaqti" - Namoz vaqtlari uchun.
EOT,
        'reply_markup' => json_encode($reply_keyboard),
        'parse_mode' => 'HTML',
    ]);
}


function handleCurrencyInstruction($bot, $from_id) {
    $text = <<<EOT
Valyuta konvertatsiyasi uchun quyidagicha foydalaning:
Misol: 100 USD to UZS
Bu formatda valyuta miqdorini, qaysi valyutadan qaysi valyutaga o‘tkazish kerakligini yozing.
EOT;

    $bot->makeRequest('sendMessage', [
        'chat_id' => $from_id,
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

function handleWeather($bot, $client, $from_id) {
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
        'chat_id' => $from_id,
        'text' => $text,
    ]);
}

function handlePrayerTimes($bot, $client, $from_id) {
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
        'chat_id' => $from_id,
        'text' => $text,
    ]);
}

function handleCallbackQuery($bot, $callbackQuery, $currencies) {
    $data = $callbackQuery->data;
    $from_id = $callbackQuery->message->chat->id;

    if (strpos($data, 'currency_') === 0) {
        $selectedCurrency = str_replace('currency_', '', $data);
        if (isset($currencies[$selectedCurrency])) {
            $rate = $currencies[$selectedCurrency];
            $bot->makeRequest('sendMessage', [
                'chat_id' => $from_id,
                'text' => "$selectedCurrency: $rate ",
            ]);
        } else {
            $bot->makeRequest('sendMessage', [
                'chat_id' => $from_id,
                'text' => "Kechirasiz, valyuta topilmadi.",
            ]);
        }
    }
}
echo 'Ishladi';