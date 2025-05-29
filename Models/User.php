<?php
namespace Models;

class User {
    private ?int $id;
    private string $username;
    private string $email;
    private string $password;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(?int $id, string $username, string $email, string $password, ?string $createdAt, ?string $updatedAt) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }


    // ** getters and setters **
    public function getId(): int|null {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username; 
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getCreatedAt(): string|null {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): string|null {
        return $this->updatedAt;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

}