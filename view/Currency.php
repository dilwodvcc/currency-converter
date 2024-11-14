<?php
class Currency {
    const CURRENCY_API_URL = "https://cbu.uz/uz/arkhiv-kursov-valyut/json/";

    private array $currencies = [];

    public function __construct() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::CURRENCY_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $this->currencies = json_decode($output, true);
    }

    public function getCurrencies(): array {
        $separated_data = ['UZS' => 1];
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
    
        return $formatted_amount . ' ' . $to_currency;
    }
    
}