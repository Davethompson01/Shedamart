<?php

// namespace App\Routes;

use App\Controllers\AccessoryController;
require_once __DIR__ . "/../../../assets/Controllers/categories/createaccessories.php";

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $response = AccessoryController::createAccessories($data);
    echo json_encode($response);
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
