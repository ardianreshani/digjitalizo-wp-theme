<?php
/**
 * Checkout coupon form.
 *
 * Uses WooCommerce's native checkout coupon form/classes so checkout.js handles
 * apply/remove/update behavior, with only the markup styled for the EMSA design.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.8.0
 */

defined('ABSPATH') || exit;

if (!wc_coupons_enabled()) {
    return;
}
?>

<div class="checkout-coupon-panel">
    <div class="woocommerce-form-coupon-toggle">
        <?php
        wc_print_notice(
            apply_filters(
                'woocommerce_checkout_coupon_message',
                esc_html__('Have a coupon?', 'woocommerce') . ' <a href="#" role="button" aria-label="' . esc_attr__('Enter your coupon code', 'woocommerce') . '" aria-controls="woocommerce-checkout-form-coupon" aria-expanded="false" class="showcoupon">' . esc_html__('Click here to enter your code', 'woocommerce') . '</a>'
            ),
            'notice'
        );
        ?>
    </div>

    <form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none" id="woocommerce-checkout-form-coupon">
        <p class="form-row form-row-first">
            <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>
            <input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e('Shkruaj Kodin për të përfituar Zbritjen', 'base-theme'); ?>" id="coupon_code" value="">
        </p>

        <p class="form-row form-row-last">
            <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
                <?php esc_html_e('Apliko', 'base-theme'); ?>
            </button>
        </p>

        <div class="clear"></div>
    </form>
</div>
