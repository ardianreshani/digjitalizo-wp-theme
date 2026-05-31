<?php
/**
 * Archive product template — custom layout with filter sidebar.
 *
 * @package digjitalizo-wp-theme
 */
defined('ABSPATH') || exit;
get_header();

$is_brand_archive = is_tax('product_brand');
$queried = (is_product_category() || $is_brand_archive) ? get_queried_object() : null;
$brand_category = $is_brand_archive && function_exists('emsaks_get_brand_archive_category')
    ? emsaks_get_brand_archive_category()
    : null;
?>

<div class="archive-page">
    <div class="container">

        <?php emsaks_render_breadcrumbs(); ?>

        <?php if (!$is_brand_archive) : ?>
            <div class="archive-header">
                <h1>
                    <?php if (function_exists('emsaks_is_sale_products_request') && emsaks_is_sale_products_request()) : ?>
                        <?php esc_html_e('Artikujt Me ZBRITJE', 'base-theme'); ?>
                    <?php else : ?>
                        <?php woocommerce_page_title(); ?>
                    <?php endif; ?>
                </h1>
                <?php woocommerce_taxonomy_archive_description(); ?>
            </div>
        <?php endif; ?>

        <?php if ($queried) emsaks_render_archive_tabs($queried); ?>

        <div class="archive-layout">

            <!-- ── Filter sidebar (desktop inline / mobile drawer) ── -->
            <aside class="archive-sidebar" id="archiveSidebar"
                   aria-label="<?php esc_attr_e('Filtrot e produkteve', 'base-theme'); ?>">

                <!-- Header shown only on mobile (inside the drawer) -->
                <div class="sidebar-mobile-head">
                    <span class="sidebar-mobile-title"><?php esc_html_e('Filtro Produktet', 'base-theme'); ?></span>
                    <button class="sidebar-close-btn" id="sidebarClose"
                            aria-label="<?php esc_attr_e('Mbyll filtrot', 'base-theme'); ?>">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <div class="sidebar-inner">
                    <?php get_template_part('woocommerce/loop/filter-sidebar'); ?>
                </div>
            </aside>

            <!-- ── Products column ── -->
            <div class="archive-products-col">

                <?php if ($is_brand_archive) : ?>
                    <header class="archive-products-heading">
                        <h1><?php echo esc_html($brand_category ? $brand_category->name : $queried->name); ?></h1>
                    </header>
                <?php endif; ?>

                <?php woocommerce_output_all_notices(); ?>

                <!-- Toolbar: Filtro button (mobile) + sort -->
                <div class="archive-toolbar">
                    <button class="filtro-btn" id="filtroBtn"
                            aria-expanded="false" aria-controls="archiveSidebar">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M2 4h12M4 8h8M6 12h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <?php esc_html_e('Filtro', 'base-theme'); ?>
                        <?php $fc = emsaks_count_active_filters(); if ($fc > 0) : ?>
                            <span class="filtro-badge" aria-label="<?php printf(esc_attr__('%d filtra aktiv', 'base-theme'), $fc); ?>">
                                <?php echo esc_html($fc); ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <div class="archive-sort-right">
                        <?php woocommerce_result_count(); ?>
                        <?php woocommerce_catalog_ordering(); ?>
                    </div>
                </div>

                <!-- Product loop -->
                <?php if (woocommerce_product_loop()) : ?>
                    <?php woocommerce_product_loop_start(); ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <?php wc_get_template_part('content', 'product'); ?>
                        <?php endwhile; ?>
                    <?php woocommerce_product_loop_end(); ?>
                    <?php woocommerce_pagination(); ?>
                <?php else : ?>
                    <?php do_action('woocommerce_no_products_found'); ?>
                <?php endif; ?>

            </div><!-- .archive-products-col -->
        </div><!-- .archive-layout -->
    </div><!-- .container -->
</div><!-- .archive-page -->

<!-- Mobile filter overlay -->
<div class="filter-overlay" id="filterOverlay" aria-hidden="true"></div>

<?php get_footer(); ?>
