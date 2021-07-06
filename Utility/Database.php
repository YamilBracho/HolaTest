<?php

class Database {
    private $host = "localhost";
    private $dbname = "holatestdb";
    private $username = "root";
    private $password = "";
    public $conn;


    // get the database connection
    public function getConnection() {
    
        $this->conn = null;

        try {
              $options = [
                PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
              ];

              $dsn = "mysql:host={$this->host};dbname={$this->dbname}";

              $this->conn = new PDO($dsn, $this->username, $this->password, $options);
              $this->conn->exec("set names utf8");

        } catch (PDOException $ex) {
            throw $ex;
        }

        return$this->conn;
    }

}