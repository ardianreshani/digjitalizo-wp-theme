<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$is_checkout_flow = (is_cart() || (is_checkout() && !is_wc_endpoint_url('order-received')));
$company_logo_dark = function_exists('get_field') ? get_field('company_logo_dark', 'option') : null;
$header_logo_url = THEME_URI . '/assets/images/logo.svg';
$header_logo_alt = get_bloginfo('name');
$my_account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/');
$cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
$account_name = '';
$account_email = '';
$account_initial = '';
$profile_url = $my_account_url;
$address_url = $my_account_url;
$orders_url = $my_account_url;
$logout_url = wp_logout_url(home_url('/'));
$is_catalog_context = function_exists('is_shop')
    && (is_shop() || is_product_category() || is_product() || is_tax('product_brand'));
$is_account_context = function_exists('is_account_page') && is_account_page();

if (is_array($company_logo_dark) && !empty($company_logo_dark['url'])) {
    $header_logo_url = $company_logo_dark['url'];
    $header_logo_alt = !empty($company_logo_dark['alt']) ? $company_logo_dark['alt'] : $header_logo_alt;
} elseif (is_string($company_logo_dark) && $company_logo_dark !== '') {
    $header_logo_url = $company_logo_dark;
}

if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $account_name = $current_user->display_name ?: $current_user->user_login;
    $account_email = $current_user->user_email;
    $account_initial = strtoupper(substr($account_name, 0, 1));
    $profile_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('edit-account') : $my_account_url;
    $address_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('edit-address') : $my_account_url;
    $orders_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('orders') : $my_account_url;
    $logout_url = function_exists('wc_logout_url') ? wc_logout_url() : $logout_url;
}

$lang_items       = [];
$current_lang_code = '';

if (function_exists('pll_the_languages')) {
    $_pll = pll_the_languages(['raw' => 1, 'dropdown' => 0, 'show_flags' => 0, 'show_names' => 1, 'display_names_as' => 'name', 'hide_current' => 0]);
    if (!empty($_pll) && is_array($_pll)) {
        foreach ($_pll as $_l) {
            $code = strtoupper($_l['slug'] ?? '');
            $active = !empty($_l['current_lang']);
            if ($active) $current_lang_code = $code;
            $lang_items[] = ['url' => $_l['url'] ?? '#', 'code' => $code, 'name' => $_l['name'] ?? $code, 'active' => $active];
        }
    }
} elseif (defined('ICL_SITEPRESS_VERSION') || function_exists('icl_object_id')) {
    $_wpml = apply_filters('wpml_active_languages', null, ['skip_missing' => 0, 'orderby' => 'code']);
    if (!empty($_wpml) && is_array($_wpml)) {
        foreach ($_wpml as $_l) {
            $code = strtoupper($_l['language_code'] ?? '');
            $active = !empty($_l['active']);
            if ($active) $current_lang_code = $code;
            $lang_items[] = ['url' => $_l['url'] ?? '#', 'code' => $code, 'name' => $_l['translated_name'] ?? $code, 'active' => $active];
        }
    }
}

if (empty($lang_items)) {
    $current_lang_code = strtoupper(substr(determine_locale(), 0, 2));
    $lang_items = [['url' => '#', 'code' => $current_lang_code, 'name' => $current_lang_code, 'active' => true]];
}

// Determine active step
$step = 1;
if (is_checkout() && !is_wc_endpoint_url('order-received')) $step = 2;
?>

<?php if ($is_checkout_flow) : ?>

<header class="header-checkout">
    <div class="container flex items-center gap-6">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo-link">
            <img src="<?php echo esc_url($header_logo_url); ?>"
                 alt="<?php echo esc_attr($header_logo_alt); ?>" class="site-logo-img" style="filter:none;">
        </a>

        <div class="checkout-steps">
            <div class="checkout-step <?php echo $step === 1 ? 'active' : 'done'; ?>">
                <span class="checkout-step-num"><?php echo $step > 1 ? '✓' : '1'; ?></span>
                <?php if (is_cart()) : ?>
                    <span>Shporta</span>
                <?php else : ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="hover:text-brand transition-colors">Shporta</a>
                <?php endif; ?>
            </div>

            <!-- <span class="checkout-step-sep mx-2">›</span> -->

            <div class="checkout-step <?php echo $step === 2 ? 'active' : ($step > 2 ? 'done' : ''); ?>">
                <span class="checkout-step-num">2</span>
                <span>Vazhdo te pagesa</span>
            </div>

            <!-- <span class="checkout-step-sep mx-2">›</span> -->

            <div class="checkout-step <?php echo $step === 3 ? 'active' : ''; ?>">
                <span class="checkout-step-num">3</span>
                <span>Përfundimi i porosisë</span>
            </div>
        </div>

        <a href="<?php echo esc_url(home_url('/')); ?>"
           class="hidden sm:block shrink-0 text-sm text-muted hover:text-brand transition-colors whitespace-nowrap ml-auto">
            &lsaquo; Vazhdo blerjen
        </a>
    </div>
