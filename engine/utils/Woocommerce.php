<?php

namespace engine\utils;

use DivisionByZeroError;
use engine\database\enums\Table;
use engine\database\QueryBuilder;
use engine\woocommerce\ProductVariations;
use WC_Product;
use WC_Product_Attribute;

defined('ABSPATH') || exit;

class Woocommerce
{
    /**
     * Prints sale percentage
     *
     * @param WC_Product $product
     * @return void
     */
    public static function printSalePercentage(WC_Product $product): void
    {
        if($product->is_type('simple'))
        {
            try {
                echo (100 - round(((int)$product->get_sale_price() * 100) / $product->get_regular_price())).'%';
            }catch (DivisionByZeroError $error){
                echo '0%';
            }
        }

        else if($product->is_type('variable'))
        {
            // "sale_percentage" is also the max sale percentage
            $max = get_post_meta($product->get_id(),'sale_percentage',true);
            $min = get_post_meta($product->get_id(),'min_sale_percentage',true);

            echo $min.'% - '.$max.'%';
        }

        echo '0%';
    }

    /**
     * Returns sale percentage
     *
     * @param WC_Product $product
     * @return float|int|mixed
     */
    public static function getSalePercentage(WC_Product $product): mixed
    {
        if($product->is_type('simple'))
        {
            try {
                return (100 - round(((int)$product->get_sale_price() * 100) / (int)$product->get_regular_price()));
            }catch (DivisionByZeroError $error){
                return 0;
            }
        }

        else if($product->is_type('variable'))
        {
            // "sale_percentage" is also the max sale percentage
            return get_post_meta($product->get_id(),'sale_percentage',true);
        }

        return 0;
    }

    /**
     * Returns regular price of product
     *
     * @param WC_Product $product
     * @param string $separator
     * @return int|string
     */
    public static function getRegularPrice(WC_Product $product,string $separator = '/'): int|string
    {
        if($product->is_type('simple'))
        {
            $price = $product->get_regular_price();
            return number_format((float)$price,0,$separator,$separator);
        }

        // if is variable , return price range
        else if($product->is_type('variable'))
        {
            $maxPrice = get_post_meta($product->get_id(),'max_price',true);
            $minPrice = get_post_meta($product->get_id(),'min_price',true);

            return number_format((float)$maxPrice,0,$separator,$separator).' - '.
                number_format((float)$minPrice,0,$separator,$separator);
        }

        return 0;
    }

    /**
     * Returns the product's sale price
     *
     * @param WC_Product $product
     * @param string $separator
     * @return int|string
     */
    public static function getSalePrice(WC_Product $product,string $separator = '/'): int|string
    {
        if($product->is_type('simple'))
        {
            $price = $product->get_sale_price();
            return number_format((float)$price,0,$separator,$separator);
        }

        else if($product->is_type('variable'))
        {
            $maxPrice = get_post_meta($product->get_id(),'max_price',true);
            $minPrice = get_post_meta($product->get_id(),'min_price',true);

            return number_format((float)$maxPrice,0,$separator,$separator).' - '.
                number_format((float)$minPrice,0,$separator,$separator);
        }

        return 0;
    }

    /**
     * Returns the product's categories
     *
     * @param bool $hide
     * @param bool $addDefault
     * @param bool $rootCats
     * @return array
     */
    public static function getProductCats(bool $hide = true, bool $addDefault = true, bool $rootCats = false): array
    {
        $args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => $hide,
        ];

        if($rootCats)
            $args['parent'] = 0;

        $terms = get_terms($args);

        $options = [];

        if($addDefault)
            $options[0] = 'هیچ کدام';

        if(!empty($terms) && !is_wp_error($terms))
            foreach($terms as $term)
                $options[$term->term_id] = $term->name;

