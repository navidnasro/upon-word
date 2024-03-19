<?php

namespace engine\utils;

use engine\wc\ProductVariations;
use WC_Product;
use WC_Product_Attribute;

defined('ABSPATH') || exit;

class WcUtils
{
    /**
    * Get sale percentage
    *
    * @param WC_Product $product
    *
    * @return String $percentage
    */
    public static function salePecentage($product)
    {
        if($product->is_type('simple'))
            return '%' . ( 100 - round( ((int)$product->get_sale_price()*100) / $product->get_regular_price() ) );

        else if($product->is_type('variable'))
        {
            $variations = new ProductVariations($product);
            return '%'.$variations->getFirstVariant()->getSalePercentage();
        }

        return null;
    }

    /**
    * Get product rating
    *
    * @param WC_Product $product
    *
    * @return Integer $rating_count
    */
    public static function getRatingCount($product)
    {
        global $wpdb;

        //number of people who rated for this product
        $rating_count = $wpdb->get_var(
            'SELECT rating_count 
            FROM `wp_wc_product_meta_lookup`
            WHERE product_id="'.$product->get_id().'"'
        );

        return $rating_count;
    }

    /**
     * @param WC_Product $product
     * @param string $separator
     * @return int|string
     */
    public static function getRegularPrice(WC_Product $product,string $separator = '/')
    {
        if($product->is_type('simple'))
        {
            $price = $product->get_regular_price();
            return number_format((float)$price,0,$separator,$separator);
        }

        else if($product->is_type('variable'))
        {
            $variations = new ProductVariations($product);
            $price = $variations->getFirstVariant()->getDisplayPrice();

            return number_format((float)$price,0,$separator,$separator);
        }

        return -1;
    }

    /**
     * @param WC_Product $product
     * @param string $separator
     * @return int|string
     */
    public static function getSalePrice(WC_Product $product,string $separator = '/')
    {
        if($product->is_type('simple'))
        {
            $price = $product->get_sale_price();
            return number_format((float)$price,0,$separator,$separator);
        }

        else if($product->is_type('variable'))
        {
            $variations = new ProductVariations($product);
            $price = $variations->getFirstVariant()->getRegularPrice();

            return number_format((float)$price,0,$separator,$separator);
        }

        return -1;
    }

    /**
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
     * @return array
     */
    public static function getProducts()
    {
        $products = get_posts(
            [
                'post_type' => 'product',
                'posts_per_page' => -1,
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
    * Get product average rating
    *
    * @param WC_Product $product
    *
    * @return Float $average_rating
    */
    public static function getAverageRating($product)
    {
        global $wpdb;
		
		//product's average rating
		$average_rating = $wpdb->get_var(
			'SELECT average_rating 
			FROM `wp_wc_product_meta_lookup` 
			WHERE product_id="'.$product->get_id().'"'
		);

        return $average_rating;
    }

    /**
     * Returns the color of passed product variation
     * 
     * @param Int $variationID
     * 
     * @return String color
     */
    public static function getColor($variationID)
    {
        global $wpdb;

        $termID = $wpdb->get_var(
            'SELECT term_id 
            FROM wp_wc_product_attributes_lookup
            WHERE product_id = "'.$variationID.'"'
        );

        $color = $wpdb->get_var(
            'SELECT meta_value
            FROM wp_termmeta
            WHERE term_id = "'.$termID.'" AND meta_key = "color"'
        );

        return $color;
    }

    /**
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

    public static function getAttributeId(string $name) : ?string
    {
        global $wpdb;

        return $wpdb->get_var(
            "SELECT attribute_id
             from {$wpdb->prefix}woocommerce_attribute_taxonomies 
             WHERE attribute_name={$name}"
        );
    }

    public static function pasteVariationHTML($product=null)
    {
        if(is_null($product))
            $product = wc_get_product(get_the_ID());

        $variants = new ProductVariations($product);
        $variants = $variants->getVariants();

?>
        <div class="woocommerce-product-variations">
<?php
            foreach($variants as $variant)
            {
                $color = self::getColor($variant->getID());
?>
                <div class="product-variation" 
                data-variation-id="<?php echo $variant->getID() ?>"
                data-regular-price="<?php echo $variant->getRegularPrice() ?>"
                data-sale-price="<?php echo $variant->getDisplayPrice() ?>">
<?php
                if(!is_null($color) && !empty($color))
                {
?>
                    <span 
                    class="product-variation__-color"
                    style="border-radius: 100%;
                        background-color:<?php echo $color ? $color : '#f3f3f3' ?>;"></span>
                    <span>
<?php
                } 
                        echo $variant->getAttributeName()
?>
                    </span>
                    <span class="tooltipp">
<?php 
                        echo $variant->getAttributeName()
?>
                    </span>
                </div>
<?php
            }
?>
        </div>
<?php
    }

    /**
     * @param WC_Product | int
     * 
     * @return array | null
     */
    public static function getVariableColors($product)
    {
        if(!is_object($product))
            $product = wc_get_product($product);

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
     * @param WC_Product_Attribute[] $attributes
     */
    public static function getSimpleColors($attributes)
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
     * @param WC_Product $product
     * @param Int $colorID
     * 
     * @return Boolean
     */
    public static function hasColor($product,$colorID)
    {
        global $wpdb;

        if(!$colorID)
            return false;

        $hasColor = $wpdb->get_results(
            'SELECT
             * FROM `wp_wc_product_attributes_lookup` 
             WHERE product_or_parent_id="'.$product->get_id().'" 
             AND term_id="'.$colorID.'";'
        );

        return $hasColor;
    }

    /**
     * @param WC_Product $product
     * @param Int $attributeID
     * 
     * @return Boolean
     */
    public static function hasAttribute($product,$attributeID)
    {
        global $wpdb;

        if(!$attributeID)
            return false;

        $hasAttribute = $wpdb->get_results(
            "SELECT
             * FROM {$wpdb->prefix}wc_product_attributes_lookup
             WHERE product_or_parent_id={$product->get_id()}
             AND term_id={$attributeID}"
        );
        
        return $hasAttribute;
    }

    /**
     * @return array
     */
    public static function getSaleDatedProducts()
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
     * @param WC_Product $product
     * 
     * @return array | null
     */
    public static function getOnsaleDate($product)
    {
        if(is_object($product))
            $product = $product->get_id();

        //The Date(timestamp) Where Sale starts 
        $startDate = get_post_meta($product,'_sale_price_dates_from',true);

        //The Date(timestamp) Where Sale ends
        $endDate = get_post_meta($product,'_sale_price_dates_to',true);

        if($startDate && $endDate)
            return [
                'start' => $startDate,
                'end'   => $endDate
            ];

        return null;
    }

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
}