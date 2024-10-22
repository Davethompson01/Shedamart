<?php

namespace App\Controllers;

use App\Models\Product;
use App\Config\Database;
use App\Utilities\TokenGenerator;

class ProductController {
    private $db;

    public function __construct() {
        // Create the database connection
        $database = new Database();
        $this->db = $database->getConnection();
        // Set the static database connection in the Product model
        Product::setDatabase($this->db);
    }

    public function uploadProducts($productsData, $token) {
        // Decode the JWT token to verify the user's role
        $decodedToken = TokenGenerator::decodeToken($token);
    
        if ($decodedToken['status'] !== 'success') {
            // If token decoding failed, return the error
            return [
                'status' => 'error',
                'message' => $decodedToken['message']
            ];
        }
    
        // Check if the user is an admin
        $userData = $decodedToken['data']->data;
        if ($userData->role !== 'admin') {
            return [
                'status' => 'error',
                'message' => 'Unauthorized action. Only admins can upload products.'
            ];
        }
    
        // Proceed with product upload if user is admin
        $successfulUploads = [];
        $errors = [];
    
        // Loop through each product data and handle validation + insertion
        foreach ($productsData as $product) {
            // Validate product data
            $validationErrors = Product::validateProductData($product);
            if (!empty($validationErrors)) {
                $errors[] = [
                    'product_name' => $product['product_name'] ?? 'Unknown',  // Handle missing product name
                    'errors' => $validationErrors
                ];
                continue; // Skip to the next product if there are validation errors
            }
    
            // Call the Product model's createProduct method
            $result = Product::createProduct($product);
    
            if (isset($result['error'])) {
                // Handle errors during product creation and add to the errors array
                $errors[] = [
                    'product_name' => $product['product_name'] ?? 'Unknown', // Handle missing product name
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
    
        // Return the response based on whether there were errors
        if (!empty($errors)) {
            return [
                'status' => 'partial',
                'uploaded' => $successfulUploads,
                'errors' => $errors,
                'message' => 'Some products failed to upload.'
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
