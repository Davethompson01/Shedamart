<?php

namespace App\Utilities;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authorization {
    private $secretKey="1234Sheda";

    public function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }
    
    public function authorize($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            var_dump($decoded);
            return [
                'status' => 'success',
                'data' => [
                    'id' => $decoded->data->id,
                    'username' => $decoded->data->username,
                    'role' => $decoded->data->role // Extract the role
                ]
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Unauthorized: Invalid token'];
        }
    }
}
