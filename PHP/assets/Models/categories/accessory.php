<?php

namespace App\Models;

require_once __DIR__ . "/../../../config/Database.php";

use App\Config\Database; 
use PDO;

class AccessoryModel
{
    private static $db;

    public static function initialize()
    {
        if (!self::$db) {
            self::$db = (new Database())->getConnection();
        }
    }

    public static function insertAccess($accessoryData) {
        self::initialize();
    
        if (!is_array($accessoryData)) {
            throw new \Exception("Accessory data must be an array.");
        }
    
        $sql = "INSERT INTO accessories 
                (categories_name, product_image, price, product_details, colors, amount_in_stock, origin, amount_of_rating, about_items, product_name, product_token) 
                VALUES 
                (:categories_name, :product_image, :price, :product_details, :colors, :amount_in_stock, :origin, :amount_of_rating, :about_items, :product_name, :product_token)";
    
        $stmt = self::$db->prepare($sql);
    
        // Generate a unique product token
        $productToken = self::generateUniqueProductToken();
    
        // Bind parameters
        $categoryName = 'accessory'; // Set to accessory category
        $stmt->bindParam(':categories_name', $categoryName);
        $stmt->bindParam(':product_image', $accessoryData['product_image']);
        $stmt->bindParam(':price', $accessoryData['price']);
        $stmt->bindParam(':product_details', $accessoryData['product_details']);
        $stmt->bindParam(':colors', $accessoryData['colors']);
        $stmt->bindParam(':amount_in_stock', $accessoryData['amount_in_stock']);
        $stmt->bindParam(':origin', $accessoryData['origin']);
        $stmt->bindParam(':amount_of_rating', $accessoryData['amount_of_rating']);
        $stmt->bindParam(':about_items', $accessoryData['about_items']);
        $stmt->bindParam(':product_name', $accessoryData['product_name']);
        $stmt->bindParam(':product_token', $productToken); // New product token
    
        if ($stmt->execute()) {
            return self::$db->lastInsertId(); // Return the accessory_id for each item
        }
    
        return false;
    }
    
    public static function generateUniqueProductToken() {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 9);
    }

    public static function getAccessory($limit, $offset) {
        self::initialize();
        
        $sql = "SELECT admin_id, accessory_id, product_token, categories_name, product_image, price, product_details, colors, amount_in_stock, origin, amount_of_rating, about_items, product_name
                FROM accessories 
                LIMIT :limit OFFSET :offset";
    
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all matching accessories
        }
    
        return false;
    }

    public static function getAccessoryCount() {
        self::initialize();
        
        $sql = "SELECT COUNT(*) AS total_items FROM accessories"; // Adjust table name
        $stmt = self::$db->prepare($sql);
    
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_items'];
        }
        return 0;
    }

    public static function deleteAccessory($accessoryId) {
        self::initialize();

        $sql = "DELETE FROM accessories WHERE accessory_id = :accessory_id"; // Adjust table name
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':accessory_id', $accessoryId);
        return $stmt->execute();
    }
}
