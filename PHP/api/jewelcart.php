<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once __DIR__ . "/../assets/Controllers/jewelcrt.php";
use App\Controllers\JewelryController;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$products = JewelryController::getJewelryProducts($page);

echo json_encode([
    'status' => 'success',
    'products' => $products,
    'currentPage' => $page,
]);
