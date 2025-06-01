<?php
namespace Services;

use Models\User;
use Repositories\UserRepository;

class UserService {
    private UserRepository $userRepo;

    // inject userRepo
    public function __construct($userRepo) {
        $this->userRepo = $userRepo;
    }

    public function register($username, $email, $password): array {
        // validation 
        if (empty($username) || empty($email) || empty($password)) {
            return [
                "success" => false,
                "message" => "All fields required",
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                "success" => false,
                "message" => "$email is not a valid email address",
            ];
        }

        // check if email address already exists
        if ($this->userRepo->emailExists($email)) {
            return [
                "success" => false,
                "message" => "$email is already claimed",
            ];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $user = new User(
                id: null,
                username: $username,
                email: $email,
                password: $hashedPassword,
                createdAt: null,
                updatedAt: null
            );

            $createdUser = $this->userRepo->create($user);

            return [
                "success" => true,
                "message" => "User registered successfully",
                "user" => $createdUser
            ];

        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Registration failed: " . $e->getMessage()
            ];
        }
    }

    public function login($email, $password): array {
        // validation
        if (empty($email) || empty($password)) {
            return [
                "success" => false,
                "message" => "All fields required",
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                "success" => false,
                "message" => "$email is not a valid email address",
            ];
        }

        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            return [
                "success" => false,
                "mesage" => "Invalid credentials"
            ];
        }

        if (!password_verify($password, $user->getPassword())) {
            return [
                "success" => false,
                "message" => "Invalid credentials"
            ];
        }

        // if everything is fine
        return [
            "success" => true,
            "message" => "Successful login",
            "user" => $user
        ];
    }

    public function getProfile($id): array {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            return [
                "success" => false,
                "message" => "User not found"
            ];
        }
        return [
            "success" => true,
            "user" => $user
        ];
    }

    public function updateProfile($userId, $username, $email): array {
        // check if user exists
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            return [
                "success" => false,
                "message" => "User not found"
            ];
        }

        // check if new email is taken by another user
        $existingUser = $this->userRepo->findByEmail($email);
        if ($existingUser && $existingUser->getId() !== $userId) {
            return [
                "success" => false,
                "message" => "Email already taken"
            ];
        }

        $user->setUsername($username);
        $user->setEmail($email);

        try {
            $updatedUser = $this->userRepo->update($user);
            return [
                "success" => true,
                "message" => "User updated successfully",
                "user" => $updatedUser
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Update failed" . $e->getMessage()
            ];
        }

    }

    public function deleteProfile($id): array {
        $user = $this->userRepo->findById($id);

        if (!$user) {
            return [
                "success" => false,
                "message" => "User not found"
            ];
        }

        try {
            $this->userRepo->delete($id);
            return [
                "success" => true,
                "message" => "User deleted succesfully"
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Deletion failed: " . $e->getMessage()
            ];
        }
    }
}