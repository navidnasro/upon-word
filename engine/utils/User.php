<?php

namespace engine\utils;

use WC_Product;

defined('ABSPATH') || exit;

class User
{
    /**
     * Checks if the user has already added the product to their favorites
     *
     * @param WC_Product $product
     * @return bool
     */
    public static function hasFavorite(WC_Product $product) : bool
    {
        $userID = get_current_user_id();
        $data = get_user_meta($userID,'favorites',true);

        if($data)
            return in_array($product->get_id(),$data);

        return false;
    }

    /**
     * Adds the product to user's favorites
     *
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
     * Removes the product from user's favorites
     *
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
     * Returns user favorite products
     *
     * @return mixed
     */
    public static function getFavorites() : mixed
    {
        $userID = get_current_user_id();
        return get_user_meta($userID,'favorites',true);
    }

    /**
     * Checks if the product exists in compare
     *
     * @param int $productID
     * @return bool
     */
    public static function hasCompare(int $productID): bool
    {
        if(Cookie::exists('compare'))
        {
            $products = Cookie::get('compare');

            return in_array($productID,$products);
        }

        return false;
    }

    /**
     * Adds the product for compare
     *
     * @param int $productID
     * @return void
     */
    public static function addCompare(int $productID): void
    {
        //if user already has added some products and cookie's been set
        if(Cookie::exists('compare'))
        {
            $products = Cookie::get('compare');

            //cookie must be updated if a new product is viewed
            if (!in_array($productID, $products))
            {
                $products[] = $productID;
                Cookie::set('compare',$products,0);
            }
        }

        //else the user is adding their first product and cookie must be set
        else
        {
            Cookie::set('compare',[$productID],0);
        }
    }

    /**
     * Removes the product from compare
     *
     * @param int $productID
     * @return void
     */
    public static function removeCompare(int $productID): void
    {
        //if user already has seen some products and cookie's been set
        if (Cookie::exists('compare'))
        {
            $products = self::getCompare();

            //cookie must be updated if a new product is viewed
            if(in_array($productID,$products))
            {
                unset($products[$productID]);
                Cookie::set('compare',$products,0);
            }
        }
    }

    /**
     * Returns array of product ids added for compare
     *
     * @return array|mixed
     */
    public static function getCompare(): mixed
    {
        $compare = [];

        if (Cookie::exists('compare'))
            $compare = Cookie::get('compare');

        return $compare;
    }

    /**
     * Checks if the product has been recently visited by user
     * @param int $productID
     * @return bool
     */
    public static function hasRecentVisits(int $productID): bool
    {
        if(Cookie::exists('recent-visits'))
        {
            $recentVisits = self::getRecentVisits();

            return in_array($productID,$recentVisits);
        }

        return false;
    }

    /**
     * Adds the product to user's recently seen products
     *
     * @param int $productID
     * @return void
     */
    public static function addRecentVisits(int $productID): void
    {
        $recentVisits = [];

        //if user already has seen some products and cookie's been set
        if (Cookie::exists('recent-visits'))
        {
            $recentVisits = self::getRecentVisits();

            //cookie must be updated if a new product is viewed
            if(!in_array($productID,$recentVisits))
            {
                $recentVisits[] = $productID;
                //adds the cookie for a month
                Cookie::set('recent-visits',$recentVisits,time()+60*60*24*30);
            }
        }

        //else the user is viewing their first product and cookie must be set
        else
        {
            $recentVisits[] = $productID;
            Cookie::set('recent-visits',$recentVisits,time()+60*60*24*30);
        }
    }

    /**
     * Removes a product from user's recently seen products
     *
     * @param int $productID
     * @return void
     */
    public static function removeRecentVisits(int $productID): void
    {
        //if user already has seen some products and cookie's been set
        if (Cookie::exists('recent-visits'))
        {
            $recentVisits = self::getRecentVisits();

            //cookie must be updated if a new product is viewed
            if(in_array($productID,$recentVisits))
            {
                unset($recentVisits[$productID]);
                Cookie::set('recent-visits',$recentVisits,time()+60*60*24*30);
            }
        }
    }

    /**
     * Returns all user's recently seen products
     *
     * @return array|mixed
     */
    public static function getRecentVisits(): mixed
    {
        $recentVisits = [];

        if (Cookie::exists('recent-visits'))
            $recentVisits = Cookie::get('recent-visits');

        return $recentVisits;
    }

    /**
     * Returns the rating that user has submitted for the product
     *
     * @param int $productID
     * @param int $userID
     * @return int
     */
    public static function getProductRating(int $productID,int $userID = -1): int
    {
        if ($userID == -1)
            $userID = get_current_user_id();

        $products = get_user_meta($userID,'rated_products',true);
        $rating = 0;

        if($products)
        {
            if(array_key_exists($productID,$products))
                $rating = $products[$productID];
        }

        return $rating;
    }

    /**
     * Checks whether the user has rated the product
     *
     * @param int $productID
     * @param int $userID
     * @return bool
     */
    public static function hasRated(int $productID,int $userID = -1): bool
    {
        if ($userID == -1)
            $userID = get_current_user_id();

        $products = get_user_meta($userID,'rated_products',true);

        return array_key_exists($productID,$products);
    }
}