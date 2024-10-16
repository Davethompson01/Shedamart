<?php

namespace App\Controllers;

use App\Models\JewelryModel;
use Exception;

class JewelryController
{
    public static function createJewelry($jewelryData) {
        try {
            $jewelryId = JewelryModel::insertJewelry($jewelryData);
            if ($jewelryId) {
                return [
                    'status' => 'success',
                    'message' => 'Jewelry added successfully.',
                    'jewelry_id' => $jewelryId
                ];
            }
            return [
                'status' => 'error',
                'message' => 'Failed to add jewelry.'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function getJewelry($jewelryId) {
        $jewelry = JewelryModel::getJewelryById($jewelryId);
        if ($jewelry) {
            return [
                'status' => 'success',
                'data' => $jewelry
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Jewelry not found.'
        ];
    }

    public static function getAllJewelry($limit = 10, $offset = 0) {
        $jewelry = JewelryModel::getAllJewelry($limit, $offset);
        return [
            'status' => 'success',
            'data' => $jewelry
        ];
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
