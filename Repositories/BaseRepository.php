<?php
namespace Repositories;

use PDO;
use Database\Connection;

abstract class BaseRepository {
    protected PDO $db;
    
    public function __construct() {
        // get our singleton database connection
        $this->db = Connection::getInstance();
    }
    
    // A helper method for executing queries with parameters
    protected function execute(string $query, array $params = []): bool | \PDOStatement { // `\` means from the global namespace
        $stmt = $this->db->prepare($query);     // Prepare the SQL
        $stmt->execute($params);             // Execute with parameters
        return $stmt;                               // Return the statement for further use
    }
}