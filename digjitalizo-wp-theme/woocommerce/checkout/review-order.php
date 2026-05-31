<?php
/**
 * Checkout order review.
 *
 * Keeps WooCommerce's review-order template as the AJAX-refreshable surface,
 * while rendering the custom EMSA order summary design.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;

$shipping_packages = [];

if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) {
    $shipping_packages = WC()->shipping()->get_packages();
}
?>

<div class="woocommerce-checkout-review-order-table order-summary sticky top-24">
    <h3 class="order-summary-title"><?php esc_html_e('Totali i porosisë:', 'base-theme'); ?></h3>

    <div class="mb-3">
        <div class="flex items-center justify-between text-xs font-semibold text-muted uppercase tracking-wide pb-2 border-b border-[#f0f0f0]">
            <span><?php esc_html_e('Produktet', 'base-theme'); ?></span>
            <span><?php esc_html_e('Çmimi total', 'base-theme'); ?></span>
        </div>

        <?php
        do_action('woocommerce_review_order_before_cart_contents');

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

            if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0 || !apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                continue;
            }

            $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
            $subtotal     = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
        ?>
            <div class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'order-summary-product cart_item', $cart_item, $cart_item_key)); ?>">
                <img src="<?php echo esc_url(wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail')); ?>"
                     alt="<?php echo esc_attr($_product->get_name()); ?>">

                <div class="order-summary-product-info">
                    <div class="order-summary-product-name"><?php echo wp_kses_post($product_name); ?></div>
                    <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                </div>

                <div class="order-summary-product-meta">
                    <?php
                    echo apply_filters(
                        'woocommerce_checkout_cart_item_quantity',
                        sprintf(esc_html__('%1$s x %2$s', 'base-theme'), esc_html($cart_item['quantity']), wp_kses_post(WC()->cart->get_product_price($_product))),
                        $cart_item,
                        $cart_item_key
                    );
                    ?>
                </div>

                <div class="order-summary-product-price">
                    <?php if ($_product->is_on_sale()) : ?>
                        <span class="was"><?php echo wc_price((float) $_product->get_regular_price() * $cart_item['quantity']); ?></span>
                    <?php endif; ?>
                    <?php echo wp_kses_post($subtotal); ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php do_action('woocommerce_review_order_after_cart_contents'); ?>
    </div>

    <div class="order-summary-row border-t border-[#e0e0e0] pt-3">
        <span><?php esc_html_e('Nëntotali', 'base-theme'); ?></span>
        <span><?php wc_cart_totals_subtotal_html(); ?></span>
    </div>

    <?php if (wc_coupons_enabled()) : ?>
        <div class="checkout-coupon-proxy flex gap-2 py-3">
            <input type="text" id="checkout_coupon_proxy"
                   class="flex-1 border border-[#e0e0e0] rounded px-3 py-2 text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand"
                   placeholder="<?php esc_attr_e('Shkruaj Kodin për të përfituar Zbritjen', 'base-theme'); ?>">
            <button type="button" id="checkout_coupon_proxy_apply" class="btn shrink-0 py-2">
                <?php esc_html_e('Apliko', 'base-theme'); ?>
            </button>
        </div>
    <?php endif; ?>

    <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
        <div class="order-summary-row discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
            <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
            <span><?php wc_cart_totals_coupon_html($coupon); ?></span>
        </div>
    <?php endforeach; ?>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
        <?php do_action('woocommerce_review_order_before_shipping'); ?>

        <?php
        $chosen_methods = WC()->session->get('chosen_shipping_methods', []);

        foreach ($shipping_packages as $i => $package) :
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

        <?php do_action('woocommerce_review_order_after_shipping'); ?>
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

    <?php do_action('woocommerce_review_order_before_order_total'); ?>

    <div class="order-summary-total order-total">
        <span><?php esc_html_e('Total', 'woocommerce'); ?></span>
        <span><?php echo wp_kses_post(WC()->cart->get_total()); ?></span>
    </div>

    <?php do_action('woocommerce_review_order_after_order_total'); ?>
</div>
