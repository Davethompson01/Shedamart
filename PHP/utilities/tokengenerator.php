<?php

namespace App\Utilities;

require_once __DIR__ . "/../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class TokenGenerator {
    private static $secretKey = '1234Sheda';
    private static $algorithm = 'HS256';
    public function generateToken($userId, $username, $email, $isAdminSignup) {
        // Example code to assign role
        if ($isAdminSignup) {
            $role = 'admin'; // Ensure this is set based on the signup request
        } else {
            $role = 'user';
        }
    
        // Create the payload
        $payload = [
            "iat" => time(),
            "exp" => time() + (160 * 160), 
            "data" => [
                "id" => $userId,
                "username" => $username,
                "email" => $email, // Include the email
                "role" => $role
            ]

        ];
        
        // Encode and return the token
        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }
    

    // Enhanced decodeToken with better error handling and logging
    public static function decodeToken($token) {
        try {
            // Store static properties in local variables
            $secretKey = self::$secretKey;
    
            // Decode the token, passing the secret key wrapped in a Key object
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    
            return [
                'status' => 'success',
                'data' => $decoded
            ];
        } catch (ExpiredException $e) {
            return [
                'status' => 'error',
                'message' => 'Token has expired.',
                'error_type' => 'expired_token',
                'timestamp' => time()
            ];
        } catch (SignatureInvalidException $e) {
            return [
                'status' => 'error',
                'message' => 'Invalid token signature.',
                'error_type' => 'invalid_signature',
                'timestamp' => time()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An error occurred while decoding the token: ' . $e->getMessage(),
                'error_type' => 'general_error',
                'timestamp' => time()
            ];
        }
    }
}