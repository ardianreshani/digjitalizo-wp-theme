<?php

define('THEME_VERSION', '1.0.40');
define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

// ─── ACF Local JSON ─────────────────────────────────────────────────────────
add_filter('acf/settings/save_json', function () {
    return THEME_DIR . '/acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = THEME_DIR . '/acf-json';

    return $paths;
});

// ─── Ensure attachment metadata always has width/height (SVGs have none) ────
// Prevents Rank Math schema warnings: "Undefined array key width/height"
add_filter('wp_get_attachment_metadata', function ($data, $attachment_id) {
    if (!is_array($data)) {
        return $data;
    }
    if (!array_key_exists('width', $data)) {
        $data['width'] = 0;
    }
    if (!array_key_exists('height', $data)) {
        $data['height'] = 0;
    }
    return $data;
}, 10, 2);

// ─── Disable Gutenberg ───────────────────────────────────────────────────────
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);
add_filter('use_widgets_block_editor', '__return_false');

// ─── Theme Setup ──────────────────────────────────────────────────────────────
add_action('after_setup_theme', function () {
    load_theme_textdomain('base-theme', THEME_DIR . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 600,
        'single_image_width'    => 900,
        'product_grid'          => ['default_rows' => 3, 'default_columns' => 3],
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    add_image_size('emsaks-favicon-32', 32, 32, true);
    add_image_size('emsaks-favicon-180', 180, 180, true);
    add_image_size('emsaks-favicon-192', 192, 192, true);
    add_image_size('emsaks-favicon-270', 270, 270, true);
    add_image_size('emsaks-favicon-512', 512, 512, true);

    register_nav_menus([
        'primary'        => __('Primary Menu', 'base-theme'),
        'footer'         => __('Footer Menu', 'base-theme'),
        'footer_account' => __('Footer Account Menu', 'base-theme'),
        'copyright'      => __('Copyright / Footer Bottom', 'base-theme'),
    ]);
});

// ─── Media Uploads ───────────────────────────────────────────────────────────
function emsaks_svg_uploads_enabled() {
    return function_exists('get_field') && (bool) get_field('enable_svg_uploads', 'option');
}

function emsaks_user_can_upload_svg() {
    return emsaks_svg_uploads_enabled() && current_user_can('manage_options');
}

function emsaks_file_is_svg($file) {
    if (!$file || !is_readable($file)) {
        return false;
    }

    $contents = file_get_contents($file, false, null, 0, 1024);

    if ($contents === false) {
        return false;
    }

    $contents = ltrim($contents);

    return strpos($contents, '<svg') === 0 || (strpos($contents, '<?xml') === 0 && strpos($contents, '<svg') !== false);
}

function emsaks_attachment_is_svg($attachment_id) {
    $mime = get_post_mime_type($attachment_id);

    if ($mime === 'image/svg+xml') {
        return true;
    }

    $file = get_attached_file($attachment_id);

    return $file && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'svg';
}

function emsaks_get_svg_dimensions($attachment_id) {
    $file = get_attached_file($attachment_id);

    if (!$file || !is_readable($file)) {
        return [300, 300];
    }

    $contents = file_get_contents($file, false, null, 0, 4096);

    if ($contents === false) {
        return [300, 300];
    }

    if (
        preg_match('/\bwidth=["\']([0-9.]+)(?:px)?["\']/i', $contents, $width_match)
        && preg_match('/\bheight=["\']([0-9.]+)(?:px)?["\']/i', $contents, $height_match)
    ) {
        return [max(1, (int) round((float) $width_match[1])), max(1, (int) round((float) $height_match[1]))];
    }

    if (preg_match('/\bviewBox=["\']\s*[-0-9.]+\s+[-0-9.]+\s+([0-9.]+)\s+([0-9.]+)\s*["\']/i', $contents, $viewbox_match)) {
        return [max(1, (int) round((float) $viewbox_match[1])), max(1, (int) round((float) $viewbox_match[2]))];
    }

    return [300, 300];
}

add_filter('upload_mimes', function ($mimes) {
    if (emsaks_user_can_upload_svg()) {
        $mimes['svg'] = 'image/svg+xml';
    }

    return $mimes;
});

add_filter('wp_handle_upload_prefilter', function ($file) {
    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

    if ($extension !== 'svg') {
        return $file;
    }

    if (!emsaks_user_can_upload_svg()) {
        $file['error'] = __('SVG uploads are disabled.', 'base-theme');
        return $file;
    }

    if (!emsaks_file_is_svg($file['tmp_name'] ?? '')) {
        $file['error'] = __('This file is not a valid SVG.', 'base-theme');
    }

    return $file;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    if (!emsaks_user_can_upload_svg()) {
        return $data;
    }

    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($extension === 'svg' && emsaks_file_is_svg($file)) {
        $data['ext']             = 'svg';
        $data['type']            = 'image/svg+xml';
        $data['proper_filename'] = $filename;
    }

    return $data;
}, 10, 4);

add_filter('wp_get_attachment_image_src', function ($image, $attachment_id) {
    if (!$image || !emsaks_attachment_is_svg($attachment_id)) {
        return $image;
    }

    if (empty($image[1]) || empty($image[2])) {
        [$width, $height] = emsaks_get_svg_dimensions($attachment_id);
        $image[1] = $width;
        $image[2] = $height;
    }

    return $image;
}, 10, 2);

add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment) {
    if (!$attachment || !emsaks_attachment_is_svg($attachment->ID)) {
        return $attr;
    }

    if (empty($attr['width']) || empty($attr['height']) || (int) $attr['width'] <= 0 || (int) $attr['height'] <= 0) {
        [$width, $height] = emsaks_get_svg_dimensions($attachment->ID);
        $attr['width'] = $width;
        $attr['height'] = $height;
    }

    return $attr;
}, 10, 2);

function emsaks_get_favicon_src($favicon, $size = 'full') {
    if (!is_array($favicon)) {
        return '';
    }

    $attachment_id = !empty($favicon['ID']) ? (int) $favicon['ID'] : 0;

    if ($attachment_id) {
        $image = wp_get_attachment_image_src($attachment_id, $size);
        if (!empty($image[0])) {
            return $image[0];
        }
    }

    if ($size !== 'full' && !empty($favicon['sizes'][$size])) {
        return $favicon['sizes'][$size];
    }

    return !empty($favicon['url']) ? $favicon['url'] : '';
}

