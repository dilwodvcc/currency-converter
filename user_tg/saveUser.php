<?php
require 'db.php';

function saveUser($chatId, $firstName, $lastName, $username) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO user (chat_id, first_name, last_name, username) 
                               VALUES (:chat_id, :first_name, :last_name, :username)
                               ON DUPLICATE KEY UPDATE 
                               first_name = VALUES(first_name), 
                               last_name = VALUES(last_name), 
                               username = VALUES(username)");
        $stmt->execute([
            ':chat_id' => $chatId,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':username' => $username
        ]);
    } catch (PDOException $e) {
        error_log("Foydalanuvchini saqlashda xato: " . $e->getMessage());
    }
}
?>
