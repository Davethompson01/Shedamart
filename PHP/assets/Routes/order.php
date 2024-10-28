<?php


require_once __DIR__ . '/../Requests/order.php'; // Include the Request class
use App\Http\Request; // Import the Request class

class Router {
    private $routes = [];

    public function post($route, $callback) {
        $this->routes['POST'][$route] = $callback;
    }

    public function get($route, $callback) {
        $this->routes['GET'][$route] = $callback;
    }
    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = strtok($_SERVER['REQUEST_URI'], '?'); // Removes query string
    
        error_log("Request Method: $requestMethod");
        error_log("Request URI: $requestUri");
    
        // Print all routes for debugging
        error_log("Registered Routes: " . print_r($this->routes, true));
    
        if (isset($this->routes[$requestMethod][$requestUri])) {
            $callback = $this->routes[$requestMethod][$requestUri];
            echo $callback(new Request());
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
    }
    
    
// public function run() {
//     echo json_encode(['message' => 'Router is working!']);
// }

}
