<?php

namespace App\Utilities;

require_once __DIR__ . "/../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class TokenGenerator {
    private static $secretKey = '1234Sheda';  // Store your secret key securely
    private static $algorithm = 'HS256';      // Hashing algorithm for JWT

    // Generate token with different payloads for admin and user
    public function generateToken($userId, $username, $userRole) {
        $issuedAt = time();
        
        // Set a longer expiration time for admins (e.g., 2 hours) and shorter for users (e.g., 1 hour)
        $expirationTime = ($userRole === 'admin') ? $issuedAt + 7200 : $issuedAt + 3600;
        
        // Build the token payload based on the user role
        $payload = [
            'iss' => 'yourdomain.com',   // Issuer
            'iat' => $issuedAt,          // Issued at
            'exp' => $expirationTime,    // Expiration time (depends on user type)
            'data' => [
                'id' => $userId,
                'username' => $username,
                'role' => $userRole,      // Include the user's role
                // You can add more claims here for admins or users as needed
                'privileges' => $userRole === 'admin' ? 'all-access' : 'limited-access'
            ]
        ];

        // Encode the JWT with the payload and secret key
        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    // Enhanced decodeToken with better error handling and logging
    public static function decodeToken($token) {
        try {
            // Attempt to decode the token
            $decoded = JWT::decode($token, self::$secretKey, [self::$algorithm]);
            return [
                'status' => 'success',
                'data' => $decoded
            ];
        } catch (ExpiredException $e) {
            // Handle token expiration
            return [
                'status' => 'error',
                'message' => 'Token has expired.',
                'error_type' => 'expired_token',
                'timestamp' => time()  // Log the current time for debugging
            ];
        } catch (SignatureInvalidException $e) {
            // Handle invalid token signatures
            return [
                'status' => 'error',
                'message' => 'Invalid token signature.',
                'error_type' => 'invalid_signature',
                'timestamp' => time()
            ];
        } catch (\Exception $e) {
            // Catch any other JWT decoding-related errors
            return [
                'status' => 'error',
                'message' => 'An error occurred while decoding the token: ' . $e->getMessage(),
                'error_type' => 'general_error',
                'timestamp' => time()
            ];
        }
    }
}