        return $options;
    }

    /**
     * Returns products
     *
     * @param int $limit
     * @return array
     */
    public static function getProducts(int $limit = -1): array
    {
        $products = get_posts(
            [
                'post_type' => 'product',
                'posts_per_page' => $limit,
            ]
        );

        $IDs = array(
            '0' => 'هیچ کدام',
        );

        if (!empty($products))
            foreach($products as $product)
                $IDs[$product->ID] = $product->post_title;

        return $IDs;
    }

    /**
     * Returns the color of passed product variation
     *
     * @param int $variationID
     * @return string|null
     */
    public static function getColor(int $variationID): ?string
    {
        $builder = new QueryBuilder();

        return $builder->select('tm.meta_value')
            ->from(Table::WC_PRODUCT_ATTRIBUTES_LOOKUP,'pal')
            ->innerJoin(Table::TERMMETA,'tm')
            ->on('pal','term_id','=','tm','term_id')
            ->where('pal.product_id','=',$variationID)
            ->andWhere('tm.meta_key','=','color')
            ->getVar();
    }

    /**
     * If the attribute is used as variation of a product
     * @param WC_Product_Attribute $attribute
     * @return bool
     */
    public static function isUsedAsVariation(WC_Product_Attribute $attribute): bool
    {
        if ($attribute->get_variation())
            return true;

        return false;
    }

    /**
     * Returns attributes of the product
     *
     * @param int $productID
     * @return array
     */
    public static function getAttributes(int $productID): array
    {
        $product = wc_get_product($productID);
        $productAttributes = $product->get_attributes();
        $attributes = [];

        foreach ($productAttributes as $attribute)
            $attributes[$attribute->get_name()] = $attribute->get_options();

        return $attributes;
    }

    /**
     * Returns color attributes of a variable product
     *
     * @param WC_Product $product
     * @return array|null
     */
    public static function getVariableColors(WC_Product $product): ?array
    {
        //get variant object for each variation
        $variants = new ProductVariations($product);
        $variants = $variants->getVariants();

        $colors = array();

        foreach($variants as $variant)
        {
            $color = self::getColor($variant->getID());

            if(is_null($color))
                continue;

            $colors[$variant->getAttributeName()] = $color;
        }

        if(empty($colors))
            return null;

        return $colors;
    }

    /**
     * Returns color attributes of a simple product
     *
     * @param array $attributes
     * @return array
     */
    public static function getSimpleColors(array $attributes): array
    {
        $colors = array();

        foreach(array_keys($attributes) as $attribute)
        {
            $attribute = substr($attribute,3);
            
            if(strpos($attribute,'color'))
            {
                $ids = $attributes['pa_'.$attribute]->get_options();

                //['سفید'] => #fff
                foreach($ids as $id) 
                    $colors[get_term($id)->name] = get_term_meta($id,'color',true);
            }
        }

        return $colors;
    }

    /**
     * Checks whether the attribute id is a color attribute
     *
     * @param int $id
     * @return bool
     */
    public static function isColor(int $id): bool
    {
        $attributes = get_option('color_attributes');

        return in_array($id,$attributes);
    }

    /**
     * Returns sale dated products
     *
     * @return array
     */
    public static function getSaleDatedProducts(): array
    {
        $saleProducts = array();
        
        //All OnSale Products (POST_IDs) , Whether Scheduled Or Not
        $productIDs = wc_get_product_ids_on_sale();
        
        //If Scheduled , It Has _Sale_Price_Dates_from/to postmeta
        foreach( $productIDs as $productID )
        {
            $isDated = get_post_meta($productID,'_sale_price_dates_to',true);
            
            if($isDated)
                $saleProducts[$productID] = wc_get_product($productID)->get_title();
        }

        return $saleProducts;
    }

    /**
     * Returns sale dates for the product
     *
     * @param WC_Product $product
     * @return array|null
     */
    public static function getOnsaleDate(WC_Product $product): ?array
    {
        $productID = $product->get_id();

        //The Date(timestamp) Where Sale starts 
        $startDate = get_post_meta($productID,'_sale_price_dates_from',true);

        //The Date(timestamp) Where Sale ends
        $endDate = get_post_meta($productID,'_sale_price_dates_to',true);

        if($startDate && $endDate)
            return [
                'start' => $startDate,
                'end'   => $endDate
            ];

        return null;
    }

    /**
     * Returns all product brands
     *
     * @param bool $hideEmpty
     * @return array
     */
    public static function getBrands(bool $hideEmpty = true): array
    {
        $options = [];

        $brands = get_terms(
            [
                'taxonomy' => 'brands',
                'hide_empty' => $hideEmpty
            ]
        );

        if (!is_wp_error($brands))
            foreach ($brands as $brand)
                $options[$brand->term_id] = $brand->name;

        return $options;
    }

    /**
     * Merges arrays of attribute keys
     *
     * @usage in compare page
     * @param array $productIDs
     * @return array|int[]|string[]
     */
    public static function mergeAttributes(array $productIDs): array
    {
        $attributesKeys = [];

        foreach ($productIDs as $productID)
        {
            //attributes in desired format, key => options[]
            $attributes = Woocommerce::getAttributes($productID);
            $attributesKeys = array_merge($attributesKeys,array_keys($attributes));
        }

        return $attributesKeys;
    }
}