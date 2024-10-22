<?php

require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../Models/product.php";
require_once __DIR__ . "/../Controllers/products.php";
require_once __DIR__ . "/../../utilities/tokengenerator.php";

use App\Config\Database;
use App\Models\Product;
use App\Controllers\ProductController;
use App\Utilities\TokenGenerator;

// Create database connection
$database = new Database();
$db = $database->getConnection();
Product::setDatabase($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Get the token from the headers
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        echo json_encode(['status' => 'error', 'message' => 'Authorization token not provided.']);
        exit;
    }

    // Extract the token
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    // Create the product controller instance
    $productController = new ProductController();

    // Call the uploadProducts method with the token
    $response = $productController->uploadProducts($input, $token);
    echo json_encode($response);
} else {
    // Handle invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
