<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_account_navigation');

$nav_icons = [
    'dashboard'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>',
    'orders'          => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 7 8-4 8 4-8 4-8-4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M4 7v9l8 5 8-5V7" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 11v10" stroke="currentColor" stroke-width="1.8"/></svg>',
    'edit-address'    => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-4.7 7-11a7 7 0 1 0-14 0c0 6.3 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8"/></svg>',
    'edit-account'    => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/><path d="M4 20c0-3.3 3.6-6 8-6 1.5 0 2.9.3 4.1.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="m17.2 19.6 3.9-3.9a1.4 1.4 0 0 0-2-2l-3.9 3.9-.4 2.4 2.4-.4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
    'customer-logout' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 8 19 12 15 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 12H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M11 5H6a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
];
?>

<nav class="account-nav" aria-label="<?php esc_html_e('Navigimi i llogarisë', 'base-theme'); ?>">
    <ul class="account-nav-list">
        <?php foreach (wc_get_account_menu_items() as $endpoint => $label) :
            $classes = wc_get_account_menu_item_classes($endpoint);
            $url     = wc_get_account_endpoint_url($endpoint);
            $is_active = wc_is_current_account_menu_item($endpoint);
            $icon    = $nav_icons[$endpoint] ?? '';
        ?>
        <li class="<?php echo esc_attr($classes); ?>">
            <a href="<?php echo esc_url($url); ?>"
               class="account-nav-link<?php echo $is_active ? ' active' : ''; ?><?php echo $endpoint === 'customer-logout' ? ' logout' : ''; ?>"
               <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
                <?php if ($icon) : ?>
                <span class="account-nav-icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
                <?php endif; ?>
                <span><?php echo esc_html($label); ?></span>
                <?php if ($endpoint === 'orders') :
                    $order_count = wc_get_customer_order_count(get_current_user_id());
                    if ($order_count > 0) : ?>
                    <span class="account-nav-badge"><?php echo esc_html($order_count); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php do_action('woocommerce_after_account_navigation'); ?>
