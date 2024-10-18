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
        $user = $this->userModel->checkUser($email, $password);
    
        if (is_array($user)) {
            $userType = $user['user_type'];
            $userId = $userType === 'admin' ? $user['admin_id'] : $user['user_id'];
    
            $jwtToken = $this->tokenGenerator->generateToken(
                $userId,
                $user['username'],
                $userType
            );
    
            return [
                'status' => 'success', 
                'message' => 'Login successful.', 
                'token' => $jwtToken, 
                'user_details' => $user
            ];
        } elseif ($user === "Wrong") {
            return ['status' => 'error', 'message' => 'Invalid password.'];
        } else {
            return ['status' => 'error', 'message' => 'Invalid email or password.'];
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
