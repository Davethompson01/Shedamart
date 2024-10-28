<?php

namespace App\Models;

use PDO;

class Order {
    private static $db;

    public static function setDatabase(PDO $database) {
        self::$db = $database;
    }

    public static function createOrder($userId, $productToken) {
        if (!self::$db) {
            return ['error' => 'Database connection not set'];
        }
    
        if (!isset($productToken['product_id']) || !is_array($productToken['product_id'])) {
            return ['error' => 'Invalid product token'];
        }
    
        $totalPrice = 0;
    
        foreach ($productToken['product_id'] as $product) {
            if (!isset($product['id'], $product['quantity'])) {
                return ['error' => 'Invalid product data'];
            }
    
            $productId = $product['id'];
            $quantity = $product['quantity'];
    
            // Get product price and check stock
            $query = "SELECT price, amount_in_stock FROM products WHERE product_id = :product_id";
            $stmt = self::$db->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->execute();
            $productInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$productInfo) {
                return ['error' => 'Product not found'];
            }
    
            // Check stock availability
            if ($productInfo['amount_in_stock'] < $quantity) {
                return ['error' => 'Insufficient stock for product ID: ' . $productId];
            }
    
            // Calculate the total price on the backend
            $totalPrice += $productInfo['price'] * $quantity;
        }
    
        // Insert the order into the 'orders' table
        $query = "INSERT INTO orders (user_id, total_price, order_status, created_at) VALUES (:user_id, :total_price, 'pending', NOW())";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':total_price', $totalPrice);
    
        if ($stmt->execute()) {
            // After placing the order, update the stock levels
            foreach ($productToken['product_id'] as $product) {
                $productId = $product['id'];
                $quantity = $product['quantity'];
    
                // Reduce stock by the ordered quantity
                $updateStockQuery = "UPDATE products SET amount_in_stock = amount_in_stock - :quantity WHERE product_id = :product_id";
                $updateStmt = self::$db->prepare($updateStockQuery);
                $updateStmt->bindParam(':quantity', $quantity);
                $updateStmt->bindParam(':product_id', $productId);
                $updateStmt->execute();
            }
    
            return ['success' => 'Order placed successfully', 'total_price' => $totalPrice];
        } else {
            return ['error' => 'Failed to place order'];
        }
    }
    
    
    public static function getProductByToken($productToken) {
        $query = "SELECT * FROM products WHERE product_token = :product_token LIMIT 1";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':product_token', $productToken);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function updateStock($productId, $newStock) {
        $query = "UPDATE products SET amount_in_stock = :new_stock WHERE product_id = :product_id";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':new_stock', $newStock);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
    }
    
}
