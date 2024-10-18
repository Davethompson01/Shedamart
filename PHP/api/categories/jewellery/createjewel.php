<?php

// namespace App\Routes;

use App\Controllers\JewelryController;
require_once __DIR__ . "/../../../assets/Controllers/categories/createjewel.php";

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['jewelryDataArray']) && isset($data['token'])) {
        $jewelryDataArray = $data['jewelryDataArray'];
        $token = $data['token'];
        $result = JewelryController::createJewelry($jewelryDataArray, $token); 
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
