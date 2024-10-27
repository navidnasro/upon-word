<?php

namespace engine\admin\hooks;

use engine\database\enums\Table;
use engine\database\QueryBuilder;
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

        if (!$product)
            return;

        // if product is variable
        if ($product->is_type('variable'))
        {
            // get all its variations
            $variations = new ProductVariations($product);
            $variations = $variations->getVariants();

            // taking first variant as default min and max percentage
            $minPercentage = $variations[0]->getSalePercentage();
            $maxPercentage = $variations[0]->getSalePercentage();

            // for each variation add "sale_percentage" meta data
            foreach ($variations as $variant)
            {
                if ($variant->getSalePercentage() > $maxPercentage)
                    $maxPercentage = $variant->getSalePercentage();

                elseif ($variant->getSalePercentage() < $minPercentage)
                    $minPercentage = $variant->getSalePercentage();

                update_post_meta(
                    $variant->getID(),
                    'sale_percentage',
                    $variant->getSalePercentage()
                );
            }

            $builder = QueryBuilder::getInstance();
            $builder->insert(
                [$product->get_id(),'max_sale_percentage',$maxPercentage],
                [$product->get_id(),'min_sale_percentage',$minPercentage],
            )->into(Table::POSTMETA,
                'post_id','meta_key','meta_value'
            )->doQuery();
        }
    }
}

new Actions();