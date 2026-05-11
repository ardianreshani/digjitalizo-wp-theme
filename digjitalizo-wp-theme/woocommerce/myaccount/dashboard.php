<?php
defined('ABSPATH') || exit;

$user_id      = get_current_user_id();
$current_user = wp_get_current_user();

// Quick stats
$orders       = wc_get_orders(['customer' => $user_id, 'limit' => -1, 'return' => 'ids']);
$total_orders = count($orders);
$total_spent  = wc_get_customer_total_spent($user_id);
$last_order   = wc_get_orders(['customer' => $user_id, 'limit' => 1, 'orderby' => 'date', 'order' => 'DESC']);
$last_order   = !empty($last_order) ? $last_order[0] : null;
?>

<div class="account-dashboard">

    <!-- Welcome -->
    <div class="account-welcome">
        <h2><?php printf(esc_html__('Mirë se vini, %s!', 'base-theme'), '<strong>' . esc_html($current_user->first_name ?: $current_user->display_name) . '</strong>'); ?></h2>
        <p class="text-sm text-[#777] mt-1">
            <?php esc_html_e('Nga paneli juaj mund të shikoni porositë, adresat dhe të dhënat e llogarisë.', 'base-theme'); ?>
        </p>
    </div>

    <!-- Stats row -->
    <div class="account-stats">
        <div class="account-stat">
            <div class="account-stat-icon orders-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                </svg>
            </div>
            <div class="account-stat-body">
                <div class="account-stat-number"><?php echo esc_html($total_orders); ?></div>
                <div class="account-stat-label"><?php esc_html_e('Porosi gjithsej', 'base-theme'); ?></div>
            </div>
        </div>

        <div class="account-stat">
            <div class="account-stat-icon spent-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33"/>
                </svg>
            </div>
            <div class="account-stat-body">
                <div class="account-stat-number"><?php echo wp_kses_post(wc_price($total_spent)); ?></div>
                <div class="account-stat-label"><?php esc_html_e('Shuma totale', 'base-theme'); ?></div>
            </div>
        </div>

        <?php if ($last_order) : ?>
        <div class="account-stat">
            <div class="account-stat-icon last-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <div class="account-stat-body">
                <div class="account-stat-number">#<?php echo esc_html($last_order->get_order_number()); ?></div>
                <div class="account-stat-label">
                    <?php echo esc_html(wc_format_datetime($last_order->get_date_created(), get_option('date_format'))); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick links -->
    <div class="account-quick-links">
        <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'))); ?>" class="account-quick-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
            </svg>
            <span class="font-semibold text-sm"><?php esc_html_e('Porositë e mia', 'base-theme'); ?></span>
            <span class="text-xs text-[#777]"><?php printf(esc_html__('%d gjithsej', 'base-theme'), $total_orders); ?></span>
        </a>

        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount'))); ?>" class="account-quick-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
            </svg>
            <span class="font-semibold text-sm"><?php esc_html_e('Adresat e mia', 'base-theme'); ?></span>
            <span class="text-xs text-[#777]"><?php esc_html_e('Ndrysho adresën', 'base-theme'); ?></span>
        </a>

        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account', '', wc_get_page_permalink('myaccount'))); ?>" class="account-quick-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
            <span class="font-semibold text-sm"><?php esc_html_e('Të dhënat', 'base-theme'); ?></span>
            <span class="text-xs text-[#777]"><?php esc_html_e('Ndrysho fjalëkalimin', 'base-theme'); ?></span>
        </a>
    </div>

</div>

<?php do_action('woocommerce_account_dashboard'); ?>