</header>

<?php else : ?>

<header class="site-header">
    <div class="container">
        <div class="header-top">
            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo-link mr-4">
                <img src="<?php echo esc_url($header_logo_url); ?>"
                     alt="<?php echo esc_attr($header_logo_alt); ?>" class="site-logo-img">
            </a>
            <div class="flex items-center gap-2">
            <?php if (count($lang_items) > 1) : ?>
            <div class="lang-switcher block lg:hidden" data-lang-switcher>
                <button type="button" class="lang-switcher-toggle" aria-expanded="false" aria-haspopup="listbox"
                        aria-label="<?php esc_attr_e('Ndrysho gjuhën', 'base-theme'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    <span class="lang-switcher-code"><?php echo esc_html($current_lang_code); ?></span>
                    <svg class="lang-switcher-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div class="lang-switcher-dropdown" role="listbox" aria-label="<?php esc_attr_e('Zgjidhni gjuhën', 'base-theme'); ?>">
                    <?php foreach ($lang_items as $_li) : ?>
                    <a href="<?php echo esc_url($_li['url']); ?>"
                       class="lang-switcher-item<?php echo $_li['active'] ? ' is-active' : ''; ?>"
                       role="option" aria-selected="<?php echo $_li['active'] ? 'true' : 'false'; ?>">
                        <span><?php echo esc_html($_li['code']); ?></span>
                        <?php if ($_li['active']) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <button type="button"
                    class="header-mobile-menu-toggle"
                    data-mobile-menu-toggle
                    aria-expanded="false"
                    aria-controls="mobile-primary-menu"
                    aria-label="<?php echo esc_attr__('Hap menunë', 'base-theme'); ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                </svg>
            </button>
            </div>
            <!-- Search -->
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                  class="header-search" autocomplete="off">
                <button type="submit" aria-label="<?php echo esc_attr__('Kërko', 'base-theme'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </button>
                <input type="search" name="s" placeholder="<?php echo esc_attr__('Kërko produkte', 'base-theme'); ?>"
                       value="<?php echo esc_attr(get_search_query()); ?>"
                       class="header-search-input"
                       aria-autocomplete="list"
                       aria-expanded="false"
                       aria-controls="header-search-results">
                <input type="hidden" name="post_type" value="product">
                <div id="header-search-results" class="header-search-results" aria-live="polite"></div>
            </form>

            <!-- Actions -->
            <div class="header-actions">
            <?php if (count($lang_items) > 1) : ?>
            <div class="lang-switcher" data-lang-switcher>
                <button type="button" class="lang-switcher-toggle" aria-expanded="false" aria-haspopup="listbox"
                        aria-label="<?php esc_attr_e('Ndrysho gjuhën', 'base-theme'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    <span class="lang-switcher-code"><?php echo esc_html($current_lang_code); ?></span>
                    <svg class="lang-switcher-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div class="lang-switcher-dropdown" role="listbox" aria-label="<?php esc_attr_e('Zgjidhni gjuhën', 'base-theme'); ?>">
                    <?php foreach ($lang_items as $_li) : ?>
                    <a href="<?php echo esc_url($_li['url']); ?>"
                       class="lang-switcher-item<?php echo $_li['active'] ? ' is-active' : ''; ?>"
                       role="option" aria-selected="<?php echo $_li['active'] ? 'true' : 'false'; ?>">
                        <span><?php echo esc_html($_li['code']); ?></span>
                        <?php if ($_li['active']) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
                <?php if (is_user_logged_in()) :
                    ?>
                    <div class="header-account">
                        <button type="button"
                                class="header-action-link header-account-toggle"
                                aria-expanded="false"
                                aria-controls="header-account-menu">
                            <span class="header-account-avatar"><?php echo esc_html($account_initial); ?></span>
                            <span class="header-account-toggle-name"><?php echo esc_html($account_name); ?></span>
                            <svg class="header-account-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div id="header-account-menu" class="header-account-menu" aria-hidden="true">
                            <a href="<?php echo esc_url($my_account_url); ?>" class="header-account-menu-head">
                                <span class="header-account-avatar header-account-avatar-lg"><?php echo esc_html($account_initial); ?></span>
                                <span class="header-account-head-text">
                                    <strong><?php echo esc_html($account_name); ?></strong>
                                    <span><?php echo esc_html($account_email); ?></span>
                                </span>
                            </a>
                            <a href="<?php echo esc_url($orders_url); ?>">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m4 7 8-4 8 4-8 4-8-4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M4 7v9l8 5 8-5V7" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M12 11v10" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                <?php esc_html_e('Porositë', 'base-theme'); ?>
                            </a>
                            <a href="<?php echo esc_url($address_url); ?>">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 21s7-4.7 7-11a7 7 0 1 0-14 0c0 6.3 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M12 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                <?php esc_html_e('Adresa', 'base-theme'); ?>
                            </a>
                            <a href="<?php echo esc_url($profile_url); ?>">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4 20c0-3.3 3.6-6 8-6 1.5 0 2.9.3 4.1.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="m17.2 19.6 3.9-3.9a1.4 1.4 0 0 0-2-2l-3.9 3.9-.4 2.4 2.4-.4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                </svg>
                                <?php esc_html_e('Të dhënat e llogarisë', 'base-theme'); ?>
                            </a>
                            <a href="<?php echo esc_url($logout_url); ?>">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M15 8 19 12 15 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19 12H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M11 5H6a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <?php esc_html_e('Dilni', 'base-theme'); ?>
                            </a>
                        </div>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url($my_account_url); ?>"
                       class="header-action-link header-login-link"
                       aria-label="<?php echo esc_attr__('Kyçu', 'base-theme'); ?>">
                        <span><?php esc_html_e('Kyçu', 'base-theme'); ?></span>
                        <svg class="header-account-icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M14.0007 12.5418C16.578 12.5418 18.6673 10.4525 18.6673 7.87516C18.6673 5.29783 16.578 3.2085 14.0007 3.2085C11.4233 3.2085 9.33398 5.29783 9.33398 7.87516C9.33398 10.4525 11.4233 12.5418 14.0007 12.5418Z" fill="currentColor"/>
                            <path d="M23.3327 19.5415C23.3327 22.4407 23.3327 24.7915 13.9993 24.7915C4.66602 24.7915 4.66602 22.4407 4.66602 19.5415C4.66602 16.6423 8.84502 14.2915 13.9993 14.2915C19.1537 14.2915 23.3327 16.6423 23.3327 19.5415Z" fill="currentColor"/>
                        </svg>
                    </a>
                <?php endif; ?>

                <button class="mini-cart-toggle header-action-link relative" aria-label="Shporta">
                    <span><?php esc_html_e('Shporta', 'base-theme'); ?></span>
                    <span class="relative">
                <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.89413 1.08333C6.19747 0.444167 6.8518 0 7.60797 0H11.9413C12.6975 0 13.3507 0.444167 13.6551 1.08333C14.395 1.08983 14.9725 1.12342 15.4881 1.32492C16.1037 1.56571 16.6391 1.97493 17.033 2.50575C17.4305 3.04092 17.618 3.72667 17.8736 4.67025L18.6775 7.61908L18.9808 8.53017L19.0068 8.56267C19.9829 9.81283 19.5181 11.6718 18.5886 15.3888C17.9971 17.7537 17.7025 18.9356 16.8206 19.6246C15.9388 20.3125 14.72 20.3125 12.2825 20.3125H7.26672C4.82922 20.3125 3.61047 20.3125 2.72863 19.6246C1.8468 18.9356 1.55105 17.7537 0.960633 15.3888C0.0311327 11.6718 -0.433618 9.81283 0.542466 8.56267L0.568466 8.53017L0.871799 7.61908L1.67563 4.67025C1.93238 3.72667 2.1198 3.03983 2.5163 2.50467C2.91034 1.97425 3.44569 1.56541 4.06113 1.32492C4.5768 1.12342 5.15313 1.08875 5.89413 1.08333ZM5.8963 2.71158C5.17913 2.71917 4.89097 2.74625 4.65263 2.83942C4.32109 2.96907 4.03276 3.18945 3.82063 3.47533C3.62997 3.73208 3.5173 4.09067 3.20313 5.24658L2.58563 7.50967C3.69063 7.3125 5.2008 7.3125 7.26563 7.3125H12.2825C14.3485 7.3125 15.8575 7.3125 16.9625 7.5075L16.3461 5.24442C16.032 4.0885 15.9193 3.72992 15.7286 3.47317C15.5165 3.18728 15.2282 2.9669 14.8966 2.83725C14.6583 2.74408 14.3701 2.717 13.653 2.70942C13.4991 3.03317 13.2567 3.30668 12.9537 3.49823C12.6508 3.68979 12.2997 3.79153 11.9413 3.79167H7.60797C7.24965 3.79163 6.89868 3.69004 6.59573 3.49869C6.29279 3.30734 6.05027 3.03513 5.8963 2.71158Z" fill="currentColor"></path>
                        </svg>
                        <?php if ($cart_count > 0) : ?>
                        <span class="cart-count absolute -top-2 -right-2">
                            <?php echo esc_html($cart_count); ?>
                        </span>
                        <?php endif; ?>
                    </span>
                </button>
            </div>
        </div>

        <div id="mobile-primary-menu" class="mobile-primary-menu" aria-hidden="true">
            <div class="mobile-primary-menu-head">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-primary-menu-logo">
                    <img src="<?php echo esc_url($header_logo_url); ?>"
                         alt="<?php echo esc_attr($header_logo_alt); ?>">
                </a>
                <button type="button"
                        class="mobile-primary-menu-close"
                        aria-label="<?php esc_attr_e('Mbyll', 'base-theme'); ?>">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => 'nav',
                'container_class'=> 'mobile-primary-menu-nav',
                'fallback_cb'    => '__return_false',
                'walker'         => null,
            ]); ?>
        </div>
    </div>

    <!-- Navigation bar -->
    <div class="nav-bar">
        <div class="container flex items-center">

            <!-- Kategorit toggle button -->
            <button type="button"
                    class="nav-kategorit-btn<?php echo $is_catalog_context ? ' is-active' : ''; ?>"
                    id="kategoritBtn"
                    aria-expanded="false"
                    aria-controls="kategorit-panel">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <?php esc_html_e('Kategoritë', 'base-theme'); ?>
            </button>

            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => 'nav',
                'fallback_cb'    => '__return_false',
                'walker'         => null,
            ]); ?>
        </div>
    </div>

    <!-- Kategorit drawer panel (slides in from left, fixed position) -->
    <?php
    $panel_logo_url = THEME_URI . '/assets/images/logo.svg';
    $panel_logo_alt = get_bloginfo('name');
    $logo_dark      = function_exists('get_field') ? get_field('company_logo_dark', 'option') : null;
    if (is_array($logo_dark) && !empty($logo_dark['url'])) {
        $panel_logo_url = $logo_dark['url'];
        $panel_logo_alt = !empty($logo_dark['alt']) ? $logo_dark['alt'] : $panel_logo_alt;
    } elseif (is_string($logo_dark) && $logo_dark !== '') {
        $panel_logo_url = $logo_dark;
    }
    $panel_phone = function_exists('get_field') ? get_field('company_phone', 'option') : '';
    $panel_email = function_exists('get_field') ? get_field('company_email', 'option') : '';
    ?>
    <div class="kategorit-panel" id="kategorit-panel" aria-hidden="true"
         role="dialog" aria-label="<?php esc_attr_e('Kategorit e produkteve', 'base-theme'); ?>">

        <!-- Panel head: logo + close -->
        <div class="kategorit-panel-head">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="kategorit-logo">
                <img src="<?php echo esc_url($panel_logo_url); ?>"
                     alt="<?php echo esc_attr($panel_logo_alt); ?>">
            </a>
            <button type="button" class="kategorit-close-btn" id="kategoritClose"
                    aria-label="<?php esc_attr_e('Mbyll', 'base-theme'); ?>">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <!-- Panel body: drill-down category list -->
        <div class="kategorit-panel-body">
            <?php if (function_exists('emsaks_render_kategorit_categories')) {
                emsaks_render_kategorit_categories();
            } ?>
        </div>

        <!-- Panel footer: email + phone -->
        <?php if ($panel_email || $panel_phone) : ?>
        <div class="kategorit-panel-foot">
            <?php if ($panel_email) : ?>
            <a href="mailto:<?php echo esc_attr($panel_email); ?>" class="kategorit-foot-link">
                <span class="kategorit-foot-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                        <rect x="2" y="4" width="14" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M2 6l7 4.5L16 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <?php echo esc_html($panel_email); ?>
            </a>
            <?php endif; ?>
            <?php if ($panel_phone) : ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $panel_phone)); ?>" class="kategorit-foot-link">
                <span class="kategorit-foot-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                        <path d="M3 3.5c0-.3.2-.6.6-.6h2.6c.3 0 .5.2.6.4l1 2.4c.1.3 0 .6-.2.8l-1.2 1c.7 1.4 1.7 2.4 3.1 3.1l1-1.2c.2-.2.5-.3.8-.2l2.4 1c.2.1.4.3.4.6v2.6c0 .4-.3.6-.6.6C6.6 14 3 10.4 3 6.1V3.5Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
                <?php echo esc_html($panel_phone); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

