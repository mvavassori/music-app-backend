<?php
namespace Repositories;

use Models\Artist;

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

        $this->execute($query, [
            $artist->getName(),
            $artist->getBio(),
            $artist->getImageUrl()
        ]);
        $newId = (int) $this->db->lastInsertId();

        $created = $this->findById($newId);

        if (!$created) {
            throw new \RuntimeException("Failed to retrieve newly created artist");
        }

        return $created;
    }

    public function update(Artist $artist): Artist {
        if (!$artist->getId()) {
            throw new \InvalidArgumentException("Cannot update artist without ID");
        }
        $query = "UPDATE artists 
                  SET name = ?, bio = ?, image_url = ?, updated_at = NOW() 
                  WHERE id = ?";

        $stmt = $this->execute($query, [
            $artist->getName(),
            $artist->getBio(),
            $artist->getImageUrl(),
            $artist->getId()
        ]);

        // check if any row was actually updated
        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException("Artist with ID {$artist->getId()} not found");
        }

        $updated = $this->findById($artist->getId());
        if (!$updated) {
            throw new \RuntimeException("Failed to retrieve updated artist");
        }

        return $updated;
    }

    public function delete(int $id): bool {
        // no need to catch and re-throw with less information
        $stmt = $this->execute("DELETE FROM artists WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    }

    public function countAll(): int {
        $stmt = $this->execute("SELECT COUNT(*) FROM artists");
        return (int) $stmt->fetchColumn();
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