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
        function koboToNaira($kobo) {
            return $kobo / 100;
        }
        
        function nairaToKobo($naira) {
            return (int)($naira * 100);         }
    
        $user = User::getUserByToken($token);
        if (!$user || !isset($user['balance'])) {
            return ['status' => 'error', 'message' => 'Unauthorized user or missing balance info.'];
        }
    
        if (empty($orderData['productToken']) || !is_array($orderData['productToken']['product_id'])) {
            return ['status' => 'error', 'message' => 'Invalid product data'];
        }
    
        $products = $orderData['productToken']['product_id'];
        $totalOrderPriceKobo = 0;
        $quantity = 0;
    
        foreach ($products as $productItem) {
            if (empty($productItem['id']) || empty($productItem['quantity'])) {
                return ['status' => 'error', 'message' => 'Invalid product data'];
            }
    
            $product = Product::getProductByToken($productItem['id']);
            if (!$product) {
                return ['status' => 'error', 'message' => 'Product not found'];
            }
    
            if ($product['amount_in_stock'] < $productItem['quantity']) {
                return ['status' => 'error', 'message' => 'Insufficient stock for product'];
            }
    
            // No need to check again; proceed to calculate total price
            $totalOrderPriceKobo += nairaToKobo($product['price']) * $productItem['quantity'];
        }

        foreach ($products as $productItem) {
            $product = Product::getProductByToken($productItem['id']);
            
            // Deduct stock only if the product was found
            if ($product) {
                // Update the stock level
                $newStock = $product['amount_in_stock'] - $productItem['quantity'];
                if ($newStock < 0) {
                    return ['status' => 'error', 'message' => 'Insufficient stock for product'];
                }
                Product::updateStock($product['product_id'], $newStock);
            }
        }
    
        if ($user['balance'] < $totalOrderPriceKobo) {
            return ['status' => 'error', 'message' => 'Insufficient balance'];
        }
        $newBalanceKobo = $user['balance'] - $totalOrderPriceKobo;
        User::updateBalance($user['user_id'], $newBalanceKobo);
        foreach ($products as $productItem) {
            $product = Product::getProductByToken($productItem['id']);
            Product::updateStock($product['product_id'], $product['amount_in_stock'] - $productItem['quantity']);
        }
    
        
        $orderStatus = $orderData['order_status'] ?? 'pending';
        Order::createOrder($user['user_id'], $orderData['productToken'], $totalOrderPriceKobo, $orderStatus);
    
        // Return the new balance
        return [
            'status' => 'success',
            'message' => 'Order placed successfully',
            'new_balance' => koboToNaira($newBalanceKobo)
        ];
    }
}

