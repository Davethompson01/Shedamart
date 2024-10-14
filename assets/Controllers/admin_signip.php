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

    public function handleSignup() {
        header('Content-Type: application/json');
        $signupRequest = new SignupRequest();
        function getIpAddress() {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            if (filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                return $ipAddress;
            } else {
                return 'UNKNOWN';
            }
        }
        
        
        $data = $signupRequest->validateSignupData();
        $data['ip_address'] = getIpAddress();
        

        if (!$data) {
            return; // Errors are already handled in the request class
        }

        if ($this->userModel->checkEmail($data['email'])) {
            $this->sendResponse(['status' => 'error', 'message' => 'Email already taken.']);
            return;
        }

        $userId = $this->userModel->createUser($data );
        // $token = $this->tokenGenerator->generateToken($userId, $data['username'], 'admin');
        if ($userId) {
            $token = $this->tokenGenerator->generateToken($userId, $data['username'], 'admin');
    
            // Now, update the user record with the token
            $this->userModel->updateToken($userId, $token);
    
            $this->sendResponse([
                'status' => 'success',
                'message' => 'Signup successful',
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'number' => $data['number'],
                'userType' => 'admin',
                'token' => $token,
            ]);
        } else {
            $this->sendResponse(['status' => 'error', 'message' => 'Signup failed. Please try again.']);
        }
    }


    private function updateUserToken($userId, $token) {
        $this->userModel->updateToken($userId, $token);
    }

    private function sendResponse(array $response) {
        echo json_encode($response);
        exit;
    }
}