<?php
// Front controller for Salvio POS system

// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Autoload classes (assuming PSR-4 or simple autoloader)
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/core/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load config
require_once __DIR__ . '/config/config.php';

// Simple router based on URL path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove trailing slash
$uri = rtrim($uri, '/');

// Remove leading slash
$uri = ltrim($uri, '/');

// Define routes
$routes = [
    '' => 'DashboardController@index',
    'login' => 'AuthController@login',
    'logout' => 'AuthController@logout',
    'admin/dashboard' => 'DashboardController@index',
    'admin/products' => 'ProductController@index',
    'admin/sales' => 'SalesController@index',
    'admin/orders' => 'OrderController@index',
    'admin/users' => 'UserController@index',
    'admin/settings' => 'SettingsController@index',
    // Add more routes here as needed
];

// Route handling
if (array_key_exists($uri, $routes)) {
    list($controllerName, $method) = explode('@', $routes[$uri]);
    $controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $method)) {
                $controller->$method();
                exit;
            }
        }
    }
}

// If no route matched, show 404
http_response_code(404);
echo "404 Not Found";
exit;
?>
