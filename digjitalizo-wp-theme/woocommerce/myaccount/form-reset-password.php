<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_reset_password_form');
?>

<div class="account-lost-pw-wrap">

    <div class="auth-card-header text-center mb-6">
        <div class="auth-icon mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
            </svg>
        </div>
        <h2 class="auth-card-title"><?php esc_html_e('Vendosni fjalëkalimin e ri', 'base-theme'); ?></h2>
        <p class="auth-card-sub"><?php echo esc_html(apply_filters('woocommerce_reset_password_message', __('Zgjidhni një fjalëkalim të ri të sigurt.', 'base-theme'))); ?></p>
    </div>

    <form method="post" class="woocommerce-ResetPassword lost_reset_password">

        <div class="auth-fields">
            <div class="form-field">
                <label for="password_1">
                    <?php esc_html_e('Fjalëkalimi i ri', 'base-theme'); ?>
                    <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                </label>
                <div class="auth-password-wrap">
                    <input type="password" id="password_1" name="password_1"
                           autocomplete="new-password"
                           placeholder="<?php esc_attr_e('Fjalëkalimi i ri', 'base-theme'); ?>"
                           required aria-required="true">
                    <button type="button" class="auth-toggle-pw" aria-label="<?php esc_attr_e('Trego', 'base-theme'); ?>">
                        <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg class="eye-hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
            </div>

            <div class="form-field">
                <label for="password_2">
                    <?php esc_html_e('Konfirmo fjalëkalimin e ri', 'base-theme'); ?>
                    <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                </label>
                <div class="auth-password-wrap">
                    <input type="password" id="password_2" name="password_2"
                           autocomplete="new-password"
                           placeholder="<?php esc_attr_e('Konfirmo fjalëkalimin e ri', 'base-theme'); ?>"
                           required aria-required="true">
                    <button type="button" class="auth-toggle-pw" aria-label="<?php esc_attr_e('Trego', 'base-theme'); ?>">
                        <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg class="eye-hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <input type="hidden" name="reset_key"   value="<?php echo esc_attr($args['key']); ?>">
        <input type="hidden" name="reset_login" value="<?php echo esc_attr($args['login']); ?>">
        <input type="hidden" name="wc_reset_password" value="true">

        <?php do_action('woocommerce_resetpassword_form'); ?>
        <?php wp_nonce_field('reset_password', 'woocommerce-reset-password-nonce'); ?>

        <button type="submit" class="btn w-full py-3 text-base mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <?php esc_html_e('Ruaj fjalëkalimin e ri', 'base-theme'); ?>
        </button>

        <div class="text-center mt-4">
            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="auth-lost-pw">
                &larr; <?php esc_html_e('Kthehu te hyrja', 'base-theme'); ?>
            </a>
        </div>

    </form>

</div>

<?php do_action('woocommerce_after_reset_password_form'); ?>
