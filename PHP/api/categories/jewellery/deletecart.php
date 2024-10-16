<?php

// namespace App\Controllers;

require_once __DIR__ . "/../../../assets/Controllers/createjewel.php";

// use App\Models\JewelryModel;
use App\Models\JewelryController;
// use Exception;

$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($uri[1] === 'jewelry') {
    switch ($requestMethod) {
        case 'DELETE':
            if (isset($uri[2]) && is_numeric($uri[2])) {
                $jewelryId = (int)$uri[2];
                $response = JewelryController::deleteJewelry($jewelryId);
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Jewelry ID is required.']);
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
