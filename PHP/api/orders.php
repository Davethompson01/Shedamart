<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../assets/Models/product.php';
require_once __DIR__ . '/../assets/Models/Order.php';
require_once __DIR__ . '/../assets/Controllers/order.php';

use App\Config\Database;
use App\Models\Product;
use App\Controllers\OrderController;

$database = new Database();
$db = $database->getConnection();
Product::setDatabase($db);
OrderController::setDatabase($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if required fields are present
    if (!isset($input['user_id']) || !isset($input['product_token']) || !isset($input['order_quantity'])) {
        echo json_encode(['status' => 'error', 'message' => 'User ID, product token, and quantity are required']);
        exit;
    }

    $userId = $input['user_id']; // Get user ID from the input
    $orderController = new OrderController();
    $response = $orderController->createOrder($input); // Pass the entire input
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
