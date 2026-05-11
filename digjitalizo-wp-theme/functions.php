<?php

define('THEME_VERSION', '1.0.0');
define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

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

    register_nav_menus([
        'primary' => __('Primary Menu', 'base-theme'),
        'footer'  => __('Footer Menu', 'base-theme'),
    ]);
});

// ─── CSS Custom Properties from ACF Global Options ────────────────────────────
add_action('wp_head', function () {
    if (!function_exists('get_field')) return;

    $color_primary   = get_field('color_primary',   'option') ?: '#165783';
    $color_secondary = get_field('color_secondary', 'option') ?: '#004c7a';
    $color_accent    = get_field('color_accent',    'option') ?: '#1face3';
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
    wp_enqueue_style('theme-main', THEME_URI . '/assets/css/main.css', $deps, THEME_VERSION);

    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-cart-fragments');
    }

    wp_enqueue_script('theme-main', THEME_URI . '/assets/js/main.js', ['jquery'], THEME_VERSION, true);

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
            'cartUrl'      => wc_get_cart_url(),
            'checkoutUrl'  => wc_get_checkout_url(),
            'cartBehavior' => function_exists('get_field') ? (get_field('woo_cart_behavior', 'option') ?: 'minicart') : 'minicart',
        ]);
    }
});

// ─── WooCommerce: Replace Wrappers ────────────────────────────────────────────
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

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

// ─── WooCommerce: Products Per Page ───────────────────────────────────────────
add_filter('loop_shop_per_page', function () {
    if (!function_exists('get_field')) return 12;
    return (int) (get_field('woo_products_per_page', 'option') ?: 12);
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
        'edit-address'    => __('Adresat', 'base-theme'),
        'edit-account'    => __('Të dhënat', 'base-theme'),
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
});
