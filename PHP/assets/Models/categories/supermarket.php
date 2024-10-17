<?php

namespace App\Models;

require_once __DIR__ . "/../../../config/Database.php";

use App\Config\Database; 
use PDO;

class SupermarketModel
{
    private static $db;

    public static function initialize()
    {
        if (!self::$db) {
            self::$db = (new Database())->getConnection();
        }
    }

    public static function insertItem($itemData) {
        self::initialize();

        if (!is_array($itemData)) {
            throw new \Exception("Item data must be an array.");
        }

        $sql = "INSERT INTO supermarket 
                (categories_name, product_image, price, product_details, brand, quantity, product_name, product_token) 
                VALUES 
                (:categories_name, :product_image, :price, :product_details, :brand, :quantity, :product_name, :product_token)";

        $stmt = self::$db->prepare($sql);

        // Generate a unique product token
        $productToken = self::generateUniqueProductToken();

        // Bind parameters
        $categoryname = 'supermarket'; // Category is set to supermarket
        $stmt->bindParam(':categories_name', $categoryname);
        $stmt->bindParam(':product_image', $itemData['product_image']);
        $stmt->bindParam(':price', $itemData['price']);
        $stmt->bindParam(':product_details', $itemData['product_details']);
        $stmt->bindParam(':brand', $itemData['brand']);
        $stmt->bindParam(':quantity', $itemData['quantity']);
        $stmt->bindParam(':product_name', $itemData['product_name']);
        $stmt->bindParam(':product_token', $productToken);

        if ($stmt->execute()) {
            return self::$db->lastInsertId(); // Return the item_id for each item
        }

        return false;
    }

    public static function generateUniqueProductToken() {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 9);
    }

    public static function getItems($limit, $offset) {
        self::initialize();
        
        $sql = "SELECT item_id, product_token, categories_name, product_image, price, product_details, brand, quantity, product_name
                FROM supermarket 
                LIMIT :limit OFFSET :offset";

        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all matching supermarket items
        }

        return false;
    }

    public static function getItemCount() {
        self::initialize();
        
        $sql = "SELECT COUNT(*) AS total_items FROM supermarket";
        $stmt = self::$db->prepare($sql);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_items'];
        }
        return 0;
    }

    public static function deleteItem($itemId) {
        self::initialize();

        $sql = "DELETE FROM supermarket WHERE item_id = :item_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':item_id', $itemId);
        return $stmt->execute();
    }
}
