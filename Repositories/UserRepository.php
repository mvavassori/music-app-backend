<?php
namespace Repositories;

use Models\User;

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

    public function findByUsername(string $username): User|null {
        $stmt = $this->execute("SELECT * FROM users WHERE username = ?", [$username]);
        $row = $stmt->fetch();
        return $row ? $this->mapRowToUser($row) : null;
    }

    public function create(User $user): User {
        $query = "INSERT INTO users (username, email, password, created_at, updated_at) 
                  VALUES (?, ?, ?, NOW(), NOW())";

        // let PDOException bubble up for duplicate email/username
        $this->execute($query, [
            $user->getUsername(),
            $user->getEmail(),
            $user->getPassword()
        ]);

        $newId = (int) $this->db->lastInsertId();

        $created = $this->findById($newId);
        if (!$created) {
            throw new \RuntimeException("Failed to retrieve newly created user");
        }

        return $created;
    }

    public function update(User $user): User {
        $query = "UPDATE users 
                  SET username = ?, email = ?, updated_at = NOW() 
                  WHERE id = ?";

        $stmt = $this->execute($query, [
            $user->getUsername(),
            $user->getEmail(),
            $user->getId()
        ]);

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException("User with ID {$user->getId()} not found");
        }

        $updated = $this->findById($user->getId());
        if (!$updated) {
            throw new \RuntimeException("Failed to retrieve updated user");
        }

        return $updated;
    }

    public function updatePassword(int $userId, string $hashedPassword): bool {
        $query = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";

        $stmt = $this->execute($query, [$hashedPassword, $userId]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool {
        $stmt = $this->execute("DELETE FROM users WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    }

    // helper method
    public function emailExists(string $email): bool {
        $stmt = $this->execute("SELECT COUNT(*) FROM users WHERE email = ?", [$email]);
        $count = $stmt->fetchColumn();  // fetchColumn() gets the first column of first row (i just need to check if there's a row)
        return $count > 0;
    }

    public function usernameExists(string $username): bool {
        $stmt = $this->execute("SELECT COUNT(*) FROM users WHERE username = ?", [$username]);
        $count = $stmt->fetchColumn() > 0;
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