function emsaks_get_company_logo($variant = 'dark') {
    $fallback = [
        'url' => THEME_URI . '/assets/images/logo.svg',
        'alt' => get_bloginfo('name'),
    ];

    if (function_exists('get_field')) {
        $field = $variant === 'light' ? 'company_logo_light' : 'company_logo_dark';
        $logo = get_field($field, 'option');

        if (is_array($logo) && !empty($logo['url'])) {
            return [
                'url' => $logo['url'],
                'alt' => !empty($logo['alt']) ? $logo['alt'] : $fallback['alt'],
            ];
        }

        if (is_string($logo) && $logo !== '') {
            return [
                'url' => $logo,
                'alt' => $fallback['alt'],
            ];
        }
    }

    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $custom_logo_url = wp_get_attachment_image_url((int) $custom_logo_id, 'full');

        if ($custom_logo_url) {
            return [
                'url' => $custom_logo_url,
                'alt' => get_post_meta((int) $custom_logo_id, '_wp_attachment_image_alt', true) ?: $fallback['alt'],
            ];
        }
    }

    return $fallback;
}

add_action('login_enqueue_scripts', function () {
    $logo = emsaks_get_company_logo('dark');

    if (empty($logo['url'])) {
        return;
    }

    ?>
    <style>
        body.login div#login h1 a {
            background-image: url('<?php echo esc_url($logo['url']); ?>');
            background-position: center;
            background-size: contain;
            height: 96px;
            width: 240px;
            max-width: 100%;
        }
    </style>
    <?php
});

add_filter('login_headerurl', function () {
    return home_url('/');
});

add_filter('login_headertext', function () {
    $logo = emsaks_get_company_logo('dark');

    return !empty($logo['alt']) ? $logo['alt'] : get_bloginfo('name');
});

function emsaks_output_site_icons() {
    if (!function_exists('get_field')) {
        return;
    }

    $favicon = get_field('company_favicon', 'option');
    if (!is_array($favicon) || empty($favicon['url'])) {
        return;
    }

    remove_action('wp_head', 'wp_site_icon', 99);
    remove_action('admin_head', 'wp_site_icon', 99);
    remove_action('login_head', 'wp_site_icon', 99);

    $attachment_id = !empty($favicon['ID']) ? (int) $favicon['ID'] : 0;
    $mime_type = $attachment_id ? get_post_mime_type($attachment_id) : '';
    $mime_type = $mime_type ?: 'image/png';
    $is_svg = $mime_type === 'image/svg+xml';
    $theme_color = get_field('color_primary', 'option') ?: '#165783';
    $site_name = get_bloginfo('name');

    $icon_full = emsaks_get_favicon_src($favicon, 'full');

    if ($is_svg) {
        // SVG favicons must not have a sizes attribute — one tag only
        if ($icon_full) {
            echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($icon_full) . '">' . "\n";
        }
    } else {
        $icon_32 = emsaks_get_favicon_src($favicon, 'emsaks-favicon-32') ?: $icon_full;
        $icon_180 = emsaks_get_favicon_src($favicon, 'emsaks-favicon-180') ?: $icon_full;
        $icon_192 = emsaks_get_favicon_src($favicon, 'emsaks-favicon-192') ?: $icon_full;
        $icon_270 = emsaks_get_favicon_src($favicon, 'emsaks-favicon-270') ?: $icon_full;
        $icon_512 = emsaks_get_favicon_src($favicon, 'emsaks-favicon-512') ?: $icon_full;

        if ($icon_full) {
            echo '<link rel="icon" href="' . esc_url($icon_full) . '" sizes="any">' . "\n";
        }
        if ($icon_32) {
            echo '<link rel="icon" type="' . esc_attr($mime_type) . '" sizes="32x32" href="' . esc_url($icon_32) . '">' . "\n";
            echo '<link rel="shortcut icon" href="' . esc_url($icon_32) . '">' . "\n";
        }
        if ($icon_180) {
            echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url($icon_180) . '">' . "\n";
        }
        if ($icon_192) {
            echo '<link rel="icon" type="' . esc_attr($mime_type) . '" sizes="192x192" href="' . esc_url($icon_192) . '">' . "\n";
        }
        if ($icon_512) {
            echo '<link rel="icon" type="' . esc_attr($mime_type) . '" sizes="512x512" href="' . esc_url($icon_512) . '">' . "\n";
        }
        if ($icon_270) {
            echo '<meta name="msapplication-TileImage" content="' . esc_url($icon_270) . '">' . "\n";
        }
    }

    echo '<link rel="manifest" href="' . esc_url(rest_url('emsaks/v1/site.webmanifest')) . '">' . "\n";
    echo '<meta name="application-name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta name="msapplication-TileColor" content="' . esc_attr($theme_color) . '">' . "\n";
    echo '<meta name="theme-color" content="' . esc_attr($theme_color) . '">' . "\n";
}

add_action('wp_head', 'emsaks_output_site_icons', 4);
add_action('admin_head', 'emsaks_output_site_icons', 4);
add_action('login_head', 'emsaks_output_site_icons', 4);

add_action('rest_api_init', function () {
    register_rest_route('emsaks/v1', '/site.webmanifest', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            $favicon = function_exists('get_field') ? get_field('company_favicon', 'option') : null;
            if (!is_array($favicon) || empty($favicon['url'])) {
                return new WP_Error('emsaks_no_favicon', __('No favicon configured.', 'base-theme'), ['status' => 404]);
            }

            $attachment_id = !empty($favicon['ID']) ? (int) $favicon['ID'] : 0;
            $mime_type = $attachment_id ? get_post_mime_type($attachment_id) : '';
            $mime_type = $mime_type ?: 'image/png';
            $theme_color = get_field('color_primary', 'option') ?: '#165783';
            $icon_192 = emsaks_get_favicon_src($favicon, 'emsaks-favicon-192') ?: emsaks_get_favicon_src($favicon, 'full');
            $icon_512 = emsaks_get_favicon_src($favicon, 'emsaks-favicon-512') ?: emsaks_get_favicon_src($favicon, 'full');

            $icons = [];
            if ($icon_192) {
                $icons[] = [
                    'src'   => esc_url_raw($icon_192),
                    'sizes' => '192x192',
                    'type'  => $mime_type,
                ];
            }
            if ($icon_512) {
                $icons[] = [
                    'src'     => esc_url_raw($icon_512),
                    'sizes'   => '512x512',
                    'type'    => $mime_type,
                    'purpose' => 'any maskable',
                ];
            }

            return new WP_REST_Response([
                'name'             => get_bloginfo('name'),
                'short_name'       => get_bloginfo('name'),
                'icons'            => $icons,
                'theme_color'      => $theme_color,
                'background_color' => $theme_color,
                'display'          => 'standalone',
                'start_url'        => home_url('/'),
            ]);
        },
    ]);
});

