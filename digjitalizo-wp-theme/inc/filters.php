<?php
defined('ABSPATH') || exit;

// ── Category visibility helper ────────────────────────────────────────────────
function emsaks_hide_empty_categories(): bool {
    $show = function_exists('get_field') ? get_field('woo_show_empty_categories', 'option') : false;
    return !$show;
}

// ── Sale products archive ────────────────────────────────────────────────────
function emsaks_is_sale_products_request(): bool {
    return isset($_GET['on_sale'])
        && sanitize_text_field(wp_unslash((string) $_GET['on_sale'])) === '1';
}

function emsaks_get_sale_product_ids(): array {
    if (!function_exists('wc_get_product_ids_on_sale')) return [];

    return array_values(array_unique(array_map('intval', wc_get_product_ids_on_sale())));
}

function emsaks_has_sale_products(): bool {
    return !empty(emsaks_get_sale_product_ids());
}

function emsaks_get_sale_products_url(): string {
    if (!function_exists('wc_get_page_permalink')) return home_url('/');

    return add_query_arg('on_sale', '1', wc_get_page_permalink('shop'));
}

function emsaks_preserve_sale_products_url($url): string {
    if (is_wp_error($url)) return '';

    return emsaks_is_sale_products_request() ? add_query_arg('on_sale', '1', (string) $url) : (string) $url;
}

// ── Price range filter from URL params ────────────────────────────────────────
// WC handles attribute filters natively via ?filter_[attr]=value1,value2.
// Price is not covered natively without the widget, so we add it here.
add_action('woocommerce_product_query', function (WP_Query $q) {
    if (!$q->is_main_query()) return;

    $is_brand_query = $q->is_tax('product_brand')
        || $q->get('taxonomy') === 'product_brand'
        || (string) $q->get('product_brand') !== '';

    if ($is_brand_query && !empty($_GET['brand_cat'])) {
        $category_slug = sanitize_title(wp_unslash($_GET['brand_cat']));

        if ($category_slug) {
            $tax_query   = (array) $q->get('tax_query');
            $tax_query[] = [
                'taxonomy'         => 'product_cat',
                'field'            => 'slug',
                'terms'            => $category_slug,
                'include_children' => true,
            ];
            $q->set('tax_query', $tax_query);
        }
    }

    if (emsaks_is_sale_products_request()) {
        $sale_ids     = emsaks_get_sale_product_ids();
        $existing_ids = array_map('intval', (array) $q->get('post__in'));

        $q->set('post__in', $existing_ids ? array_intersect($existing_ids, $sale_ids) : ($sale_ids ?: [0]));
    }

    $min = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
    $max = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';

    if ($min === '' && $max === '') return;

    $meta = (array) $q->get('meta_query');

    if ($min !== '' && $max !== '') {
        $meta[] = ['key' => '_price', 'value' => [$min, $max], 'compare' => 'BETWEEN', 'type' => 'NUMERIC'];
    } elseif ($min !== '') {
        $meta[] = ['key' => '_price', 'value' => $min, 'compare' => '>=', 'type' => 'NUMERIC'];
    } else {
        $meta[] = ['key' => '_price', 'value' => $max, 'compare' => '<=', 'type' => 'NUMERIC'];
    }

    $q->set('meta_query', $meta);
});

// ── Base archive URL (no filter params) ───────────────────────────────────────
function emsaks_filter_base_url(): string {
    if (is_product_category()) return emsaks_preserve_sale_products_url(get_term_link(get_queried_object()));
    if (is_tax('product_brand')) return get_term_link(get_queried_object());
    if (function_exists('is_shop') && is_shop()) return emsaks_preserve_sale_products_url(wc_get_page_permalink('shop'));
    return home_url('/');
}

