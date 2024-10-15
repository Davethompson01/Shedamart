
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once(__DIR__ . '/../config/Database.php');
require_once(__DIR__ . '/../assets/Controllers/admin_signip.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new App\Controllers\SignupController();

    $data = json_decode(file_get_contents('php://input'), true);
    // if (isset($data['role'])  === 'admin') {
        $controller->handleAdminSignup();
        exit;
    // } 
    
} else {
    header("HTTP/1.1 405 Method Not Allowed");
}