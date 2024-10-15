<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


use App\Controllers\ProductController;
require_once __DIR__ . "/../assets/Controllers/createproduct.php";
require_once __DIR__ . "/../assets/Routes/createproduct.php";
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Check if JSON data is valid and contains the required fields
if ($data === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

$categoryName = $data['category_name'] ?? null;
$productData = [
    'product_image' => $data['product_image'] ?? null,
    'price' => $data['price'] ?? null,
    'product_details' => $data['product_details'] ?? null,
    'colors' => $data['colors'] ?? null,
    'amount_in_stock' => $data['amount_in_stock'] ?? null,
    'origin' => $data['origin'] ?? null,
    'amount_of_rating' => $data['amount_of_rating'] ?? null,
    'about_items' => $data['about_items'] ?? null,
    'product_name' => $data['product_name'] ?? null
];

// Check if required fields are missing
if (!$categoryName || !$productData['product_image'] || !$productData['price'] || !$productData['product_name']) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Call the static method to insert the product into the correct table
if (ProductController::insertProduct($categoryName, $productData)) {
    echo json_encode(['status' => 'success', 'message' => 'Product inserted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to insert product']);
}