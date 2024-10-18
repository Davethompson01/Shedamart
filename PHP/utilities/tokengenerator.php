<?php
namespace App\Utilities;


require_once __DIR__ . "/../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class TokenGenerator { 
    private static $secretKey = '1234Sheda';
    private static $algorithm = 'HS256'; 

    public function generateToken($userId, $username, $userRole) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // 1 hour expiry
        $payload = [
            'iss' => 'yourdomain.com',  // Issuer
            'iat' => $issuedAt,          // Issued at
            'exp' => $expirationTime,     // Expiration time (1 hour)
            'data' => [
                'id' => $userId,
                'username' => $username,
                'role' => $userRole // Add the user's role to the JWT payload
            ]
        ];

        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    public static function decodeToken($token) {
        try {
            $secretKey = self::$secretKey; // Store the secret key in a variable
            
            $decoded = JWT::decode($token, $secretKey);
            
            return $decoded; // Return decoded object
        } catch (ExpiredException $e) {
            return (object)[
                'status' => 'error',
                'message' => 'Token has expired.'
            ];
        } catch (SignatureInvalidException $e) {
            return (object)[
                'status' => 'error',
                'message' => 'Invalid token signature.'
            ];
        } catch (\Exception $e) {
            return (object)[
                'status' => 'error',
                'message' => 'An error occurred while decoding the token: ' . $e->getMessage()
            ];
        }
    }
    
    
    
    
    
}
