<?php

namespace engine\utils;

use WC_Cart;

defined('ABSPATH') || exit;

class Cart
{
    private static ?WC_Cart $cart = null;

    /**
     * Returns the cart instance
     *
     * @return WC_Cart|null
     */
    public static function getCart(): ?WC_Cart
    {
        if (is_null(self::$cart))
            self::$cart = WC()->cart;

        return self::$cart;
    }

    /**
     * Calculate All Discounts (Coupons + Line Items)
     *
     * @return float
     */
    public static function getTotalDiscounts(): float
    {
        $cart = self::getCart();

        // Calculate coupon discount total
        $couponDiscountTotal = $cart->get_coupon_discount_totals();
        $totalCouponDiscount = array_sum($couponDiscountTotal);

        $totalLineItemDiscount = 0;

        foreach ($cart->get_cart() as $cartItem)
        {
            $product = $cartItem['data'];

            $regularPrice = (float) $product->get_regular_price();
            $salePrice = (float) $product->get_sale_price();

            if ($salePrice > 0)
            {
                $discountPerItem = $regularPrice - $salePrice;
                $totalLineItemDiscount += $discountPerItem * $cartItem['quantity'];
            }
        }

        // Combine coupon discounts and line item discounts
        return (float)$totalCouponDiscount + $totalLineItemDiscount;
    }

    /**
     * Returns the cart total ignoring sale prices
     *
     * @return float
     */
    public static function getOriginalTotal(): float
    {
        $cart = self::getCart();

        $totalRegularPrice = 0;

        foreach ($cart->get_cart() as $cartItem)
        {
            $product = $cartItem['data'];
            $regularPrice = (float) $product->get_regular_price();
            $quantity = $cartItem['quantity'];

            // Calculate total regular price for this item (price * quantity)
            $totalRegularPrice += $regularPrice * $quantity;
        }

        return (float)$totalRegularPrice;
    }

    /**
     * Returns the price to pay of cart
     *
     * @return float
     */
    public static function getPriceToPay(): float
    {
        return (float)Cart::getCart()->get_total('');
    }
}