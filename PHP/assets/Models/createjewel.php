<?php

namespace App\Models;

require_once __DIR__ . "/../../config/Database.php";

use App\Config\Database; 
use PDO;

class JewelryModel
{
    private static $db;

    public static function initialize()
    {
        if (!self::$db) {
            self::$db = (new Database())->getConnection();
        }
    }

    public static function insertJewelry($jewelryData) {
        self::initialize();
    
        if (!is_array($jewelryData)) {
            throw new \Exception("Jewelry data must be an array.");
        }
    
        $sql = "INSERT INTO jewelry 
                (categories_name, product_image, price, product_details, colors, amount_in_stock, origin, amount_of_rating, about_items, product_name, product_token) 
                VALUES 
                (:categories_name, :product_image, :price, :product_details, :colors, :amount_in_stock, :origin, :amount_of_rating, :about_items, :product_name, :product_token)";
    
        $stmt = self::$db->prepare($sql);
    
        // Generate a unique product token
        $productToken = JewelryModel::generateUniqueProductToken();
    
        // Bind parameters
        $categoryname = 'jewelry'; // If category is always jewelry, else set dynamically
        $stmt->bindParam(':categories_name', $categoryname);
        $stmt->bindParam(':product_image', $jewelryData['product_image']);
        $stmt->bindParam(':price', $jewelryData['price']);
        $stmt->bindParam(':product_details', $jewelryData['product_details']);
        $stmt->bindParam(':colors', $jewelryData['colors']);
        $stmt->bindParam(':amount_in_stock', $jewelryData['amount_in_stock']);
        $stmt->bindParam(':origin', $jewelryData['origin']);
        $stmt->bindParam(':amount_of_rating', $jewelryData['amount_of_rating']);
        $stmt->bindParam(':about_items', $jewelryData['about_items']);
        $stmt->bindParam(':product_name', $jewelryData['product_name']);
        $stmt->bindParam(':product_token', $productToken); // New product token
    
        if ($stmt->execute()) {
            return self::$db->lastInsertId(); // Return the jewelry_id for each item
        }
    
        return false;
    }
    
    public static function generateUniqueProductToken() {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 9);
    }
        private static function generateShortToken() {
        
        $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
    
        $uniqueId = substr(uniqid(), -3);  // Last 3 digits of a unique ID
    
        return $randomString . $uniqueId;
    }
    
    // public static function getJewelryById($jewelryId) {
    //     self::initialize();

    //     $sql = "SELECT * FROM jewelry WHERE jewelry_id = :jewelry_id";
    //     $stmt = self::$db->prepare($sql);
    //     $stmt->bindParam(':jewelry_id', $jewelryId);
    //     $stmt->execute();
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public static function getJewelry($limit, $offset) {
        self::initialize();
        
        $sql = "SELECT admin_id, jewelry_id, product_token, categories_name, product_image, price, product_details, colors, amount_in_stock, origin, amount_of_rating, about_items, product_name
                FROM jewelry 
                LIMIT :limit OFFSET :offset";
    
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all matching jewelry
        }
    
        return false;
    }

    public static function getJewelryCount() {
        self::initialize();
        
        $sql = "SELECT COUNT(*) AS total_items FROM jewelry";
        $stmt = self::$db->prepare($sql);
    
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_items'];
        }
        return 0;
    }

    public static function deleteJewelry($jewelryId) {
        self::initialize();

        $sql = "DELETE FROM jewelry WHERE jewelry_id = :jewelry_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':jewelry_id', $jewelryId);
        return $stmt->execute();
    }
}
