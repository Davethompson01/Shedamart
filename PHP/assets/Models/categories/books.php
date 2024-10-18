<?php

namespace App\Models;

require_once __DIR__ . "/../../../config/Database.php";

use App\Config\Database; 
use PDO;

class BookModel
{
    private static $db;
    public static function initialize()
    {
        if (!self::$db) {
            self::$db = (new Database())->getConnection();
        }
    }

    public static function insertBook($bookData) {
        self::initialize();

        if (!is_array($bookData)) {
            throw new \Exception("Book data must be an array.");
        }

        $sql = "INSERT INTO books 
                (categories_name, product_image, price, product_details, author, publisher, amount_in_stock, about_items, product_name, product_token) 
                VALUES 
                (:categories_name, :product_image, :price, :product_details, :author, :publisher, :amount_in_stock, :about_items, :product_name, :product_token)";

        $stmt = self::$db->prepare($sql);

        // Generate a unique product token
        $productToken = self::generateUniqueProductToken();

        // Bind parameters
        $categoryname = 'books'; // Category is set to books
        $stmt->bindParam(':categories_name', $categoryname);
        $stmt->bindParam(':product_image', $bookData['product_image']);
        $stmt->bindParam(':price', $bookData['price']);
        $stmt->bindParam(':product_details', $bookData['product_details']);
        $stmt->bindParam(':author', $bookData['author']);
        $stmt->bindParam(':publisher', $bookData['publisher']);
        $stmt->bindParam(':amount_in_stock', $bookData['amount_in_stock']);
        $stmt->bindParam(':about_items', $bookData['about_items']);
        $stmt->bindParam(':product_name', $bookData['product_name']);
        $stmt->bindParam(':product_token', $productToken);

        if ($stmt->execute()) {
            return self::$db->lastInsertId(); // Return the book_id for each item
        }

        return false;
    }

    public static function generateUniqueProductToken() {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 9);
    }

    public static function getBooks($limit, $offset) {
        self::initialize();
        
        $sql = "SELECT book_id, product_token, categories_name, product_image, price, product_details, author, publisher, amount_in_stock, about_items, product_name
                FROM books 
                LIMIT :limit OFFSET :offset";

        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all matching books
        }

        return false;
    }

    public static function getBookCount() {
        self::initialize();
        
        $sql = "SELECT COUNT(*) AS total_items FROM books";
        $stmt = self::$db->prepare($sql);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_items'];
        }
        return 0;
    }

    public static function deleteBook($bookId) {
        self::initialize();

        $sql = "DELETE FROM books WHERE book_id = :book_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':book_id', $bookId);
        return $stmt->execute();
    }
}
