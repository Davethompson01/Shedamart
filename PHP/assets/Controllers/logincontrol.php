<?php

namespace App\Controllers;
require_once __DIR__ . "/../Models/loginuser.php";
require_once __DIR__ . "/../../utilities/tokengenerator.php";
require_once __DIR__ . "/../../utilities/authorisation.php"; // Add this

use App\Models\User;
use App\Utilities\TokenGenerator;
use App\Utilities\Authorization;

class LoginController {
    private $userModel;
    private $tokenGenerator;
    private $authorization;

    public function __construct(User $userModel, TokenGenerator $tokenGenerator, Authorization $authorization) {
        $this->userModel = $userModel;
        $this->tokenGenerator = $tokenGenerator;
        $this->authorization = $authorization; // Add authorization
    }

    public function handleLogin($email, $password) {
        // Authenticate user (assuming userModel has a method for this)
        $user = $this->userModel->checkUser($email, $password);

        if ($user) {
            $userId = $user['id'] ?? null;  // Use null if 'id' is not set
            $userRole = $user['role'] ?? 'user';  // Default to 'user' if 'role' is not set
        
            // Now generate the token or do whatever processing you need
            $jwtToken = $this->tokenGenerator->generateToken($userId, $user['username'], $userRole);
        
            // Return response
            return [
                'status' => 'success',
                'message' => 'Login successful.',
                'token' => $jwtToken,
                'user_details' => $user
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid email or password.'
            ];
        }
        
    }
    


    // Example of a function that requires authorization
    // public function getProfile($request) {
    //     $token = $request['token']; // Assume token is passed in request headers
    //     $authResponse = $this->authorization->authorize($token);

    //     if ($authResponse['status'] === 'error') {
    //         return ['status' => 'error', 'message' => $authResponse['message']];
    //     }

    //     // Authorization successful, proceed with profile retrieval
    //     $userData = $authResponse['data'];
    //     return ['status' => 'success', 'message' => 'Profile retrieved.', 'user' => $userData];
    // }
}
