<?php
/**
 * Filter sidebar — categories (SEO links) + attribute checkboxes + price slider.
 *
 * @package digjitalizo-wp-theme
 */
defined('ABSPATH') || exit;

$active_attrs = emsaks_get_active_attribute_filters();
$has_active   = !empty($active_attrs) || isset($_GET['min_price']) || isset($_GET['max_price']);
?>

<?php if ($has_active) : ?>
    <div class="filter-active-bar">
        <span class="filter-active-label">
            <?php
            $fc = emsaks_count_active_filters();
            printf(
                esc_html(_n('%d filtër aktiv', '%d filtra aktiv', $fc, 'base-theme')),
                $fc
            );
            ?>
        </span>
        <a href="<?php echo esc_url(emsaks_filter_base_url()); ?>" class="filter-clear-all">
            <?php esc_html_e('Spastro të gjitha', 'base-theme'); ?>
        </a>
    </div>
<?php endif; ?>

<?php emsaks_render_filter_categories(); ?>

<?php if (function_exists('wc_get_attribute_taxonomies') && wc_get_attribute_taxonomies()) : ?>
    <p class="filter-section-heading"><?php esc_html_e('Filtro Produktet', 'base-theme'); ?></p>
    <?php emsaks_render_attribute_filters($active_attrs); ?>
<?php endif; ?>

<?php emsaks_render_price_filter(); ?>
