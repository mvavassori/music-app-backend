<?php
namespace Services;

use Models\Enums\Genre;
use Models\Song;
use Repositories\SongRepository;
use Repositories\ArtistRepository;

class SongService {
    private SongRepository $songRepo;
    private ArtistRepository $artistRepo;

    public function __construct($songRepo, $artistRepo) {
        $this->songRepo = $songRepo;
        $this->artistRepo = $artistRepo;
    }

    public function createSong($title, $artistId, $album = null, $genre = null): array {
        // check if title or artistId are empty
        if (empty($title) || empty($artistId)) {
            return [
                "success" => false,
                "message" => "You must provide a title and an artistId"
            ];
        }

        $artist = $this->artistRepo->findById($artistId);
        if (!$artist) {
            return [
                "success" => false,
                "message" => "Artist not found"
            ];
        }

        if ($genre !== null && !Genre::isValid($genre)) {
            return [
                "success" => false,
                "message" => "Genre \"$genre\" is not valid. These are the valid genres: " . implode(", ", Genre::getAll())
            ];
        }

        try {
            $song = new Song(
                id: null,
                title: $title,
                artistId: $artistId,
                album: $album,
                genre: $genre,
                createdAt: null,
                updatedAt: null
            );

            $createdSong = $this->songRepo->create($song);
            return [
                "success" => true,
                "message" => "Song created successfully",
                "song" => $createdSong
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Song creation failed: " . $e->getMessage(),
            ];
        }
    }

    public function getSong($id): array {
        $song = $this->songRepo->findById($id);
        if (!$song) {
            return [
                "success" => false,
                "message" => "Song not found"
            ];
        }
        return [
            "success" => true,
            "song" => $song
        ];
    }

    public function getAllSongs(): array {
        $song = $this->songRepo->findAll();
        return [
            "success" => true,
            "songs" => $song
        ];
    }

    public function getSongsByArtist($id): array {
        $artist = $this->artistRepo->findById($id);
        if (!$artist) {
            return [
                "success" => false,
                "message" => "Artist not found"
            ];
        }
        $songs = $this->songRepo->findByArtistId($id);
        return [
            "success" => true,
            "songs" => $songs
        ];
    }

    public function getSongWithItsArtistName($id): array {
        $song = $this->songRepo->findByIdWithArtist($id);
        if (!$song) {
            return [
                "success" => false,
                "message" => "Song not found"
            ];
        }
        return [
            "success" => true,
            "song" => $song
        ];
    }

    public function updateSong(int $id, array $data): array { // title, album, genre
        $song = $this->songRepo->findById($id);
        if (!$song) {
            return [
                "success" => false,
                "message" => "Song not found"
            ];
        }

        if (isset($data["artistId"])) {
            $artist = $this->artistRepo->findById($data["artistId"]);
            if (!$artist) {
                return [
                    "success" => false,
                    "message" => "Artist not found"
                ];
            }
            $song->setArtistId($data["artistId"]);
        }

        if (isset($data["title"])) {
            if (empty($data["title"])) {
                return [
                    "success" => false,
                    "message" => "Title cannot be empty"
                ];
            }
            $song->setTitle($data["title"]);
        }

        if (isset($data["album"])) {
            if (empty($data["album"])) {
                return [
                    "success" => false,
                    "message" => "Album cannot be empty"
                ];
            }
            $song->setAlbum($data["album"]);
        }

        if (isset($data["genre"])) {
            if ($data["genre"] !== null && !Genre::isValid($data["genre"])) {
                return [
                    "success" => false,
                    "message" => "Genre \"" . $data["genre"] . "\" is not valid. These are the valid genres: " . implode(", ", Genre::getAll())
                ];
            }
            $song->setGenre($data["genre"]);
        }

        try {
            $updatedSong = $this->songRepo->update($song);

            return [
                "success" => true,
                "message" => "Song updated successfully",
                "song" => $updatedSong
            ];

        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Update failed" . $e->getMessage()
            ];
        }

    }

    public function deleteSong($id): array {
        $song = $this->songRepo->findById($id);
        if (!$song) {
            return [
                "success" => false,
                "message" => "Song not found"
            ];
        }
        try {
            $this->songRepo->delete($id);
            return [
                "success" => true,
                "message" => "Song deleted succesfully"
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Deletion failed: " . $e->getMessage()
            ];
        }
    }
}