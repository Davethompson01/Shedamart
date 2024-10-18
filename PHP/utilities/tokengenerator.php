<?php

namespace App\Utilities;

use Firebase\JWT\JWT;

class TokenGenerator { 
    private $secretKey = "1234Sheda"; 

    public function generateToken($userId, $username, $userType, $user = null, $admin = null) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // 1 hour expiry
        $payload = [
            "iss" => "your_domain.com",
            "aud" => "your_domain.com",
            "iat" => $issuedAt,
            'exp' => $expirationTime,
            "data" => [
                "id" => $userId,
                "username" => $username,
                "user_type" => $userType,
                "admin" => $admin, // Admin details
                "user" => $user // User details
            ]
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
