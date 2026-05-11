<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_my_account_page');
?>

<div class="account-wrap">

    <?php if (is_user_logged_in()) :
        $current_user = wp_get_current_user();
        $avatar = get_avatar_url($current_user->user_email, ['size' => 80, 'default' => 'mysteryman']);
    ?>

    <!-- Account hero -->
    <div class="account-hero">
        <div class="account-hero-left">
            <div class="account-hero-avatar">
                <img src="<?php echo esc_url($avatar); ?>"
                     alt="<?php echo esc_attr($current_user->display_name); ?>"
                     class="account-avatar-img">
            </div>
            <div class="account-hero-info">
                <div class="account-hero-name"><?php echo esc_html($current_user->display_name); ?></div>
                <div class="account-hero-email"><?php echo esc_html($current_user->user_email); ?></div>
            </div>
        </div>
        <a href="<?php echo esc_url(wc_logout_url()); ?>" class="account-logout-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"/>
            </svg>
            <?php esc_html_e('Dilni', 'base-theme'); ?>
        </a>
    </div>

    <?php endif; ?>

    <div class="account-layout">

        <!-- Sidebar navigation -->
        <aside class="account-sidebar">
            <?php do_action('woocommerce_account_navigation'); ?>
        </aside>

        <!-- Main content -->
        <div class="account-content">
            <?php do_action('woocommerce_account_content'); ?>
        </div>

    </div>

</div>

<?php do_action('woocommerce_after_my_account_page'); ?>
