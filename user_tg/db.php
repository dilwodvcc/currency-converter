<?php
$host = 'localhost';
$dbname = 'telegram_bot';
$user = 'root';
$password = '20071010';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ma'lumotlar bazasiga ulanishda xato: " . $e->getMessage());
}
?>