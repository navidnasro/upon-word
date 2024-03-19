<?php
/**
 * Template file for cart collaterals (left sidebar) in checkout page
 */

defined('ABSPATH') || exit;
?>

<div class="cart_totals border flex flex-col space-y-5 items-start justify-start border-solid border-[#EBEBEB] bg-white rounded-[5px] py-8 pr-9 pl-14 <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">
    <div class="w-full flex justify-between items-center cart-subtotal">
        <span class="text-[#43454D] font-bold text-[15px]">
            <?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
        <span class="text-[#43454D] font-bold text-[15px]">
            <?php wc_cart_totals_subtotal_html(); ?></span>
    </div>

    <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
        <div class="cart-discount w-full flex justify-between items-center coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
            <span class="text-[#44B75F] font-bold text-[15px]">
                <?php wc_cart_totals_coupon_label( $coupon ); ?></span>
            <span class="text-[#44B75F] font-bold text-[15px]">
                <?php wc_cart_totals_coupon_html( $coupon ); ?></span>
        </div>
    <?php endforeach; ?>

    <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

        <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

        <?php wc_cart_totals_shipping_html(); ?>

        <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

    <?php endif; ?>

    <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
        <div class="fee w-full flex justify-between items-center">
            <span class="text-[#43454D] font-bold text-[15px]">
                <?php echo esc_html( $fee->name ); ?></span>
            <span class="text-[#43454D] font-bold text-[15px]">
                <?php wc_cart_totals_fee_html( $fee ); ?></span>
        </div>
    <?php endforeach; ?>

    <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
        <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
            <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                <div class="tax-rate w-full flex justify-between items-center tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                    <span class="text-[#43454D] font-bold text-[15px]"><?php echo esc_html( $tax->label ); ?></span>
                    <span class="text-[#43454D] font-bold text-[15px]"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="tax-total w-full flex justify-between items-center">
                <span class="text-[#43454D] font-bold text-[15px]">
                    <?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
                <span class="text-[#43454D] font-bold text-[15px]">
                    <?php wc_cart_totals_taxes_total_html(); ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

    <div class="order-total w-full flex justify-between items-center">
        <span class="text-[#44B75F] font-bold text-[15px]">
            <?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
        <span class="text-[#44B75F] font-bold text-[15px]">
            <?php wc_cart_totals_order_total_html(); ?></span>
    </div>

    <div class="place-order flex flex-col items-center space-y-2.5 border-t border-solid border-[#EBEBEB] pt-2.5">
        <noscript>
            <?php
            /* translators: $1 and $2 opening and closing emphasis tags respectively */
            printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
            ?>
            <br/>
            <button type="submit" class="bg-[var(--theme-secondary)] flex items-center justify-center rounded py-2.5 w-52 alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>">
                        <span class="text-white text-[15px] font-bold">
                            <?php esc_html_e( 'Update totals', 'woocommerce' ); ?>
                        </span>
            </button>
        </noscript>

        <?php wc_get_template( 'checkout/terms.php' ); ?>

        <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

        <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="bg-[var(--theme-secondary)] flex text-white text-[15px] font-bold items-center justify-center rounded py-2.5 w-52 alt' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) ) ) . '" data-value="' . esc_attr( apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) ) ) . '">' . esc_html( apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) ) ).'</button>' ); // @codingStandardsIgnoreLine ?>

        <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
    </div>

    <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
</div>