// ─── CSS Custom Properties from ACF Global Options ────────────────────────────
add_action('wp_head', function () {
    if (!function_exists('get_field')) return;

    $color_primary   = get_field('color_primary',   'option') ?: '#165783';
    $color_secondary = get_field('color_secondary', 'option') ?: '#004c7a';
    $color_accent    = get_field('color_accent',    'option') ?: '#1face3';
    $color_text      = get_field('color_text',      'option') ?: '#41474f';
    $color_heading   = get_field('color_heading',   'option') ?: '#191c1d';
    $color_muted     = get_field('color_muted',     'option') ?: '#777777';
    $color_btn_bg    = get_field('color_btn_bg',    'option') ?: '#165783';
    $color_btn_text  = get_field('color_btn_text',  'option') ?: '#ffffff';
    $font_heading    = get_field('font_heading',    'option') ?: 'Roboto Slab';
    $font_body       = get_field('font_body',       'option') ?: 'Open Sans';
    $font_size_base  = get_field('font_size_base',  'option') ?: '16px';

    echo "<style>:root{";
    // Theme variables
    echo "--color-primary:{$color_primary};";
    echo "--color-secondary:{$color_secondary};";
    echo "--color-accent:{$color_accent};";
    echo "--color-text:{$color_text};";
    echo "--color-heading:{$color_heading};";
    echo "--color-muted:{$color_muted};";
    echo "--color-btn-bg:{$color_btn_bg};";
    echo "--color-btn-text:{$color_btn_text};";
    echo "--font-heading:'{$font_heading}',sans-serif;";
    echo "--font-body:'{$font_body}',sans-serif;";
    echo "font-size:{$font_size_base};";
    // Override WooCommerce CSS variables so WC elements use brand colors
    echo "--woocommerce:{$color_btn_bg};";
    echo "--wc-primary:{$color_btn_bg};";
    echo "--wc-primary-text:{$color_btn_text};";
    echo "--wc-highlight:{$color_accent};";
    echo "}</style>\n";

    $fonts_url = get_field('google_fonts_url', 'option');
    if ($fonts_url) {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="stylesheet" href="' . esc_url($fonts_url) . '">' . "\n";
    }
}, 5);

