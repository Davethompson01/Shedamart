<?php

// namespace App\Routes;
require_once __DIR__ . "/../../../assets/Controllers/categories/createjewel.php";
use App\Controllers\JewelryController;

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
  
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default page is 1
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default limit is 10

   $response = JewelryController::displayJewel($page, $limit);
   
   echo json_encode($response);
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