add_filter('wp_nav_menu_items', function (string $items, $args): string {
    if (($args->theme_location ?? '') !== 'primary' || !emsaks_has_sale_products()) {
        return $items;
    }

    $sale_item = sprintf(
        '<li class="menu-item nav-sale-item%s"><a class="nav-sale" href="%s"><svg class="nav-sale-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="10" fill="currentColor"/><path d="M7.15 12.85l5.7-5.7M7.7 7.35h.01M12.3 12.65h.01" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><circle cx="7.7" cy="7.35" r="1" stroke="#fff" stroke-width="1.2"/><circle cx="12.3" cy="12.65" r="1" stroke="#fff" stroke-width="1.2"/></svg><span>%s</span></a></li>',
        emsaks_is_sale_products_request() ? ' current-menu-item' : '',
        esc_url(emsaks_get_sale_products_url()),
        esc_html__('Artikujt Me ZBRITJE', 'base-theme')
    );

    $updated_items = preg_replace('/(?=<li[^>]*\\bmenu-item-type-post_type\\b)/', $sale_item, $items, 1, $replace_count);

    return $replace_count ? $updated_items : $items . $sale_item;
}, 10, 2);

// ── Brand archive category context ────────────────────────────────────────────
function emsaks_get_brand_archive_category(): ?WP_Term {
    if (!is_tax('product_brand') || empty($_GET['brand_cat'])) return null;

    $term = get_term_by('slug', sanitize_title(wp_unslash($_GET['brand_cat'])), 'product_cat');

    return $term instanceof WP_Term ? $term : null;
}

function emsaks_get_brand_archive_category_url(WP_Term $brand, ?WP_Term $category = null): string {
    $url = get_term_link($brand);

    if (is_wp_error($url)) return '';

    return $category ? add_query_arg('brand_cat', $category->slug, $url) : $url;
}

function emsaks_get_brand_archive_product_ids(): array {
    if (!is_tax('product_brand')) return [];

    $brand = get_queried_object();
    if (!$brand instanceof WP_Term) return [];

    $ids = get_objects_in_term((int) $brand->term_id, 'product_brand');

    return is_wp_error($ids) ? [] : array_map('intval', $ids);
}

function emsaks_get_product_category_object_ids(int $term_id): array {
    $children = get_term_children($term_id, 'product_cat');
    $term_ids = array_merge([$term_id], is_wp_error($children) ? [] : $children);
    $ids      = get_objects_in_term(array_map('intval', $term_ids), 'product_cat');

    return is_wp_error($ids) ? [] : array_map('intval', $ids);
}

function emsaks_get_root_product_category_id(?WP_Term $term): int {
    if (!$term instanceof WP_Term || $term->taxonomy !== 'product_cat') return 0;

    $ancestor_ids = get_ancestors($term->term_id, 'product_cat', 'taxonomy');

    return $ancestor_ids ? (int) end($ancestor_ids) : (int) $term->term_id;
}

function emsaks_get_filter_category_tree(?WP_Term $brand = null): array {
    $all_terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'orderby'    => 'name',
        'number'     => 0,
    ]);

    if (empty($all_terms) || is_wp_error($all_terms)) return ['map' => [], 'counts' => []];

    $scoped_product_ids = $brand instanceof WP_Term ? emsaks_get_brand_archive_product_ids() : [];
    $sale_product_ids   = emsaks_is_sale_products_request() ? emsaks_get_sale_product_ids() : [];
    $map                = [];
    $counts             = [];

    if ($sale_product_ids) {
        $scoped_product_ids = $scoped_product_ids
            ? array_intersect($scoped_product_ids, $sale_product_ids)
            : $sale_product_ids;
    }

    foreach ($all_terms as $term) {
        if ($brand instanceof WP_Term || emsaks_is_sale_products_request()) {
            $category_product_ids = emsaks_get_product_category_object_ids((int) $term->term_id);
            $count = count(array_intersect($scoped_product_ids, $category_product_ids));
        } else {
            $count = (int) $term->count;
        }

        if ($count < 1) continue;

        $counts[$term->term_id] = $count;
        $map[$term->parent][]   = $term;
    }

    return ['map' => $map, 'counts' => $counts];
}

