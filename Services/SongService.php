<?php
namespace Services;

use Models\Enums\Genre;
use Models\Song;
use Repositories\SongRepository;
use Repositories\ArtistRepository;
use Exceptions\ValidationException;
use Exceptions\NotFoundException;

class SongService {
    private SongRepository $songRepo;
    private ArtistRepository $artistRepo;

    public function __construct($songRepo, $artistRepo) {
        $this->songRepo = $songRepo;
        $this->artistRepo = $artistRepo;
    }

    public function createSong($title, $artistId, $album = null, $genre = null): Song|null {
        if (empty($title)) {
            throw new ValidationException("Song title is required");
        }

        if (strlen($title) > 255) {
            throw new ValidationException("Song title cannot exceed 255 characters");
        }

        // verify artist exists
        $artist = $this->artistRepo->findById($artistId);
        if (!$artist) {
            throw new NotFoundException("Artist with ID $artistId not found");
        }


        // validate genre if provided
        if ($genre !== null && !Genre::isValid($genre)) {
            throw new ValidationException(
                "Genre '$genre' is not valid. Valid genres are: " . implode(", ", Genre::getAll())
            );
        }

        $song = new Song(
            id: null,
            title: $title,
            artistId: $artistId,
            album: $album,
            genre: $genre,
            createdAt: null,
            updatedAt: null
        );

        return $this->songRepo->create($song);
    }

    public function getSong($id): Song {
        $song = $this->songRepo->findById($id);
        if (!$song) {
            throw new NotFoundException("Song with ID $id not found");
        }

        return $song;
    }

    public function getAllSongs(): array {
        return $this->songRepo->findAll();
    }

    public function getSongsByArtist(int $artistId): array {
        $artist = $this->artistRepo->findById($artistId);

        if (!$artist) {
            throw new NotFoundException("Artist with ID $artistId not found");
        }

        return $this->songRepo->findByArtistId($artistId);
    }

    public function getSongWithArtistName(int $id): array {
        $songData = $this->songRepo->findByIdWithArtist($id);

        if (!$songData) {
            throw new NotFoundException("Song with ID $id not found");
        }

        return $songData;
    }

    public function updateSong(int $id, array $data): Song|null { // title, album, genre
        $song = $this->songRepo->findById($id);

        if (!$song) {
            throw new NotFoundException("Song with ID $id not found");
        }

        // handle data in the $data array if present
        if (isset($data['artist_id'])) {
            $artistId = (int) $data['artist_id'];
            $artist = $this->artistRepo->findById($artistId);

            if (!$artist) {
                throw new NotFoundException("Artist with ID $artistId not found");
            }

            $song->setArtistId($artistId);
        }

        if (isset($data['title'])) {
            if (empty($data['title'])) {
                throw new ValidationException("Song title cannot be empty");
            }

            if (strlen($data['title']) > 255) {
                throw new ValidationException("Song title cannot exceed 255 characters");
            }

            $song->setTitle($data['title']);
        }

        // can be null
        if (array_key_exists('album', $data)) {
            $song->setAlbum($data['album']);
        }

        if (array_key_exists('genre', $data)) {
            if ($data['genre'] !== null && !Genre::isValid($data['genre'])) {
                throw new ValidationException(
                    "Genre '{$data['genre']}' is not valid. Valid genres are: " . implode(", ", Genre::getAll())
                );
            }

            $song->setGenre($data['genre']);
        }

        return $this->songRepo->update($song);

    }

    public function deleteSong(int $id): void {
        if (!$this->songRepo->findById($id)) {
            throw new NotFoundException("Song with ID $id not found");
        }

        $this->songRepo->delete($id);
    }

    public function searchSongs(string $query): array {
        if (empty(trim($query))) {
            throw new ValidationException("Search query cannot be empty");
        }

        return $this->songRepo->searchByTitle($query);
    }

    public function getSongsByGenre(string $genre): array {
        if (!Genre::isValid($genre)) {
            throw new ValidationException(
                "Genre '$genre' is not valid. Valid genres are: " . implode(", ", Genre::getAll())
            );
        }

        return $this->songRepo->findByGenre($genre);
    }
}