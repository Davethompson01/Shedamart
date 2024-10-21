<?php

namespace App\Requests;

class SignupRequest {

    public function validateSignupData() {
        $input = json_decode(file_get_contents('php://input'), true);
        $requiredFields = ['username', 'email', 'number', 'country', 'password'];

        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                $this->sendResponse(['status' => 'error', 'message' => "$field is required."]);
                return null;
            }
        }
        return $input;
    }

    private function sendResponse(array $response) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($response);
        exit;
    }

    public function validateAdminSignupData() {
        $input = json_decode(file_get_contents('php://input'), true);
        $requiredFields = ['username', 'email', 'number', 'country', 'password'];
    
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                $this->sendResponse(['status' => 'error', 'message' => "$field is required."]);
                return null;
            }
        }   
        return $input;
    }
}
