<?php

namespace App\Models;

require_once(__DIR__ . "/../../config/Database.php");
require_once(__DIR__ . "/../Models/loginuser.php");
require_once(__DIR__ . "/../views/login.php");
require_once(__DIR__ . "/../../utilities/TokenGenerator.php");
require_once(__DIR__ . "/../../utilities/authorisation.php"); 

use App\Controllers\LoginController;
use App\Models\User;
use App\Controllers\LoginView;
use App\Config\Database;
use App\Utilities\TokenGenerator;
use App\Utilities\Authorization;

class LoginRoute {
    private $loginController;
    private $database;
    private $loginView;

    public function __construct() {
        // Create database connection
        $this->database = new Database();
        $this->database = $this->database->getConnection();

        // Initialize necessary utilities and controllers
        $userModel = new User($this->database);
        $tokenGenerator = new TokenGenerator();
        $authorization = new Authorization('1234Sheda'); // Add your secret key here

        // Pass all three dependencies to the LoginController
        $this->loginController = new LoginController($userModel, $tokenGenerator, $authorization);
        $this->loginView = new LoginView();
    }

    public function handleLogin() {
        // Get input from request body (JSON)
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        // Check if email and password are provided
        if (empty($email) || empty($password)) {
            $response = ['status' => 'error', 'message' => 'Empty email or password'];
        } else {
            // Delegate to the login controller
            $response = $this->loginController->handleLogin($email, $password);
        }

        // Render the response via the view
        $this->loginView->render($response);
    }
}
