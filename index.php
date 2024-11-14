<?php
include 'view/Currency.php';

$currency = new Currency();
$currencies = $currency->getCurrencies();
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $from_currency = $_POST['from'];
    $to_currency = $_POST['to'];
    $result = $currency->exchange($amount, $from_currency, $to_currency);
}

require "view/currency-conventer.php";
?>