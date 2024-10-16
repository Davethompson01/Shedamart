<?php

namespace App\Models;

require_once __DIR__ . "/../../config/Database.php";

use App\Config\Database; // Import the Database class
// use PDO;
use Exception;
use PDO;

class ProductModel
{

    private static $db;
    
    public static function initialize()
    {
        if (!self::$db) {
            self::$db = (new Database())->getConnection();
        }
    }
    public static function insertProductIntoProductsTable($categoryName, $productData) {
        self::initialize();
    
        if (!is_array($productData)) {
            throw new Exception("Product data must be an array.");
        }
    
        // Ensure category_name is set in productData
        $productData['category_name'] = $categoryName;
    
        // Generate a unique token for the product
        $productData['product_token'] = self::generateToken(); // Generate token here
    
        // Build the SQL query
        $sql = "INSERT INTO products 
                (product_name, product_image, price, product_details, colors, amount_in_stock, origin, amount_of_rating, about_items, category_name, product_token) 
                VALUES 
                (:product_name, :product_image, :price, :product_details, :colors, :amount_in_stock, :origin, :amount_of_rating, :about_items, :category_name, :product_token)";
        
        $stmt = self::$db->prepare($sql);
    
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
        $stmt->bindParam(':category_name', $productData['category_name']);
        $stmt->bindParam(':product_token', $productData['product_token']); // Bind the token
    
        // Execute the query
        if ($stmt->execute()) {
            return self::$db->lastInsertId(); // Return the product_id
        }
    
        return false;
    }
    
    

    public function getNewlyCreatedProducts()
    {
        $query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 10"; // Adjust table and fields accordingly
        // $stmt = $this->db->prepare($query);
        $stmt = self::$db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


        public function getMostCheckedProducts($limit = 10)
    {
        $query = "SELECT * FROM products ORDER BY views DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function generateToken($length = 8) {
        return bin2hex(random_bytes($length / 2) ); 
    }

    

}
