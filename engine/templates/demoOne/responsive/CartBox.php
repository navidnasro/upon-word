<?php

namespace engine\templates\demoOne\responsive;

use engine\enums\Defaults;
use engine\templates\demoOne\CartBox as Demo1CartBox;
use engine\utils\Woocommerce;
use engine\VarDump;

class CartBox extends Demo1CartBox
{
    private static ?CartBox $instance = null;

    public static function getInstance(): CartBox
    {
        if (is_null(self::$instance))
            return self::$instance = new self();

        return self::$instance;
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
        <a href="<?php echo esc_url($this->product->get_permalink()) ?>"
            class="flex items-center bg-white p-[15px] h-full w-full space-x-2.5 space-x-reverse">
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
        <h3 class="mb-auto h-[99px] text-[13px] font-medium w-full text-darkblue leading-6 overflow-hidden">
            <?php echo $this->product->get_title(); ?>
        </h3>
        <?php
    }

    public function price(): void
    {
        ?>
        <div class="product-price flex flex-col items-end mt-auto">
            <?php
            if($this->product->is_on_sale())
                $this->onSalePriceDisplay();

            else
                $this->regularPriceDisplay();
            ?>
        </div>
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
        <?php
    }
}