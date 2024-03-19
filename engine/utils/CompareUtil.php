<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class CompareUtil
{
    /**
     * @param integer $id
     * @return boolean
     */
    public static function isIncluded(int $id): bool
    {
        if(isset($_COOKIE['compare']))
        {
            $productIDs = unserialize(base64_decode($_COOKIE['compare']));

            if(in_array($id,$productIDs))
                return true;
        }

        return false;
    }

    /**
     * Merges arrays of attribute keys
     *
     * @param array $productIDs
     * @return array|int[]|string[]
     */
    public static function mergeAttributes(array $productIDs): array
    {
        $attributesKeys = [];

        foreach ($productIDs as $productID)
        {
            //attributes in desired format, key => options[]
            $attributes = WcUtils::getAttributes($productID);
            $attributesKeys = array_merge($attributesKeys,array_keys($attributes));
        }

        return $attributesKeys;
    }
}