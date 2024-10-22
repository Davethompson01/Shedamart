<?php


namespace App\Controllers;

require_once(__DIR__ . "/../../vendor/autoload.php");
require_once(__DIR__ . '/../Models/adminsignup.php');
require_once(__DIR__ . "/../../utilities/tokengenerator.php");
require_once(__DIR__ . "/../Requests/adminrequest.php");
use App\Config\Database;
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
        $this->processSignup(false);
    }

    public function handleAdminSignup() {
        $this->processSignup(true);
    }

    private function processSignup($isAdminSignup) {
        header('Content-Type: application/json');
        $signupRequest = new SignupRequest();
        $data = $signupRequest->validateSignupData();
        $data['ip_address'] = $this->getIpAddress();

        if (!$data) {
            return; // Errors handled in the request class
        }

        if ($this->userModel->checkEmail($data['email'])) {
            $this->sendResponse(['status' => 'error', 'message' => 'Email already taken.']);
            return;
        }

        $userId = $isAdminSignup ? $this->userModel->createAdminUser($data) : $this->userModel->createUser($data);
        
        if ($userId) {
            $token = $this->tokenGenerator->generateToken($userId, $data['username'], $data['email'], $isAdminSignup);
            $this->userModel->updateToken($userId, $token);

            $this->sendResponse([
                'status' => 'success',
                'message' => $isAdminSignup ? 'Admin signup successful' : 'Signup successful',
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'number' => $data['number'],
                'userType' => $isAdminSignup ? 'admin' : 'user',
                'token' => $token,
            ]);
        } else {
            $this->sendResponse(['status' => 'error', 'message' => 'Signup failed. Please try again.']);
        }
    }

    private function getIpAddress() {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        return filter_var($ipAddress, FILTER_VALIDATE_IP) ? $ipAddress : 'UNKNOWN';
    }

    private function sendResponse(array $response) {
        echo json_encode($response);
        exit;
    }
}
