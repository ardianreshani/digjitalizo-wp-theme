<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_customer_login_form');

$registration_enabled = 'yes' === get_option('woocommerce_enable_myaccount_registration');
?>

<div class="login-page">

    <div class="login-layout<?php echo $registration_enabled ? ' login-layout--split' : ' login-layout--center'; ?>">

        <!-- ── Login ───────────────────────────────────────────────── -->
        <div class="auth-card" id="login">

            <div class="auth-card-header">
                <div class="auth-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                </div>
                <h2 class="auth-card-title"><?php esc_html_e('Identifikohu', 'base-theme'); ?></h2>
                <p class="auth-card-sub"><?php esc_html_e('Hyr në llogarinë tënde', 'base-theme'); ?></p>
            </div>

            <form class="woocommerce-form woocommerce-form-login" method="post" novalidate>

                <?php do_action('woocommerce_login_form_start'); ?>

                <div class="auth-fields">
                    <div class="form-field">
                        <label for="username">
                            <?php esc_html_e('Email ose emri i përdoruesit', 'base-theme'); ?>
                            <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input type="text"
                               id="username"
                               name="username"
                               autocomplete="username"
                               placeholder="<?php esc_attr_e('Shkruaj email-in ose username', 'base-theme'); ?>"
                               value="<?php echo (!empty($_POST['username']) && is_string($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                               required aria-required="true">
                    </div>

                    <div class="form-field">
                        <label for="password">
                            <?php esc_html_e('Fjalëkalimi', 'base-theme'); ?>
                            <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <div class="auth-password-wrap">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   autocomplete="current-password"
                                   placeholder="<?php esc_attr_e('Fjalëkalimi', 'base-theme'); ?>"
                                   required aria-required="true">
                            <button type="button" class="auth-toggle-pw" aria-label="<?php esc_attr_e('Trego fjalëkalimin', 'base-theme'); ?>">
                                <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg class="eye-hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="auth-remember-row">
                        <label class="auth-checkbox-label">
                            <input type="checkbox" name="rememberme" id="rememberme" value="forever"
                                   class="accent-brand">
                            <span><?php esc_html_e('Mbaj mend llogarinë', 'base-theme'); ?></span>
                        </label>
                        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="auth-lost-pw">
                            <?php esc_html_e('Harruat fjalëkalimin?', 'base-theme'); ?>
                        </a>
                    </div>
                </div>

                <?php do_action('woocommerce_login_form'); ?>

                <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>

                <button type="submit" name="login" value="<?php esc_attr_e('Identifikohu', 'base-theme'); ?>"
                        class="btn w-full py-3 text-base mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                    </svg>
                    <?php esc_html_e('Identifikohu', 'base-theme'); ?>
                </button>

                <?php do_action('woocommerce_login_form_end'); ?>

            </form>
        </div>

        <?php if ($registration_enabled) : ?>

        <!-- ── Register ───────────────────────────────────────────── -->
        <div class="auth-card" id="register">

            <div class="auth-card-header">
                <div class="auth-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>
                    </svg>
                </div>
                <h2 class="auth-card-title"><?php esc_html_e('Regjistrohu', 'base-theme'); ?></h2>
                <p class="auth-card-sub"><?php esc_html_e('Krijo llogarinë tënde falas', 'base-theme'); ?></p>
            </div>

            <form method="post" class="woocommerce-form woocommerce-form-register" <?php do_action('woocommerce_register_form_tag'); ?>>

                <?php do_action('woocommerce_register_form_start'); ?>

                <div class="auth-fields">
                    <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                    <div class="form-field">
                        <label for="reg_username">
                            <?php esc_html_e('Emri i përdoruesit', 'base-theme'); ?>
                            <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input type="text"
                               id="reg_username"
                               name="username"
                               autocomplete="username"
                               placeholder="<?php esc_attr_e('Emri i përdoruesit', 'base-theme'); ?>"
                               value="<?php echo !empty($_POST['username']) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                               required aria-required="true">
                    </div>
                    <?php endif; ?>

                    <div class="form-field">
                        <label for="reg_email">
                            <?php esc_html_e('Adresa e emailit', 'base-theme'); ?>
                            <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input type="email"
                               id="reg_email"
                               name="email"
                               autocomplete="email"
                               placeholder="<?php esc_attr_e('email@shembull.com', 'base-theme'); ?>"
                               value="<?php echo !empty($_POST['email']) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>"
                               required aria-required="true">
                    </div>

                    <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                    <div class="form-field">
                        <label for="reg_password">
                            <?php esc_html_e('Fjalëkalimi', 'base-theme'); ?>
                            <span class="text-sale ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <div class="auth-password-wrap">
                            <input type="password"
                                   id="reg_password"
                                   name="password"
                                   autocomplete="new-password"
                                   placeholder="<?php esc_attr_e('Krijo fjalëkalim të fortë', 'base-theme'); ?>"
                                   required aria-required="true">
                            <button type="button" class="auth-toggle-pw" aria-label="<?php esc_attr_e('Trego fjalëkalimin', 'base-theme'); ?>">
                                <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg class="eye-hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <?php else : ?>
                    <div class="auth-info-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                        </svg>
                        <span><?php esc_html_e('Fjalëkalimi do të dërgohet në emailin tuaj.', 'base-theme'); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php do_action('woocommerce_register_form'); ?>

                <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>

                <button type="submit" name="register" value="<?php esc_attr_e('Regjistrohu', 'base-theme'); ?>"
                        class="btn-outline w-full py-3 text-base mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>
                    </svg>
                    <?php esc_html_e('Krijo llogarinë', 'base-theme'); ?>
                </button>

                <?php do_action('woocommerce_register_form_end'); ?>

            </form>
        </div>

        <?php endif; ?>

    </div>

</div>

<?php do_action('woocommerce_after_customer_login_form'); ?>
