<?php
/**
 * Additional information tab with an optional product PDF download.
 *
 * @package Emsaks
 */

defined('ABSPATH') || exit;

global $product;

$heading = apply_filters(
    'woocommerce_product_additional_information_heading',
    __('Additional information', 'woocommerce')
);
$pdf_url = is_a($product, 'WC_Product')
    ? emsaks_get_product_pdf_url($product->get_id())
    : '';
?>

<?php if ($heading || $pdf_url) : ?>
    <div class="product-additional-information-heading">
        <?php if ($heading) : ?>
            <h2><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>

        <?php if ($pdf_url) : ?>
            <a
                class="product-pdf-button"
                href="<?php echo esc_url($pdf_url); ?>"
                download
                aria-label="<?php echo esc_attr(sprintf(__('Download PDF for %s', 'base-theme'), $product->get_name())); ?>">
                <?php esc_html_e('Shkarko', 'base-theme'); ?>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php do_action('woocommerce_product_additional_information', $product); ?>
