<?php

use Services\SongService;
use Services\UserService;
use Services\ArtistService;
use Controllers\SongController;
use Controllers\UserController;
use Repositories\SongRepository;
use Repositories\UserRepository;
use Controllers\ArtistController;
use Repositories\ArtistRepository;

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Manual imports - load all required classes
require_once '../Core/Router.php';
require_once '../Database/Connection.php';

// Models
require_once '../Models/Enums/Genre.php';
require_once '../Models/User.php';
require_once '../Models/Artist.php';
require_once '../Models/Song.php';

// Exceptions
require_once '../Exceptions/ValidationException.php';
require_once '../Exceptions/NotFoundException.php';
require_once '../Exceptions/DuplicateEntityException.php';
require_once '../Exceptions/AuthenticationException.php';

// Repositories
require_once '../Repositories/BaseRepository.php';
require_once '../Repositories/UserRepository.php';
require_once '../Repositories/ArtistRepository.php';
require_once '../Repositories/SongRepository.php';

// Services
require_once '../Services/UserService.php';
require_once '../Services/ArtistService.php';
require_once '../Services/SongService.php';

// Controllers
require_once '../Controllers/BaseController.php';
require_once '../Controllers/UserController.php';
require_once '../Controllers/ArtistController.php';
require_once '../Controllers/SongController.php';

// Create router and register routes
$router = new Router();

// Artist routes
$router->addRoute('GET', '/api/artists', 'ArtistController', 'getAll');
$router->addRoute('GET', '/api/artists/{id}', 'ArtistController', 'get');
$router->addRoute('POST', '/api/artists', 'ArtistController', 'create');
$router->addRoute('PUT', '/api/artists/{id}', 'ArtistController', 'update');
$router->addRoute('DELETE', '/api/artists/{id}', 'ArtistController', 'delete');

// Song routes
$router->addRoute('GET', '/api/songs', 'SongController', 'getAll');
$router->addRoute('GET', '/api/songs/{id}', 'SongController', 'get');
$router->addRoute('POST', '/api/songs', 'SongController', 'create');
$router->addRoute('PUT', '/api/songs/{id}', 'SongController', 'update');
$router->addRoute('DELETE', '/api/songs/{id}', 'SongController', 'delete');
$router->addRoute('GET', '/api/songs/search/{query}', 'SongController', 'search');
$router->addRoute('GET', '/api/songs/genre/{genre}', 'SongController', 'getByGenre');

// User routes
$router->addRoute('POST', '/api/users/register', 'UserController', 'register');
$router->addRoute('POST', '/api/users/login', 'UserController', 'login');
$router->addRoute('GET', '/api/users/{id}', 'UserController', 'get');
$router->addRoute('PUT', '/api/users/{id}', 'UserController', 'update');
$router->addRoute('DELETE', '/api/users/{id}', 'UserController', 'delete');

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Dispatch the request
$route = $router->dispatch($requestMethod, $requestUri);

if ($route) {
    $controllerClass = $route['controller'];
    $action = $route['action'];
    $params = $route['params'];

    try {
        // Create controller instance with dependencies
        if ($controllerClass === 'ArtistController') {
            $repository = new ArtistRepository();
            $service = new ArtistService($repository);
            $controller = new ArtistController($service);
        } elseif ($controllerClass === 'SongController') {
            $songRepository = new SongRepository();
            $artistRepository = new ArtistRepository();
            $service = new SongService($songRepository, $artistRepository);
            $controller = new SongController($service);
        } elseif ($controllerClass === 'UserController') {
            $repository = new UserRepository();
            $service = new UserService($repository);
            $controller = new UserController($service);
        } else {
            throw new Exception("Unknown controller: $controllerClass");
        }

        // Call the controller method
        if (method_exists($controller, $action)) {
            if (!empty($params)) {
                call_user_func_array([$controller, $action], array_values($params));
            } else {
                $controller->$action();
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Method not found']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}