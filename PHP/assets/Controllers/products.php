<?php

namespace App\Controllers;

use App\Models\Product;
use App\Config\Database;

class ProductController {
    private $db;

    public function __construct() {
        // Create the database connection
        $database = new Database();
        $this->db = $database->getConnection();
        // Set the static database connection in the Product model
        Product::setDatabase($this->db);
    }

    public function uploadProducts($productsData) {
        $successfulUploads = [];
        $errors = [];

        // Loop through the products data
        foreach ($productsData as $product) {
            // Call the Product model's createProduct method
            $result = Product::createProduct($product);

            if (isset($result['error'])) {
                // Handle errors and add to the errors array
                $errors[] = [
                    'product_name' => $product['product_name'],
                    'errors' => $result['error']
                ];
            } else {
                // Add to successful uploads
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
}
