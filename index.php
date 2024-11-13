<?php
require "Currency.php";

$currency = new Currency();
$currencies = $currency->getCurrencies();

$result = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $qiymat = $_POST['qiymat'];
    if (isset($_POST["qiymat"])) {
    } else {
        $result = "Qiymat kiritilmadi.";
    }
    $from = $_POST['from'];
    $to = $_POST['froms'];
    $result = $currency->exchange($qiymat, $from, $to);
}
require "view/currency-conventer.php";
?>