<?php

namespace App\Controllers;

use App\Models\JewelryModel;
use Exception;

$requestMethod = $_SERVER['REQUEST_METHOD'];
// $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($uri[1] === 'jewelry') {
    switch ($requestMethod) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $response = JewelryController::createJewelry($data);
            echo json_encode($response);
            break;
      
        
        default:
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['status' => 'error', 'message' => 'Route not found']);
}
