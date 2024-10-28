<?php

namespace App\Http;

class Request {
    public function getBody() {
        $input = file_get_contents("php://input");
        error_log("Raw Input: " . $input);  // Debug raw input
        return json_decode($input, true);
    }
    

    public function getHeader($header) {
        return isset($_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $header))]) ? $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $header))] : null;
    }
    
}
