<?php 
class Currency{
 const CURRENCY_API_URL = "https://cbu.uz/uz/arkhiv-kursov-valyut/json/";
 public $currencies;
 public function __construct(){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,self::CURRENCY_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    $output = curl_exec($ch);
    curl_close($ch);
    $this->currencies = json_decode($output);
    }
public function getCurrencies():array{
    $separated_date = [];
    $currensies_info = $this->currencies;
    foreach($currensies_info as $currency){
        $separated_date[$currency->Ccy] = $currency->Rate;}
        return $separated_date;
}
public function exchange($value, $from, $to) {
    $currencies = $this->getCurrencies();

    if (!isset($currencies[$from]) || !isset($currencies[$to])) {
        throw new Exception("Valyuta topilmadi.");
    }

    $fromRate = $currencies[$from];
    $toRate = $currencies[$to];


    $convertedValue = ($value / $fromRate) * $toRate;
    return round($convertedValue, 2) . ' ' . $to;
}

}

$currency = new Currency() ;
?>