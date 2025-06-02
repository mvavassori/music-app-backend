<?php
namespace Controllers;

use Services\SongService;
use Exceptions\ValidationException;
use Exceptions\NotFoundException;

class SongController extends BaseController {
    private SongService $songService;

    public function __construct(SongService $songService) {
        $this->songService = $songService;
    }

    // POST /songs
    public function create(): void {
        try {
            $data = $this->getJsonInput();

            $song = $this->songService->createSong(
                $data["title"] ?? "",
                $data["artistId"] ?? 0,
                $data["album"] ?? null,
                $data["genre"] ?? null
            );

            $this->sendResponse([
                "message" => "Song created successfully",
                "song" => [
                    "id" => $song->getId(),
                    "title" => $song->getTitle(),
                    "artist_id" => $song->getArtistId(),
                    "album" => $song->getAlbum(),
                    "genre" => $song->getGenre(),
                    "created_at" => $song->getCreatedAt()
                ]
            ], 201);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\PDOException $e) {
            // check if it"s a foreign key constraint error
            if ($e->getCode() == "23000") {
                $this->sendError("Invalid artist ID", 400);
            } else {
                throw $e;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to create song", 500);
        }
    }

    // GET /songs/{id}
    public function get(int $id): void {
        try {
            $song = $this->songService->getSong($id);

            $this->sendResponse([
                "song" => [
                    "id" => $song->getId(),
                    "title" => $song->getTitle(),
                    "artist_id" => $song->getArtistId(),
                    "album" => $song->getAlbum(),
                    "genre" => $song->getGenre(),
                    "created_at" => $song->getCreatedAt(),
                    "updated_at" => $song->getUpdatedAt()
                ]
            ]);

        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to fetch song", 500);
        }
    }

    // GET /songs/{id}/details (with artist name)
    public function getWithArtist(int $id): void {
        try {
            $songData = $this->songService->getSongWithArtistName($id);

            $this->sendResponse([
                "song" => $songData
            ]);

        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to fetch song details", 500);
        }
    }

    // GET /songs
    public function getAll(): void {
        try {
            $songs = $this->songService->getAllSongs();

            // format songs for response
            $formattedSongs = array_map(function ($song) {
                return [
                    "id" => $song->getId(),
                    "title" => $song->getTitle(),
                    "artist_id" => $song->getArtistId(),
                    "album" => $song->getAlbum(),
                    "genre" => $song->getGenre(),
                    "created_at" => $song->getCreatedAt(),
                    "updated_at" => $song->getUpdatedAt()
                ];
            }, $songs);

            $this->sendResponse([
                "songs" => $formattedSongs,
                "count" => count($songs)
            ]);

        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to fetch songs", 500);
        }
    }

    // GET /artists/{artistId}/songs
    public function getByArtist(int $artistId): void {
        try {
            $songs = $this->songService->getSongsByArtist($artistId);

            $formattedSongs = array_map(function ($song) {
                return [
                    "id" => $song->getId(),
                    "title" => $song->getTitle(),
                    "artist_id" => $song->getArtistId(),
                    "album" => $song->getAlbum(),
                    "genre" => $song->getGenre(),
                    "created_at" => $song->getCreatedAt(),
                    "updated_at" => $song->getUpdatedAt()
                ];
            }, $songs);

            $this->sendResponse([
                "artist_id" => $artistId,
                "songs" => $formattedSongs,
                "count" => count($songs)
            ]);

        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to fetch artist songs", 500);
        }
    }

    // GET /songs/genre/{genre}
    public function getByGenre(string $genre): void {
        try {
            $songs = $this->songService->getSongsByGenre($genre);

            $formattedSongs = array_map(function ($song) {
                return [
                    "id" => $song->getId(),
                    "title" => $song->getTitle(),
                    "artist_id" => $song->getArtistId(),
                    "album" => $song->getAlbum(),
                    "genre" => $song->getGenre(),
                    "created_at" => $song->getCreatedAt(),
                    "updated_at" => $song->getUpdatedAt()
                ];
            }, $songs);

            $this->sendResponse([
                "genre" => $genre,
                "songs" => $formattedSongs,
                "count" => count($songs)
            ]);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to fetch songs by genre", 500);
        }
    }

    // GET /songs/search?q={query}
    public function search(): void {
        try {
            // $_GET is a PHP superglobal array that contains all query parameters from the URL
            $query = $_GET["q"] ?? "";

            $songs = $this->songService->searchSongs($query);

            $formattedSongs = array_map(function ($song) {
                return [
                    "id" => $song->getId(),
                    "title" => $song->getTitle(),
                    "artist_id" => $song->getArtistId(),
                    "album" => $song->getAlbum(),
                    "genre" => $song->getGenre(),
                    "created_at" => $song->getCreatedAt(),
                    "updated_at" => $song->getUpdatedAt()
                ];
            }, $songs);

            $this->sendResponse([
                "query" => $query,
                "songs" => $formattedSongs,
                "count" => count($songs)
            ]);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to search songs", 500);
        }
    }

    // PUT /songs/{id}
    public function update(int $id): void {
        try {
            $data = $this->getJsonInput();

            $song = $this->songService->updateSong($id, $data);

            $this->sendResponse([
                "message" => "Song updated successfully",
                "song" => [
                    "id" => $song->getId(),
                    "title" => $song->getTitle(),
                    "artist_id" => $song->getArtistId(),
                    "album" => $song->getAlbum(),
                    "genre" => $song->getGenre(),
                    "updated_at" => $song->getUpdatedAt()
                ]
            ]);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to update song", 500);
        }
    }

    // DELETE /songs/{id}
    public function delete(int $id): void {
        try {
            $this->songService->deleteSong($id);

            $this->sendResponse(["message" => "Song deleted successfully"]);

        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to delete song", 500);
        }
    }
}