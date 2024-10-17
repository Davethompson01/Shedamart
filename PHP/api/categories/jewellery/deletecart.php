<?php

namespace App\Routes;
require_once __DIR__ . "/../../../assets/Controllers/categories/createjewel.php";
use App\Controllers\JewelryController;

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $jewelryId = (int)$_GET['id'];
        $response = JewelryController::deleteJewelry($jewelryId);
        echo json_encode($response);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Jewelry ID is required for deletion.']);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}