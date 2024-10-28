<?php

namespace App\Controllers;

use PDO;
use App\Models\Order;
use App\Models\Product;

class OrderController {
    private static $db; // Define the database connection property

    public static function setDatabase(PDO $database) {
        self::$db = $database;
    }

    public static function createOrder($userId, $orderData) {
        // Get the product by ID
        $product = Product::getProductById($orderData['product_id']);
        if (!$product) {
            return ['status' => 'error', 'message' => 'Invalid product ID'];
        }

        // Calculate the total amount
        $orderTotal = $product['price'] * $orderData['order_quantity'];

        // Prepare an array of products (can include more than one if needed)
        $products = [
            [
                'product_id' => $product['product_id'],
                'price' => $product['price'],
                'quantity' => $orderData['order_quantity'],
            ]
        ];

        // Create the order and pass the user ID and products array
        return Order::createOrder($userId, $products);
    }
}
