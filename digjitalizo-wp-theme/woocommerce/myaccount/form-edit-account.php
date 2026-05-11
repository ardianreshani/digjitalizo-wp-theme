<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_edit_account_form');
?>

<div class="account-section-header">
    <h2 class="account-section-title"><?php esc_html_e('Të dhënat e llogarisë', 'base-theme'); ?></h2>
</div>

<form class="woocommerce-EditAccountForm account-form" action="" method="post" <?php do_action('woocommerce_edit_account_form_tag'); ?>>

    <?php do_action('woocommerce_edit_account_form_start'); ?>

    <!-- Personal info -->
    <div class="account-form-section">
        <h3 class="account-form-section-title"><?php esc_html_e('Informacionet personale', 'base-theme'); ?></h3>

        <div class="account-row">
            <div class="form-field">
                <label for="account_first_name">
                    <?php esc_html_e('Emri', 'base-theme'); ?>
                    <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                </label>
                <input type="text" id="account_first_name" name="account_first_name"
                       autocomplete="given-name"
                       value="<?php echo esc_attr($user->first_name); ?>"
                       aria-required="true" required>
            </div>
            <div class="form-field">
                <label for="account_last_name">
                    <?php esc_html_e('Mbiemri', 'base-theme'); ?>
                    <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                </label>
                <input type="text" id="account_last_name" name="account_last_name"
                       autocomplete="family-name"
                       value="<?php echo esc_attr($user->last_name); ?>"
                       aria-required="true" required>
            </div>
        </div>

        <div class="form-field">
            <label for="account_display_name">
                <?php esc_html_e('Emri i shfaqur', 'base-theme'); ?>
                <span class="text-sale ml-0.5" aria-hidden="true">*</span>
            </label>
            <input type="text" id="account_display_name" name="account_display_name"
                   value="<?php echo esc_attr($user->display_name); ?>"
                   aria-required="true" required>
            <p class="text-xs text-[#777] mt-1"><?php esc_html_e('Ky emër do të shfaqet në llogarinë tuaj dhe në komente.', 'base-theme'); ?></p>
        </div>

        <div class="form-field">
            <label for="account_email">
                <?php esc_html_e('Adresa e emailit', 'base-theme'); ?>
                <span class="text-sale ml-0.5" aria-hidden="true">*</span>
            </label>
            <input type="email" id="account_email" name="account_email"
                   autocomplete="email"
                   value="<?php echo esc_attr($user->user_email); ?>"
                   aria-required="true" required>
        </div>

        <?php do_action('woocommerce_edit_account_form_fields'); ?>
    </div>

    <!-- Password change -->
    <div class="account-form-section">
        <h3 class="account-form-section-title"><?php esc_html_e('Ndrysho fjalëkalimin', 'base-theme'); ?></h3>
        <p class="text-sm text-[#777] mb-4"><?php esc_html_e('Lërini bosh nëse nuk dëshironi të ndryshoni fjalëkalimin.', 'base-theme'); ?></p>

        <div class="form-field">
            <label for="password_current"><?php esc_html_e('Fjalëkalimi aktual', 'base-theme'); ?></label>
            <div class="auth-password-wrap">
                <input type="password" id="password_current" name="password_current"
                       autocomplete="current-password"
                       placeholder="<?php esc_attr_e('Fjalëkalimi aktual', 'base-theme'); ?>">
                <button type="button" class="auth-toggle-pw" aria-label="<?php esc_attr_e('Trego', 'base-theme'); ?>">
                    <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    <svg class="eye-hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
        </div>

        <div class="account-row">
            <div class="form-field">
                <label for="password_1"><?php esc_html_e('Fjalëkalimi i ri', 'base-theme'); ?></label>
                <div class="auth-password-wrap">
                    <input type="password" id="password_1" name="password_1"
                           autocomplete="new-password"
                           placeholder="<?php esc_attr_e('Fjalëkalimi i ri', 'base-theme'); ?>">
                    <button type="button" class="auth-toggle-pw" aria-label="<?php esc_attr_e('Trego', 'base-theme'); ?>">
                        <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg class="eye-hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
            </div>
            <div class="form-field">
                <label for="password_2"><?php esc_html_e('Konfirmo fjalëkalimin', 'base-theme'); ?></label>
                <input type="password" id="password_2" name="password_2"
                       autocomplete="new-password"
                       placeholder="<?php esc_attr_e('Konfirmo fjalëkalimin e ri', 'base-theme'); ?>">
            </div>
        </div>
    </div>

    <?php do_action('woocommerce_edit_account_form'); ?>

    <div class="flex items-center gap-3 mt-2">
        <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
        <input type="hidden" name="action" value="save_account_details">
        <button type="submit" name="save_account_details"
                value="<?php esc_attr_e('Ruaj ndryshimet', 'base-theme'); ?>"
                class="btn py-3 px-8">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <?php esc_html_e('Ruaj ndryshimet', 'base-theme'); ?>
        </button>
    </div>

    <?php do_action('woocommerce_edit_account_form_end'); ?>

</form>

<?php do_action('woocommerce_after_edit_account_form'); ?>
