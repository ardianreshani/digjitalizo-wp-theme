<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_lost_password_form');
?>

<div class="account-lost-pw-wrap">

    <div class="auth-card-header text-center mb-6">
        <div class="auth-icon mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
            </svg>
        </div>
        <h2 class="auth-card-title"><?php esc_html_e('Keni harruar fjalëkalimin?', 'base-theme'); ?></h2>
        <p class="auth-card-sub"><?php esc_html_e('Shkruani emailin tuaj dhe do të merrni lidhjen e rivendosjes.', 'base-theme'); ?></p>
    </div>

    <form method="post" class="woocommerce-ResetPassword lost_reset_password">

        <div class="auth-fields">
            <div class="form-field">
                <label for="user_login">
                    <?php esc_html_e('Email ose emri i përdoruesit', 'base-theme'); ?>
                    <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                </label>
                <input type="text" id="user_login" name="user_login"
                       autocomplete="username"
                       placeholder="<?php esc_attr_e('email@shembull.com', 'base-theme'); ?>"
                       required aria-required="true">
            </div>
        </div>

        <?php do_action('woocommerce_lostpassword_form'); ?>

        <?php wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce'); ?>
        <input type="hidden" name="wc_reset_password" value="true">

        <button type="submit"
                value="<?php esc_attr_e('Rivendos fjalëkalimin', 'base-theme'); ?>"
                class="btn w-full py-3 text-base mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
            <?php esc_html_e('Dërgoni lidhjen e rivendosjes', 'base-theme'); ?>
        </button>

        <div class="text-center mt-4">
            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="auth-lost-pw">
                &larr; <?php esc_html_e('Kthehu te hyrja', 'base-theme'); ?>
            </a>
        </div>

    </form>

</div>

<?php do_action('woocommerce_after_lost_password_form'); ?>
