<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart');

if (!WC()->cart->is_empty()) :
?>

<?php do_action('woocommerce_before_mini_cart_contents'); ?>

<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

    if (!$_product || !$_product->exists() || $cart_item['quantity'] === 0 || !apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) continue;

    $product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
    $thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
    $product_price     = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
    $subtotal          = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
    $max_quantity      = $_product->get_max_purchase_quantity();
?>

<div class="mini-cart-item <?php echo esc_attr(apply_filters('woocommerce_mini_cart_item_class', 'mini-cart-item', $cart_item, $cart_item_key)); ?>">
    <!-- Remove -->
    <?php echo apply_filters(
        'woocommerce_cart_item_remove_link',
        sprintf(
            '<a href="%s" class="mini-cart-item-remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
            esc_url(wc_get_cart_remove_url($cart_item_key)),
            esc_attr__('Remove this item', 'base-theme'),
            esc_attr($product_id),
            esc_attr($cart_item_key),
            esc_attr($_product->get_sku())
        ),
        $cart_item_key
    ); ?>

    <!-- Image -->
    <?php if ($product_permalink) : ?>
        <a href="<?php echo esc_url($product_permalink); ?>" class="shrink-0">
            <img class="mini-cart-item-img" src="<?php echo esc_url(wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail')); ?>"
                 alt="<?php echo esc_attr($_product->get_name()); ?>">
        </a>
    <?php else : ?>
        <img class="mini-cart-item-img" src="<?php echo esc_url(wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail')); ?>"
             alt="<?php echo esc_attr($_product->get_name()); ?>">
    <?php endif; ?>

    <!-- Info -->
    <div class="mini-cart-item-info flex-1 min-w-0">
        <?php if ($product_permalink) : ?>
            <a href="<?php echo esc_url($product_permalink); ?>" class="mini-cart-item-name hover:text-brand transition-colors">
                <?php echo wp_kses_post($product_name); ?>
            </a>
        <?php else : ?>
            <span class="mini-cart-item-name"><?php echo wp_kses_post($product_name); ?></span>
        <?php endif; ?>

        <div class="mini-cart-item-price">
            <?php if ($_product->is_on_sale()) : ?>
                <span class="was"><?php echo wc_price($_product->get_regular_price()); ?></span>
                <span class="now"><?php echo $product_price; ?></span>
            <?php else : ?>
                <?php echo $product_price; ?>
            <?php endif; ?>
        </div>

        <div class="mini-cart-qty">
            <button class="qty-minus" data-key="<?php echo esc_attr($cart_item_key); ?>"
                    aria-label="<?php esc_attr_e('Decrease quantity', 'base-theme'); ?>">&#8722;</button>
            <input type="number" min="1" <?php echo $max_quantity > 0 ? 'max="' . esc_attr($max_quantity) . '"' : ''; ?>
                   value="<?php echo esc_attr($cart_item['quantity']); ?>"
                   class="qty-input" data-key="<?php echo esc_attr($cart_item_key); ?>"
                   aria-label="<?php esc_attr_e('Quantity', 'base-theme'); ?>">
            <button class="qty-plus" data-key="<?php echo esc_attr($cart_item_key); ?>"
                    aria-label="<?php esc_attr_e('Increase quantity', 'base-theme'); ?>">&#43;</button>
        </div>
    </div>

    <span class="mini-cart-item-subtotal"><?php echo $subtotal; ?></span>
</div>

<?php endforeach; ?>

<?php do_action('woocommerce_after_mini_cart_contents'); ?>

<?php else : ?>

<p class="text-sm text-muted text-center py-8"><?php esc_html_e('Shporta juaj është bosh.', 'base-theme'); ?></p>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>
