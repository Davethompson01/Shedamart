<?php


namespace App\Models;

use App\Utilities\Authorization;
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../utilities/authorisation.php";
include_once(__DIR__ . '/../../Config/Database.php');

use PDO;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class User{
    // private $db;
    private static $db;

    // public function __construct( $db)
    // {
    //     $this->db = $db;
    // }
    

    public static function setDatabase(PDO $database) {
        self::$db = $database;
    }
    public function checkUser($email, $password)
    {
        $stmt = $this->db->prepare("
        SELECT * user WHERE user_email = :email
        ");
        $stmt->execute(['email' => $email]);
    
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['user_password'])) {
                return $row; 
            } else {
                return "Wrong";
            }
        } else {
            return null;
        }
    }
    

    public function checkAdminEmail($email,$password) {
        $stmt =  $this->db->prepare("SELECT email FROM admin WHERE email = :email");
        $stmt->execute(['email' => $email]);
    
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['user_password'])) {
                return $row; 
            } else {
                return "Wrong";
            }
        } else {
            return null;
        }
    }


    public static function updateBalance($userId, $newBalance) {
        $query = "UPDATE users SET balance = :new_balance WHERE user_id = :user_id";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':new_balance', $newBalance);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    public static function getUserByToken($token) {
        $secretKey = "1234Sheda"; // Replace this with your actual secret key
        $auth = new Authorization($secretKey); // Pass the secret key here
        $decoded = $auth->authorize($token);  // Call the authorize method
        
        
        if ($decoded['status'] === 'success') {
            $userId = $decoded['data']['id']; // Extract the user ID from the decoded token
            $query = "SELECT * FROM users WHERE user_id = :user_id"; // Ensure you have a correct query
            $stmt = self::$db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); // Return user details
        } else {
            return false;
        }
    }

   
}

