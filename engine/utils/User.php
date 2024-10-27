<?php

namespace engine\utils;

use engine\VarDump;
use WC_Product;
use WP_User;

defined('ABSPATH') || exit;

class User
{
    private static ?WP_User $currentUser = null;
    private static array $metaData = [];
    private static array $shippingAddress = [];
    private static string $formattedShippingAddress = '';
    private static array $billingAddress = [];
    private static string $formattedBillingAddress = '';

    /**
     * Returns current logged-in user
     *
     * @param bool $asObject if true WP_User object , else user id is returned
     * @return WP_User|int|null null if not is guest
     */
    public static function getCurrentUser(bool $asObject = true): WP_User|int|null
    {
        if (is_null(self::$currentUser))
        {
            $user = wp_get_current_user();

            if (!is_null($user) && $user->exists())
                self::$currentUser = $user;

            else
                return null;
        }

        return $asObject ? self::$currentUser : self::$currentUser->ID;
    }

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
     * @return bool|int
     */
    public static function addFavorite(int $productID): bool|int
    {
        $userID = get_current_user_id();
        $data = get_user_meta($userID,'favorites',true);

        if(!$data)
            $data = [];

        $data[] = $productID;

        return update_user_meta($userID,'favorites',$data);
    }

    /**
     * Removes the product from user's favorites
     *
     * @param int $productID
     * @return bool|int
     */
    public static function removeFavorite(int $productID): bool|int
    {
        $userID = get_current_user_id();
        $data = get_user_meta($userID,'favorites',true);

        if(in_array($productID,$data))
            unset($data[array_search($productID,$data)]);

        return update_user_meta($userID,'favorites',$data);
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
//        if(Cookie::exists('recent-visits'))
        if(Session::exists('recent-visits'))
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
//        if (Cookie::exists('recent-visits'))
        if (Session::exists('recent-visits'))
        {
            $recentVisits = self::getRecentVisits();

            //cookie must be updated if a new product is viewed
            if(!in_array($productID,$recentVisits))
            {
                $recentVisits[] = $productID;
                //adds the cookie for a month
//                Cookie::set('recent-visits',$recentVisits,time()+60*60*24*30);
                Session::put('recent-visits',$recentVisits);
            }
        }

        //else the user is viewing their first product and cookie must be set
        else
        {
            $recentVisits[] = $productID;
//            Cookie::set('recent-visits',$recentVisits,time()+60*60*24*30);
            Session::put('recent-visits',$recentVisits);
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
//        if (Cookie::exists('recent-visits'))
        if (Session::exists('recent-visits'))
        {
            $recentVisits = self::getRecentVisits();

            //cookie must be updated if a new product is viewed
            if(in_array($productID,$recentVisits))
            {
                unset($recentVisits[$productID]);
//                Cookie::set('recent-visits',$recentVisits,time()+60*60*24*30);
                Session::put('recent-visits',$recentVisits);
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

//        if (Cookie::exists('recent-visits'))
//            $recentVisits = Cookie::get('recent-visits');

        if (Session::exists('recent-visits'))
            $recentVisits = Session::get('recent-visits');

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

    /**
     * Checks if user has submitted comment for given post
     *
     * @param $postId
     * @return bool
     */
    public static function hasCommented($postId): bool
    {
        $user = wp_get_current_user();

        if (!is_null($user))
        {
            $args = array(
                'post_id' => $postId,
                'user_id' => $user->ID,
                'count' => true
            );
        }

        else
            return false;

        $count = get_comments($args);

        return $count > 0;
    }

    /**
     * Returns shipping address
     *
     * @param string $part
     * @param bool $return
     * @return array|void
     */
    public static function getShippingAddress(string $part = '',bool $return = false)
    {
        if (empty(self::$shippingAddress))
        {
            self::$shippingAddress = [
                'country'   => User::getMeta(-1,'shipping_country',''),
                'state'     => User::getMeta(-1,'shipping_state',''),
                'city'      => User::getMeta(-1,'shipping_city',''),
                'address_1' => User::getMeta(-1,'shipping_address_1',''),
                'address_2' => User::getMeta(-1,'shipping_address_2',''),
            ];

            self::$formattedShippingAddress = WC()->countries->get_formatted_address(self::$shippingAddress,' , ');

            self::$shippingAddress['phone']      = User::getMeta(-1,'shipping_phone','');
            self::$shippingAddress['postcode']   = User::getMeta(-1,'shipping_postcode','');
            self::$shippingAddress['first_name'] = User::getMeta(-1,'shipping_first_name','');
            self::$shippingAddress['last_name']  = User::getMeta(-1,'shipping_last_name','');
        }

        if (!empty($part))
            return self::$shippingAddress[$part];

        if ($return)
            return self::$shippingAddress;
    }

    /**
     * Returns user shipping address
     *
     * @return string
     */
    public static function getFormattedShippingAddress(): string
    {
        if (empty(self::$shippingAddress))
            self::getShippingAddress();

        return self::$formattedShippingAddress;
    }

    /**
     * Returns billing address
     *
     * @param string $part
     * @param bool $return
     * @return array|void
     */
    public static function getBillingAddress(string $part = '',bool $return = false)
    {
        if (empty(self::$billingAddress))
        {
            self::$billingAddress = [
                'country'   => User::getMeta(-1,'billing_country',''),
                'state'     => User::getMeta(-1,'billing_state',''),
                'city'      => User::getMeta(-1,'billing_city',''),
                'address_1' => User::getMeta(-1,'billing_address_1',''),
                'address_2' => User::getMeta(-1,'billing_address_2',''),
            ];

            self::$formattedBillingAddress = WC()->countries->get_formatted_address(self::$billingAddress,' , ');

            self::$billingAddress['phone']      = User::getMeta(-1,'billing_phone','');
            self::$billingAddress['postcode']   = User::getMeta(-1,'billing_postcode','');
            self::$billingAddress['first_name'] = User::getMeta(-1,'billing_first_name','');
            self::$billingAddress['last_name']  = User::getMeta(-1,'billing_last_name','');
        }

        if (!empty($part))
            return self::$billingAddress[$part];

        if ($return)
            return self::$billingAddress;
    }

    /**
     * Forms user billing address
     *
     * @return string
     */
    public static function getFormattedBillingAddress(): string
    {
        if (empty(self::$formattedBillingAddress))
            self::getBillingAddress();

        return self::$formattedBillingAddress;
    }

    /**
     * Retrieves user meta data from database and caches it
     * 
     * @param int $userID -1 means current user
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getMeta(int $userID,string $key = '',mixed $default = false): mixed
    {
        // If user id == -1 means consider current user
        if ($userID == -1)
            $userID = self::getCurrentUser(false);

        // Check the cache to retrieve data from
        if (isset(self::$metaData[$userID]))
        {
            if (empty($key) && isset(self::$metaData[$userID]['all']))
                return self::$metaData[$userID]['all'];

            else if (isset(self::$metaData[$userID][$key]))
                return self::$metaData[$userID][$key];
        }

        // If data is not cached yet , retrieve it from database
        $data = get_user_meta($userID,$key,true);

        if ($data)
        {
            if (empty($key))
            {
                foreach ($data as $key => $dataArray)
                    self::$metaData[$userID]['all'][$key] = $dataArray[0];
            }

            else
            {
                if (is_serialized($data))
                    $data = unserialize($data);

                else
                    self::$metaData[$key] = $data;
            }

            return $data;
        }

        return $default;
    }

    /**
     * Adds or updates user meta, if already exists
     *
     * @param int $userID
     * @param string $key
     * @param mixed $value
     * @return bool|int
     */
    public static function updateOrAddMeta(int $userID,string $key,mixed $value): bool|int
    {
        if ($userID == -1)
            $userID = self::getCurrentUser(false);

        self::$metaData[$userID][$key] = $value;
        return update_user_meta($userID,$key,$value);
    }
}