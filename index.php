<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/weather') {
    require 'weather/weather.php';
} elseif ($uri === '/currency') {
    require 'view/Currency.php';
    $currency = new Currency();
    require 'view/currency-conventer.php';
} elseif ($uri === '/telegram') {
    require 'app/sendbot.php';
} else {
    // Default Home Page
    $title = 'Home';
    $content = 'view/home.php';
    require 'main/layout.php';
}
?>