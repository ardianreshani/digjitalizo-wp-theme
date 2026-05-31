<?php
/**
 * Shared product card for WooCommerce loops and homepage sliders.
 *
 * @package digjitalizo-wp-theme
 */

defined('ABSPATH') || exit;

global $product;

if (empty($product) || !($product instanceof WC_Product) || !$product->is_visible()) {
    return;
}

$context = isset($emsaks_card_context) ? (string) $emsaks_card_context : 'loop';
$classes = ['product-loop-card'];

if ($context === 'slider') {
    $classes[] = 'swiper-slide';
}

$short_description = trim((string) $product->get_short_description());
$show_description_overlay = function_exists('emsaks_product_has_brand')
    && emsaks_product_has_brand($product, 'eagle')
    && trim(wp_strip_all_tags($short_description)) !== '';

if ($show_description_overlay) {
    $classes[] = 'product-loop-card--has-overlay';
}

$regular_price = $product->is_type('variable')
    ? (float) $product->get_variation_regular_price('min', true)
    : (float) $product->get_regular_price();
$sale_price = $product->is_type('variable')
    ? (float) $product->get_variation_sale_price('min', true)
    : (float) $product->get_sale_price();
$discount = ($product->is_on_sale() && $regular_price > 0 && $sale_price > 0)
    ? (int) round((($regular_price - $sale_price) / $regular_price) * 100)
    : 0;

$has_risi_tag = false;
$product_tags = get_the_terms($product->get_id(), 'product_tag');

if (!empty($product_tags) && !is_wp_error($product_tags)) {
    foreach ($product_tags as $product_tag) {
        if ($product_tag->slug === 'risi' || strtolower($product_tag->name) === 'risi') {
            $has_risi_tag = true;
            break;
        }
    }
}

$is_in_stock = $product->is_in_stock();

$button_classes = ['product-loop-card-button'];
if ($product->supports('ajax_add_to_cart') && $product->is_purchasable() && $is_in_stock) {
    $button_classes[] = 'ajax_add_to_cart';
    $button_classes[] = 'add_to_cart_button';
} else {
    $button_classes[] = 'add_to_cart_button';
}

static $inquiry_form_id = null;
if ($inquiry_form_id === null) {
    $inquiry_form_id = function_exists('get_field') ? (int) get_field('woo_inquiry_form', 'option') : 0;
}
?>

<li <?php wc_product_class($classes, $product); ?>>
    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-loop-card-media">
        <?php if ($discount > 0) : ?>
            <span class="product-loop-card-badge product-loop-card-badge-sale">-<?php echo esc_html($discount); ?>%</span>
        <?php elseif ($has_risi_tag) : ?>
            <span class="product-loop-card-badge product-loop-card-badge-new"><?php esc_html_e('Risi', 'base-theme'); ?></span>
        <?php endif; ?>

        <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail')); ?>

        <?php if ($show_description_overlay) : ?>
            <span class="product-loop-card-overlay" aria-hidden="true">
                <span class="product-loop-card-overlay-text">
                    <?php echo wp_kses_post(wpautop($short_description)); ?>
                </span>
            </span>
        <?php endif; ?>
    </a>

    <div class="product-loop-card-content">
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-loop-card-title">
            <?php echo esc_html($product->get_name()); ?>
        </a>

        <div class="product-loop-card-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
    </div>

    <?php if (!$is_in_stock && $inquiry_form_id) : ?>
        <a href="<?php echo esc_url($product->get_permalink()); ?>"
           class="product-loop-card-button product-loop-card-inquiry">
            <?php esc_html_e('Porosit', 'base-theme'); ?>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
        </a>
    <?php elseif ($is_in_stock) : ?>
        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
           data-quantity="1"
           data-product_id="<?php echo esc_attr($product->get_id()); ?>"
           data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
           aria-label="<?php echo esc_attr($product->add_to_cart_description()); ?>"
           rel="nofollow"
           class="<?php echo esc_attr(implode(' ', $button_classes)); ?>">
            <?php esc_html_e('Blej tani', 'base-theme'); ?>
            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.89413 1.08333C6.19747 0.444167 6.8518 0 7.60797 0H11.9413C12.6975 0 13.3507 0.444167 13.6551 1.08333C14.395 1.08983 14.9725 1.12342 15.4881 1.32492C16.1037 1.56571 16.6391 1.97493 17.033 2.50575C17.4305 3.04092 17.618 3.72667 17.8736 4.67025L18.6775 7.61908L18.9808 8.53017L19.0068 8.56267C19.9829 9.81283 19.5181 11.6718 18.5886 15.3888C17.9971 17.7537 17.7025 18.9356 16.8206 19.6246C15.9388 20.3125 14.72 20.3125 12.2825 20.3125H7.26672C4.82922 20.3125 3.61047 20.3125 2.72863 19.6246C1.8468 18.9356 1.55105 17.7537 0.960633 15.3888C0.0311327 11.6718 -0.433618 9.81283 0.542466 8.56267L0.568466 8.53017L0.871799 7.61908L1.67563 4.67025C1.93238 3.72667 2.1198 3.03983 2.5163 2.50467C2.91034 1.97425 3.44569 1.56541 4.06113 1.32492C4.5768 1.12342 5.15313 1.08875 5.89413 1.08333ZM5.8963 2.71158C5.17913 2.71917 4.89097 2.74625 4.65263 2.83942C4.32109 2.96907 4.03276 3.18945 3.82063 3.47533C3.62997 3.73208 3.5173 4.09067 3.20313 5.24658L2.58563 7.50967C3.69063 7.3125 5.2008 7.3125 7.26563 7.3125H12.2825C14.3485 7.3125 15.8575 7.3125 16.9625 7.5075L16.3461 5.24442C16.032 4.0885 15.9193 3.72992 15.7286 3.47317C15.5165 3.18728 15.2282 2.9669 14.8966 2.83725C14.6583 2.74408 14.3701 2.717 13.653 2.70942C13.4991 3.03317 13.2567 3.30668 12.9537 3.49823C12.6508 3.68979 12.2997 3.79153 11.9413 3.79167H7.60797C7.24965 3.79163 6.89868 3.69004 6.59573 3.49869C6.29279 3.30734 6.05027 3.03513 5.8963 2.71158Z" fill="#F9F9F9"/>
            </svg>
        </a>
    <?php endif; ?>
</li>
