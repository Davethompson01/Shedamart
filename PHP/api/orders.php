<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../assets/Models/product.php';
require_once __DIR__ . '/../assets/Models/Order.php';
require_once __DIR__ . '/../assets/Controllers/order.php';
require_once __DIR__ . '/../assets/Utilities/Authorization.php'; // Include your Authorization class

use App\Config\Database;
use App\Models\Product;
use App\Controllers\OrderController; // Fixed use statement
use App\Utilities\Authorization; // Add the Authorization class

$database = new Database();
$db = $database->getConnection();
Product::setDatabase($db);
OrderController::setDatabase($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['product_id']) || !isset($input['order_quantity'])) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID and quantity are required']);
        exit;
    }

    // Fetch the JWT token from the Authorization header
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    $authorization = new Authorization('1234Sheda'); // Your secret key
    $authResponse = $authorization->authorize($token);

    if ($authResponse['status'] === 'error') {
        echo json_encode($authResponse); // Return error if token is invalid
        exit;
    }

    // Extract user ID from the decoded token
    $userId = $authResponse['data']['id']; // User ID from the JWT

    $orderController = new OrderController();
    $response = $orderController->createOrder($userId, $input);
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
