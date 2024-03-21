<?php

namespace engine\admin\hooks;

use engine\utils\Woocommerce;
use engine\woocommerce\ProductVariations;

defined('ABSPATH') || exit;

class Actions
{
    public function __construct()
    {
        add_action('save_post_product',[$this,'addSalePercentageMeta'],999999999);
    }

    /**
     * Adds sale percentage value among product's meta data on post save/update
     *
     * @param int $postID Post ID.
     * @return void
     */
    public function addSalePercentageMeta(int $postID): void
    {
        $product = wc_get_product($postID);

        // if product is variable
        if ($product->is_type('variable'))
        {
            // get all its variations
            $variations = new ProductVariations($product);
            $variations = $variations->getVariants();

            // taking first variant as default min and max percentage
            $minPercentage = $variations[0]->getSalePercentage();
            $maxPercentage = $variations[0]->getSalePercentage();

            // taking first variant as default min and max price
            $minPrice = $variations[0]->getDisplayPrice();
            $maxPrice = $variations[0]->getDisplayPrice();

            // for each variation add "sale_percentage" meta data
            foreach ($variations as $variant)
            {
                if ($variant->getSalePercentage() > $maxPercentage)
                    $maxPercentage = $variant->getSalePercentage();

                elseif ($variant->getSalePercentage() < $minPercentage)
                    $minPercentage = $variant->getSalePercentage();

                if ($variant->getDisplayPrice() > $maxPrice)
                    $maxPrice = $variant->getDisplayPrice();

                elseif ($variant->getDisplayPrice() < $minPrice)
                    $minPrice = $variant->getDisplayPrice();

                update_post_meta(
                    $variant->getID(),
                    'sale_percentage',
                    $variant->getSalePercentage()
                );
            }

            // adding the max sale percentage for the parent product as its sale percentage
            update_post_meta(
                $product->get_id(),
                'sale_percentage',
                $maxPercentage
            );

            // adding the max sale percentage for the parent product
            update_post_meta(
                $product->get_id(),
                'min_sale_percentage',
                $minPercentage
            );

            // adding the min price for the parent product
            update_post_meta(
                $product->get_id(),
                'min_price',
                $minPrice
            );

            // adding the max price for the parent product
            update_post_meta(
                $product->get_id(),
                'max_price',
                $maxPrice
            );

//            $builder = new QueryBuilder();
//            $builder->insert(
//                [$product->get_id(),'sale_percentage',$maxPercentage],
//                [$product->get_id(),'max_sale_percentage',$maxPercentage],
//                [$product->get_id(),'min_sale_percentage',$minPercentage],
//                [$product->get_id(),'max_price',$maxPrice],
//                [$product->get_id(),'min_price',$minPrice],
//            )->into(Table::POSTMETA,
//                'post_id','meta_key','meta_value'
//            )->doQuery();
        }

        // if product is simple
        else if ($product->is_type('simple'))
        {
            // add "sale_percentage" meta data
            update_post_meta(
                $postID,
                'sale_percentage',
                Woocommerce::getSalePercentage(wc_get_product($postID))
            );
        }

    }
}

new Actions();