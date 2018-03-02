<?php
namespace src;
use PDO;
abstract class Database {
    const USERNAME="root";
    const PASSWORD="";
    const HOST="localhost";
    const DB="msg_board";

    public function getConnection(){
        $username = self::USERNAME;
        $password = self::PASSWORD;
        $host = self::HOST;
        $db = self::DB;
        $connection = new PDO("mysql:dbname=$db;host=$host", $username, $password);
        return $connection;
    }
    protected function query($sql, $args){
        $connection = $this->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}