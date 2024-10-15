<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once __DIR__ . "/../assets/Routes/category.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    CategoryController::listCategories();
} else{
    echo "Invalid method passed";
}