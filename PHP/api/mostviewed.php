<?php

use App\Controllers\ProductController;
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../assets/Models/product.php';
require_once __DIR__ . '/../assets/Controllers/products.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productController = new ProductController();
    $response = $productController->getMostCheckedCategory();
    echo json_encode($response);
}