</header>

<nav class="mobile-bottom-nav" aria-label="<?php echo esc_attr__('Navigimi kryesor', 'base-theme'); ?>">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-bottom-nav-item<?php echo is_front_page() ? ' is-active' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        <span><?php esc_html_e('Ballina', 'base-theme'); ?></span>
    </a>
    <button type="button"
            class="mobile-bottom-nav-item<?php echo $is_catalog_context ? ' is-active' : ''; ?>"
            data-kategorit-open
            aria-expanded="false"
            aria-controls="kategorit-panel">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
        </svg>
        <span><?php esc_html_e('Kategoria', 'base-theme'); ?></span>
    </button>
    <button type="button" class="mobile-bottom-nav-item mini-cart-toggle<?php echo is_cart() ? ' is-active' : ''; ?>">
        <span class="mobile-bottom-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 8V7a5 5 0 0 1 10 0v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M5.5 8h13l1 12h-15l1-12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
            <?php if ($cart_count > 0) : ?>
            <span class="cart-count absolute -top-2 -right-2">
                <?php echo esc_html($cart_count); ?>
            </span>
            <?php endif; ?>
        </span>
        <span><?php esc_html_e('Shporta', 'base-theme'); ?></span>
    </button>
    <a href="<?php echo esc_url($my_account_url); ?>" class="mobile-bottom-nav-item<?php echo $is_account_context ? ' is-active' : ''; ?>">
        <?php if (is_user_logged_in()) : ?>
            <span class="mobile-bottom-avatar"><?php echo esc_html($account_initial); ?></span>
            <span><?php esc_html_e('Llogaria', 'base-theme'); ?></span>
        <?php else : ?>
            <svg viewBox="0 0 28 28" fill="none" aria-hidden="true">
                <path d="M14.0007 12.5418C16.578 12.5418 18.6673 10.4525 18.6673 7.87516C18.6673 5.29783 16.578 3.2085 14.0007 3.2085C11.4233 3.2085 9.33398 5.29783 9.33398 7.87516C9.33398 10.4525 11.4233 12.5418 14.0007 12.5418Z" fill="currentColor"/>
                <path d="M23.3327 19.5415C23.3327 22.4407 23.3327 24.7915 13.9993 24.7915C4.66602 24.7915 4.66602 22.4407 4.66602 19.5415C4.66602 16.6423 8.84502 14.2915 13.9993 14.2915C19.1537 14.2915 23.3327 16.6423 23.3327 19.5415Z" fill="currentColor"/>
            </svg>
            <span><?php esc_html_e('Kyçu', 'base-theme'); ?></span>
        <?php endif; ?>
    </a>
