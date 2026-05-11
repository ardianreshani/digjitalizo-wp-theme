<?php
/**
 * Cart totals.
 *
 * Uses WooCommerce's native .cart_totals surface so cart.js can refresh this
 * block after coupon, quantity, and shipping changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;
?>

<div class="cart_totals order-summary <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">
    <?php do_action('woocommerce_before_cart_totals'); ?>

    <h3 class="order-summary-title"><?php esc_html_e('Shporta juaj', 'base-theme'); ?></h3>

    <div class="order-summary-row cart-subtotal">
        <span><?php esc_html_e('Çmimi total', 'base-theme'); ?></span>
        <span><?php wc_cart_totals_subtotal_html(); ?></span>
    </div>

    <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
        <div class="order-summary-row discount cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
            <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
            <span><?php wc_cart_totals_coupon_html($coupon); ?></span>
        </div>
    <?php endforeach; ?>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
        <?php do_action('woocommerce_cart_totals_before_shipping'); ?>
        <?php
        $packages       = WC()->shipping()->get_packages();
        $chosen_methods = WC()->session->get('chosen_shipping_methods', []);

        foreach ($packages as $i => $package) :
            $chosen = $chosen_methods[$i] ?? '';

            foreach ($package['rates'] as $rate_id => $rate) :
                if ($chosen && $rate_id !== $chosen) {
                    continue;
                }
        ?>
            <div class="order-summary-row shipping">
                <span><?php echo esc_html($rate->get_label()); ?></span>
                <span><?php echo $rate->cost > 0 ? wp_kses_post(wc_price($rate->cost)) : esc_html__('Pa pagesë', 'base-theme'); ?></span>
            </div>
        <?php
                if (!$chosen) {
                    break;
                }
            endforeach;
        endforeach;
        ?>
        <?php do_action('woocommerce_cart_totals_after_shipping'); ?>
    <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>
        <div class="order-summary-row shipping">
            <span><?php esc_html_e('Transporti', 'base-theme'); ?></span>
            <span><?php woocommerce_shipping_calculator(); ?></span>
        </div>
    <?php endif; ?>

    <?php foreach (WC()->cart->get_fees() as $fee) : ?>
        <div class="order-summary-row fee">
            <span><?php echo esc_html($fee->name); ?></span>
            <span><?php wc_cart_totals_fee_html($fee); ?></span>
        </div>
    <?php endforeach; ?>

    <?php if (wc_tax_enabled()) : ?>
        <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
            <div class="order-summary-row tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                <span><?php echo esc_html($tax->label); ?></span>
                <span><?php echo wp_kses_post($tax->formatted_amount); ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php do_action('woocommerce_cart_totals_before_order_total'); ?>

    <div class="order-summary-total order-total">
        <span><?php esc_html_e('Shuma', 'base-theme'); ?></span>
        <span><?php echo wp_kses_post(WC()->cart->get_total()); ?></span>
    </div>

    <?php do_action('woocommerce_cart_totals_after_order_total'); ?>

    <div class="wc-proceed-to-checkout">
        <?php do_action('woocommerce_proceed_to_checkout'); ?>
    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>
</div>