// ─── Enqueue Assets ───────────────────────────────────────────────────────────
add_action('wp_enqueue_scripts', function () {
    $deps = class_exists('WooCommerce') ? ['woocommerce-general'] : [];

    wp_enqueue_style('swiper', THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.css', [], '12.1.4');
    wp_enqueue_script('swiper', THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.js', [], '12.1.4', true);

    wp_enqueue_style('theme-main', THEME_URI . '/assets/css/main.css', $deps, THEME_VERSION);

    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-cart-fragments');
    }

    $main_deps = ['jquery', 'swiper'];
    wp_enqueue_script('theme-main', THEME_URI . '/assets/js/main.js', $main_deps, THEME_VERSION, true);

    /* Product gallery + attribute swatches */
    if (is_product() && class_exists('WooCommerce')) {
        $raw             = function_exists('get_field') ? get_field('woo_gallery_enabled', 'option') : null;
        $gallery_enabled = ($raw === null || $raw === '' || (bool) $raw);
        if ($gallery_enabled) {
            wp_enqueue_script(
                'theme-product-gallery',
                THEME_URI . '/assets/js/product-gallery.js',
                ['jquery'],
                THEME_VERSION,
                true
            );
        }
    }

    /* Attribute swatches — loaded on all product pages, ACF option can switch to dropdown */
    if (is_product() && class_exists('WooCommerce')) {
        $attr_display = function_exists('get_field') ? get_field('woo_attr_display', 'option') : '';
        if ($attr_display !== 'dropdown') {
            wp_enqueue_script(
                'theme-attr-swatches',
                THEME_URI . '/assets/js/attr-swatches.js',
                ['jquery', 'wc-add-to-cart-variation'],
                THEME_VERSION,
                true
            );
        }
    }

    if (class_exists('WooCommerce')) {
        wp_localize_script('theme-main', 'themeData', [
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('theme-nonce'),
            'cartNonce'    => wp_create_nonce('emsaks-cart-nonce'),
            'searchNonce'  => wp_create_nonce('emsaks-product-search-nonce'),
            'cartUrl'      => wc_get_cart_url(),
            'checkoutUrl'  => wc_get_checkout_url(),
            'cartBehavior' => function_exists('get_field') ? (get_field('woo_cart_behavior', 'option') ?: 'minicart') : 'minicart',
            'search'       => [
                'loading'     => __('Duke kërkuar...', 'base-theme'),
                'noResults'   => __('Nuk u gjet asnjë produkt.', 'base-theme'),
                'viewAll'     => __('Shiko të gjitha rezultatet', 'base-theme'),
                'error'       => __('Kërkimi nuk është i disponueshëm për momentin.', 'base-theme'),
                'minChars'    => 2,
                'resultsLimit'=> 6,
            ],
        ]);
    }
});

// ─── WooCommerce: Replace Wrappers ────────────────────────────────────────────
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

add_filter('woocommerce_product_single_add_to_cart_text', function () {
    return __('Blej tani', 'base-theme');
});

function emsaks_render_product_loop_card($product, $context = 'loop') {
    if (!function_exists('wc_get_template') || !class_exists('WC_Product') || !($product instanceof WC_Product)) {
        return;
    }

    $previous_product = $GLOBALS['product'] ?? null;
    $previous_post    = $GLOBALS['post'] ?? null;
    $product_id       = $product->get_id();

    $GLOBALS['product'] = $product;
    $GLOBALS['post']    = get_post($product_id);

    if ($GLOBALS['post'] instanceof WP_Post) {
        setup_postdata($GLOBALS['post']);
    }

    wc_get_template('content-product.php', [
        'emsaks_card_context' => $context,
    ]);

    $GLOBALS['product'] = $previous_product;
    $GLOBALS['post']    = $previous_post;

    if ($previous_post instanceof WP_Post) {
        setup_postdata($previous_post);
    } else {
        wp_reset_postdata();
    }
}

function emsaks_product_has_brand($product, $brand_slug = 'eagle') {
    if (!class_exists('WC_Product') || !($product instanceof WC_Product)) {
        return false;
    }

    if (!taxonomy_exists('product_brand')) {
        return false;
    }

    $target = sanitize_title($brand_slug);
    $terms = get_the_terms($product->get_id(), 'product_brand');

    if (empty($terms) || is_wp_error($terms)) {
        return false;
    }

    foreach ($terms as $term) {
        if (sanitize_title($term->slug) === $target || sanitize_title($term->name) === $target) {
            return true;
        }
    }

    return false;
}

function emsaks_get_single_product_related_products($product, $limit = 5) {
    if (!class_exists('WC_Product') || !($product instanceof WC_Product)) {
        return [];
    }

    $product_id = $product->get_id();
    $exclude = [$product_id];
    $products = [];
    $cat_terms = get_the_terms($product_id, 'product_cat');
    $cat_slugs = (!empty($cat_terms) && !is_wp_error($cat_terms)) ? wp_list_pluck($cat_terms, 'slug') : [];

    if ($cat_slugs) {
        $products = wc_get_products([
            'status'  => 'publish',
            'limit'   => $limit,
            'exclude' => $exclude,
            'category'=> $cat_slugs,
            'orderby' => 'rand',
        ]);

        foreach ($products as $related_product) {
            $exclude[] = $related_product->get_id();
        }
    }

    if (count($products) < $limit) {
        $fallback = wc_get_products([
            'status'  => 'publish',
            'limit'   => $limit - count($products),
            'exclude' => array_values(array_unique($exclude)),
            'orderby' => 'rand',
        ]);

        $products = array_merge($products, $fallback);
    }

    return array_slice($products, 0, $limit);
}

function emsaks_render_single_product_section($heading, $products, $class = '') {
    if (empty($products) || !function_exists('emsaks_render_product_loop_card')) {
        return;
    }
    ?>
    <section class="single-product-carousel-section <?php echo esc_attr($class); ?>">
        <div class="single-product-carousel-inner">
            <h2><?php echo esc_html($heading); ?></h2>
            <ul class="single-product-card-grid products">
                <?php foreach ($products as $section_product) : ?>
                    <?php emsaks_render_product_loop_card($section_product); ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php
}

function emsaks_render_product_extra_sections($product_id) {
    $sections = function_exists('get_field') ? get_field('product_extra_sections', $product_id) : [];

    if (empty($sections) || !is_array($sections)) {
        return;
    }

    $visible_index = 0;
    ob_start();
    ?>
    <div class="single-product-extra-sections">
        <?php foreach ($sections as $section) :
            $title = trim((string) ($section['title'] ?? ''));
            $content = trim((string) ($section['content'] ?? ''));
            $image = $section['image'] ?? null;
            $image_url = '';
            $image_alt = $title ?: get_the_title($product_id);

            if (is_array($image) && !empty($image['url'])) {
                $image_url = $image['url'];
                $image_alt = !empty($image['alt']) ? $image['alt'] : $image_alt;
            } elseif (is_numeric($image)) {
                $image_url = wp_get_attachment_image_url((int) $image, 'large');
                $attachment_alt = get_post_meta((int) $image, '_wp_attachment_image_alt', true);
                $image_alt = $attachment_alt ?: $image_alt;
            } elseif (is_string($image) && $image !== '') {
                $image_url = $image;
            }

            if (!$title && !$content && !$image_url) {
                continue;
            }

            $image_position = ($visible_index % 2 === 0) ? 'left' : 'right';
            $visible_index++;
            ?>
            <section class="single-product-extra-row single-product-extra-row--image-<?php echo esc_attr($image_position); ?>">
                <?php if ($image_url) : ?>
                    <figure class="single-product-extra-image">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                    </figure>
                <?php endif; ?>

                <div class="single-product-extra-copy">
                    <?php if ($title) : ?>
                        <h2><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if ($content) : ?>
                        <div class="single-product-extra-content">
                            <?php echo wp_kses_post(apply_filters('the_content', $content)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <?php
    $html = trim(ob_get_clean());

    if ($visible_index > 0) {
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

function emsaks_render_product_masonry_gallery($product_id) {
    $gallery = function_exists('get_field')
        ? get_field('product_masonry_gallery', $product_id)
        : [];

    if (empty($gallery) || !is_array($gallery)) {
        return;
    }

    $images = [];

    foreach ($gallery as $image) {
        $attachment_id = 0;
        $image_url = '';
        $image_alt = get_the_title($product_id);

        if (is_array($image)) {
            $attachment_id = (int) ($image['ID'] ?? $image['id'] ?? 0);
            $image_url = (string) ($image['url'] ?? '');
            $image_alt = trim((string) ($image['alt'] ?? '')) ?: $image_alt;
        } elseif (is_numeric($image)) {
            $attachment_id = (int) $image;
        } elseif (is_string($image)) {
            $image_url = $image;
        }

        if ($attachment_id) {
            $image_url = wp_get_attachment_image_url($attachment_id, 'large');
            $attachment_alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
            $image_alt = $attachment_alt ?: $image_alt;
        }

        if (!$image_url) {
            continue;
        }

        $images[] = [
            'id'  => $attachment_id,
            'url' => $image_url,
            'alt' => $image_alt,
        ];
    }

    if (!$images) {
        return;
    }
    ?>
    <section class="single-product-masonry-gallery" aria-labelledby="product-masonry-gallery-title">
        <h2 id="product-masonry-gallery-title"><?php esc_html_e('Galeria e produktit', 'base-theme'); ?></h2>

        <div class="single-product-masonry-gallery-grid">
            <?php foreach ($images as $image) : ?>
                <figure class="single-product-masonry-gallery-item">
                    <?php if ($image['id']) : ?>
                        <?php
                        echo wp_get_attachment_image(
                            $image['id'],
                            'large',
                            false,
                            [
                                'class'   => 'single-product-masonry-gallery-image',
                                'loading' => 'lazy',
                                'sizes'   => '(min-width: 1024px) 50vw, (min-width: 640px) 100vw, 100vw',
                            ]
                        );
                        ?>
                    <?php else : ?>
                        <img
                            class="single-product-masonry-gallery-image"
                            src="<?php echo esc_url($image['url']); ?>"
                            alt="<?php echo esc_attr($image['alt']); ?>"
                            loading="lazy">
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}

function emsaks_get_product_pdf_url($product_id) {
    if (!function_exists('get_field')) {
        return '';
    }

    $pdf = get_field('product_pdf_file', $product_id);
    $pdf_url = '';

    if (is_array($pdf) && !empty($pdf['url'])) {
        $pdf_url = $pdf['url'];
    } elseif (is_numeric($pdf)) {
        $pdf_url = wp_get_attachment_url((int) $pdf);
    } elseif (is_string($pdf)) {
        $pdf_url = $pdf;
    }

    return $pdf_url ? esc_url_raw($pdf_url) : '';
}

// WooCommerce normally hides this tab when a product has no standard
// attributes. Keep it available when the product PDF is the only content.
add_filter('woocommerce_product_tabs', function ($tabs) {
    global $product;

    if (
        isset($tabs['additional_information'])
        || !is_a($product, 'WC_Product')
        || !emsaks_get_product_pdf_url($product->get_id())
    ) {
        return $tabs;
    }

    $tabs['additional_information'] = [
        'title'    => __('Additional information', 'woocommerce'),
        'priority' => 20,
        'callback' => 'woocommerce_product_additional_information_tab',
    ];

    return $tabs;
}, 20);

/* Dequeue WC default gallery scripts when our custom gallery is active */
add_action('wp_enqueue_scripts', function () {
    if (!is_product() || !class_exists('WooCommerce')) return;
    $raw             = function_exists('get_field') ? get_field('woo_gallery_enabled', 'option') : null;
    $gallery_enabled = ($raw === null || $raw === '' || (bool) $raw);
    if (!$gallery_enabled) return;
    wp_dequeue_script('wc-flexslider');
    wp_dequeue_script('wc-photoswipe');
    wp_dequeue_script('wc-photoswipe-ui-default');
    wp_dequeue_style('wc-photoswipe');
    wp_dequeue_style('wc-photoswipe-default-skin');
}, 99);

// Our thankyou.php renders its own product table; prevent WC duplicating it.
remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);

// Cart/checkout/thankyou templates control their own layout; other WC pages get a standard wrapper.
add_action('woocommerce_before_main_content', function () {
    if (is_cart() || is_checkout()) {
        echo '<div class="container py-8">';
    } elseif (is_product()) {
        echo '<main class="woo-main container">';
    } else {
        echo '<main class="woo-main container py-12">';
    }
});
add_action('woocommerce_after_main_content', function () {
    if (is_cart() || is_checkout()) {
        echo '</div>';
    } else {
        echo '</main>';
    }
});

// ─── Mini Cart AJAX Fragment ───────────────────────────────────────────────────
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();
    $count = WC()->cart->get_cart_contents_count();

    $fragments['.mini-cart-items'] = '<div class="mini-cart-items">' . $mini_cart . '</div>';

    // Badge: only render when there are items so CSS :empty fallback also works
    $fragments['.cart-count'] = $count > 0
        ? '<span class="cart-count absolute -top-2 -right-2">' . esc_html($count) . '</span>'
        : '<span class="cart-count absolute -top-2 -right-2" style="display:none"></span>';

    // Update the subtotal in the drawer footer
    $fragments['.mini-cart-subtotal'] = '<span class="mini-cart-subtotal">' . WC()->cart->get_cart_subtotal() . '</span>';

    return $fragments;
});

add_action('wp_ajax_emsaks_update_cart_item_quantity', 'emsaks_update_cart_item_quantity');
add_action('wp_ajax_nopriv_emsaks_update_cart_item_quantity', 'emsaks_update_cart_item_quantity');

add_action('wp_ajax_emsaks_product_search', 'emsaks_product_search');
add_action('wp_ajax_nopriv_emsaks_product_search', 'emsaks_product_search');

function emsaks_product_search() {
    check_ajax_referer('emsaks-product-search-nonce', 'security');

    if (!class_exists('WooCommerce')) {
        wp_send_json_error(['message' => __('WooCommerce nuk është i disponueshëm.', 'base-theme')], 400);
    }

    $term = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';
    $term = trim($term);

    if (strlen($term) < 2) {
        wp_send_json_success([
            'products' => [],
            'total'    => 0,
        ]);
    }

    $query = new WP_Query([
        'post_type'              => 'product',
        'post_status'            => 'publish',
        'posts_per_page'         => 6,
        's'                      => $term,
        'orderby'                => 'relevance',
        'no_found_rows'          => false,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ]);

    $products = [];

    foreach ($query->posts as $post) {
        $product = wc_get_product($post->ID);

        if (!$product || !$product->is_visible()) {
            continue;
        }

        $image_id = $product->get_image_id();
        $image    = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src('thumbnail');

        $products[] = [
            'title'      => html_entity_decode(get_the_title($post), ENT_QUOTES, get_bloginfo('charset')),
            'url'        => get_permalink($post),
            'image'      => $image,
            'price_html' => wp_kses_post($product->get_price_html()),
        ];
    }

    wp_send_json_success([
        'products' => $products,
        'total'    => (int) $query->found_posts,
        'url'      => add_query_arg([
            's'         => $term,
            'post_type' => 'product',
        ], home_url('/')),
    ]);
}

function emsaks_update_cart_item_quantity() {
    check_ajax_referer('emsaks-cart-nonce', 'security');

    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(['message' => __('Cart is unavailable.', 'base-theme')], 400);
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? wc_clean(wp_unslash($_POST['cart_item_key'])) : '';
    $quantity      = isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : 0;

    if (!$cart_item_key || !isset(WC()->cart->cart_contents[$cart_item_key])) {
        wp_send_json_error(['message' => __('Cart item was not found.', 'base-theme')], 404);
    }

    WC()->cart->set_quantity($cart_item_key, max(0, $quantity), true);
    WC()->cart->calculate_totals();

    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    wp_send_json_success([
        'cart_hash' => WC()->cart->get_cart_hash(),
        'count'     => WC()->cart->get_cart_contents_count(),
        'subtotal'  => WC()->cart->get_cart_subtotal(),
        'fragments' => apply_filters('woocommerce_add_to_cart_fragments', [
            '.mini-cart-items'    => '<div class="mini-cart-items">' . $mini_cart . '</div>',
            '.mini-cart-subtotal' => '<span class="mini-cart-subtotal">' . WC()->cart->get_cart_subtotal() . '</span>',
        ]),
    ]);
}

function emsaks_render_checkout_shipping_methods() {
    if (!function_exists('WC') || !WC()->cart || !WC()->cart->needs_shipping()) {
        return '<div class="shipping-methods"></div>';
    }

    WC()->cart->calculate_shipping();

    $shipping_packages = WC()->shipping()->get_packages();
    $chosen_methods    = WC()->session->get('chosen_shipping_methods', []);
    $has_shipping_rates = false;

    ob_start();
    ?>
    <div class="shipping-methods">
        <?php foreach ($shipping_packages as $i => $package) :
            $chosen = $chosen_methods[$i] ?? '';

            if (empty($chosen) && !empty($package['rates'])) {
                $chosen = array_key_first($package['rates']);
            }

            foreach ($package['rates'] as $rate_id => $rate) :
                $has_shipping_rates = true;
                $shipping_method_id = sprintf('shipping_method_%1$s_%2$s', $i, sanitize_title($rate_id));
        ?>
            <label class="shipping-method" for="<?php echo esc_attr($shipping_method_id); ?>">
                <div class="shipping-method-label">
                    <input type="radio"
                           id="<?php echo esc_attr($shipping_method_id); ?>"
                           class="shipping_method"
                           data-index="<?php echo esc_attr($i); ?>"
                           name="shipping_method[<?php echo esc_attr($i); ?>]"
                           value="<?php echo esc_attr($rate_id); ?>"
                           <?php checked($rate_id, $chosen); ?>>
                    <span><?php echo esc_html($rate->get_label()); ?></span>
                </div>
                <span class="shipping-method-price">
                    <?php echo $rate->cost > 0 ? wp_kses_post(wc_price($rate->cost)) : esc_html__('Pa pagesë shtesë', 'base-theme'); ?>
                </span>
            </label>
        <?php endforeach; endforeach; ?>

        <?php if (!$has_shipping_rates) : ?>
            <div class="shipping-method">
                <div class="shipping-method-label">
                    <span><?php esc_html_e('Nuk ka metoda transporti për këtë adresë.', 'base-theme'); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean();
}

add_filter('woocommerce_update_order_review_fragments', function ($fragments) {
    $fragments['.shipping-methods'] = emsaks_render_checkout_shipping_methods();

    return $fragments;
});

// The custom checkout asks for a single delivery address. WooCommerce still
// validates billing address fields, so mirror that address before validation.
add_filter('woocommerce_checkout_posted_data', function ($data) {
    $address_fields = ['country', 'address_1', 'postcode', 'city', 'state'];
    $base_country   = WC()->countries ? WC()->countries->get_base_country() : '';

    foreach ($address_fields as $field) {
        $billing_key  = 'billing_' . $field;
        $shipping_key = 'shipping_' . $field;
        $posted_value = isset($_POST[$shipping_key]) ? wc_clean(wp_unslash($_POST[$shipping_key])) : '';

        if (empty($data[$shipping_key]) && $posted_value !== '') {
            $data[$shipping_key] = $posted_value;
        }

        if (empty($data[$billing_key]) && $posted_value !== '') {
            $data[$billing_key] = $posted_value;
        } elseif (empty($data[$billing_key]) && !empty($data[$shipping_key])) {
            $data[$billing_key] = $data[$shipping_key];
        }

        if (empty($data[$shipping_key]) && !empty($data[$billing_key])) {
            $data[$shipping_key] = $data[$billing_key];
        }
    }

    foreach (['first_name', 'last_name'] as $field) {
        $billing_key  = 'billing_' . $field;
        $shipping_key = 'shipping_' . $field;

        if (empty($data[$shipping_key]) && !empty($data[$billing_key])) {
            $data[$shipping_key] = $data[$billing_key];
        }

        if (empty($data[$billing_key]) && !empty($data[$shipping_key])) {
            $data[$billing_key] = $data[$shipping_key];
        }
    }

    if (empty($data['billing_country']) && $base_country) {
        $data['billing_country'] = $base_country;
    }

    if (empty($data['shipping_country']) && !empty($data['billing_country'])) {
        $data['shipping_country'] = $data['billing_country'];
    }

    return $data;
});

// WooCommerce's checkout AJAX reads country fields from top-level $_POST
// before calculating shipping/tax fragments. Our checkout shows one delivery
// address, so mirror those posted shipping fields early for live recalculation.
add_action('woocommerce_checkout_update_order_review', function ($post_data) {
    if (!$post_data) {
        return;
    }

    parse_str($post_data, $data);

    $field_map = [
        'country'   => 'shipping_country',
        'state'     => 'shipping_state',
        'postcode'  => 'shipping_postcode',
        'city'      => 'shipping_city',
        'address'   => 'shipping_address_1',
        'address_2' => 'shipping_address_2',
    ];

    foreach ($field_map as $ajax_key => $shipping_key) {
        $billing_key = 'billing_' . ($ajax_key === 'address' ? 'address_1' : $ajax_key);
        $value       = $data[$shipping_key] ?? $data[$billing_key] ?? '';

        if ($value === '') {
            continue;
        }

        $value = wc_clean(wp_unslash($value));

        $_POST[$ajax_key]       = $value;
        $_POST['s_' . $ajax_key] = $value;
    }
}, 5);

add_filter('woocommerce_checkout_fields', function ($fields) {
    foreach (['billing', 'shipping'] as $fieldset) {
        foreach (['state'] as $field) {
            $key = $fieldset . '_' . $field;

            if (isset($fields[$fieldset][$key])) {
                $fields[$fieldset][$key]['required'] = false;
            }
        }
    }

    return $fields;
});

add_action('wp', function () {
    if (!function_exists('WC') || !WC()->customer || (!is_cart() && !is_checkout())) {
        return;
    }

    $base_country = WC()->countries ? WC()->countries->get_base_country() : '';
    $base_state   = WC()->countries ? WC()->countries->get_base_state() : '';

    if ($base_country) {
        if (!WC()->customer->get_billing_country()) {
            WC()->customer->set_billing_country($base_country);
        }

        if (!WC()->customer->get_shipping_country()) {
            WC()->customer->set_shipping_country($base_country);
        }
    }

    if ($base_state) {
        if (!WC()->customer->get_billing_state()) {
            WC()->customer->set_billing_state($base_state);
        }

        if (!WC()->customer->get_shipping_state()) {
            WC()->customer->set_shipping_state($base_state);
        }
    }

    WC()->customer->save();
});

// ─── My Account: Navigation (Albanian labels, no Downloads/Payment Methods) ───
add_filter('woocommerce_account_menu_items', function ($items) {
    unset($items['downloads'], $items['payment-methods']);

    return [
        'dashboard'       => __('Paneli', 'base-theme'),
        'orders'          => __('Porositë', 'base-theme'),
        'edit-address'    => __('Adresa', 'base-theme'),
        'edit-account'    => __('Të dhënat e llogarisë', 'base-theme'),
        'customer-logout' => __('Dilni', 'base-theme'),
    ];
});

// Status badge colours for order list
add_filter('woocommerce_get_order_status_name', function ($status_name, $status) {
    $map = [
        'pending'    => 'Pret pagesën',
        'processing' => 'Në procesim',
        'on-hold'    => 'Pezull',
        'completed'  => 'Përfunduar',
        'cancelled'  => 'Anuluar',
        'refunded'   => 'Rimbursuar',
        'failed'     => 'Dështuar',
    ];
    return $map[$status] ?? $status_name;
}, 10, 2);

// ─── ACF Global Options ───────────────────────────────────────────────────────
add_action('acf/init', function () {
    require_once THEME_DIR . '/inc/options.php';

    if (function_exists('acf_add_local_field_group')) {
        require_once THEME_DIR . '/inc/fields/home.php';
        require_once THEME_DIR . '/inc/fields/contact.php';
        require_once THEME_DIR . '/inc/fields/about.php';
        require_once THEME_DIR . '/inc/fields/product.php';
    }
});

// ─── Reusable Benefits Shortcode ─────────────────────────────────────────────
function emsaks_get_benefits_defaults() {
    return [
        [
            'icon'  => 'delivery',
            'title' => __('Dërgesa të shpejta', 'base-theme'),
            'text'  => __('Kudo në Kosovë', 'base-theme'),
        ],
        [
            'icon'  => 'service',
            'title' => __('Shërbim profesional', 'base-theme'),
            'text'  => __('Këshilla profesionale dhe mbështetje miqësore për çdo blerje.', 'base-theme'),
        ],
        [
            'icon'  => 'secure',
            'title' => __('Blerje e sigurt', 'base-theme'),
            'text'  => __('Paguani sigurt me kartelë ose në momentin e pranimit të produktit', 'base-theme'),
        ],
    ];
}

function emsaks_benefit_icon_svg($icon) {
    $icons = [
        'delivery' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7h10v9H3V7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M13 10h4l3 3v3h-7v-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM17 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" stroke="currentColor" stroke-width="1.8"/><path d="M6 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
        'service'  => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" stroke="currentColor" stroke-width="1.8"/><path d="M4 8h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M4 12h16M10 12v1h4v-1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'secure'   => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 19 6v5.5c0 4.4-2.8 7.2-7 9.5-4.2-2.3-7-5.1-7-9.5V6l7-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m8.8 12.2 2.1 2.1 4.5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ];

    return $icons[$icon] ?? $icons['delivery'];
}

function emsaks_render_benefits_section($args = []) {
    $args = wp_parse_args($args, [
        'echo' => true,
    ]);

    $enabled = function_exists('get_field') ? get_field('benefits_enabled', 'option') : true;

    if ($enabled === false || $enabled === '0') {
        return '';
    }

    $items = function_exists('get_field') ? get_field('benefits_items', 'option') : [];

    if (empty($items) || !is_array($items)) {
        $items = emsaks_get_benefits_defaults();
    }

    $background = function_exists('get_field') ? (get_field('benefits_background', 'option') ?: '#d7ebf6') : '#d7ebf6';
    $icon_color = function_exists('get_field') ? (get_field('benefits_icon_color', 'option') ?: '#165783') : '#165783';
    $text_color = function_exists('get_field') ? (get_field('benefits_text_color', 'option') ?: '#2c2c2c') : '#2c2c2c';

    ob_start();
    ?>
    <section class="reusable-benefits" style="--benefits-bg: <?php echo esc_attr($background); ?>; --benefits-icon: <?php echo esc_attr($icon_color); ?>; --benefits-text: <?php echo esc_attr($text_color); ?>;">
        <div class="container">
            <div class="reusable-benefits-inner">
                <?php foreach ($items as $index => $item) :
                    $title = $item['title'] ?? '';
                    $text = $item['text'] ?? '';
                    $default_icons = ['delivery', 'service', 'secure'];
                    $icon = $item['icon'] ?? ($default_icons[$index] ?? 'delivery');
                    $image = $item['image'] ?? null;
                    $image_url = '';
                    $image_alt = $title;

                    if (is_array($image) && !empty($image['url'])) {
                        $image_url = $image['url'];
                        $image_alt = !empty($image['alt']) ? $image['alt'] : $image_alt;
                    } elseif (is_numeric($image)) {
                        $image_url = wp_get_attachment_image_url((int) $image, 'thumbnail');
                        $image_alt = get_post_meta((int) $image, '_wp_attachment_image_alt', true) ?: $image_alt;
                    } elseif (is_string($image) && $image !== '') {
                        $image_url = $image;
                    }

                    if (!$title && !$text) {
                        continue;
                    }
                    ?>
                    <article class="reusable-benefit-item">
                        <span class="reusable-benefit-icon">
                            <?php if ($image_url) : ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                            <?php else : ?>
                                <?php echo emsaks_benefit_icon_svg($icon); ?>
                            <?php endif; ?>
                        </span>
                        <?php if ($title) : ?>
                            <h3><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>
                        <?php if ($text) : ?>
                            <p><?php echo esc_html($text); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    $output = ob_get_clean();

    if ($args['echo']) {
        echo $output;
    }

    return $output;
}

add_shortcode('emsaks_benefits', function () {
    return emsaks_render_benefits_section(['echo' => false]);
});

// ─── Reusable Brands / Partners Shortcode ────────────────────────────────────
function emsaks_render_brands_section($args = []) {
    $args = wp_parse_args($args, ['echo' => true]);

    $enabled = function_exists('get_field') ? get_field('brands_enabled', 'option') : true;
    if ($enabled === false || $enabled === '0') {
        return '';
    }

    $logos = function_exists('get_field') ? get_field('brands_logos', 'option') : [];
    if (empty($logos) || !is_array($logos)) {
        return '';
    }

    $title = function_exists('get_field') ? (get_field('brands_title', 'option') ?: '') : '';

    ob_start();
    ?>
    <section class="brands-section" data-brands-slider>
        <div class="container">
            <?php if ($title) : ?>
                <p class="brands-section-title"><?php echo esc_html($title); ?></p>
            <?php endif; ?>
            <div class="brands-swiper swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($logos as $item) :
                        $logo = $item['logo'] ?? null;
                        $url  = $item['url']  ?? '';

                        $logo_url = '';
                        $logo_alt = '';

                        if (is_array($logo) && !empty($logo['url'])) {
                            $logo_url = $logo['url'];
                            $logo_alt = $logo['alt'] ?? '';
                        } elseif (is_numeric($logo)) {
                            $logo_url = wp_get_attachment_image_url((int) $logo, 'medium');
                            $logo_alt = get_post_meta((int) $logo, '_wp_attachment_image_alt', true) ?: '';
                        }

                        if (!$logo_url) {
                            continue;
                        }
                        ?>
                        <div class="swiper-slide brands-logo-slide">
                            <?php if ($url) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($logo_alt); ?>">
                                </a>
                            <?php else : ?>
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($logo_alt); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    $output = ob_get_clean();

    if ($args['echo']) {
        echo $output;
    }

    return $output;
}

add_shortcode('emsaks_brands', function () {
    return emsaks_render_brands_section(['echo' => false]);
});

// ─── Breadcrumbs ─────────────────────────────────────────────────────────────
function emsaks_render_breadcrumbs(array $fallback_crumbs = []) {
    if (function_exists('rank_math_the_breadcrumbs')) {
        ob_start();
        rank_math_the_breadcrumbs();
        $rank_math_breadcrumbs = trim((string) ob_get_clean());

        if ($rank_math_breadcrumbs !== '') {
            echo '<div class="site-breadcrumb site-breadcrumb--rank-math">';
            echo $rank_math_breadcrumbs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '</div>';
            return;
        }
    }

    if (function_exists('is_woocommerce') && is_woocommerce() && function_exists('woocommerce_breadcrumb')) {
        woocommerce_breadcrumb([
            'delimiter'   => '<span class="delimiter" aria-hidden="true">/</span>',
            'wrap_before' => '<nav class="site-breadcrumb woocommerce-breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'base-theme') . '">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => __('Ballina', 'base-theme'),
        ]);
        return;
    }

    $crumbs = array_merge(
        [
            [
                'label' => __('Ballina', 'base-theme'),
                'url'   => home_url('/'),
            ],
        ],
        $fallback_crumbs
    );

    if (count($crumbs) < 2) {
        return;
    }
    ?>
    <nav class="site-breadcrumb" aria-label="<?php echo esc_attr__('Breadcrumb', 'base-theme'); ?>">
        <?php foreach ($crumbs as $index => $crumb) : ?>
            <?php if ($index > 0) : ?>
                <span class="delimiter" aria-hidden="true">/</span>
            <?php endif; ?>

            <?php if (!empty($crumb['url'])) : ?>
                <a href="<?php echo esc_url($crumb['url']); ?>"><?php echo esc_html($crumb['label']); ?></a>
            <?php else : ?>
                <span><?php echo esc_html($crumb['label']); ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <?php
}

// ─── Blog Helpers ────────────────────────────────────────────────────────────
function emsaks_get_reading_time($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $content = get_post_field('post_content', $post_id);
    preg_match_all('/[\p{L}\p{N}]+/u', wp_strip_all_tags(strip_shortcodes($content)), $words);
    $word_count = count($words[0]);
    $minutes = max(1, (int) ceil($word_count / 200));

    return sprintf(_n('%d min lexim', '%d min lexim', $minutes, 'base-theme'), $minutes);
}

function emsaks_get_post_category_label($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $categories = get_the_category($post_id);

    if (empty($categories) || is_wp_error($categories)) {
        return __('Lajme', 'base-theme');
    }

    return $categories[0]->name;
}

function emsaks_render_blog_card($post_id = null) {
    $post = get_post($post_id ?: get_the_ID());

    if (!$post) {
        return;
    }

    $post_id = $post->ID;
    $image = get_the_post_thumbnail_url($post_id, 'large');
    $label = emsaks_get_post_category_label($post_id);
    $excerpt = get_the_excerpt($post_id);
    $excerpt = $excerpt ? wp_trim_words($excerpt, 18) : wp_trim_words(wp_strip_all_tags($post->post_content), 18);
    ?>
    <article class="blog-card">
        <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="blog-card-image">
            <?php if ($image) : ?>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
            <?php endif; ?>
            <span class="blog-card-badge"><?php echo esc_html($label); ?></span>
        </a>
        <div class="blog-card-body">
            <div class="blog-card-meta">
                <span><?php echo esc_html(get_the_date('j F Y', $post_id)); ?></span>
                <span aria-hidden="true">•</span>
                <span><?php echo esc_html(emsaks_get_reading_time($post_id)); ?></span>
            </div>
            <h3 class="blog-card-title">
                <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a>
            </h3>
            <?php if ($excerpt) : ?>
                <p class="blog-card-excerpt"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="blog-card-link">
                <?php esc_html_e('Lexo më shumë', 'base-theme'); ?> <span aria-hidden="true">→</span>
            </a>
        </div>
    </article>
    <?php
}

function emsaks_render_blog_archive_page($title = '', $description = '') {
    ?>
    <main class="blog-archive-page">
        <div class="container">
            <?php emsaks_render_breadcrumbs([
                [
                    'label' => $title ?: __('Blog', 'base-theme'),
                ],
            ]); ?>

            <header class="blog-archive-header">
                <h1><?php echo esc_html($title ?: __('Këshilla & lajme nga EMSA', 'base-theme')); ?></h1>
                <?php if ($description) : ?>
                    <p><?php echo wp_kses_post($description); ?></p>
                <?php endif; ?>
            </header>

            <?php if (have_posts()) : ?>
                <div class="blog-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php emsaks_render_blog_card(get_the_ID()); ?>
                    <?php endwhile; ?>
                </div>

                <div class="blog-pagination">
                    <?php the_posts_pagination([
                        'mid_size'  => 1,
                        'prev_text' => '&larr;',
                        'next_text' => '&rarr;',
                    ]); ?>
                </div>
            <?php else : ?>
                <p class="blog-empty"><?php esc_html_e('Nuk u gjet asnjë artikull.', 'base-theme'); ?></p>
            <?php endif; ?>
        </div>
    </main>
    <?php
}

// ─── Archive filters & helpers ────────────────────────────────────────────────
require_once THEME_DIR . '/inc/attribute-filter-settings.php';
require_once THEME_DIR . '/inc/filters.php';

add_action('wp_enqueue_scripts', function () {
    // nav.js: Kategorit mega menu — load globally (not on checkout flow)
    if (!is_cart() && !is_checkout()) {
        wp_enqueue_script('theme-nav', THEME_URI . '/assets/js/nav.js', [], THEME_VERSION, true);
    }

    // filter.js: archive/shop attribute filters
    if (!function_exists('is_woocommerce') || !is_woocommerce()) return;
    if (is_product() || is_cart() || is_checkout() || is_account_page()) return;

    wp_enqueue_script(
        'theme-filter',
        THEME_URI . '/assets/js/filter.js',
        [],
        THEME_VERSION,
        true
    );
}, 20);