</nav>

<!-- Mini cart drawer -->
<div class="mini-cart-overlay"></div>
<div class="mini-cart-drawer" aria-hidden="true" role="dialog" aria-label="Shporta">
    <div class="mini-cart-head">
        <h3>Shporta</h3>
        <button class="mini-cart-close" aria-label="Mbyll">&times;</button>
    </div>
    <div class="mini-cart-body">
        <div class="mini-cart-items">
            <?php if (function_exists('WC')) woocommerce_mini_cart(); ?>
        </div>
    </div>
    <div class="mini-cart-foot">
        <?php if (function_exists('WC') && WC()->cart) :
            $threshold = function_exists('get_field') ? (float) get_field('woo_free_shipping_threshold', 'option') : 0;
            $fs_msg    = function_exists('get_field') ? get_field('woo_free_shipping_message', 'option') : '';
            $fs_msg    = $fs_msg ?: 'Shto {amount} për transport falas!';
            $subtotal  = WC()->cart->get_subtotal();
        ?>

        <?php if ($threshold > 0) :
            $remaining = max(0, $threshold - $subtotal);
            $progress  = min(100, round(($subtotal / $threshold) * 100));
        ?>
        <div class="mini-cart-free-ship">
            <?php if ($remaining <= 0) : ?>
                <p class="mini-cart-free-ship-msg mini-cart-free-ship-done">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 shrink-0">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>
                    </svg>
                    <?php esc_html_e('Ke fituar transport falas!', 'base-theme'); ?>
                </p>
            <?php else : ?>
                <p class="mini-cart-free-ship-msg"><?php echo wp_kses_post(str_replace('{amount}', wc_price($remaining), $fs_msg)); ?></p>
            <?php endif; ?>
            <div class="mini-cart-free-ship-track">
                <div class="mini-cart-free-ship-fill" style="width:<?php echo esc_attr($progress); ?>%"></div>
            </div>
        </div>
        <?php endif; ?>

        <div class="mini-cart-total">
            <span><?php esc_html_e('Nëntotali', 'base-theme'); ?></span>
            <span class="mini-cart-subtotal"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
        </div>

        <div class="mini-cart-actions">
            <!-- <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn w-full py-3 text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                </svg>
                <?php esc_html_e('Vazhdo me blerjen', 'base-theme'); ?>
            </a> -->
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn w-full w-full py-3 text-base">
               <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.89413 1.08333C6.19747 0.444167 6.8518 0 7.60797 0H11.9413C12.6975 0 13.3507 0.444167 13.6551 1.08333C14.395 1.08983 14.9725 1.12342 15.4881 1.32492C16.1037 1.56571 16.6391 1.97493 17.033 2.50575C17.4305 3.04092 17.618 3.72667 17.8736 4.67025L18.6775 7.61908L18.9808 8.53017L19.0068 8.56267C19.9829 9.81283 19.5181 11.6718 18.5886 15.3888C17.9971 17.7537 17.7025 18.9356 16.8206 19.6246C15.9388 20.3125 14.72 20.3125 12.2825 20.3125H7.26672C4.82922 20.3125 3.61047 20.3125 2.72863 19.6246C1.8468 18.9356 1.55105 17.7537 0.960633 15.3888C0.0311327 11.6718 -0.433618 9.81283 0.542466 8.56267L0.568466 8.53017L0.871799 7.61908L1.67563 4.67025C1.93238 3.72667 2.1198 3.03983 2.5163 2.50467C2.91034 1.97425 3.44569 1.56541 4.06113 1.32492C4.5768 1.12342 5.15313 1.08875 5.89413 1.08333ZM5.8963 2.71158C5.17913 2.71917 4.89097 2.74625 4.65263 2.83942C4.32109 2.96907 4.03276 3.18945 3.82063 3.47533C3.62997 3.73208 3.5173 4.09067 3.20313 5.24658L2.58563 7.50967C3.69063 7.3125 5.2008 7.3125 7.26563 7.3125H12.2825C14.3485 7.3125 15.8575 7.3125 16.9625 7.5075L16.3461 5.24442C16.032 4.0885 15.9193 3.72992 15.7286 3.47317C15.5165 3.18728 15.2282 2.9669 14.8966 2.83725C14.6583 2.74408 14.3701 2.717 13.653 2.70942C13.4991 3.03317 13.2567 3.30668 12.9537 3.49823C12.6508 3.68979 12.2997 3.79153 11.9413 3.79167H7.60797C7.24965 3.79163 6.89868 3.69004 6.59573 3.49869C6.29279 3.30734 6.05027 3.03513 5.8963 2.71158Z" fill="currentColor"></path>
                </svg>
                <?php esc_html_e('Shiko shportën', 'base-theme'); ?>
            </a>
        </div>

        <?php endif; ?>
    </div>
</div>

<?php endif; ?>
