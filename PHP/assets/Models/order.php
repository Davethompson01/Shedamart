<?php

namespace App\Models;

use PDO;

class Order {
    private static $db;

    public static function setDatabase(PDO $database) {
        self::$db = $database;
    }

    // Create an order
    public static function createOrder($userId, $products) {
        // Verify the user exists
        $userCheckQuery = "SELECT * FROM users WHERE user_id = :user_id";
        $userCheckStmt = self::$db->prepare($userCheckQuery);
        $userCheckStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $userCheckStmt->execute();
        if (!$userCheckStmt->fetch(PDO::FETCH_ASSOC)) {
            return ['error' => 'User does not exist'];
        }

        // Start transaction
        self::$db->beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($products as $product) {
                $totalAmount += $product['price'] * $product['quantity'];
            }

            // Insert into orders table
            $orderQuery = "INSERT INTO orders (user_id, total_amount) VALUES (:user_id, :total_amount)";
            $orderStmt = self::$db->prepare($orderQuery);
            $orderStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $orderStmt->bindParam(':total_amount', $totalAmount);
            $orderStmt->execute();
            $orderId = self::$db->lastInsertId();

            // Insert each product as an order item
            $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price, total) VALUES (:order_id, :product_id, :quantity, :price, :total)";
            $orderItemStmt = self::$db->prepare($orderItemQuery);

            foreach ($products as $product) {
                $total = $product['price'] * $product['quantity'];
                $orderItemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $product['product_id'],
                    ':quantity' => $product['quantity'],
                    ':price' => $product['price'],
                    ':total' => $total
                ]);
            }

            self::$db->commit();
            return ['success' => 'Order created successfully', 'order_id' => $orderId];

        } catch (Exception $e) {
            self::$db->rollBack();
            return ['error' => 'Failed to create order: ' . $e->getMessage()];
        }
    }
}
