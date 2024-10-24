<?php


namespace App\Models;


include_once(__DIR__ . '/../../Config/Database.php');

use PDO;

class User{
    private $db;

    public function __construct( $db)
    {
        $this->db = $db;
    }

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
}
