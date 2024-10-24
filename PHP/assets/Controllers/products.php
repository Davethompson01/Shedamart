<?php

namespace App\Controllers;

use App\Models\Product;
use App\Config\Database;

class ProductController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        Product::setDatabase($this->db);
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
}

