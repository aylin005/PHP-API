<?php
namespace App\Models;
use App\Core\Database;
use PDO;
use Exception;
class ProductGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM product";
        $stmt = $this->conn->query($sql); //query executes a SQL statement and returns a PDOStatement object
        
        $data = []; //array to hold the results

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { //fetch fetches the next row from a result set
            
            $row["is_available"] = (bool)$row["is_available"]; //convert the is_available field to a boolean
            
            $data[] = $row; //add the row to the results array
        }

        return $data; //return the results array
    }

    public function create (array $data): string
    {
        $sql = "INSERT INTO product (name, size, is_available) 
                VALUES (:name, :size, :is_available)"; // : named placeholders
        
        $stmt = $this->conn->prepare($sql); //prepare prepares a SQL statement for execution and returns a PDOStatement object

        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":size", $data["size"] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(":is_available", (bool) ($data["is_available"] ?? false),
         PDO::PARAM_BOOL);

        if ($stmt->execute()) { //execute executes a prepared statement
            return (int)$this->conn->lastInsertId(); //lastInsertId returns the ID of the last inserted row
        } else {
            throw new Exception("Error creating product");
        }
    }

    public function get(string $id):array|false
    {
        $sql = "SELECT * FROM product WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $data["is_available"] = (bool)$data["is_available"]; //cast to boolean
        }

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE product SET name = :name, size = :size, is_available = :is_available
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
        $stmt->bindValue(":size", $new["size"] ?? $current["size"], PDO::PARAM_INT);
        $stmt->bindValue(":is_available", 
            isset($new["is_available"]) ? (bool)$new["is_available"] : (bool)$current["is_available"],
            PDO::PARAM_BOOL);

        $stmt->execute();

        return $stmt->rowCount(); //rowCount returns the number of rows affected by the last SQL statement
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM product WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}