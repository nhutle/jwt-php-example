<?php

namespace JWT;

use PDO;

class DatabaseService
{
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPassword;
    private $connection;

    public function __construct()
    {
        $this->dbHost     = 'localhost';
        $this->dbName     = 'jwt';
        $this->dbUser     = 'root';
        $this->dbPassword = '';
        $this->connection = NULL;
    }

    public function getConnection() {
        try {
            $this->connection = new PDO('mysql:host='.$this->dbHost.';dbname='.$this->dbName, $this->dbUser, $this->dbPassword);
        } catch (PDOException $exception) {
            echo 'Connection failed: '.$exception->getMessage();
        }

        return $this->connection;
    }
}