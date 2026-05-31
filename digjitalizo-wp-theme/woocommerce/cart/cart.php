<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');

$shipping_packages = [];

if (WC()->cart->needs_shipping()) {
    WC()->cart->calculate_shipping();
    $shipping_packages = WC()->shipping()->get_packages();
}
?>

<div class="cart-layout">
    <!-- ── Left: product table ──────────────────────────────────────────── -->
    <div>
        <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">

            <?php do_action('woocommerce_before_cart_table'); ?>

            <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th class="w-8"></th>
                            <th><?php esc_html_e('Produkti', 'base-theme'); ?></th>
                            <th class="text-right"><?php esc_html_e('Mimi për njësi', 'base-theme'); ?></th>
                            <th class="text-center"><?php esc_html_e('Sasia', 'base-theme'); ?></th>
                            <th class="text-right"><?php esc_html_e('Çmimi total', 'base-theme'); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php do_action('woocommerce_before_cart_contents'); ?>

                    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                        if (!$_product || !$_product->exists() || $cart_item['quantity'] === 0 || !apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) continue;

                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        $product_price     = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                        $product_subtotal  = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                    ?>
                        <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                            <!-- Remove -->
                            <td class="text-center">
                                <?php echo apply_filters('woocommerce_cart_item_remove_link',
                                    sprintf('<a href="%s" class="cart-remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s">&times;</a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_html__('Remove this item', 'base-theme'),
                                        esc_attr($product_id),
                                        esc_attr($cart_item_key)
                                    ),
                                $cart_item_key); ?>
                            </td>

                            <!-- Product -->
                            <td>
                                <div class="cart-product-cell">
                                    <?php if ($product_permalink) : ?>
                                        <a href="<?php echo esc_url($product_permalink); ?>">
                                            <img class="cart-product-img"
                                                 src="<?php echo esc_url(wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail')); ?>"
                                                 alt="<?php echo esc_attr($_product->get_name()); ?>">
                                        </a>
                                        <a href="<?php echo esc_url($product_permalink); ?>" class="cart-product-name">
                                            <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                                        </a>
                                    <?php else : ?>
                                        <img class="cart-product-img"
                                             src="<?php echo esc_url(wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail')); ?>"
                                             alt="<?php echo esc_attr($_product->get_name()); ?>">
                                        <span class="cart-product-name">
                                            <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Unit price -->
                            <td class="text-right">
                                <?php if ($_product->is_on_sale()) : ?>
                                    <span class="line-through text-[#aaa] text-xs block">
                                        <?php echo wc_price($_product->get_regular_price()); ?>
                                    </span>
                                    <span class="font-semibold text-sale">
                                        <?php echo wc_price($_product->get_sale_price()); ?>
                                    </span>
                                <?php else : ?>
                                    <span class="font-semibold"><?php echo $product_price; ?></span>
                                <?php endif; ?>
                            </td>

                            <!-- Quantity -->
                            <td class="text-center">
                                <div class="qty-stepper mx-auto w-fit">
                                    <button type="button" class="qty-minus">&#8722;</button>
                                    <input type="number"
                                           id="quantity_<?php echo esc_attr($cart_item_key); ?>"
                                           name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]"
                                           value="<?php echo esc_attr($cart_item['quantity']); ?>"
                                           min="0"
                                           max="<?php echo esc_attr(0 < $_product->get_max_purchase_quantity() ? $_product->get_max_purchase_quantity() : ''); ?>"
                                           step="1">
                                    <button type="button" class="qty-plus">&#43;</button>
                                </div>
                            </td>

                            <!-- Line total -->
                            <td class="cart-line-total"><?php echo $product_subtotal; ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php do_action('woocommerce_after_cart_contents'); ?>
                    </tbody>
                </table>

                <!-- Coupon + actions -->
                <div class="cart-coupon">
                    <?php if (wc_coupons_enabled()) : ?>
                        <input type="text" name="coupon_code" id="coupon_code" class="input-text" value=""
                               placeholder="<?php esc_attr_e('Shkruaj Kodin për të përfituar Zbritjen', 'base-theme'); ?>">
                        <button type="submit" class="btn" name="apply_coupon" value="<?php esc_attr_e('Apliko', 'base-theme'); ?>">
                            <?php esc_html_e('Apliko', 'base-theme'); ?>
                        </button>
                    <?php endif; ?>
                    <?php do_action('woocommerce_cart_actions'); ?>
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                    <input type="hidden" name="update_cart" value="1">
                </div>

                <?php if (WC()->cart->get_applied_coupons()) : ?>
                    <div class="cart-coupon-msg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        <?php esc_html_e('Kuponi u shtua me sukses!', 'base-theme'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php do_action('woocommerce_after_cart_table'); ?>

        </form>
    </div>

    <!-- ── Right: order summary ──────────────────────────────────────────── -->
    <div>
        <?php woocommerce_cart_totals(); ?>
    </div>

</div><!-- .cart-layout -->

<?php
$cart_product_ids = array_map(
    static function ($item) {
        return $item['product_id'];
    },
    WC()->cart->get_cart()
);

$related_products = wc_get_products([
    'limit'   => 3,
    'status'  => 'publish',
    'exclude' => $cart_product_ids,
    'orderby' => 'rand',
]);
?>

<?php if ($related_products) : ?>
<section class="cart-related">
    <h2><?php esc_html_e('Produkte të ngjashme', 'base-theme'); ?></h2>
    <ul class="cart-related-grid products">
        <?php foreach ($related_products as $related_product) : ?>
            <?php emsaks_render_product_loop_card($related_product); ?>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>

<?php do_action('woocommerce_after_cart'); ?>
