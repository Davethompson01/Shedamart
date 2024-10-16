<?php

// namespace App\Routes;

use App\Controllers\JewelryController;

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default limit
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0; // Default offset
    $response = JewelryController::getAllJewelry($limit, $offset);
    echo json_encode($response);
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
