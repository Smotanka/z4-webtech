<?php


class dbController
{
    private PDO $conn;


    public function __construct()
    {
        $this->conn = (new db())->connect();
    }

    public function insertAttendance(attendance $attendance,string $name): int
    {

        $query = "INSERT INTO`".$name."`(name, timestamp, action)  
                    VALUES (:person,:timestamp ,:action) ";

        $person=str_replace("\\","",$attendance->getName());


        $timestamp=$attendance->getTimestamp();
        $action=$attendance->getAction();

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":person", $person, PDO::PARAM_STR);
        $stmt->bindParam(":timestamp", $timestamp, PDO::PARAM_STR);
        $stmt->bindParam(":action", $action, PDO::PARAM_STR);

        $stmt->execute();
        $attendance->setId($this->conn->lastInsertId());
        return $this->conn->lastInsertId();

    }
    public function createTable(string $name): string|null
    {
        try{
            $query = "CREATE TABLE `".$name."` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(128) CHARACTER 
                    SET utf8 COLLATE utf8_slovak_ci NOT NULL , `timestamp` DATETIME NOT NULL , `action` 
                    ENUM('Joined','Left') CHARACTER SET utf8 COLLATE utf8_slovak_ci NOT NULL , 
                    PRIMARY KEY (`id`)) ENGINE = InnoDB;";
            $stmt=$this->conn->exec($query);
        }catch (PDOException $e){
            return null;
        }
       return $name;
    }

    public function getTables($db): ?array{
        try{
            $query="SELECT DISTINCT TABLE_NAME 
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = '".$db."'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        }catch (PDOException $e){

        }
        return null;
    }

    public function isCsvInTable($name): bool
    {
        try{
            $query="SELECT * AS 
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_NAME = '".$name."'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }catch (PDOException $e){
            return  false;
        }

        return  true;
    }

    public function getNames($table): ?array
    {
        try{
        $query="SELECT DISTINCT `".$table."`.`name` FROM  `".$table."`";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }catch (PDOException $e){
            return  null;
        }

    }
    public function getDate($table): ?array
    {
        try{
            $query="SELECT DISTINCT `".$table."`.`timestamp` FROM  `".$table."`";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }catch (PDOException $e){
            return  null;
        }
    }

    public function isName($name,$table): bool
    {
        try{
            $query="SELECT `".$table."`.`name` FROM  `".$table."` WHERE `".$table."`.`name`=?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name]);
            return true;
        }catch (PDOException $e){
            return  false;
        }

    }

    public function getActivity($name,$table): ?array
    {
        try{
            $query="SELECT DISTINCT`".$table."`.`timestamp` AS activity FROM  `".$table."` WHERE `".$table."`.`name`=? ORDER BY `".$table."`.`timestamp` ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }catch (PDOException $e){
            return  null;
        }
    }
    public function getDetailActivity($name,$table): ?array
    {
        try{
            $query="SELECT CONCAT(`".$table."`.`action`,`".$table."`.`timestamp`) AS activity FROM  `".$table."` WHERE `".$table."`.`name`=? ORDER BY `".$table."`.`timestamp` ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }catch (PDOException $e){
            return  null;
        }
    }
    public function getCountPerson($table): ?array
    {
        try{
            $query="SELECT COUNT(DISTINCT `".$table."`.`name`)  FROM  `Attendance`.`".$table."`";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }catch (PDOException $e){
            return  null;
        }
    }
    public function getLastDate($table): ?array
    {
        try{
            $query="SELECT MAX(`".$table."`.`timestamp`) FROM  `".$table."`";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }catch (PDOException $e){
            return  null;
        }
    }
}





