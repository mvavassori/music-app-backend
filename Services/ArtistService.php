<?php
namespace Services;

use Models\Artist;
use Repositories\ArtistRepository;
use Exceptions\ValidationException;
use Exceptions\NotFoundException;

class ArtistService {
    private ArtistRepository $artistRepo;

    public function __construct($artistRepo) {
        $this->artistRepo = $artistRepo;
    }

    public function createArtist($name, $bio = null, $imageUrl = null): Artist {
        if (empty($name)) {
            throw new ValidationException("Artist name is required");
        }

        if (strlen($name) > 255) {
            throw new ValidationException("Artist name cannot exceed 255 characters");
        }

        $artist = new Artist(
            id: null,
            name: $name,
            bio: $bio,
            imageUrl: $imageUrl,
            createdAt: null,
            updatedAt: null
        );

        $createdArtist = $this->artistRepo->create($artist);

        return $createdArtist;

    }

    public function getArtist($id): Artist {

        $artist = $this->artistRepo->findById($id);

        if (!$artist) {
            throw new NotFoundException("Artist with ID $id not found");
        }

        return $artist;

    }

    public function getAllArtists(): array {
        // no need to check for anything. If there's no artist it will return an empty array
        $artists = $this->artistRepo->findAll();
        return $artists;
    }

    public function updateArtist(int $id, array $data): Artist { // $name, $bio, $imageUrl

        // get existing artist
        $artist = $this->artistRepo->findById($id);
        if (!$artist) {
            throw new NotFoundException("Artist with ID $id not found");
        }

        // update only the fields that were provided
        if (isset($data['name'])) {
            if (empty($data['name'])) {
                throw new ValidationException("Artist name cannot be empty");
            }
            if (strlen($data['name']) > 255) {
                throw new ValidationException("Artist name cannot exceed 255 characters");
            }
            $artist->setName($data['name']);
        }

        if (isset($data['bio'])) {
            $artist->setBio($data['bio']);
        }

        if (isset($data['imageUrl'])) {
            $artist->setImageUrl($data['imageUrl']);
        }

        return $this->artistRepo->update($artist);
    }

    public function deleteArtist($id): void {

        if (!$this->artistRepo->findById($id)) {
            throw new NotFoundException("Artist with ID $id not found");
        }

        $this->artistRepo->delete($id);
    }
}