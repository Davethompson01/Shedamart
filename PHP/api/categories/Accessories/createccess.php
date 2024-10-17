<?php

// namespace App\Routes;

use App\Controllers\JewelryController;
require_once __DIR__ . "/../../../assets/Controllers/createaccessories.php";

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $response = JewelryController::createJewelry($data);
    echo json_encode($response);
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
