<?php

namespace App\Models;

require_once __DIR__ . "/../../config/Database.php";

use App\Config\Database; // Import the Database class
// use PDO;
use Exception;

class ProductModel
{
    public static function insertIntoCategoryTable($categoryName, $productData)
    {
        $db = (new Database())->getConnection();

        if (!$db) {
            throw new Exception("Database connection failed.");
        }

        $tableName = $categoryName;

        // Build the SQL query
        $sql = "INSERT INTO {$tableName} 
                (product_name, product_image, price, product_details, colors, amount_in_stock, origin, amount_of_rating, about_items) 
                VALUES 
                (:product_name, :product_image, :price, :product_details, :colors, :amount_in_stock, :origin, :amount_of_rating, :about_items)";
        
        $stmt = $db->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':product_name', $productData['product_name']);
        $stmt->bindParam(':product_image', $productData['product_image']);
        $stmt->bindParam(':price', $productData['price']);
        $stmt->bindParam(':product_details', $productData['product_details']);
        $stmt->bindParam(':colors', $productData['colors']);
        $stmt->bindParam(':amount_in_stock', $productData['amount_in_stock']);
        $stmt->bindParam(':origin', $productData['origin']);
        $stmt->bindParam(':amount_of_rating', $productData['amount_of_rating']);
        $stmt->bindParam(':about_items', $productData['about_items']);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
