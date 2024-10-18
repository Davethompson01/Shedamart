<?php

namespace App\Controllers;

use App\Models\AccessoryModel;
require_once __DIR__ . "/../../Models/categories/accessory.php";
use Exception;

class AccessoryController
{
    public static function createAccessories($accessoryDataArray) {
        try {
            if (!is_array($accessoryDataArray)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid data format. Must be an array of accessory items.'
                ];
            }
    
            $accessoryIds = [];
            foreach ($accessoryDataArray as $accessoryData) {
                $accessoryId = AccessoryModel::insertAccess($accessoryData);
                if ($accessoryId) {
                    $accessoryIds[] = $accessoryId;
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
                'accessory_id' => $accessoryIds
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
                $totalItems = AccessoryModel::getAccessoryCount();
                $totalPages = ceil($totalItems / $limit);
    
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
