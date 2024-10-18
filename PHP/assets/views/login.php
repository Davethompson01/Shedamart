<?php

// namespace App\;
namespace App\Controllers;

class LoginView
{
    public function render($response)
    {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
