<?php

require_once "config.php";

class db
{
    private $conn;

    public function connect(){

        try {
            $this->conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
        return $this->conn;
    }




}