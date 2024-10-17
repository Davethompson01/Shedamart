<?php

namespace App\Controllers;

use App\Models\AccessoryModel; // Corrected to use a consistent class naming
require_once __DIR__ . "/../../Models/categories/accessory.php";
use Exception;

class AccessoryController
{
    public static function createAccessories($accessoryDataArray) {
        try {
            // Ensure the incoming data is an array of accessory items
            if (!is_array($accessoryDataArray)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid data format. Must be an array of accessory items.'
                ];
            }
    
            $accessoryIds = [];
            foreach ($accessoryDataArray as $accessoryData) {
                $accessoryId = AccessoryModel::insertAccess($accessoryData); // Changed accessdata to accessoryData
                if ($accessoryId) {
                    $accessoryIds[] = $accessoryId; // Collect all inserted accessory IDs
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to add one or more accessory items.'
                    ];
                }
            }
    
            return [
                'status' => 'success',
                'message' => 'Accessory items added successfully.',
                'accessory_ids' => $accessoryIds // Return all the accessory IDs
            ];
    
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function displayAccessories($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            $result = AccessoryModel::getAccessory($limit, $offset);
    
            if ($result) {
                // Get the total number of items to calculate pagination info
                $totalItems = AccessoryModel::getAccessoryCount(); // Get total count of items
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
                    'message' => 'No accessory items found.'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function deleteAccessory($accessoryId) {
        $deleted = AccessoryModel::deleteAccessory($accessoryId);
        if ($deleted) {
            return [
                'status' => 'success',
                'message' => 'Accessory deleted successfully.'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Failed to delete accessory.'
        ];
    }
}
