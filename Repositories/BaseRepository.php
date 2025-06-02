<?php
namespace Repositories;

use PDO;
use Database\Connection;

abstract class BaseRepository {
    protected PDO $db;

    public function __construct() {
        // get the singleton database connection, i.e. every repository gets the same connection instance
        $this->db = Connection::getInstance();
    }

    // A helper method for executing queries with parameters
    protected function execute(string $query, array $params = []): bool|\PDOStatement { // `\` means from the global namespace
        $stmt = $this->db->prepare($query);     // prepare the SQL  (stmt stays for statement)
        $stmt->execute($params);             // execute with parameters
        return $stmt;
    }
}