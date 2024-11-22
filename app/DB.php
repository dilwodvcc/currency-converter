<?php

class DB
{
    public $host = "localhost";
    public $user = "root";
    public $pass = "20071010";
    public $db_name = "bot_database";
    public $conn;
    public function __construct(){
        $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->user, $this->pass);
    }
}