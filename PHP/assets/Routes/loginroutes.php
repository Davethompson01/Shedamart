<?php

namespace App\Models;

require_once(__DIR__ . "/../../config/Database.php");
require_once(__DIR__ . "/../Models/loginuser.php");
require_once(__DIR__ . "/../views/login.php");
require_once(__DIR__ . "/../../utilities/TokenGenerator.php");

use App\Controllers\LoginController;
use App\Models\User;
use App\Controllers\LoginView;
use App\Config\Database;
use App\Utilities\TokenGenerator;

class LoginRoute {
    private $loginController;
    private $database;
    private $loginView;

    public function __construct() {
        $this->database = new Database();
        $this->database = $this->database->getConnection(); // Use the getConnection method
        $this->loginController = new LoginController(new User($this->database), new TokenGenerator());
        $this->loginView = new LoginView();
    }

    public function handleLogin() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        if (empty($email) || empty($password)) {
            $response = ['status' => 'error', 'message' => 'Empty email or password'];
        } else {
            $response = $this->loginController->handleLogin($email, $password);
        }

        $this->loginView->render($response);
    }
}
