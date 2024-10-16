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
                (categories_name, product_image, price, product_details, colors, amount_in_stock, origin, amount_of_rating, about_items, product_name) 
                VALUES 
                (:categories_name, :product_image, :price, :product_details, :colors, :amount_in_stock, :origin, :amount_of_rating, :about_items, :product_name)";

        $stmt = self::$db->prepare($sql);

        // Bind parameters
        $categoryname = 'jewelry';
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

        if ($stmt->execute()) {
            return self::$db->lastInsertId(); // Return the jewelry_id
        }

        return false;
    }

    public static function getJewelryById($jewelryId) {
        self::initialize();

        $sql = "SELECT * FROM jewelry WHERE jewelry_id = :jewelry_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':jewelry_id', $jewelryId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllJewelry($limit = 10, $offset = 0) {
        self::initialize();

        $sql = "SELECT * FROM jewelry LIMIT :limit OFFSET :offset";
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteJewelry($jewelryId) {
        self::initialize();

        $sql = "DELETE FROM jewelry WHERE jewelry_id = :jewelry_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':jewelry_id', $jewelryId);
        return $stmt->execute();
    }
}
