<?php

namespace App\Controllers;

require_once(__DIR__ . "/../../vendor/autoload.php");
require_once(__DIR__ . '/../Models/adminsignup.php');
require_once(__DIR__ . "/../../utilities/tokengenerator.php");
require_once(__DIR__ . "/../Requests/adminrequest.php");

use App\config\Database;
use App\Models\User;
use App\Utilities\TokenGenerator;
use App\Requests\SignupRequest;

class SignupController {
    private $userModel;
    private $tokenGenerator;

    public function __construct() {
        $this->userModel = new User(new Database());
        $this->tokenGenerator = new TokenGenerator();
    }

    public function handleSignup($role) {
        header('Content-Type: application/json');
        $signupRequest = new SignupRequest();
        $data = $signupRequest->validateSignupData();

        if (!$data) {
            return; // Errors are already handled in the request class
        }

        if ($this->userModel->checkEmail($data['email'])) {
            $this->sendResponse(409, ['status' => 'error', 'message' => 'Email already taken.']);
            return;
        }

        $userId = ($role === 'admin') ? $this->userModel->createAdminUser($data) : $this->userModel->createUser($data);
        
        if ($userId) {
            $token = $this->tokenGenerator->generateToken($userId, $data['username'], $role);
            $this->userModel->updateToken($userId, $token);
            $this->sendResponse(201, [
                'status' => 'success',
                'message' => ucfirst($role) . ' signup successful',
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'number' => $data['number'],
                'userType' => $role,
                'token' => $token,
            ]);
        } else {
            $this->sendResponse(500, ['status' => 'error', 'message' => ucfirst($role) . ' signup failed. Please try again.']);
        }
    }

    private function sendResponse($statusCode, array $response) {
        http_response_code($statusCode);
        echo json_encode($response);
        exit;
    }

    public function handleAdminSignup() {
        $this->handleSignup('admin');
    }

    public function handleUserSignup() {
        $this->handleSignup('user');
    }
}
