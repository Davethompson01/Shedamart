<?php

namespace App\Utilities;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once __DIR__ . '/../vendor/autoload.php';

class Authorization {
    private $secretKey="1234Sheda";

    public function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }
    
    public function authorize($token) {
        // Log the token for debugging
        error_log("Token received: " . $token);
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            var_dump($decoded);
            return [
                'status' => 'success',
                'data' => [
                    'id' => $decoded->data->id,
                    'username' => $decoded->data->username,
                    'role' => $decoded->data->role
                ]
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Unauthorized: Invalid token. Error: ' . $e->getMessage()];
        }
    }
}
