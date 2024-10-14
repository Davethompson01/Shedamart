<?php
namespace App\Utilities;

use Firebase\JWT\JWT;

class TokenGenerator {
    private $secretKey = "1234Sheda";

    // Adjust the parameters to handle both employeeId and employerId correctly
    public function generateToken($userId, $username, $userType, $user = null, $admin = null) {
        $issuedAt = time(); 
        $expirationTime = $issuedAt + 3600;
        
        $payload = [
            "iss" => "your_domain.com",
            "aud" => "your_domain.com",
            "iat" => $issuedAt,
            'exp' => $expirationTime,
            "data" => [
                "id" => $userId,
                "username" => $username,
                "user_type" => $userType,
                "admin" => $admin,
                "user" => $user,
            ]
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
