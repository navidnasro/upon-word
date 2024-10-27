<?php

namespace engine\templates\demoOne;

use engine\enums\Defaults;
use engine\security\Escape;
use engine\templates\CartBox as CartBoxInterface;
use engine\templates\demoOne\responsive\CartBox as ResponsiveCartBox;
use engine\utils\Woocommerce;
use WC_Product;

class CartBox implements CartBoxInterface
{
    protected WC_Product $product;

    public function prepare(array $data = []): CartBox
    {
        if (isset($data['product']))
            $this->product = $data['product'];

        else
            $this->product = Woocommerce::getCurrentProduct();

        return $this;
    }

    public function getResponsive(): CartBox
    {
        // single object
        return ResponsiveCartBox::getInstance();
    }

    public function render(): void
    {
        $this->openWrapper();
        $this->image();
        $this->title();
        $this->price();
        $this->closeWrapper();
    }

    public function openWrapper(): void
    {
        ?>
        <article <?php wc_product_class('product-cart relative mt-2.5 w-[225px] mr-2.5 bg-white rounded-3xl',$this->product); ?>>
            <div class="product-card flex items-center bg-white rounded-3xl">
                <a class="w-full p-5 flex flex-col"
                   href="<?php echo $this->product->get_permalink() ?>">
        <?php
    }

    public function image(): void
    {
        $thumbnail = get_the_post_thumbnail_url($this->product->get_id(),'product_thumb');
        ?>
        <figure>
            <img style="width: 100%; height: 150px"
                 src="<?php echo $thumbnail ?: Defaults::ProductNoImage; ?>"
                 alt="<?php echo $this->product->get_title(); ?>">
        </figure>
        <?php
    }

    public function title(): void
    {
        ?>
        <h3 class="my-2.5 text-[15px] font-medium leading-6 text-darkblue text-center h-[47px] overflow-hidden">
            <?php echo $this->product->get_title(); ?>
        </h3>
        <?php
    }

    public function price(): void
    {
        ?>
        <div class="product-price flex flex-col items-end h-14 mt-auto">
            <?php
            if (!$this->product->is_in_stock())
                $this->notInStockDisplay();

            else if (!$this->product->is_purchasable())
                $this->notPurchasableDisplay();

            else
            {
                if($this->product->is_on_sale())
                    $this->onSalePriceDisplay();

                else
                    $this->regularPriceDisplay();
            }
            ?>
        </div>
        <?php
    }

    private function notInStockDisplay(): void
    {
        ?>
        <label class="text-red mt-2.5">
            <?php echo Escape::htmlWithTranslation('غیرقابل خرید') ?>
        </label>
        <?php
    }

    private function notPurchasableDisplay(): void
    {
        ?>
        <label class="text-red mt-2.5">
            <?php echo Escape::htmlWithTranslation('ناموجود') ?>
        </label>
        <?php
    }

    private function regularPriceDisplay(): void
    {
        ?>
        <div class="product-normal-price space-x-0.5 space-x-reverse my-[10px]">
            <span class="text-lg font-bold">
                <?php echo Woocommerce::getRegularPrice($this->product,false,false,','); ?>
            </span>
            <span class="text-[11px]">
                <?php echo get_woocommerce_currency_symbol(); ?>
            </span>
        </div>
        <?php
    }

    private function onSalePriceDisplay(): void
    {
        ?>
        <div class="flex items-center space-x-1 space-x-reverse">
            <div class="product-normal-price text-sm text-gray line-through">
                <?php echo Woocommerce::getRegularPrice($this->product,false,false,',').' '.get_woocommerce_currency_symbol() ?>
            </div>
            <div class="product-sale-percentage flex items-center justify-center text-sm w-9 h-4 leading-[19px] font-medium rounded-[10px] bg-rose text-white">
                <?php echo '%'.Woocommerce::getSalePercentage($this->product) ?>
            </div>
        </div>
        <div class="product-sale-price space-x-0.5 space-x-reverse">
            <span class="text-lg font-bold">
                <?php echo Woocommerce::getSalePrice($this->product,false,false,',') ?>
            </span>
            <span class="text-[11px]">
                <?php echo get_woocommerce_currency_symbol(); ?>
            </span>
        </div>
        <?php
    }

    public function closeWrapper(): void
    {
        ?>
                </a>
            </div>
        </article>
        <?php
    }
}