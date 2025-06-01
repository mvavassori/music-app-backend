<?php
namespace Models;

class Song {
    private ?int $id;
    private string $title;
    private string $artistId; // foreign key
    private ?string $album;
    private ?string $genre;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(?int $id, string $title, string $artistId, ?string $album, ?string $genre, ?string $createdAt, ?string $updatedAt) {
        $this->id = $id;
        $this->title = $title;
        $this->artistId = $artistId;
        $this->album = $album;
        $this->genre = $genre;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getArtistId(): string {
        return $this->artistId;
    }

    public function getAlbum(): string {
        return $this->album;
    }

    public function getGenre(): string {
        return $this->genre;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string {
        return $this->updatedAt;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setArtistId(int $artistId): void {
        $this->artistId = $artistId;
    }

    public function setAlbum(?string $album): void {
        $this->album = $album;
    }

    public function setGenre(?string $genre): void {
        $this->genre = $genre;
    }
}