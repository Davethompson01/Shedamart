<?php

namespace App\Controllers;

use App\Models\SupermarketModel;
require_once __DIR__ . "/../../Models/categories/supermarket.php";
use Exception;

class SupermarketController
{
    public static function createSupermarketItems($itemDataArray) {
        try {
            if (!is_array($itemDataArray)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid data format. Must be an array of supermarket items.'
                ];
            }

            $itemIds = [];
            foreach ($itemDataArray as $itemData) {
                $itemId = SupermarketModel::insertItem($itemData);
                if ($itemId) {
                    $itemIds[] = $itemId;
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to add one or more supermarket items.'
                    ];
                }
            }

            return [
                'status' => 'success',
                'message' => 'Supermarket items added successfully.',
                'item_ids' => $itemIds
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function displaySupermarketItems($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;

            $result = SupermarketModel::getItems($limit, $offset);

            if ($result) {
                $totalItems = SupermarketModel::getItemCount();
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
                    'message' => 'No supermarket items found.'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function deleteSupermarketItem($itemId) {
        $deleted = SupermarketModel::deleteItem($itemId);
        if ($deleted) {
            return [
                'status' => 'success',
                'message' => 'Supermarket item deleted successfully.'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Failed to delete supermarket item.'
        ];
    }
}
