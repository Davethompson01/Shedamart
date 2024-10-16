<?php

use App\Controllers\JewelryController;

$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($uri[1] === 'jewelry') {
    switch ($requestMethod) {
        case 'POST':
            if (isset($uri[2]) && is_numeric($uri[2])) {
                $jewelryId = (int)$uri[2];
                $response = JewelryController::getJewelry($jewelryId);
                echo json_encode($response);
            } else {
                $limit = isset($uri[3]) ? (int)$uri[3] : 10;
                $offset = isset($uri[4]) ? (int)$uri[4] : 0;
                $response = JewelryController::getAllJewelry($limit, $offset);
                echo json_encode($response);
            }
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
