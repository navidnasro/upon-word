<?php

namespace engine\utils;

use WC_Product;

defined('ABSPATH') || exit;

class UserUtils
{
    /**
     * @param WC_Product $product
     * @return bool
     */
    public static function hasFavorited(WC_Product $product) : bool
    {
        $userID = get_current_user_id();
        $data = get_user_meta($userID,'favorites',true);

        if($data)
            return in_array($product->get_id(),$data);

        return false;
    }

    /**
     * @param int $productID
     * @return void
     */
    public static function addFavorite(int $productID): void
    {
        $userID = get_current_user_id();
        $data = get_user_meta($userID,'favorites',true);

        if(!$data)
            $data = [];

        $data[] = $productID;

        update_user_meta($userID,'favorites',$data);
    }

    /**
     * @param int $productID
     * @return void
     */
    public static function removeFavorite(int $productID): void
    {
        $userID = get_current_user_id();
        $data = get_user_meta($userID,'favorites',true);

        if(in_array($productID,$data))
            unset($data[array_search($productID,$data)]);

        update_user_meta($userID,'favorites',$data);
    }

    /**
     * @return mixed
     */
    public static function getFavorites() : mixed
    {
        $userID = get_current_user_id();
        return get_user_meta($userID,'favorites',true);
    }

    /**
     * @param int $productID
     * @return bool
     */
    public static function hasCompare(int $productID): bool
    {
        if(isset($_COOKIE['compare']))
        {
            $products = unserialize(base64_decode($_COOKIE['compare']));

            return in_array($productID,$products);
        }

        return false;
    }

    /**
     * @param int $productID
     * @return void
     */
    public static function addCompare(int $productID): void
    {
        //if user already has added some products and cookie's been set
        if(isset($_COOKIE['compare']))
        {
            $products = unserialize(base64_decode($_COOKIE['compare']));

            //cookie must be updated if a new product is viewed
            if (!in_array($productID, $products)) {
                $products[] = $productID;
                $products = base64_encode(serialize($products));

                setcookie('compare', $products,
                    [
                        'expires' => 0,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );
            }
        }

        //else the user is adding their first product and cookie must be set
        else
        {
            $products[] = $productID;
            $products = base64_encode(serialize($products));

            setcookie('compare',$products,
                [
                    'expires' => 0,
                    'path' => '/',
                    'httponly' => true,
                ]
            );
        }
    }

    /**
     * @param int $productID
     * @return void
     */
    public static function removeCompare(int $productID): void
    {
        //if user already has seen some products and cookie's been set
        if (isset($_COOKIE['compare']))
        {
            $products = self::getCompare();

            //cookie must be updated if a new product is viewed
            if(in_array($productID,$products))
            {
                unset($products[$productID]);
                $products = base64_encode(serialize($products));

                setcookie('compare',$products,
                    [
                        'expires' => 0,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );
            }
        }
    }

    /**
     * @return array|mixed
     */
    public static function getCompare(): mixed
    {
        $compare = [];

        if (isset($_COOKIE['compare']))
            $compare = unserialize(base64_decode($_COOKIE['compare']));

        return $compare;
    }

    /**
     * @param int $productID
     * @return bool
     */
    public static function hasRecentVisits(int $productID): bool
    {
        if(isset($_COOKIE['recent-visits']))
        {
            $recentVisits = self::getRecentVisits();

            return in_array($productID,$recentVisits);
        }

        return false;
    }

    /**
     * @param int $productID
     * @return void
     */
    public static function addRecentVisits(int $productID): void
    {
        $recentVisits = [];

        //if user already has seen some products and cookie's been set
        if (isset($_COOKIE['recent-visits']))
        {
            $recentVisits = self::getRecentVisits();

            //cookie must be updated if a new product is viewed
            if(!in_array($productID,$recentVisits))
            {
                $recentVisits[] = $productID;
                $recentVisits = base64_encode(serialize($recentVisits));

                setcookie('recent-visits',$recentVisits,
                    [
                        'expires' => time()+60*60*24*30,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );
            }
        }

        //else the user is viewing their first product and cookie must be set
        else
        {
            $recentVisits[] = $productID;
            $recentVisits = base64_encode(serialize($recentVisits));

            setcookie('recent-visits',$recentVisits,
                [
                    'expires' => time()+60*60*24*30,
                    'path' => '/',
                    'httponly' => true,
                ]
            );
        }
    }

    /**
     * @param int $productID
     * @return void
     */
    public static function removeRecentVisits(int $productID): void
    {
        //if user already has seen some products and cookie's been set
        if (isset($_COOKIE['recent-visits']))
        {
            $recentVisits = self::getRecentVisits();

            //cookie must be updated if a new product is viewed
            if(in_array($productID,$recentVisits))
            {
                unset($recentVisits[$productID]);
                $recentVisits = base64_encode(serialize($recentVisits));

                setcookie('recent-visits',$recentVisits,
                    [
                        'expires' => time()+60*60*24*30,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );
            }
        }
    }

    /**
     * @return array|mixed
     */
    public static function getRecentVisits(): mixed
    {
        $recentVisits = [];

        if (isset($_COOKIE['recent-visits']))
            $recentVisits = unserialize(base64_decode($_COOKIE['recent-visits']));

        return $recentVisits;
    }
}