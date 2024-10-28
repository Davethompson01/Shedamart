<?php

namespace App\Models;

use PDO;


use Exception; // Import the Exception class



class Order {
    private static $db;

    public static function setDatabase(PDO $database) {
        self::$db = $database;
    }

  public static function createOrder($userId, $productToken, $orderStatus) {
    if (!self::$db) {
        return ['error' => 'Database connection not set'];
    }

    if (!isset($productToken['product_id']) || !is_array($productToken['product_id'])) {
        return ['error' => 'Invalid product token'];
    }

    self::$db->beginTransaction();
    try {
        $totalPrice = 0;

        foreach ($productToken['product_id'] as $product) {
            if (!isset($product['id'], $product['quantity'])) {
                throw new Exception('Invalid product data');
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
                throw new Exception('Product not found');
            }

            // Check stock availability
            if ($productInfo['amount_in_stock'] < $quantity) {
                throw new Exception('Insufficient stock for product ID: ' . $productId);
            }

            // Calculate the total price on the backend
            $totalPrice += $productInfo['price'] * $quantity;
        }

        // Insert the order into the 'orders' table
        $query = "INSERT INTO orders (user_id, total_price, order_status, created_at) VALUES (:user_id, :total_price, :order_status, NOW())";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':total_price', $totalPrice);
        $orderStatus = $orderStatus ?? 'pending'; // Set default status if not provided
        $stmt->bindParam(':order_status', $orderStatus);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to place order');
        }

        // After placing the order, update the stock levels
        foreach ($productToken['product_id'] as $product) {
            $productId = $product['id'];
            $quantity = $product['quantity'];

            // Reduce stock by the ordered quantity
            $updateStockQuery = "UPDATE products SET amount_in_stock = amount_in_stock - :quantity WHERE product_id = :product_id";
            $updateStmt = self::$db->prepare($updateStockQuery);
            $updateStmt->bindParam(':quantity', $quantity);
            $updateStmt->bindParam(':product_id', $productId);
            
            if (!$updateStmt->execute()) {
                throw new Exception('Failed to update stock for product ID: ' . $productId);
            }
        }

        self::$db->commit();
        return ['success' => 'Order placed successfully', 'total_price' => $totalPrice];
    } catch (Exception $e) {
        self::$db->rollBack();
        return ['error' => $e->getMessage()];
    }
}

    
    
public static function getProductByToken($token) {
    $query = "SELECT * FROM products WHERE product_token = :token";
    $stmt = self::$db->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        error_log("Product not found for token: $token");
    }

    return $result;
}


    
    
    public static function updateStock($productId, $newStock) {
        $query = "UPDATE products SET amount_in_stock = :new_stock WHERE product_id = :product_id";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':new_stock', $newStock);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
    }
    
}
