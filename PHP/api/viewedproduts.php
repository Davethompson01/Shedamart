<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

use App\Models\ProductModel;
require_once __DIR__ . "/../assets/Models/createproduct.php";


$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

$productID = $data['product_id'] ?? null;

if ($productID === null) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is missing']);
    exit;
}

$productModel = new ProductModel();

$query = "UPDATE products SET views = views + 1 WHERE id = :product_id";
$stmt = $productModel->self::db->prepare($query);
$stmt->bindParam(':product_id', $productID, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Product view incremented']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to increment product view']);
}
