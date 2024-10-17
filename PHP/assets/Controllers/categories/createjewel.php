<?php

namespace App\Controllers;

use App\Models\JewelryModel;
require_once __DIR__ . "/../../Models/categories/createjewel.php";
use Exception;

class JewelryController
{
    public static function createJewelry($jewelryDataArray) {
        try {
            // Ensure the incoming data is an array of jewelry items
            if (!is_array($jewelryDataArray)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid data format. Must be an array of jewelry items.'
                ];
            }
    
            $jewelryIds = [];
            foreach ($jewelryDataArray as $jewelryData) {
                $jewelryId = JewelryModel::insertJewelry($jewelryData);
                if ($jewelryId) {
                    $jewelryIds[] = $jewelryId; // Collect all inserted jewelry IDs
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to add one or more jewelry items.'
                    ];
                }
            }
    
            return [
                'status' => 'success',
                'message' => 'Jewelry items added successfully.',
                'jewelry_ids' => $jewelryIds // Return all the jewelry IDs
            ];
    
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    

    // public static function getJewelry($jewelryId) {
    //     $jewelry = JewelryModel::getJewelryById($jewelryId);
    //     if ($jewelry) {
    //         return [
    //             [
    //             'status' => 'success',
    //             'data' => $jewelry
    //             ]
    //         ];
    //     }
    //     return [
    //         'status' => 'error',
    //         'message' => 'Jewelry not found.'
    //     ];
    // }

    // public static function getAllJewelry($limit = 10, $offset = 0) {
    //     $jewelry = JewelryModel::getAllJewelry($limit, $offset);
    //     return [
    //         [
    //             'status' => 'success',
    //             'message'=>'fetching jewellery',
    //             'data' => $jewelry
    //         ]
    //     ];
    // }


    public static function displayJewel($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;

    
            // Fetch jewelry items with pagination
            $result = JewelryModel::getJewelry($limit, $offset);
    
            if ($result) {
                // Get the total number of items to calculate pagination info
                $totalItems = JewelryModel::getJewelryCount(); // Get total count of items
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
                    'message' => 'No jewelry items found.'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    
    

    public static function deleteJewelry($jewelryId) {
        $deleted = JewelryModel::deleteJewelry($jewelryId);
        if ($deleted) {
            return [
                'status' => 'success',
                'message' => 'Jewelry deleted successfully.'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Failed to delete jewelry.'
        ];
    }
}
