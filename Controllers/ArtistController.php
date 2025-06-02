<?php
namespace Controllers;

use Exceptions\NotFoundException;
use Exceptions\ValidationException;
use Services\ArtistService;

class ArtistController extends BaseController {
    private ArtistService $artistService;

    public function __construct($artistService) {
        $this->artistService = $artistService;
    }

    // POST /artists
    public function create(): void {
        try {
            // req body
            $data = $this->getJsonInput();

            // we use empty strings nullish operator for fields that are mandatory, so that it will throw an exception.
            $artist = $this->artistService->createArtist(
                name: $data["name"] ?? '',
                bio: $data["bio"] ?? null,
                imageUrl: $data["imageUrl"] ?? null);

            $this->sendResponse([
                "message" => "Artist created successfully",
                "artist" => [
                    "id" => $artist->getId(),
                    "name" => $artist->getName(),
                    "bio" => $artist->getBio(),
                    "imageUrl" => $artist->getImageUrl(),
                    "createdAt" => $artist->getCreatedAt()
                ]
            ], 201);
        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to create artist", 500);
        }
    }

    // GET /artists/{id}
    public function get(int $id): void {
        try {
            $artist = $this->artistService->getArtist($id);

            $this->sendResponse([
                "artist" => [
                    "id" => $artist->getId(),
                    "name" => $artist->getName(),
                    "bio" => $artist->getBio(),
                    "imageUrl" => $artist->getImageUrl(),
                    "createdAt" => $artist->getCreatedAt(),
                    "updatedAt" => $artist->getUpdatedAt()
                ]
            ], 200);
        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to get artist", 500);
        }
    }

    // GET /artists
    public function getAll(): void {
        try {
            $artists = $this->artistService->getAllArtists();

            // map over artists array
            $formattedArtists = array_map(function ($artist) {
                return [
                    'id' => $artist->getId(),
                    'name' => $artist->getName(),
                    'bio' => $artist->getBio(),
                    'image_url' => $artist->getImageUrl(),
                    'created_at' => $artist->getCreatedAt(),
                    'updated_at' => $artist->getUpdatedAt()
                ];
            }, $artists);

            $this->sendResponse([
                "artists" => $formattedArtists,
                "count" => count($artists)
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError("Failed to get artists", 500);
        }
    }

    // PUT /artists/{id}
    public function update(int $id): void {
        try {
            $data = $this->getJsonInput();

            $artist = $this->artistService->updateArtist($id, $data);

            $this->sendResponse([
                'message' => 'Artist updated successfully',
                'artist' => [
                    'id' => $artist->getId(),
                    'name' => $artist->getName(),
                    'bio' => $artist->getBio(),
                    'image_url' => $artist->getImageUrl(),
                    'updated_at' => $artist->getUpdatedAt()
                ]
            ]);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Failed to update artist', 500);
        }
    }

    // DELETE /artists/{id}
    public function delete(int $id): void {
        try {
            $this->artistService->deleteArtist($id);

            $this->sendResponse(['message' => 'Artist deleted successfully']);

        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\PDOException $e) {
            // check if it's a foreign key constraint error
            if ($e->getCode() == '23000') {
                $this->sendError('Cannot delete artist with existing songs', 409);
            } else {
                throw $e;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Failed to delete artist', 500);
        }
    }
}