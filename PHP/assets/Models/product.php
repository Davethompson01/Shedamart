<?php

namespace App\Models;

use PDO;

class Product {
    private static $db;

    // Set the database connection
    public static function setDatabase(PDO $database) {
        self::$db = $database;
    }

    // Create a product
    public static function createProduct($productData) {
        if (!self::$db) {
            return ['error' => 'Database connection not set'];
        }
    
        // Get categories_id from the categories table
        $categoryQuery = "SELECT categories_id FROM categories WHERE category_name = :category_name";
        $categoryStmt = self::$db->prepare($categoryQuery);
        $categoryStmt->bindParam(':category_name', $productData['categories_name']);
        $categoryStmt->execute();
        $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$category) {
            return ['error' => 'Invalid category name'];
        }
    
        $productToken = bin2hex(random_bytes(16));
    
        // Insert the product
        $query = "INSERT INTO products 
            (product_name, product_category, product_token, product_image, price, amount_in_stock, product_details, colors, origin, about_items) 
            VALUES 
            (:product_name, :product_category, :product_token, :product_image, :price, :amount_in_stock, :product_details, :colors, :origin, :about_items)";
        
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':product_name', $productData['product_name']);
        $stmt->bindParam(':product_category', $category['categories_id']);
        $stmt->bindParam(':product_token', $productToken);
        $stmt->bindParam(':product_image', $productData['product_image']);
        $stmt->bindParam(':price', $productData['price']);
        $stmt->bindParam(':amount_in_stock', $productData['amount_in_stock']);
        $stmt->bindParam(':product_details', $productData['product_details']);
        $stmt->bindParam(':colors', $productData['colors']);
        $stmt->bindParam(':origin', $productData['origin']);
        $stmt->bindParam(':about_items', $productData['about_items']);
    
        // Execute query
        if ($stmt->execute()) {
            return ['success' => 'Product uploaded successfully'];
        } else {
            $errorInfo = $stmt->errorInfo();
            return ['error' => 'Failed to upload product: ' . $errorInfo[2]];
        }
    }

    // Validate product data
    public static function validateProductData($data) {
        $errors = [];

        if (empty($data['product_name'])) {
            $errors['product_name'] = "Product name is required.";
        }

        if (empty($data['categories_name'])) {
            $errors['categories_name'] = "Categories name is required.";
        }

        if (empty($data['price']) || !is_numeric($data['price'])) {
            $errors['price'] = "Valid price is required.";
        }

        if (empty($data['amount_in_stock']) || !is_numeric($data['amount_in_stock'])) {
            $errors['amount_in_stock'] = "Amount in stock must be a valid number.";
        }

        return $errors; // Return an array of errors if any
    }

    public static function getProductsByCategory($categoryName) {
        if (!self::$db) {
            return ['error' => 'Database connection not set'];
        }

        $query = "SELECT p.product_name, p.product_image, p.price, p.amount_in_stock, p.product_details, p.colors, p.origin, p.about_items
                  FROM products p
                  JOIN categories c ON p.product_category = c.categories_id
                  WHERE c.category_name = :category_name LIMIT 0, 25";
        
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':category_name', $categoryName);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRandomProducts($limit = 20) {
        if (!self::$db) {
            return ['error' => 'Database connection not set'];
        }

        $query = "SELECT product_name, product_image, price, amount_in_stock, product_details, colors, origin, about_items 
                  FROM products 
                  ORDER BY RAND() 
                  LIMIT :limit";

        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLastUpdatedProducts($limit = 20) {
        if (!self::$db) {
            return ['error' => 'Database connection not set'];
        }

        // Query to select the last updated products based on `updated_at`
        $query = "SELECT product_name, product_image, price, amount_in_stock, product_details, colors, origin, about_items
                  FROM products
                  ORDER BY last_updated
 DESC
                  LIMIT :limit";

        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
