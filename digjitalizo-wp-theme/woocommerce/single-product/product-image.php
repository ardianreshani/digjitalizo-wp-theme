<?php
defined('ABSPATH') || exit;

global $product;

/* ── Fallback to WC default if custom gallery is disabled ─────────────────── */
$raw_enabled     = function_exists('get_field') ? get_field('woo_gallery_enabled', 'option') : null;
$gallery_enabled = ($raw_enabled === null || $raw_enabled === '' || (bool) $raw_enabled);

if (!$gallery_enabled) {
    if (!function_exists('wc_get_gallery_image_html')) return;
    $columns        = apply_filters('woocommerce_product_thumbnails_columns', 4);
    $thumb_id       = $product->get_image_id();
    $wrapper_classes = apply_filters('woocommerce_single_product_image_gallery_classes', [
        'woocommerce-product-gallery',
        'woocommerce-product-gallery--' . ($thumb_id ? 'with-images' : 'without-images'),
        'woocommerce-product-gallery--columns-' . absint($columns),
        'images',
    ]);
    echo '<div class="' . esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))) . '" data-columns="' . esc_attr($columns) . '" style="opacity:0;transition:opacity .25s ease-in-out;">';
    echo '<div class="woocommerce-product-gallery__wrapper">';
    if ($thumb_id) echo wc_get_gallery_image_html($thumb_id, true); // phpcs:ignore
    do_action('woocommerce_product_thumbnails');
    echo '</div></div>';
    return;
}

/* ── Build image list ─────────────────────────────────────────────────────── */
$thumb_pos   = function_exists('get_field') ? (get_field('woo_gallery_thumb_pos', 'option') ?: 'bottom') : 'bottom';
$raw_lightbox_thumbs = function_exists('get_field') ? get_field('woo_gallery_lightbox_thumbnails', 'option') : null;
$lightbox_thumbs_enabled = ($raw_lightbox_thumbs === null || $raw_lightbox_thumbs === '' || (bool) $raw_lightbox_thumbs);
$main_id     = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$all_ids     = $main_id ? array_merge([$main_id], $gallery_ids) : $gallery_ids;

$images = [];
foreach ($all_ids as $id) {
    $full      = wp_get_attachment_image_src($id, 'woocommerce_single');
    $thumb     = wp_get_attachment_image_src($id, 'woocommerce_thumbnail');
    $srcset    = wp_get_attachment_image_srcset($id, 'woocommerce_single');
    $sizes     = wp_get_attachment_image_sizes($id, 'woocommerce_single');
    $alt       = trim(wp_strip_all_tags(get_post_meta($id, '_wp_attachment_image_alt', true)));
    if (!$full) continue;
    $images[] = compact('id', 'full', 'thumb', 'srcset', 'sizes', 'alt');
}

if (empty($images)) {
    /* No images — render placeholder */
    echo '<div class="pga pga--placeholder"><img src="' . esc_url(wc_placeholder_img_src('woocommerce_single')) . '" alt="' . esc_attr__('No image', 'woocommerce') . '"></div>';
    return;
}

$has_thumbs = count($images) > 1;
?>

<div class="pga<?php echo $has_thumbs ? ' pga--has-thumbs' : ''; ?>"
     data-thumbs="<?php echo esc_attr($thumb_pos); ?>"
     data-lightbox-thumbnails="<?php echo $lightbox_thumbs_enabled ? '1' : '0'; ?>"
     data-original-src="<?php echo esc_url($images[0]['full'][0]); ?>"
     data-original-srcset="<?php echo esc_attr($images[0]['srcset'] ?: ''); ?>"
     data-original-sizes="<?php echo esc_attr($images[0]['sizes'] ?: ''); ?>"
     data-original-alt="<?php echo esc_attr($images[0]['alt']); ?>">

    <!-- Main image -->
    <div class="pga__main">
        <img class="pga__main-img"
             src="<?php echo esc_url($images[0]['full'][0]); ?>"
             <?php if ($images[0]['srcset']) : ?>
             srcset="<?php echo esc_attr($images[0]['srcset']); ?>"
             sizes="<?php echo esc_attr($images[0]['sizes']); ?>"
             <?php endif; ?>
             alt="<?php echo esc_attr($images[0]['alt']); ?>"
             loading="eager">

        <?php if ($has_thumbs) : ?>
            <button type="button" class="pga__nav pga__nav--prev" aria-label="<?php esc_attr_e('Previous product image', 'base-theme'); ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 5 8 12l7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <button type="button" class="pga__nav pga__nav--next" aria-label="<?php esc_attr_e('Next product image', 'base-theme'); ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        <?php endif; ?>
    </div>

    <?php if ($has_thumbs) : ?>
    <!-- Thumbnail strip -->
    <div class="pga__thumbs" role="list">
        <?php foreach ($images as $i => $img) : ?>
        <button type="button"
                class="pga__thumb<?php echo $i === 0 ? ' is-active' : ''; ?>"
                role="listitem"
                data-full="<?php echo esc_url($img['full'][0]); ?>"
                data-srcset="<?php echo esc_attr($img['srcset'] ?: ''); ?>"
                data-sizes="<?php echo esc_attr($img['sizes'] ?: ''); ?>"
                data-alt="<?php echo esc_attr($img['alt']); ?>">
            <img src="<?php echo esc_url($img['thumb'][0]); ?>"
                 alt="<?php echo esc_attr($img['alt']); ?>"
                 loading="lazy"
                 width="<?php echo esc_attr($img['thumb'][1]); ?>"
                 height="<?php echo esc_attr($img['thumb'][2]); ?>">
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
