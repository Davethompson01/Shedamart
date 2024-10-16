<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

use App\Controllers\ProductController;
require_once __DIR__ . "/../assets/Controllers/createproduct.php";

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProducts = ProductController::getNewlyCreatedProducts();
    if ($newProducts) {
        echo json_encode(['status' => 'success', 'data' => $newProducts]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products']);
    }
// } else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
// }