// ── Active attribute filters from URL ─────────────────────────────────────────
function emsaks_get_active_attribute_filters(): array {
    $active = [];
    if (!function_exists('wc_get_attribute_taxonomies')) return $active;

    foreach (wc_get_attribute_taxonomies() as $attr) {
        $key = 'filter_' . $attr->attribute_name;
        if (!empty($_GET[$key])) {
            $values = array_values(array_filter(array_map(
                'sanitize_text_field',
                explode(',', wp_unslash($_GET[$key]))
            )));
            if ($values) $active[$attr->attribute_name] = $values;
        }
    }
    return $active;
}

// ── Count total active filters (for badge) ────────────────────────────────────
function emsaks_count_active_filters(): int {
    $count = 0;
    foreach (emsaks_get_active_attribute_filters() as $values) {
        $count += count($values);
    }
    if (isset($_GET['min_price']) || isset($_GET['max_price'])) $count++;
    return $count;
}

// ── Price range for the slider (scoped to current archive) ────────────────────
function emsaks_get_archive_price_range(): array {
    global $wpdb;

    $args = [
        'post_type'              => 'product',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ];

    if (is_product_category()) {
        $args['tax_query'] = [[
            'taxonomy'         => 'product_cat',
            'field'            => 'term_id',
            'terms'            => get_queried_object_id(),
            'include_children' => true,
        ]];
    } elseif (is_tax()) {
        $t = get_queried_object();
        $args['tax_query'] = [[
            'taxonomy' => $t->taxonomy,
            'field'    => 'term_id',
            'terms'    => $t->term_id,
        ]];

        $brand_category = emsaks_get_brand_archive_category();
        if ($brand_category) {
            $args['tax_query'][] = [
                'taxonomy'         => 'product_cat',
                'field'            => 'term_id',
                'terms'            => $brand_category->term_id,
                'include_children' => true,
            ];
        }
    }

    if (emsaks_is_sale_products_request()) {
        $args['post__in'] = emsaks_get_sale_product_ids() ?: [0];
    }

    $ids = get_posts($args);
    if (empty($ids)) return ['min' => 0, 'max' => 1000];

    $ids_sql = implode(',', array_map('intval', $ids));
    $row = $wpdb->get_row(
        "SELECT FLOOR(MIN(CAST(meta_value AS DECIMAL(10,2)))) AS mn,
                CEIL(MAX(CAST(meta_value AS DECIMAL(10,2))))  AS mx
         FROM {$wpdb->postmeta}
         WHERE post_id IN ({$ids_sql})
           AND meta_key = '_price'
           AND meta_value != ''"
    );

    $min = ($row && $row->mn !== null) ? max(0, (int) $row->mn) : 0;
    $max = ($row && $row->mx !== null) ? max($min + 1, (int) $row->mx) : 1000;

    return ['min' => $min, 'max' => $max];
}

// ── Build attribute filter URL (toggle one value in the comma list) ────────────
function emsaks_build_attr_filter_url(string $attr_name, string $slug, array $active_values): string {
    $key = 'filter_' . $attr_name;

    $new_values = in_array($slug, $active_values, true)
        ? array_values(array_diff($active_values, [$slug]))
        : array_values(array_merge($active_values, [$slug]));

    $params = [];
    foreach ($_GET as $k => $v) {
        $params[sanitize_key($k)] = sanitize_text_field(wp_unslash((string) $v));
    }
    unset($params['paged'], $params['page']);

    if (empty($new_values)) {
        unset($params[$key]);
    } else {
        $params[$key] = implode(',', $new_values);
    }

    $base = strtok(get_pagenum_link(1, false), '?');
    return $params ? $base . '?' . http_build_query($params) : $base;
}

