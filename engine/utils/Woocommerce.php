<?php

namespace engine\utils;

use DivisionByZeroError;
use engine\database\enums\OutputType;
use engine\database\enums\Table;
use engine\database\QueryBuilder;
use engine\security\Escape;
use engine\VarDump;
use engine\woocommerce\ProductVariant;
use engine\woocommerce\ProductVariations;
use WC_Product;
use WC_Product_Attribute;
use WC_Product_Variation;

defined('ABSPATH') || exit;

class Woocommerce
{
    private static ?WC_Product $product = null;

    // all retrieved data from database in app life cycle
    private static array $fetchedData;

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

            return;
        }

        else if($product->is_type('variable'))
        {
            if ($product->get_parent_id() != 0)
            {
                echo (100 - round(((int)$product->get_sale_price() * 100) / (int)$product->get_regular_price())).'%';
            }

            // "sale_percentage" is also the max sale percentage
            $variationsMax = self::getMaxSalePercentage($product);

            if ($range)
            {
                $variationsMin = self::getMinSalePercentage($product);

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
            if ($product->get_parent_id() != 0)
            {
                return (100 - round(((int)$product->get_sale_price() * 100) / (int)$product->get_regular_price()));
            }

            // "sale_percentage" is also the max sale percentage
            $variationsMax = self::getMaxSalePercentage($product);

            if ($range)
            {
                $variationsMin = self::getMinPrice($product);

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
            // if a variation is passed not a product root
            if ($product->get_parent_id() != 0)
            {
                $price = $product->get_regular_price();
                return number_format((float)$price,0,$separator,$separator);
            }

            $maxPrice = self::getMaxPrice($product);

            if ($range)
            {
                $minPrice = self::getMinPrice($product);

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
            // if a variation is passed not a product root
            if ($product->get_parent_id() != 0)
            {
                $price = $product->get_sale_price();
                return number_format((float)$price,0,$separator,$separator);
            }

            $maxPrice = self::getMaxPrice($product);

            if ($range)
            {
                $minPrice = self::getMinPrice($product);

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
     * Returns the discount amount of product price
     *
     * @param WC_Product $product
     * @return int
     */
    public static function getDiscountedPrice(WC_Product $product): float
    {
        return ((float)$product->get_regular_price())-((float)$product->get_sale_price());
    }

    /**
     * Returns the max price of product
     *
     * @param WC_Product $product
     * @return string|null
     */
    public static function getMaxPrice(WC_Product $product): ?string
    {
        $builder = QueryBuilder::getInstance();

        return $builder->select('max_price')
            ->from(Table::WC_PRODUCT_META_LOOKUP)
            ->where('product_id','=',$product->get_id())
            ->getVar();
    }

    /**
     * Returns the min price of product
     *
     * @param WC_Product $product
     * @return string|null
     */
    public static function getMinPrice(WC_Product $product): ?string
    {
        $builder = QueryBuilder::getInstance();

        return $builder->select('min_price')
            ->from(Table::WC_PRODUCT_META_LOOKUP)
            ->where('product_id','=',$product->get_id())
            ->getVar();
    }

    /**
     * Returns the max sale percentage of product
     *
     * @param WC_Product $product
     * @return string|bool
     */
    public static function getMaxSalePercentage(WC_Product $product): string|bool
    {
        return get_post_meta($product->get_id(),'max_sale_percentage',true);
    }

    /**
     * Returns the min sale percentage of product
     *
     * @param WC_Product $product
     * @return string|bool
     */
    public static function getMinSalePercentage(WC_Product $product): string|bool
    {
        return get_post_meta($product->get_id(),'min_sale_percentage',true);
    }

    /**
     * Returns the product's categories
     *
     * @param bool $hide
     * @param bool $addDefault
     * @param bool $rootCats
     * @param bool $onlyIDs
     * @param bool $returnObject
     * @return array
     */
    public static function getProductCats(bool $hide = true, bool $addDefault = true, bool $rootCats = false, bool $onlyIDs = false, bool $returnObject = false): array
    {
        $args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => $hide,
        ];

        if($rootCats)
            $args['parent'] = 0;

        $terms = get_terms($args);

        if ($returnObject)
            return $terms;

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
        $builder = QueryBuilder::getInstance();

        return $builder->setQuery(
            'SELECT meta_value
                    FROM (
                            (SELECT term_id FROM
                            '.$builder->getPrefix().'wc_product_attributes_lookup WHERE
                            product_id = '.$variationID.') AS t1
                            INNER JOIN '.$builder->getPrefix().'termmeta AS t2
                             ON t1.term_id = t2.term_id
                         )
                    WHERE meta_key = "color";'
        )->getVar();
    }

    /**
     * Returns color of passed product attribute
     *
     * @param string $attributeName
     * @param string $optionName
     * @return string|null
     */
    public static function getColorFromAttribute(string $attributeName,string $optionName): ?string
    {
        $builder = QueryBuilder::getInstance();

        $optionName = urldecode($optionName);

        return $builder->setQuery(
            "SELECT tm.meta_value
            FROM wp_terms AS t
            INNER JOIN wp_term_taxonomy AS tt ON t.term_id = tt.term_id
            INNER JOIN wp_termmeta AS tm ON tm.term_id = t.term_id
            WHERE tt.taxonomy = '{$attributeName}'
              AND t.name = '{$optionName}'
              AND tm.meta_key = 'color';"
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
            $color = self::getColorFromVariation($variant->getID());

            if(is_null($color))
                continue;

            $colors[] = $color;
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
        $inRangeIDs = [];
        $start = $dateRange[0];
        $end = $dateRange[1];

        //post_id of products that have the specified start date
        $builder = QueryBuilder::getInstance();

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
     * @param bool $returnObject
     * @return array
     */
    public static function getBrands(bool $hideEmpty = true ,bool $returnObject = false): array
    {
        $options = [];

        $brands = get_terms(
            [
                'taxonomy' => 'brands',
                'hide_empty' => $hideEmpty
            ]
        );

        if (!is_wp_error($brands))
        {
            if ($returnObject)
                return $brands;

            else
            {
                foreach ($brands as $brand)
                    $options[$brand->term_id] = $brand->name;
            }
        }

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
        if (is_null(self::$product) || self::$product->get_id() != get_the_ID())
        {
            if (Elementor::isPreview() || Elementor::isEditor())
                self::$product = wc_get_product(CodeStar::getOption('sample-product'));

            else
                self::$product = wc_get_product(get_the_ID());

            return self::$product;
        }

        else
            return self::$product;
    }

    /**
     * Returns both average_rating and rating_count of product
     *
     * @param WC_Product $product
     * @return array
     */
    public static function getProductRating(WC_Product $product): array
    {
        $query = QueryBuilder::getInstance();

        return $query->select('average_rating','rating_count')
            ->from(Table::WC_PRODUCT_META_LOOKUP)
            ->where('product_id','=',$product->get_id())
            ->getRow(OutputType::ASSOCIATIVE_ARRAY);
    }

    /**
     * Finds the product variation that matches the default attribute values
     *
     * @param WC_Product $product
     * @param array|null $defaultAttributes
     * @return ProductVariant|null
     */
    public static function getDefaultVariation(WC_Product $product,array|null $defaultAttributes = null): ?ProductVariant
    {
        // null means it is not passed to the function
        if (is_null($defaultAttributes))
            $defaultAttributes = $product->get_default_attributes();

        if (!empty($defaultAttributes))
        {
            $variations = new ProductVariations($product);
            $variations = $variations->getVariants();
            $foundVariation = false;

            foreach ($variations as $variation)
            {
                foreach ($defaultAttributes as $name => $value)
                {
                    // this array has only one element , repeats only once
                    if ($variation->hasAttribute($name,$value))
                        $foundVariation = true;

                    else
                    {
                        $foundVariation = false;
                        break;
                    }
                }

                if ($foundVariation)
                    return $variation;
            }
        }

        return null;
    }

    /**
     * Checks whether the passed variation id is for the passed root product
     *
     * @param WC_Product $parentProduct if is variable , this instance is for parent product
     * @param int $variationId
     * @return bool
     */
    public static function isVariation(WC_Product $parentProduct,int $variationId): bool
    {
        $variationProduct = wc_get_product($variationId);

        // if it is variation product
        if ($variationProduct instanceof WC_Product_Variation)
        {
            if ($variationProduct->get_parent_id() != 0 &&
                $variationProduct->get_parent_id() == $parentProduct->get_id())
                return true;
        }

        return false;
    }

    /**
     * Retrieves all colors
     *
     * @return array
     */
    public static function getAllColors(): array
    {
        $builder = QueryBuilder::getInstance();
        $colorAttributes = get_option('color_attributes'); // color taxonomies
        $orClauses = '';

        if($colorAttributes)
        {
            foreach ($colorAttributes as $colorAttribute)
            {
                $orClauses .= 'attribute_id = '.$colorAttribute;

                if (end($colorAttributes) != $colorAttribute)
                    $orClauses .= ' OR ';
            }

            return $builder->setQuery(
                'SELECT term_id FROM
                   (SELECT attribute_name
                    FROM '.$builder->getPrefix().'woocommerce_attribute_taxonomies
                    WHERE '.$orClauses.') AS t1
                   INNER JOIN '.$builder->getPrefix().'term_taxonomy AS t2
                   ON t2.taxonomy LIKE CONCAT("pa_",t1.attribute_name);'
            )->getColumn();
        }

        return [];
    }

    /**
     * Retrieves all attributes from database
     *
     * @param bool $doSort whether to return retrieved data or do sorting before returning
     * @return array
     */
    public static function getAllAttributes(bool $doSort = true): array
    {
        $builder = QueryBuilder::getInstance();

        $results = $builder->setQuery(
            "SELECT attribute_label,taxonomy,t3.term_id,name 
               FROM (SELECT attribute_label,taxonomy,term_id 
               FROM (".$builder->getPrefix()."woocommerce_attribute_taxonomies AS t1
               INNER JOIN ".$builder->getPrefix()."term_taxonomy AS t2 ON
               t2.taxonomy = CONCAT('pa_',t1.attribute_name))) AS t3
               INNER JOIN ".$builder->getPrefix()."terms AS t4 ON t3.term_id = t4.term_id;"
        )->getResults(OutputType::ASSOCIATIVE_ARRAY);

        if (!$doSort)
            return $results;

        $attributes = [];

        foreach ($results as $attribute)
        {
            $label = $attribute['attribute_label'];
            $taxonomy = $attribute['taxonomy'];
            $termId = $attribute['term_id'];
            $name = $attribute['name'];

            // if attribute already in array , append terms to it
            if (isset($attributes[$label]))
                $attributes[$label]['terms'][$name] = $termId;

            // else add attribute it
            else
            {
                $attributes[$label] = [
                    'taxonomy' => $taxonomy,
                    'terms' => [
                        $name => $termId,
                    ]
                ];
            }
        }

        return $attributes;
    }

    public static function getOrderCount(string $status): ?string
    {
        $builder = QueryBuilder::getInstance();

        return $builder->select('count(*)')
            ->from(Table::WC_ORDERS)
            ->where('status','=',$status)
            ->andWhere('customer_id','=',User::getCurrentUser(false))
            ->getVar();
    }
}