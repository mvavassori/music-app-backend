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

}