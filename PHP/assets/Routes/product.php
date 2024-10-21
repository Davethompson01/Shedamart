<?php

require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../Models/product.php";
require_once __DIR__ . "/../Controllers/products.php";

use App\Config\Database;
require_once __DIR__ . "/../Models/product.php";
use App\Models\Product;

use App\Controllers\ProductController;

// Create database connection
$database = new Database();
$db = $database->getConnection();
Product::setDatabase($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON input (expecting an array of products)
    $input = json_decode(file_get_contents('php://input'), true);

    // Create an instance of ProductController
    $productController = new ProductController();

    // Call the uploadProducts method on the instance
    $response = $productController->uploadProducts($input);
    echo json_encode($response);
} else {
    // Handle invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
