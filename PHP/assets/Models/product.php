<?php

namespace App\Models;

use PDO;

class Product {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }


    public static function setDatabase(PDO $db, $database) {
        self::$db = $database;
    }

    public static function createProduct($db, $productData) {
        // First, get the categories_id from the categories table using the category name
        $categoryQuery = "SELECT categories_id FROM categories WHERE category_name = :category_name";
        $categoryStmt = $db->prepare($categoryQuery);
        $categoryStmt->bindParam(':category_name', $productData['categories_name']);
        $categoryStmt->execute();
        $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$category) {
            return ['error' => 'Invalid category name']; // Handle case where category is not found
        }
    
        // Now insert the product with the categories_id
        $query = "INSERT INTO products 
            (product_name, product_category, product_token, product_image, price, amount_in_stock, product_details, colors, origin, about_items) 
            VALUES 
            (:product_name, :product_category, :product_token, :product_image, :price, :amount_in_stock, :product_details, :colors, :origin, :about_items)";
    
        $stmt = $db->prepare($query);
    
        // Bind parameters
        $stmt->bindParam(':product_name', $productData['product_name']);
        $stmt->bindParam(':product_category', $category['categories_id']); // Bind the categories_id from the categories table
        $stmt->bindParam(':product_token', $productData['product_token']); // Token can be generated beforehand
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
            return ['error' => 'Failed to upload product'];
        }
    }
    

    public static function validateProductData($data) {
        $errors = [];

        if (empty($data['product_name'])) {
            $errors['product_name'] = "Product name is required.";
        }

        if (empty($data['product_category'])) {
            $errors['product_category'] = "Product category is required.";
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

        return $errors; // Returns an array of errors if any
    }
}
