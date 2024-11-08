<?php

namespace App\Controllers;

use PDO;
use App\Models\Order;
use App\Models\Product;

class OrderController {
    private static $db;

    public static function setDatabase(PDO $database) {
        self::$db = $database;
        Order::setDatabase($database);
    }

    public static function createOrder($orderData) {
        // Validate required fields
        if (empty($orderData['user_id']) || empty($orderData['product_id']) || empty($orderData['order_quantity'])) {
            return ['status' => 'error', 'message' => 'User ID, product ID, and quantity are required'];
        }
        
    
        $userId = $orderData['user_id'];
    
        // Get the product by ID
        // $product = Product::getProductById($orderData['product_id']);

        $product = Product::getProductById($orderData['product_id']);
        if (!$product) {
            return ['status' => 'error', 'message' => 'Invalid product ID'];
        }
    
        // Calculate the total amount
        $orderTotal = $product['price'] * $orderData['order_quantity'];
    
        // Prepare an array of products
        $products = [
            [
                'product_id' => $product['product_id'], // Use product_id here
                'price' => $product['price'],
                'quantity' => $orderData['order_quantity'],
            ]
        ];
    
        // Create the order and pass the user ID and products array
        return Order::createOrder($userId, $products);
    }
    
    
    
}
