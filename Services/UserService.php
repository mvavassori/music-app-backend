<?php
namespace Services;

use Exceptions\AuthenticationException;
use Exceptions\NotFoundException;
use Models\User;
use Repositories\UserRepository;
use Exceptions\DuplicateEntityException;
use Exceptions\ValidationException;

class UserService {
    private UserRepository $userRepo;

    private const MIN_PASSWORD_LENGTH = 8;

    // inject userRepo
    public function __construct($userRepo) {
        $this->userRepo = $userRepo;
    }

    public function register(string $username, string $email, string $password): User {
        // validation method below
        $this->validateRegistrationData($username, $email, $password);

        if ($this->userRepo->emailExists($email)) {
            throw new DuplicateEntityException("Email address is already registered");
        }

        if ($this->userRepo->usernameExists($username)) {
            throw new DuplicateEntityException("Username is already taken");
        }

        // hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $user = new User(
            id: null,
            username: $username,
            email: $email,
            password: $hashedPassword,
            createdAt: null,
            updatedAt: null
        );

        // let PDOException bubble up from repository
        return $this->userRepo->create($user);
    }

    public function login($email, $password): User {
        // validation
        if (empty($email) || empty($password)) {
            throw new ValidationException("Email and password are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Invalid email format");
        }

        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new AuthenticationException("Invalid Credentials");
        }

        return $user;
    }

    public function getProfile($id): User {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new NotFoundException("User not found");
        }
        return $user;
    }

    public function updateProfile(int $userId, array $data): User {
        // check if user exists
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            throw new NotFoundException("User not found");
        }

        if (isset($data["username"])) {
            if (empty($data["username"])) {
                throw new ValidationException("Username cannot be empty");
            }
            $user->setUsername($data["username"]);
        }

        if (isset($data["email"])) {

            if (empty($data['email'])) {
                throw new ValidationException("Email cannot be empty");
            }

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException("{$data["email"]} is not a valid email");
            }

            $existingUser = $this->userRepo->findByEmail($data["email"]);
            if ($existingUser && $existingUser->getId() !== $userId) {
                throw new DuplicateEntityException("Email address is already registered");
            }

            $user->setEmail($data["email"]);
        }

        return $this->userRepo->update($user);
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): void {
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            throw new NotFoundException("User not found");
        }

        // verify current password
        if (!password_verify($currentPassword, $user->getPassword())) {
            throw new AuthenticationException("Current password is incorrect");
        }

        // validate new password
        if (strlen($newPassword) < self::MIN_PASSWORD_LENGTH) {
            throw new ValidationException("Password must be at least " . self::MIN_PASSWORD_LENGTH . " characters");
        }

        // hash and update
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userRepo->updatePassword($userId, $hashedPassword);
    }

    public function deleteProfile($id): bool {
        $user = $this->userRepo->findById($id);

        if (!$user) {
            throw new NotFoundException("User not found");
        }


        return $this->userRepo->delete($id);
    }

    private function validateRegistrationData(string $username, string $email, string $password): void {
        if (empty($username) || empty($email) || empty($password)) {
            throw new ValidationException("All fields are required");
        }

        if (strlen($username) < 3) {
            throw new ValidationException("Username must be at least 3 characters");
        }

        if (strlen($username) > 50) {
            throw new ValidationException("Username cannot exceed 50 characters");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Invalid email format");
        }

        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new ValidationException("Password must be at least " . self::MIN_PASSWORD_LENGTH . " characters");
        }
    }
}