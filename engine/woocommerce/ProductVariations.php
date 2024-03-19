<?php

namespace engine\woocommerce;

use WC_Product;

defined('ABSPATH') || exit;

class ProductVariations
{
    private $variations;
    private $variants;

    /**
     * @param WC_Product $product
     */
    function __construct($product)
    {
        $this->variations = $product->get_available_variations();
        $this->variants = array();
        
        $this->generateVariants();
    }

    private function generateVariants(): void
    {
        foreach($this->variations as $variant)
            $this->variants[] = new ProductVariant($variant);
    }

    /**
     * @return ProductVariant[]
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * @return ProductVariant
     */
    public function getFirstVariant(): ProductVariant
    {
        return $this->variants[0];
    }

    /**
     * @return ProductVariant
     */
    public function getLastVariant(): ProductVariant
    {
        return end($this->variants);
    }
}