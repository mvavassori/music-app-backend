<?php
namespace Repositories;
use Models\Song;

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
        // no need to check artist existence, the foreign key constraint handles it

        $query = "INSERT INTO songs (title, artist_id, album, genre, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, NOW(), NOW())";

        $this->execute($query, [
            $song->getTitle(),
            $song->getArtistId(),
            $song->getAlbum(),
            $song->getGenre()
        ]);
        $newId = (int) $this->db->lastInsertId();

        $created = $this->findById($newId);
        if (!$created) {
            throw new \RuntimeException("Failed to retrieve newly created song");
        }
        return $created;
    }

    public function update(Song $song): Song|null {

        $query = "UPDATE songs 
                  SET title = ?, artist_id = ?, album = ?, genre = ?, updated_at = NOW() 
                  WHERE id = ?";

        $stmt = $this->execute($query, [
            $song->getTitle(),
            $song->getArtistId(),
            $song->getAlbum(),
            $song->getGenre(),
            $song->getId()
        ]);

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException("Song with id {$song->getId()} not found");
        }

        $updated = $this->findById($song->getId());
        if (!$updated) {
            throw new \RuntimeException("Failed to retrieve updated song");
        }

        return $updated;

    }

    public function delete(int $id): bool {
        $stmt = $this->execute("DELETE FROM songs WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    }

    public function findByGenre(string $genre): array {
        $stmt = $this->execute(
            "SELECT * FROM songs WHERE genre = ? ORDER BY title",
            [$genre]
        );
        $songs = [];
        while ($row = $stmt->fetch()) {
            $songs[] = $this->mapRowToSong($row);
        }
        return $songs;
    }

    public function searchByTitle(string $title): array {
        $stmt = $this->execute(
            "SELECT * FROM songs WHERE title LIKE ? ORDER BY title",
            ['%' . $title . '%']
        );
        $songs = [];
        while ($row = $stmt->fetch()) {
            $songs[] = $this->mapRowToSong($row);
        }
        return $songs;
    }


    private function mapRowToSong($row): Song {
        return new Song(
            (int) $row['id'],
            $row['title'],
            (int) $row['artist_id'],
            $row['album'],
            $row['genre'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}