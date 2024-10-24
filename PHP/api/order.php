<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../assets/Models/admin_users.php";
require_once __DIR__ . "/../assets/Models/product.php";
require_once __DIR__ . "/../assets/Models/order.php";
require_once __DIR__ . "/../assets/Controllers/products.php";
// require_once __DIR__ . "/../assets/Controllers/products.php";


use App\Controllers\ProductController;
header('Content-Type: application/json');
$headers = apache_request_headers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($headers['Authorization'])) {
        echo json_encode(['status' => 'error', 'message' => 'Authorization token not provided']);
        exit;
    }
    
    $token = trim(str_replace('Bearer ', '', $headers['Authorization']));
    $productController = new ProductController();
    $response = $productController->placeOrder($input, $token);
    echo json_encode($response);
}
