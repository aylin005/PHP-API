<?php

class Database
{
    public function __construct( private string $host,
                                private string $dbname,
                                private string $user,
                                private string $password,
                                private string $port)
    {
    }
    public function getConnection(): PDO //pdo = php data object
        {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4"; //dsn = data source name

             //pdo is an object that represents a connection to the database
            return new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_EMULATE_PREPARES => false, //disable emulation of prepared statements
                PDO::ATTR_STRINGIFY_FETCHES => false //disable conversion of numeric values to strings when fetching
            ]);
        }
}