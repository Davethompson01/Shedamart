<?php

namespace App\Controllers;
require_once __DIR__ . "/../Models/loginuser.php";
require_once __DIR__ . "/../../utilities/tokengenerator.php";
use App\Models\User;
use App\Utilities\TokenGenerator;

class LoginController {
    private $userModel;
    private $tokenGenerator;

    public function __construct(User $userModel, TokenGenerator $tokenGenerator) {
        $this->userModel = $userModel;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function handleLogin($email, $password) {
        $user = $this->userModel->checkUser($email, $password);
    
        if (is_array($user)) {
            $userType = $user['user_type']; // 'admin' or 'user'
            $jwtToken = $this->tokenGenerator->generateToken(
                $user['user_id'], 
                $user['username'], 
                $userType,
                $userType === 'user' ? $user : null, // Include user info if it's a user
                $userType === 'admin' ? $user : null // Include admin info if it's an admin
            );

            return ['status' => 'success', 'message' => 'Login successful.', 'token' => $jwtToken, 'user_details' => $user];
        } elseif ($user === "Wrong") {
            return ['status' => 'error', 'message' => 'Invalid password.'];
        } else {
            return ['status' => 'error', 'message' => 'Invalid email or password.'];
        }
    }
}
