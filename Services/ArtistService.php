<?php
namespace Services;

use Models\Artist;
use Repositories\ArtistRepository;
use Repositories\SongRepository;

class ArtistService {
    private ArtistRepository $artistRepo;
    private SongRepository $songRepo;

    public function __construct($artistRepo, $songRepo) {
        $this->artistRepo = $artistRepo;
        $this->songRepo = $songRepo;
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
        try {
            $artist = $this->artistRepo->findById($id);
            return [
                "success" => true,
                "artist" => $artist
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Artist not found"
            ];
        }
    }

    public function getAllArtists(): array {
        try {
            $artists = $this->artistRepo->findAll();
            return [
                'success' => true,
                'artists' => $artists,
                'count' => count($artists)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch artists: ' . $e->getMessage()
            ];
        }
    }

    // todo get artist with songs
    public function getArtistWithSongs($id) {
        $artist = $this->artistRepo->findById($id);

        if(!$artist) {
            return [
                "success" => false,
                "message" => "Artist not found"
            ];
        }

        

    }


    public function updateArtist(int $id, string $name, ?string $bio = null, ?string $imageUrl = null): array {
        // validation
        if (empty($name)) {
            return [
                'success' => false,
                'message' => 'Artist name is required'
            ];
        }

        // get existing artist
        $artist = $this->artistRepo->findById($id);
        if (!$artist) {
            return [
                'success' => false,
                'message' => 'Artist not found'
            ];
        }

        // update fields
        $artist->setName($name);
        $artist->setBio($bio);
        $artist->setImageUrl($imageUrl);

        try {
            $updatedArtist = $this->artistRepo->update($artist);
            return [
                'success' => true,
                'message' => 'Artist updated successfully',
                'artist' => $updatedArtist
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
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
                "message" => "User deleted succesfully"
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Invalid user id"
            ];
        }
    }
}