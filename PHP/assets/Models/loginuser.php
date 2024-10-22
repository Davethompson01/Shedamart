<?php

namespace App\Models;

use PDO;

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function checkUser($email, $password) {
        // Check in users table
        $stmt = $this->db->prepare("SELECT user_id, username, user_password, 'user' AS user_type FROM users WHERE user_email = :email");
        $stmt->execute(['email' => $email]);
    
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['user_password'])) {
                return [
                    'id' => $row['user_id'],
                    'username' => $row['username'],
                    'role' => 'user'
                ];
            } else {
                return "Wrong";
            }
        }
    
        // Check in admin table
        $stmt = $this->db->prepare("SELECT admin_id AS admin_id, username, password AS password, 'admin' AS user_type FROM admin WHERE email = :email");
        $stmt->execute(['email' => $email]);
    
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return [
                    'id' => $row['admin_id'],
                    'username' => $row['username'],
                    'role' => 'admin'
                ]; // Return admin details correctly
            } else {
                return "Wrong";
            }
        }
    
        return null;
    }
    
    
}
