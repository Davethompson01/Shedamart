<?php



require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../assets/Models/product.php';
require_once __DIR__ . '/../../assets/Controllers/products.php';

use App\Controllers\ProductController;

// Create the product controller instance
$productController = new ProductController();

// Fetch products by category 'jewelry'
$categoryName = 'Supermarket';
$response = $productController->getProductsByCategory($categoryName);

header('Content-Type: application/json');
echo json_encode($response);
