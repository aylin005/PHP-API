<?php
namespace App\Core;
use PDO;
use PDOException;
class Database
{
    // 1. Add a property to hold the PDO connection object.
    private ?PDO $conn = null;
    
    // 2. The constructor now initializes the property instead of returning a value.
    public function __construct(array $config)
    {
        try {
            $connectionString = "mysql:" . http_build_query($config, '', ';');
            
            // pdo is an object that represents a connection to the database
            $this->conn = new PDO($connectionString, $config['username'], $config['password'], [
                PDO::ATTR_EMULATE_PREPARES => false, // disable emulation of prepared statements
                PDO::ATTR_STRINGIFY_FETCHES => false // disable conversion of numeric values to strings when fetching
            ]);

        } catch (PDOException $e) {
            // If the connection fails, stop the script and show an error.
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    // 3. This method now correctly returns the connection stored in the property.
    public function getConnection(): ?PDO // pdo = php data object
    {
        return $this->conn;
    }
}