// ── Render: category links (SEO-friendly, always visible) ─────────────────────
function emsaks_render_filter_categories(): void {
    if (!is_woocommerce()) return;

    $brand        = is_tax('product_brand') ? get_queried_object() : null;
    $current_term = is_product_category() ? get_queried_object() : emsaks_get_brand_archive_category();
    $tree         = emsaks_get_filter_category_tree($brand instanceof WP_Term ? $brand : null);

    if (empty($tree['map'][0])) return;

    emsaks_render_filter_category_list(
        $tree['map'],
        $tree['counts'],
        $current_term instanceof WP_Term ? (int) $current_term->term_id : 0,
        $brand instanceof WP_Term ? $brand : null
    );
}

function emsaks_render_filter_category_items(array $map, array $counts, int $current_id, ?WP_Term $brand, int $parent_id = 0): void {
    $active_path = $current_id ? array_merge([$current_id], get_ancestors($current_id, 'product_cat', 'taxonomy')) : [];

    foreach ($map[$parent_id] ?? [] as $term) :
        $children     = $map[$term->term_id] ?? [];
        $is_active    = (int) $term->term_id === $current_id;
        $is_open      = in_array((int) $term->term_id, array_map('intval', $active_path), true);
        $category_url = $brand
            ? emsaks_get_brand_archive_category_url($brand, $term)
            : emsaks_preserve_sale_products_url(get_term_link($term));
        ?>
        <li class="filter-cat-item<?php echo $is_active ? ' is-active' : ''; ?><?php echo $is_open ? ' is-in-active-path' : ''; ?>">
            <div class="filter-cat-row">
                <a href="<?php echo esc_url($category_url); ?>" class="filter-cat-link">
                    <span class="filter-cat-name"><?php echo esc_html($term->name); ?></span>
                    <span class="filter-cat-count"><?php echo esc_html($counts[$term->term_id] ?? $term->count); ?></span>
                </a>
                <?php if ($children) : ?>
                    <button type="button"
                            class="filter-cat-expand"
                            aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Shfaq nënkategoritë e %s', 'base-theme'), $term->name)); ?>">
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path class="filter-cat-expand-plus" d="M3 8h10M8 3v10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path class="filter-cat-expand-minus" d="M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
            <?php if ($children) : ?>
                <ul class="filter-cat-children<?php echo $is_open ? ' is-open' : ''; ?>">
                    <?php emsaks_render_filter_category_items($map, $counts, $current_id, $brand, (int) $term->term_id); ?>
                </ul>
            <?php endif; ?>
        </li>
        <?php
    endforeach;
}

function emsaks_render_filter_category_list(array $map, array $counts, int $current_id = 0, ?WP_Term $brand = null): void {
    ?>
    <div class="filter-section">
        <button class="filter-section-toggle" aria-expanded="true" aria-controls="filter-cats">
            <span><?php esc_html_e('Kategorija', 'base-theme'); ?></span>
            <svg class="filter-toggle-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <div class="filter-collapsible is-open" id="filter-cats">
            <ul class="filter-cat-list">
                <?php emsaks_render_filter_category_items($map, $counts, $current_id, $brand); ?>
            </ul>
        </div>
    </div>
    <?php
}

