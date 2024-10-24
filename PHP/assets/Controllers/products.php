<?php

namespace App\Controllers;


use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Config\Database;

class ProductController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        Product::setDatabase($this->db);
        User::setDatabase($this->db);
        Order::setDatabase($this->db);
    }


    public function uploadProducts($productsData) {
        $successfulUploads = [];
        $errors = [];

        foreach ($productsData as $product) {
            // Validate product data
            $validationErrors = Product::validateProductData($product);
            if (!empty($validationErrors)) {
                $errors[] = [
                    'product_name' => $product['product_name'],
                    'errors' => $validationErrors
                ];
                continue;
            }

            $result = Product::createProduct($product);
            if (isset($result['error'])) {
                $errors[] = [
                    'product_name' => $product['product_name'],
                    'errors' => $result['error']
                ];
            } else {
                $successfulUploads[] = [
                    'product_name' => $product['product_name'],
                    'status' => 'Uploaded successfully'
                ];
            }
        }

        // Return the response
        if (!empty($errors)) {
            return [
                'status' => 'partial',
                'uploaded' => $successfulUploads,
                'errors' => $errors
            ];
        } else {
            return [
                'status' => 'success',
                'uploaded' => $successfulUploads,
                'message' => 'All products uploaded successfully.'
            ];
        }
    }

    public function getProductsByCategory($categoryName) {
        return Product::getProductsByCategory($categoryName);
    }

    public function getRandomProducts($limit = 20) {
        return Product::getRandomProducts($limit);
    }


    public function getLastUpdatedProducts($limit =20) {
        return Product::getLastUpdatedProducts($limit);
    }
    public function getMostCheckedCategory() {
        return Product::getMostCheckedCategory();
    }

    public function placeOrder($orderData, $token) {
        // Validate user token
        $user = User::getUserByToken($token);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Unauthorized user'];
        }

        // Validate product data and availability
        $product = Product::getProductByToken($orderData['product_token']);
        if (!$product) {
            return ['status' => 'error', 'message' => 'Invalid product'];
        }

        // Check product quantity
        if ($product['amount_in_stock'] < $orderData['quantity']) {
            return ['status' => 'error', 'message' => 'Insufficient stock'];
        }

        // Calculate total price
        $orderTotal = $product['price'] * $orderData['quantity'];

        // Check if user has enough balance
        if ($user['balance'] < $orderTotal) {
            return ['status' => 'error', 'message' => 'Insufficient balance'];
        }

        // Deduct user balance
        $newBalance = $user['balance'] - $orderTotal;
        User::updateBalance($user['user_id'], $newBalance);

        // Update product stock
        $newStock = $product['amount_in_stock'] - $orderData['quantity'];
        Product::updateStock($product['product_id'], $newStock);

        // Place the order
        $orderStatus = isset($orderData['order_status']) ? $orderData['order_status'] : 'published';
        Order::createOrder($user['user_id'], $product['product_id'], $product['product_token'], $orderData['quantity'], $orderTotal, $orderStatus);

        return ['status' => 'success', 'message' => 'Order placed successfully', 'new_balance' => $newBalance];
    }
}

