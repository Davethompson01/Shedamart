<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../assets/Models/product.php';
require_once __DIR__ . '/../assets/Controllers/products.php';
use App\Controllers\ProductController;
$productController = new ProductController();
$limit = 20;
$response = $productController->getLastUpdatedProducts($limit);
header('Content-Type: application/json');
echo json_encode($response);


