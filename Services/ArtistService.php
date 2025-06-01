<?php
namespace Services;

use Models\Artist;
use Repositories\ArtistRepository;

class ArtistService {
    private ArtistRepository $artistRepo;

    public function __construct($artistRepo) {
        $this->artistRepo = $artistRepo;
    }

    public function createArtist($name, $bio = null, $imageUrl = null): array {
        if (empty($name)) {
            return [
                "success" => false,
                "message" => "You must provide a name"
            ];
        }

        try {
            $artist = new Artist(
                id: null,
                name: $name,
                bio: $bio,
                imageUrl: $imageUrl,
                createdAt: null,
                updatedAt: null
            );

            $createdArtist = $this->artistRepo->create($artist);

            return [
                "success" => true,
                "message" => "Artist created successfully",
                "artist" => $createdArtist
            ];

        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Artist creation failed: " . $e->getMessage(),
            ];
        }
    }

    public function getArtist($id): array {

        $artist = $this->artistRepo->findById($id);
        if (!$artist) {
            return [
                "success" => false,
                "message" => "Artist not found"
            ];
        }

        return [
            "success" => true,
            "artist" => $artist
        ];

    }

    public function getAllArtists(): array {
        // no need to check for anything. If there's no artist, it will simply return count 0 and an empty array
        $artists = $this->artistRepo->findAll();
        return [
            "success" => true,
            "artists" => $artists,
            "count" => count($artists)
        ];
    }

    public function updateArtist(int $id, array $data): array { // $name, $bio, $imageUrl

        // get existing artist
        $artist = $this->artistRepo->findById($id);
        if (!$artist) {
            return [
                "success" => false,
                "message" => "Artist not found"
            ];
        }

        // update only the fields that were provided
        if (isset($data['name'])) {
            if (empty($data['name'])) { // if there's a name field, it must not be empty
                return [
                    'success' => false,
                    'message' => 'Artist name cannot be empty'
                ];
            }
            $artist->setName($data['name']);
        }

        if (isset($data['bio'])) {
            $artist->setBio($data['bio']);
        }

        if (isset($data['imageUrl'])) {
            $artist->setImageUrl($data['imageUrl']);
        }

        try {
            $updatedArtist = $this->artistRepo->update($artist);
            return [
                "success" => true,
                "message" => "Artist updated successfully",
                "artist" => $updatedArtist
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Update failed: " . $e->getMessage()
            ];
        }
    }

    public function deleteArtist($id): array {

        $artist = $this->artistRepo->findById($id);
        if (!$artist) {
            return [
                "success" => false,
                "message" => "Artist not found"
            ];
        }

        try {
            $this->artistRepo->delete($id);
            return [
                "success" => true,
                "message" => "Artist deleted succesfully"
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Deletion failed: " . $e->getMessage()
            ];
        }
    }
}