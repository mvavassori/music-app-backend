<?php
namespace Controllers;

use Services\UserService;
use Exceptions\ValidationException;
use Exceptions\DuplicateEntityException;
use Exceptions\NotFoundException;
use Exceptions\AuthenticationException;

class UserController extends BaseController {
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    // POST /users/register
    public function register() {
        try {
            // req body
            $data = $this->getJsonInput();

            $user = $this->userService->register(
                username: $data["username"] ?? '',
                email: $data["email"] ?? '',
                password: $data["password"] ?? '',
            );

            // don't send password in response
            $this->sendResponse([
                "message" => "User registered successfully",
                "user" => [
                    "id" => $user->getId(),
                    "username" => $user->getUsername(),
                    "email" => $user->getEmail(),
                    "createdAt" => $user->getCreatedAt()
                ]
            ]);
        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (DuplicateEntityException $e) {
            $this->sendError($e->getMessage(), 409);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Registration failed', 500);
        }
    }

    // POST /users/login 
    public function login(): void {
        try {
            $data = $this->getJsonInput();

            $user = $this->userService->login(
                $data['email'] ?? '',
                $data['password'] ?? ''
            );

            // just return the user data for learning purposes
            $this->sendResponse([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail()
                ]
            ]);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (AuthenticationException $e) {
            $this->sendError($e->getMessage(), 401);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Login failed', 500);
        }
    }

    // GET /users/{id}
    public function getProfile(int $id): void {
        try {
            $user = $this->userService->getProfile($id);

            $this->sendResponse([
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'created_at' => $user->getCreatedAt(),
                    'updated_at' => $user->getUpdatedAt()
                ]
            ]);

        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Failed to fetch profile', 500);
        }
    }

    // PUT /users/{id}
    public function updateProfile(int $id): void {
        try {
            $data = $this->getJsonInput();

            $user = $this->userService->updateProfile($id, $data);

            $this->sendResponse([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'updated_at' => $user->getUpdatedAt()
                ]
            ]);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (DuplicateEntityException $e) {
            $this->sendError($e->getMessage(), 409);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Failed to update profile', 500);
        }
    }

    // PUT /users/{id}/password
    public function changePassword(int $id): void {
        try {
            $data = $this->getJsonInput();

            $this->userService->changePassword(
                $id,
                $data['current_password'] ?? '',
                $data['new_password'] ?? ''
            );

            $this->sendResponse(['message' => 'Password changed successfully']);

        } catch (ValidationException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (AuthenticationException $e) {
            $this->sendError($e->getMessage(), 401);
        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Failed to change password', 500);
        }
    }

    // DELETE /users/{id}
    public function deleteProfile(int $id): void {
        try {
            $this->userService->deleteProfile($id);

            $this->sendResponse(['message' => 'Account deleted successfully']);

        } catch (NotFoundException $e) {
            $this->sendError($e->getMessage(), 404);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->sendError('Failed to delete account', 500);
        }
    }
}