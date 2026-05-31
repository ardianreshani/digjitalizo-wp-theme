<?php
/**
 * Custom single product layout.
 *
 * @package digjitalizo-wp-theme
 */

defined('ABSPATH') || exit;

global $product;

do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    return;
}

$product_id = $product->get_id();
$sku = $product->get_sku();
$brand_terms = taxonomy_exists('product_brand') ? get_the_terms($product_id, 'product_brand') : [];
$brand_names = (!empty($brand_terms) && !is_wp_error($brand_terms)) ? wp_list_pluck($brand_terms, 'name') : [];
$phone = function_exists('get_field') ? (get_field('company_phone', 'option') ?: '+383 48 400 096') : '+383 48 400 096';
$email = function_exists('get_field') ? (get_field('company_email', 'option') ?: 'info@emsaks.com') : 'info@emsaks.com';
$upsell_ids = $product->get_upsell_ids();
$upsells = $upsell_ids ? wc_get_products([
    'include' => $upsell_ids,
    'limit'   => 5,
    'status'  => 'publish',
]) : [];

$related_products = emsaks_get_single_product_related_products($product, 5);
?>

<article id="product-<?php the_ID(); ?>" <?php wc_product_class('single-product-detail', $product); ?>>
    <div class="single-product-top">
        <div class="single-product-gallery-col">
            <?php wc_get_template('single-product/product-image.php'); ?>
        </div>

        <div class="single-product-summary">
            <h1 class="single-product-title"><?php the_title(); ?></h1>

            <?php if ($sku || $brand_names) : ?>
                <div class="single-product-meta-panel">
                    <?php if ($sku) : ?>
                        <div><?php esc_html_e('Kodi:', 'base-theme'); ?> <strong><?php echo esc_html($sku); ?></strong></div>
                    <?php endif; ?>
                    <?php if ($brand_names) : ?>
                        <div><?php esc_html_e('Brand:', 'base-theme'); ?> <strong><?php echo esc_html(implode(', ', $brand_names)); ?></strong></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($product->get_short_description()) : ?>
                <div class="single-product-short-description">
                    <?php echo wp_kses_post(apply_filters('woocommerce_short_description', $product->get_short_description())); ?>
                </div>
            <?php endif; ?>

            <div class="single-product-purchase-panel">
                <?php echo wp_kses_post(wc_get_stock_html($product)); ?>

                <div class="single-product-price">
                    <?php woocommerce_template_single_price(); ?>
                </div>

                <?php woocommerce_template_single_add_to_cart(); ?>

                <?php
                $_sp_inquiry_form = function_exists('get_field') ? (int) get_field('woo_inquiry_form', 'option') : 0;
                if (!$product->is_in_stock() && $_sp_inquiry_form) : ?>
                    <button type="button"
                            class="single-product-inquiry-btn"
                            data-inquiry-trigger
                            data-product-name="<?php echo esc_attr($product->get_name()); ?>"
                            data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>
                        <?php esc_html_e('Porosit', 'base-theme'); ?>
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($phone || $email) : ?>
                <div class="single-product-help">
                    <span class="single-product-help-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4.5 6.75h15v10.5h-15V6.75Z" stroke="currentColor" stroke-width="1.7"/>
                            <path d="m5 7 7 6 7-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>
                        <strong><?php esc_html_e('Keni pyetje shtesë për këtë produkt?', 'base-theme'); ?></strong>
                        <span>
                            <?php if ($phone) : ?>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
                            <?php endif; ?>
                            <?php if ($phone && $email) : ?>
                                <span>/</span>
                            <?php endif; ?>
                            <?php if ($email) : ?>
                                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                            <?php endif; ?>
                        </span>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="single-product-tabs-wrap">
        <?php woocommerce_output_product_data_tabs(); ?>
    </div>

    <?php emsaks_render_product_extra_sections($product_id); ?>

    <?php emsaks_render_single_product_section(__('Produkte të sugjeruara', 'base-theme'), $upsells, 'single-product-upsells'); ?>
    <?php emsaks_render_single_product_section(__('Produkte të ngjashme', 'base-theme'), $related_products, 'single-product-related'); ?>
</article>

<?php do_action('woocommerce_after_single_product'); ?>
