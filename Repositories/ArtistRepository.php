<?php
namespace Repositories;

use Models\Artist;
use PDOException;

class ArtistRepository extends BaseRepository {

    public function findById($id): Artist|null {
        $stmt = $this->execute("SELECT * FROM artists WHERE id = ?", [$id]);

        $row = $stmt->fetch();

        return $row ? $this->mapRowToArtist($row) : null;
    }

    public function findAll(): array {
        $stmt = $this->execute("SELECT * FROM artists ORDER BY name");
        
        $artists = [];
        while ($row = $stmt->fetch()) {
            $artists[] = $this->mapRowToArtist($row);
        }
        
        return $artists;
    }

    public function create(Artist $artist): Artist|null {
        $query = "INSERT INTO artists (name, bio, image_url, created_at, updated_at) 
                  VALUES (?, ?, ?, NOW(), NOW())";

        try {
            $this->execute($query, [
                $artist->getName(),
                $artist->getBio(),
                $artist->getImageUrl()
            ]);
            $newId = (int) $this->db->lastInsertId();

            return $this->findById($newId);

        } catch (PDOException $e) {
            throw new PDOException("Failed to create artist" . $e->getMessage() . "\n");
        }
    }

    public function update(Artist $artist): Artist {
        $query = "UPDATE artists 
                  SET name = ?, bio = ?, image_url = ?, updated_at = NOW() 
                  WHERE id = ?";
        
        try {
            $this->execute($query, [
                $artist->getName(),
                $artist->getBio(),
                $artist->getImageUrl(),
                $artist->getId()
            ]);
            
            // return the updated artist
            return $this->findById($artist->getId());
            
        } catch (PDOException $e) {
            throw new PDOException("Failed to update artist: " . $e->getMessage());
        }
    }
    
    public function delete(int $id): bool {
        try {
            $stmt = $this->execute("DELETE FROM artists WHERE id = ?", [$id]);
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            throw new PDOException("Failed to delete artist: " . $e->getMessage());
        }
    }

    private function mapRowToArtist($row): Artist {
        return new Artist(
            (int) $row['id'],
            $row['name'],
            $row['bio'],
            $row['image_url'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}