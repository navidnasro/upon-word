<?php

namespace engine\utils;

use DivisionByZeroError;
use engine\database\enums\OutputType;
use engine\database\enums\Table;
use engine\database\QueryBuilder;
use engine\security\Escape;
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
     * @param bool $range
     * @param bool $max
     * @return void
     */
    public static function printSalePercentage(WC_Product $product,bool $range = false,bool $max = false): void
    {
        if($product->is_type('simple'))
        {
            try {
                echo (100 - round(((int)$product->get_sale_price() * 100) / $product->get_regular_price())).'%';
            }catch (DivisionByZeroError $error){
                echo '0%';
                return;
            }
        }

        else if($product->is_type('variable'))
        {
            // "sale_percentage" is also the max sale percentage
            $variationsMax = get_post_meta($product->get_id(),'sale_percentage',true);

            if ($range)
            {
                $variationsMin = get_post_meta($product->get_id(),'min_sale_percentage',true);

                echo $variationsMin.'% - '.$variationsMax.'%';
            }

            elseif ($max)
                echo $variationsMax.'%';

            else
            {
                $variations = new ProductVariations($product);
                echo '%'.$variations->getFirstVariant()->getSalePercentage();
            }

            return;
        }

        echo '0%';
    }

    /**
     * Returns sale percentage
     *
     * @param WC_Product $product
     * @param bool $range
     * @param bool $max
     * @return float|int|mixed
     */
    public static function getSalePercentage(WC_Product $product,bool $range = false,bool $max = false): mixed
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
            $variationsMax = get_post_meta($product->get_id(),'sale_percentage',true);

            if ($range)
            {
                $variationsMin = get_post_meta($product->get_id(),'min_sale_percentage',true);

                return $variationsMin.'% - '.$variationsMax.'%';
            }

            elseif ($max)
                return $variationsMax;

            else
            {
                $variations = new ProductVariations($product);
                return $variations->getFirstVariant()->getSalePercentage();
            }
        }

        return 0;
    }

    /**
     * Returns regular price of product
     *
     * @param WC_Product $product
     * @param bool $range
     * @param bool $max
     * @param string $separator
     * @return int|string
     */
    public static function getRegularPrice(WC_Product $product,bool $range = false,bool $max = false,string $separator = '/'): int|string
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

            if ($range)
            {
                $minPrice = get_post_meta($product->get_id(),'min_price',true);

                return number_format((float)$maxPrice,0,$separator,$separator).' - '.
                    number_format((float)$minPrice,0,$separator,$separator);
            }

            elseif ($max)
                return number_format((float)$maxPrice,0,$separator,$separator);

            else
            {
                $variations = new ProductVariations($product);
                $price = $variations->getFirstVariant()->getRegularPrice();

                return number_format($price,0,$separator,$separator);
            }
        }

        return 0;
    }

    /**
     * Returns the product's sale price
     *
     * @param WC_Product $product
     * @param bool $range
     * @param bool $max
     * @param string $separator
     * @return int|string
     */
    public static function getSalePrice(WC_Product $product,bool $range = false,bool $max = false,string $separator = '/'): int|string
    {
        if($product->is_type('simple'))
        {
            $price = $product->get_sale_price();
            return number_format((float)$price,0,$separator,$separator);
        }

        else if($product->is_type('variable'))
        {
            $maxPrice = get_post_meta($product->get_id(),'max_price',true);

            if ($range)
            {
                $minPrice = get_post_meta($product->get_id(),'min_price',true);

                return number_format((float)$maxPrice,0,$separator,$separator).' - '.
                    number_format((float)$minPrice,0,$separator,$separator);
            }

            elseif ($max)
                return number_format((float)$maxPrice,0,$separator,$separator);

            else
            {
                $variations = new ProductVariations($product);
                $price = $variations->getFirstVariant()->getDisplayPrice();

                return number_format($price,0,$separator,$separator);
            }
        }

        return 0;
    }

    /**
     * Returns the product's categories
     *
     * @param bool $hide
     * @param bool $addDefault
     * @param bool $rootCats
     * @param bool $onlyIDs
     * @return array
     */
    public static function getProductCats(bool $hide = true, bool $addDefault = true, bool $rootCats = false, bool $onlyIDs = false): array
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
            $options[0] = 'انتخاب کنید';

        if(!empty($terms) && !is_wp_error($terms))
        {
            if ($onlyIDs)
                foreach($terms as $term)
                    $options[] = $term->term_id;
            else
                foreach($terms as $term)
                    $options[$term->term_id] = $term->name;
        }

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
    public static function getColorFromVariation(int $variationID): ?string
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
     * Returns color of passed product attribute
     *
     * @param string $attributeName
     * @param string $optionName
     * @return string
     */
    public static function getColorFromAttribute(string $attributeName,string $optionName): ?string
    {
        $builder = new QueryBuilder();

        return $builder->setQuery(
            "SELECT meta_value FROM wp_termmeta AS t3 
                  WHERE (SELECT t2.term_id FROM 
                   (SELECT term_id FROM wp_term_taxonomy 
                   WHERE taxonomy = {$attributeName}) AS t1 INNER JOIN wp_terms AS t2
                   ON t1.term_id = t2.term_id 
                   WHERE t2.name = {$optionName}) = t3.term_id AND t3.meta_key = 'color';"
        )->getVar();
    }

    /**
     * If the attribute is used as variation of a product
     *
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
     * Checks whether the attribute is a color attribute
     *
     * @param string $attribute attribute name
     * @return bool
     */
    public static function isColor(string $attribute): bool
    {
        if (str_contains($attribute,'pa_'))
            $attribute = str_replace('pa_','', $attribute);

        $attributes = get_option('color_attributes');

        return isset($attributes[$attribute]);
    }

    /**
     * Returns sale dated products
     *
     * @return array
     */
    public static function getSaleDatedProducts(): array
    {
        $saleProducts = [];

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
     * Returns date ranges in which products are scheduled to be on sale
     *
     * @return array
     */
    public static function getSaleDatesRanges(): array
    {
        $saleTimestaps = [];

        //All OnSale Products (POST_IDs) , Whether Scheduled Or Not
        $productIDs = wc_get_product_ids_on_sale();

        //If Scheduled , It Has _Sale_Price_Dates_from/to postmeta
        foreach($productIDs as $productID)
        {
            $startDate = get_post_meta($productID,'_sale_price_dates_to',true);

            if(!empty($startDate))
            {
                //The Date(timestamp) Where Sale starts
                $endDate = get_post_meta($productID,'_sale_price_dates_to',true);

                //Serialize array of start and end dates to have them both together as one array key
                $serilizedDates = serialize(
                    [
                        $startDate,
                        $endDate
                    ]
                );

                $saleTimestaps[$serilizedDates] = Escape::htmlWithTranslation('از').date('Y-m-d',$startDate).
                    Escape::htmlWithTranslation('تا').date('Y-m-d',$endDate);
            }
        }

        return $saleTimestaps;
    }

    /**
     * Returns id of products that are on sale in passed range
     *
     * @param array $dateRange
     * @return array
     */
    public static function getSaleRangeProducts(array $dateRange): array
    {
        global $wpdb;

        $inRangeIDs = [];
        $start = $dateRange[0];
        $end = $dateRange[1];

        //post_id of products that have the specified start date
        $builder = new QueryBuilder();

        $productIDs = $builder->select('post_id')
            ->from(Table::POSTMETA)
            ->where('meta_key','=','_sale_price_dates_from')
            ->andWhere('meta_value','=',$start)
            ->getResults(OutputType::NUMERIC_ARRAY);

        /**
         * Checking the end date of returned products.
         * if the query returns non-empty,means the product has the end date
         * and surely is in specified range
         */
        foreach($productIDs as $productID)
        {
            $builder->resetQuery();

            $isInRange = $builder->select('*')
                ->from(Table::POSTMETA)
                ->where('post_id','=',$productID[0])
                ->andWhere('meta_key','=','_sale_price_dates_to')
                ->andWhere('meta_value','=',$end)
                ->getResults();

            if($isInRange)
                $inRangeIDs[] = $productID[0];
        }

        return $inRangeIDs;
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

    /**
     * Retrieves a product object
     *
     * @return bool|WC_Product|null
     */
    public static function getCurrentProduct(): bool|WC_Product|null
    {
        if (Elementor::isPreview() || Elementor::isEditor())
            return wc_get_product(CodeStar::getOption('sample-product'));

        else
            return wc_get_product(get_the_ID());
    }
}