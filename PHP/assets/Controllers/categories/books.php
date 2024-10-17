<?php

namespace App\Controllers;

use App\Models\BookModel;
require_once __DIR__ . "/../../Models/categories/book.php";
use Exception;

class BookController
{
    public static function createBooks($bookDataArray) {
        try {
            // Ensure the incoming data is an array of book items
            if (!is_array($bookDataArray)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid data format. Must be an array of book items.'
                ];
            }

            $bookIds = [];
            foreach ($bookDataArray as $bookData) {
                $bookId = BookModel::insertBook($bookData);
                if ($bookId) {
                    $bookIds[] = $bookId; // Collect all inserted book IDs
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to add one or more book items.'
                    ];
                }
            }

            return [
                'status' => 'success',
                'message' => 'Book items added successfully.',
                'book_ids' => $bookIds // Return all the book IDs
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function displayBooks($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;

            $result = BookModel::getBooks($limit, $offset);

            if ($result) {
                // Get the total number of items to calculate pagination info
                $totalItems = BookModel::getBookCount(); // Get total count of items
                $totalPages = ceil($totalItems / $limit); // Calculate total pages

                return [
                    'status' => 'success',
                    'data' => $result,
                    'pagination' => [
                        'total_items' => $totalItems,
                        'total_pages' => $totalPages,
                        'current_page' => $page,
                        'items_per_page' => $limit
                    ]
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No book items found.'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function deleteBook($bookId) {
        $deleted = BookModel::deleteBook($bookId);
        if ($deleted) {
            return [
                'status' => 'success',
                'message' => 'Book deleted successfully.'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Failed to delete book.'
        ];
    }
}
