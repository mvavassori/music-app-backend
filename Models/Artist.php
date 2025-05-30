<?php
namespace Models;
class Artist {
    private ?int $id;
    private string $name;
    private ?string $bio;
    private ?string $imageUrl;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(?int $id, string $name, ?string $bio = null, ?string $imageUrl = null, ?string $createdAt = null, ?string $updatedAt = null) {
        $this->id = $id;
        $this->name = $name;
        $this->bio = $bio;
        $this->imageUrl = $imageUrl;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getBio(): ?string {
        return $this->bio;
    }

    public function getImageUrl(): ?string {
        return $this->imageUrl;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string {
        return $this->updatedAt;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setBio(?string $bio): void {
        $this->bio = $bio;
    }

    public function setImageUrl(?string $imageUrl): void {
        $this->imageUrl = $imageUrl;
    }

}