// ── Render: dynamic attribute filter sections ──────────────────────────────────
function emsaks_render_attribute_filters(array $active_filters): void {
    if (!function_exists('wc_get_attribute_taxonomies')) return;

    foreach (wc_get_attribute_taxonomies() as $attr) {
        if (!emsaks_is_attribute_filterable($attr->attribute_name)) continue;

        $taxonomy = wc_attribute_taxonomy_name($attr->attribute_name);
        if (!taxonomy_exists($taxonomy)) continue;

        $term_args = ['taxonomy' => $taxonomy, 'hide_empty' => true, 'orderby' => 'name'];

        if (is_product_category()) {
            $ids = emsaks_get_product_category_object_ids((int) get_queried_object_id());
            $term_args['object_ids'] = $ids ?: [0];
        } elseif (is_tax('product_brand')) {
            $ids = emsaks_get_brand_archive_product_ids();
            $brand_category = emsaks_get_brand_archive_category();

            if ($brand_category) {
                $category_ids = emsaks_get_product_category_object_ids((int) $brand_category->term_id);
                $ids = array_intersect($ids, $category_ids);
            }

            $term_args['object_ids'] = $ids ?: [0];
        }

        if (emsaks_is_sale_products_request()) {
            $sale_ids = emsaks_get_sale_product_ids();
            $ids      = isset($term_args['object_ids'])
                ? array_intersect($term_args['object_ids'], $sale_ids)
                : $sale_ids;

            $term_args['object_ids'] = $ids ?: [0];
        }

        $terms = get_terms($term_args);
        if (empty($terms) || is_wp_error($terms)) continue;

        $active_values = $active_filters[$attr->attribute_name] ?? [];
        $is_open       = !empty($active_values);
        $section_id    = 'filter-attr-' . esc_attr($attr->attribute_name);
        ?>
        <div class="filter-section">
            <button class="filter-section-toggle"
                    aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                    aria-controls="<?php echo esc_attr($section_id); ?>">
                <span><?php echo esc_html($attr->attribute_label); ?></span>
                <svg class="filter-toggle-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <ul class="filter-collapsible filter-option-list <?php echo $is_open ? 'is-open' : ''; ?>"
                id="<?php echo esc_attr($section_id); ?>"
                role="group"
                aria-label="<?php echo esc_attr($attr->attribute_label); ?>">
                <?php foreach ($terms as $term) :
                    $is_checked = in_array($term->slug, $active_values, true);
                    $href       = emsaks_build_attr_filter_url($attr->attribute_name, $term->slug, $active_values);
                    ?>
                    <li class="filter-option">
                        <a href="<?php echo esc_url($href); ?>"
                           class="filter-option-link <?php echo $is_checked ? 'is-checked' : ''; ?>"
                           role="checkbox"
                           aria-checked="<?php echo $is_checked ? 'true' : 'false'; ?>">
                            <span class="filter-checkbox <?php echo $is_checked ? 'is-checked' : ''; ?>" aria-hidden="true">
                                <?php if ($is_checked) : ?>
                                    <svg width="10" height="8" viewBox="0 0 10 8" fill="none">
                                        <path d="M1 4l2.5 3L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                            </span>
                            <span class="filter-option-name"><?php echo esc_html($term->name); ?></span>
                            <span class="filter-option-count">(<?php echo esc_html($term->count); ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}

// ── Render: price slider ───────────────────────────────────────────────────────
function emsaks_render_price_filter(): void {
    $range   = emsaks_get_archive_price_range();
    $abs_min = $range['min'];
    $abs_max = $range['max'];

    if ($abs_min >= $abs_max) return;

    $cur_min   = isset($_GET['min_price']) ? max($abs_min, (int) $_GET['min_price']) : $abs_min;
    $cur_max   = isset($_GET['max_price']) ? min($abs_max, (int) $_GET['max_price']) : $abs_max;
    $is_active = isset($_GET['min_price']) || isset($_GET['max_price']);
    ?>
    <div class="filter-section">
        <button class="filter-section-toggle"
                aria-expanded="<?php echo $is_active ? 'true' : 'false'; ?>"
                aria-controls="filter-price">
            <span><?php esc_html_e('Çmimi', 'base-theme'); ?></span>
            <svg class="filter-toggle-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <div class="filter-collapsible filter-price-wrap <?php echo $is_active ? 'is-open' : ''; ?>"
             id="filter-price"
             data-abs-min="<?php echo esc_attr($abs_min); ?>"
             data-abs-max="<?php echo esc_attr($abs_max); ?>">

            <div class="price-slider">
                <div class="price-slider-track">
                    <div class="price-slider-fill"></div>
                </div>
                <input type="range" class="price-range price-range-min"
                       min="<?php echo esc_attr($abs_min); ?>" max="<?php echo esc_attr($abs_max); ?>"
                       value="<?php echo esc_attr($cur_min); ?>" step="1"
                       aria-label="<?php esc_attr_e('Çmimi minimal', 'base-theme'); ?>">
                <input type="range" class="price-range price-range-max"
                       min="<?php echo esc_attr($abs_min); ?>" max="<?php echo esc_attr($abs_max); ?>"
                       value="<?php echo esc_attr($cur_max); ?>" step="1"
                       aria-label="<?php esc_attr_e('Çmimi maximal', 'base-theme'); ?>">
            </div>

            <div class="price-inputs">
                <div class="price-input-wrap">
                    <input type="number" class="price-input price-input-min"
                           min="<?php echo esc_attr($abs_min); ?>" max="<?php echo esc_attr($abs_max); ?>"
                           value="<?php echo esc_attr($cur_min); ?>">
                    <span class="price-currency">€</span>
                </div>
                <span class="price-sep">—</span>
                <div class="price-input-wrap">
                    <input type="number" class="price-input price-input-max"
                           min="<?php echo esc_attr($abs_min); ?>" max="<?php echo esc_attr($abs_max); ?>"
                           value="<?php echo esc_attr($cur_max); ?>">
                    <span class="price-currency">€</span>
                </div>
            </div>

            <button class="price-apply-btn" type="button">
                <?php esc_html_e('Apliko', 'base-theme'); ?>
            </button>
        </div>
    </div>
    <?php
}

// ── Render: Kategorit mega-panel category drill-down ──────────────────────────
function emsaks_render_kategorit_categories(): void {
    $all_terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => emsaks_hide_empty_categories(),
        'orderby'    => 'name',
        'number'     => 0,
    ]);

    if (empty($all_terms) || is_wp_error($all_terms)) return;

    $current_term = is_product_category() ? get_queried_object() : emsaks_get_brand_archive_category();
    $current_id   = $current_term instanceof WP_Term ? (int) $current_term->term_id : 0;
    $active_path  = $current_id ? array_map('intval', array_merge([$current_id], get_ancestors($current_id, 'product_cat', 'taxonomy'))) : [];
    $get_state    = static function (WP_Term $term) use ($current_id, $active_path): string {
        if ((int) $term->term_id === $current_id) {
            return ' is-active';
        }

        return in_array((int) $term->term_id, $active_path, true) ? ' is-in-active-path' : '';
    };

    // Build parent → children map
    $map = [];
    foreach ($all_terms as $term) {
        $map[$term->parent][] = $term;
    }

    $chevron_right = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M5 3l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $chevron_left  = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M9 3L5 7l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $arrow_right   = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    // Root level
    echo '<div class="cat-level is-active" id="cat-level-root">';
    echo '<ul class="cat-list" role="list">';
    foreach ($map[0] ?? [] as $term) {
        $has_children = !empty($map[$term->term_id]);
        echo '<li class="cat-list-item">';
        if ($has_children) {
            printf(
                '<button type="button" class="cat-item-btn cat-item-drill%s" data-target="cat-level-%s"><span>%s</span>%s</button>',
                esc_attr($get_state($term)),
                esc_attr($term->term_id),
                esc_html($term->name),
                $chevron_right
            );
        } else {
            printf(
                '<a class="cat-item-btn%s" href="%s"><span>%s</span></a>',
                esc_attr($get_state($term)),
                esc_url(get_term_link($term)),
                esc_html($term->name)
            );
        }
        echo '</li>';
    }
    echo '</ul></div>';

    // Sub-levels for every term that has children
    foreach ($all_terms as $term) {
        if (empty($map[$term->term_id])) continue;
        $back_target = $term->parent ? 'cat-level-' . $term->parent : 'cat-level-root';

        printf(
            '<div class="cat-level" id="cat-level-%s">',
            esc_attr($term->term_id)
        );
        echo '<div class="cat-level-header">';
        printf(
            '<button type="button" class="cat-back-btn" data-target="%s">%s %s</button>',
            esc_attr($back_target),
            $chevron_left,
            esc_html__('Mbrapa', 'base-theme')
        );
        printf(
            '<a class="cat-goto-link%s" href="%s">%s %s</a>',
            esc_attr($get_state($term)),
            esc_url(get_term_link($term)),
            esc_html($term->name),
            $arrow_right
        );
        echo '</div>';
        echo '<ul class="cat-list" role="list">';
        foreach ($map[$term->term_id] as $child) {
            $has_gc = !empty($map[$child->term_id]);
            echo '<li class="cat-list-item">';
            if ($has_gc) {
                printf(
                    '<button type="button" class="cat-item-btn cat-item-drill%s" data-target="cat-level-%s"><span>%s</span>%s</button>',
                    esc_attr($get_state($child)),
                    esc_attr($child->term_id),
                    esc_html($child->name),
                    $chevron_right
                );
            } else {
                printf(
                    '<a class="cat-item-btn%s" href="%s"><span>%s</span></a>',
                    esc_attr($get_state($child)),
                    esc_url(get_term_link($child)),
                    esc_html($child->name)
                );
            }
            echo '</li>';
        }
        echo '</ul></div>';
    }
}

