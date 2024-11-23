<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Currency {
    const CURRENCY_API_URL = "https://cbu.uz/uz/arkhiv-kursov-valyut/json/";

    private array $currencies = [];

    public function __construct() {
        $client = new Client();

        try {
            $response = $client->get(self::CURRENCY_API_URL);
            $output = $response->getBody()->getContents();
            $this->currencies = json_decode($output, true);
        } catch (\Exception $e) {
            echo "API dan valyutalar ma'lumotini olishda xatolik yuz berdi: " . $e->getMessage();
            $this->currencies = [];
        }
    }

    public function getCurrencies(): array {
        $separated_data = ['UZS' => 1]; // Milliy valyutani qo'shamiz
        foreach ($this->currencies as $currency) {
            $separated_data[$currency['Ccy']] = $currency['Rate'];
        }
        return $separated_data;
    }

    public function exchange($amount, $from_currency = 'USD', $to_currency = 'UZS'): string {
        $rates = $this->getCurrencies();

        if (!isset($rates[$from_currency]) || !isset($rates[$to_currency])) {
            return "Currency not available for conversion.";
        }

        $amount_in_uzs = ($from_currency === 'UZS') ? $amount : $amount * $rates[$from_currency];

        $converted_amount = ($to_currency === 'UZS') ? $amount_in_uzs : $amount_in_uzs / $rates[$to_currency];

        $formatted_amount = number_format($converted_amount, 3, '.', ' ');

        return $amount . " $from_currency = $formatted_amount $to_currency";
    }

    public function listAvailableCurrencies(): string {
        $currencies = $this->getCurrencies();
        $currencyList = "Available currencies:\n";
        foreach (array_keys($currencies) as $currency) {
            $currencyList .= "- $currency\n";
        }
        return $currencyList;
    }
}
