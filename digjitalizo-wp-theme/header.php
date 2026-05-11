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

// Determine active step
$step = 1;
if (is_checkout() && !is_wc_endpoint_url('order-received')) $step = 2;
?>

<?php if ($is_checkout_flow) : ?>

<header class="header-checkout">
    <div class="container flex items-center gap-6">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="shrink-0">
            <img src="<?php echo esc_url(THEME_URI . '/assets/images/logo.svg'); ?>"
                 alt="<?php bloginfo('name'); ?>" class="h-10 w-auto" style="filter:none;">
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

            <span class="checkout-step-sep mx-2">›</span>

            <div class="checkout-step <?php echo $step === 2 ? 'active' : ($step > 2 ? 'done' : ''); ?>">
                <span class="checkout-step-num">2</span>
                <span>Vazhdo te pagesa</span>
            </div>

            <span class="checkout-step-sep mx-2">›</span>

            <div class="checkout-step <?php echo $step === 3 ? 'active' : ''; ?>">
                <span class="checkout-step-num">3</span>
                <span>Përfundimi i porosisë</span>
            </div>
        </div>

        <a href="<?php echo esc_url(home_url('/')); ?>"
           class="shrink-0 text-sm text-[#555] hover:text-brand transition-colors whitespace-nowrap ml-auto">
            &lsaquo; Vazhdo blerjen
        </a>
    </div>
</header>

<?php else : ?>

<header class="site-header">
    <div class="container">
        <div class="header-top">
            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="shrink-0 mr-4">
                <img src="<?php echo esc_url(THEME_URI . '/assets/images/logo.svg'); ?>"
                     alt="<?php bloginfo('name'); ?>" class="h-10 w-auto">
            </a>

            <!-- Search -->
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                  class="header-search">
                <button type="submit" aria-label="Kërko">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </button>
                <input type="search" name="s" placeholder="Kërko produkte"
                       value="<?php echo esc_attr(get_search_query()); ?>">
                <input type="hidden" name="post_type" value="product">
            </form>

            <!-- Actions -->
            <div class="header-actions">
                <?php if (function_exists('wc_get_account_page_permalink')) : ?>
                <a href="<?php echo esc_url(wc_get_account_page_permalink()); ?>"
                   class="header-action-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                    <span><?php is_user_logged_in() ? esc_html_e('Llogaria', 'base-theme') : esc_html_e('Kyçu', 'base-theme'); ?></span>
                </a>
                <?php endif; ?>

                <button class="mini-cart-toggle header-action-link relative" aria-label="Shporta">
                    <span class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                        </svg>
                        <?php if (function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
                        <span class="cart-count absolute -top-2 -right-2">
                            <?php echo esc_html(WC()->cart->get_cart_contents_count()); ?>
                        </span>
                        <?php endif; ?>
                    </span>
                    <span>Shporta</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation bar -->
    <div class="nav-bar">
        <div class="container">
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => 'nav',
                'fallback_cb'    => '__return_false',
                'walker'         => null,
            ]); ?>
        </div>
    </div>
</header>

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
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn w-full py-3 text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                </svg>
                <?php esc_html_e('Vazhdo me blerjen', 'base-theme'); ?>
            </a>
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn-outline w-full py-3 text-base">
                <?php esc_html_e('Shiko shportën', 'base-theme'); ?>
            </a>
        </div>

        <?php
        $badges = function_exists('get_field') ? get_field('woo_trust_badges', 'option') : [];
        if (!empty($badges)) : ?>
        <div class="mini-cart-trust">
            <?php foreach ($badges as $badge) :
                $icon  = $badge['badge_icon'] ?? [];
                $label = $badge['badge_label'] ?? '';
                if (!$label) continue;
            ?>
            <div class="mini-cart-trust-item">
                <?php if (!empty($icon['url'])) : ?>
                    <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($label); ?>" class="w-5 h-5 object-contain">
                <?php endif; ?>
                <span><?php echo esc_html($label); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php endif; ?>
