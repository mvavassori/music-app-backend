<?php
namespace Repositories;

use Models\Song;
use PDOException;

class SongRepository extends BaseRepository {

    public function findById($id): Song|null {
        $stmt = $this->execute("SELECT * FROM songs WHERE id = ?", [$id]);

        $row = $stmt->fetch();

        return $row ? $this->mapRowToSong($row) : null;
    }

    public function findAll(): array {
        $stmt = $this->execute("SELECT * FROM songs ORDER BY title");

        $songs = [];
        while ($row = $stmt->fetch()) {
            $songs[] = $this->mapRowToSong($row);
        }

        return $songs;
    }

    public function findByArtistId($id): array {
        $stmt = $this->execute("SELECT * FROM songs WHERE artist_id = ? ORDER BY title", [$id]);

        $songs = [];

        while ($row = $stmt->fetch()) {
            $songs[] = $this->mapRowToSong($row);
        }
        return $songs;
    }

    // retrieve a song and the artist's name who made that song
    public function findByIdWithArtist($id) {
        $query = "SELECT songs.*, artists.name as artist_name
                    FROM songs
                    JOIN artists ON songs.artist_id = artists.id
                    WHERE songs.id = ?";
        $stmt = $this->execute($query, [$id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function create(Song $song): Song|null {

        // first check if the artist exists (artist_id is a foreign key)
        $checkArtist = $this->execute(
            "SELECT id FROM artists WHERE id = ?",
            [$song->getArtistId()]
        );

        if (!$checkArtist->fetch()) {
            throw new PDOException("Artist with id: " . $song->getArtistId() . "doesn't exist\n");
        }

        $query = "INSERT INTO songs (title, artist_id, album, genre, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, NOW(), NOW())";

        try {
            $this->execute($query, [
                $song->getTitle(),
                $song->getArtistId(),
                $song->getAlbum(),
                $song->getGenre()
            ]);
            $newId = (int) $this->db->lastInsertId();

            return $this->findById($newId);

        } catch (PDOException $e) {
            throw new PDOException("Failed to create song" . $e->getMessage() . "\n");
        }
    }

    public function update(Song $song): Song|null {
        $checkArtist = $this->execute(
            "SELECT id FROM artists WHERE id = ?",
            [$song->getArtistId()]
        );

        if (!$checkArtist->fetch()) {
            throw new PDOException("Artist with id: " . $song->getArtistId() . "doesn't exist\n");
        }

        $query = "UPDATE songs 
                  SET title = ?, artist_id = ?, album = ?, genre = ?, updated_at = NOW() 
                  WHERE id = ?";

        try {
            $this->execute($query, [
                $song->getTitle(),
                $song->getArtistId(),
                $song->getAlbum(),
                $song->getGenre(),
                $song->getId()
            ]);

            return $this->findById($song->getId());
        } catch (PDOException $e) {
            throw new PDOException("Failed to update song: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $stmt = $this->execute("DELETE FROM songs WHERE id = ?", [$id]);
            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            throw new PDOException("Failed to delete song: " . $e->getMessage());
        }
    }

    private function mapRowToSong($row): Song {
        return new Song(
            (int) $row['id'],
            $row['title'],
            $row['artist_id'],
            $row['album'],
            $row['genre'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}