<?php
namespace Repositories;

use Models\User;
use PDOException;

class UserRepository extends BaseRepository {
    public function findById(int $id): User|null {
        $stmt = $this->execute("SELECT * FROM users WHERE id = ?", [$id]); // execute method comes from the parent class 
        
        $row = $stmt->fetch();  // get one row (or false if not found)
        
        // if we found a row, convert it to User, otherwise return null
        return $row ? $this->mapRowToUser($row) : null;
    }

    public function findByEmail(string $email): User|null {
        $stmt = $this->execute("SELECT * FROM users WHERE email = ?", [$email]);

        $row = $stmt->fetch();

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function create(User $user): User|null {
        $query = "INSERT INTO users (username, email, password, created_at, updated_at) 
                  VALUES (?, ?, ?, NOW(), NOW())"; // NOW for created_at and updated_at
        
        try {
            $this->execute($query, [
                $user->getUsername(),
                $user->getEmail(),
                $user->getPassword()
            ]);

             // Get the ID of the newly created record
            $newId = (int) $this->db->lastInsertId();

            return $this->findById($newId);

        } catch(PDOException $e) {
            throw new PDOException("Failed to create user" . $e->getMessage() . "\n");
        }
    }
    
    public function update(User $user): User {
        $query = "UPDATE users 
                  SET username = ?, email = ?, password = ?, updated_at = NOW() 
                  WHERE id = ?";
        
        try {
            $this->execute($query, [
                $user->getUsername(),
                $user->getEmail(),
                $user->getPassword(),
                $user->getId()  
            ]);
            
            // Return the updated user
            return $this->findById($user->getId());
            
        } catch (PDOException $e) {
            throw new PDOException("Failed to update user: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $stmt = $this->execute("DELETE FROM users WHERE id = ?", [$id]);
            
            // rowCount() tells us how many rows were affected
            return $stmt->rowCount() > 0;  // true if something was deleted
            
        } catch (PDOException $e) {
            throw new PDOException("Failed to delete user: " . $e->getMessage());
        }
    }

    // helper method
    public function emailExists(string $email): bool {
        $stmt = $this->execute("SELECT COUNT(*) FROM users WHERE email = ?", [$email]);
        $count = $stmt->fetchColumn();  // fetchColumn() gets the first column of first row (i just need to check if there's a row)
        
        return $count > 0;
    }


    // private helper method: convert database row to User object
    private function mapRowToUser(array $row): User {
        return new User(
            (int) $row['id'],
            $row['username'],
            $row['email'], 
            $row['password'],
            $row['created_at'], 
            $row['updated_at']
        );
    }
}