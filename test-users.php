<?php
require_once 'Database/Connection.php';
require_once 'Models/User.php';
require_once 'Repositories/BaseRepository.php';
require_once 'Repositories/UserRepository.php';

use Models\User;
use Repositories\UserRepository;

echo "--- Testing user ---\n\n";

try {
    // create user repository
    $userRepo = new UserRepository();
    echo "User repository created successfully\n\n";

    echo "1. Creating a new user\n";

    $user = new User(
        null,
        "jdoe",
        "john.doe@example.com",
        "123456",
        null,
        null
    );

    // test 1: create user
    $createdUser = $userRepo->create($user);

    echo "Created user with ID: " . $createdUser->getId() . "\n";
    echo "Username: " . $createdUser->getUsername() . "\n";
    echo "Email: " . $createdUser->getEmail() . "\n\n";

    // test 2: find by id
    echo "2. Finding user by ID " . $createdUser->getId() . "...\n";
    $foundUser = $userRepo->findById($createdUser->getId());
    if ($foundUser) {
        echo "Found: " . $foundUser->getUsername() . "\n\n";
    } else {
        echo "Not found!\n\n";
    }
    
    // test 3: find by email
    echo "3. Finding user by email...\n";
    $foundByEmail = $userRepo->findByEmail('john.doe@example.com');
    if ($foundByEmail) {
        echo "   Found: " . $foundByEmail->getUsername() . "\n\n";
    }
    
    // test 4: check if email exists
    echo "4. Checking if email exists...\n";
    if ($userRepo->emailExists('john.doe@example.com')) {
        echo "Email exists\n\n";
    } else {
        echo "Email doesn't exist\n\n";
    }
    
    // test 5: update user
    echo "5. Updating user...\n";
    $foundUser->setUsername('john_updated');
    $updatedUser = $userRepo->update($foundUser);
    echo "New username: " . $updatedUser->getUsername() . "\n\n";
    
    // test 6: delete user
    echo "6. Deleting user...\n";
    if ($userRepo->delete($updatedUser->getId())) {
        echo "User deleted\n\n";
    } else {
        echo "Failed to delete user!\n\n";
    }
    
    // test 7: verify deletion
    echo "7. Checking if user still exists...\n";
    $deletedUser = $userRepo->findById($updatedUser->getId());
    if ($deletedUser === null) {
        echo "User is gone\n";
    } else {
        echo "User still exists\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}