// ── Render: horizontal category tabs above the product grid ───────────────────
function emsaks_render_archive_tabs(WP_Term $term): void {
    if ($term->taxonomy === 'product_brand') {
        $tree = emsaks_get_filter_category_tree($term);
        $tabs = $tree['map'][0] ?? [];

        if (count($tabs) <= 1) return;

        $active_category = emsaks_get_brand_archive_category();
        $active_root_id   = emsaks_get_root_product_category_id($active_category);
        ?>
        <nav class="archive-tabs archive-tabs--brand" aria-label="<?php esc_attr_e('Kategoritë', 'base-theme'); ?>">
            <?php foreach ($tabs as $tab) : ?>
                <a href="<?php echo esc_url(emsaks_get_brand_archive_category_url($term, $tab)); ?>"
                   class="archive-tab <?php echo $active_root_id && (int) $tab->term_id === $active_root_id ? 'is-active' : ''; ?>">
                    <?php echo esc_html($tab->name); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
        return;
    }

    if ($term->parent) {
        // Child category: tabs = siblings, first tab = link to parent ("all")
        $parent = get_term($term->parent, 'product_cat');
        $tabs   = get_terms(['taxonomy' => 'product_cat', 'parent' => $term->parent, 'hide_empty' => emsaks_hide_empty_categories(), 'orderby' => 'name']);

        $all_url  = (!is_wp_error($parent)) ? emsaks_preserve_sale_products_url(get_term_link($parent)) : '';
        $all_name = (!is_wp_error($parent)) ? $parent->name : '';
    } else {
        // Top-level category: tabs = children, first tab = self ("all")
        $tabs     = get_terms(['taxonomy' => 'product_cat', 'parent' => $term->term_id, 'hide_empty' => emsaks_hide_empty_categories(), 'orderby' => 'name']);
        $all_url  = emsaks_preserve_sale_products_url(get_term_link($term));
        $all_name = $term->name;
    }

    if (empty($tabs) || is_wp_error($tabs)) return;
    ?>
    <nav class="archive-tabs archive-tabs--brand" aria-label="<?php esc_attr_e('Kategoritë', 'base-theme'); ?>">
        <?php if ($all_name && $all_url) : ?>
            <a href="<?php echo esc_url($all_url); ?>"
               class="archive-tab <?php echo !$term->parent ? 'is-active' : ''; ?>">
                <?php echo esc_html($all_name); ?>
            </a>
        <?php endif; ?>
        <?php foreach ($tabs as $tab) : ?>
            <a href="<?php echo esc_url(emsaks_preserve_sale_products_url(get_term_link($tab))); ?>"
               class="archive-tab <?php echo (int) $tab->term_id === (int) $term->term_id ? 'is-active' : ''; ?>">
                <?php echo esc_html($tab->name); ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <?php
}
