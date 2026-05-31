<?php
$is_checkout_flow = (is_cart() || (is_checkout() && !is_wc_endpoint_url('order-received')));
$footer_copyright = function_exists('get_field') ? get_field('footer_copyright', 'option') : '';
$footer_copyright = str_replace('{year}', date('Y'), $footer_copyright);

$render_footer_bottom = static function ($class_name) use ($footer_copyright) {
    $has_menu = has_nav_menu('copyright');
    if ($footer_copyright === '' && !$has_menu) {
        return;
    }
    ?>
    <div class="<?php echo esc_attr($class_name); ?>">
        <?php if ($footer_copyright !== '') : ?>
            <span><?php echo esc_html($footer_copyright); ?></span>
        <?php endif; ?>
        <?php if ($has_menu) : ?>
            <?php wp_nav_menu([
                'theme_location' => 'copyright',
                'container'      => false,
                'menu_class'     => 'footer-copyright-menu',
                'fallback_cb'    => '__return_false',
                'depth'          => 1,
            ]); ?>
        <?php endif; ?>
    </div>
    <?php
};
?>

<?php if ($is_checkout_flow) : ?>

<footer class="footer-minimal">
    <?php $render_footer_bottom('container footer-minimal-inner'); ?>
</footer>

<?php else : ?>
<?php emsaks_render_brands_section(); ?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-main">
            <!-- Logo + contact -->
            <div class="footer-col">
                <?php
                $footer_logo_url = THEME_URI . '/assets/images/logo.svg';
                $footer_logo_alt = get_bloginfo('name');
                $footer_logo = function_exists('get_field') ? get_field('company_logo_light', 'option') : null;

                if (is_array($footer_logo) && !empty($footer_logo['url'])) {
                    $footer_logo_url = $footer_logo['url'];
                    $footer_logo_alt = !empty($footer_logo['alt']) ? $footer_logo['alt'] : $footer_logo_alt;
                } elseif (is_string($footer_logo) && $footer_logo !== '') {
                    $footer_logo_url = $footer_logo;
                }
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo-link">
                    <img src="<?php echo esc_url($footer_logo_url); ?>"
                         alt="<?php echo esc_attr($footer_logo_alt); ?>" class="site-logo-img">
                </a>

                <?php
                $phone   = function_exists('get_field') ? get_field('company_phone', 'option')   : '+383 48 400 096';
                $email   = function_exists('get_field') ? get_field('company_email', 'option')   : 'info@emsaks.com';
                $address = function_exists('get_field') ? get_field('company_address', 'option') : 'Brigada 123, Suhareke, Kosovë';
                $map_link = function_exists('get_field') ? get_field('company_map_link', 'option') : [];
                $map_url = is_array($map_link) ? ($map_link['url'] ?? '') : '';
                $map_target = is_array($map_link) ? ($map_link['target'] ?? '') : '';
                ?>

                <h4 class="footer-col-title">Kontakti</h4>

                <?php if ($phone) : ?>
                <div class="footer-contact-item">
	                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
	                        <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd"/>
	                    </svg>
	                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>">
                            <?php echo esc_html($phone); ?>
                        </a>
	                </div>
	                <?php endif; ?>

                <?php if ($email) : ?>
                <div class="footer-contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                        <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                    </svg>
                    <a href="mailto:<?php echo esc_attr($email); ?>">
                        <?php echo esc_html($email); ?>
                    </a>
                </div>
                <?php endif; ?>

                <?php if ($address) : ?>
                <div class="footer-contact-item">
	                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
	                        <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.814-5.259 3.814-9.077A8.25 8.25 0 0012 3.75a8.25 8.25 0 00-8.25 8.25c0 3.818 1.87 6.994 3.814 9.077a19.58 19.58 0 002.517 2.196 16.954 16.954 0 001.31.878zm.46-13.851a3 3 0 100 6 3 3 0 000-6z" clip-rule="evenodd"/>
	                    </svg>
                        <?php if ($map_url) : ?>
                            <a href="<?php echo esc_url($map_url); ?>"<?php echo $map_target ? ' target="' . esc_attr($map_target) . '"' : ''; ?><?php echo $map_target === '_blank' ? ' rel="noopener"' : ''; ?>>
                                <?php echo esc_html($address); ?>
                            </a>
                        <?php else : ?>
                            <span><?php echo esc_html($address); ?></span>
                        <?php endif; ?>
	                </div>
	                <?php endif; ?>
            </div>

            <!-- Navigation -->
            <div class="footer-col">
                <h4 class="footer-col-title">Navigimi</h4>
                <?php wp_nav_menu([
                    'theme_location' => 'footer',
                    'container'      => false,
                    'fallback_cb'    => '__return_false',
                ]); ?>
            </div>

            <!-- Account links -->
            <div class="footer-col">
                <h4 class="footer-col-title">Llogaria</h4>
                <?php if (has_nav_menu('footer_account')) : ?>
                    <?php wp_nav_menu([
                        'theme_location' => 'footer_account',
                        'container'      => false,
                        'fallback_cb'    => '__return_false',
                    ]); ?>
                <?php elseif (function_exists('wc_get_account_page_permalink')) : ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>">Shporta ime</a>
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">Porositë</a>
                <a href="<?php echo esc_url(wc_get_account_page_permalink()); ?>">Llogaria ime</a>
                <?php endif; ?>
            </div>

            <!-- Social -->
            <div class="footer-col">
                <h4 class="footer-col-title">Na Ndiqni</h4>
                <div class="footer-social">
                    <?php
                    $fb  = function_exists('get_field') ? get_field('social_facebook', 'option')  : '#';
                    $ig  = function_exists('get_field') ? get_field('social_instagram', 'option') : '#';
                    $tt  = function_exists('get_field') ? get_field('social_tiktok', 'option')    : '#';
                    ?>
                    <?php if ($fb) : ?>
                    <a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <?php if ($ig) : ?>
                    <a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <?php if ($tt) : ?>
                    <a href="<?php echo esc_url($tt); ?>" target="_blank" rel="noopener" aria-label="TikTok">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.77a4.86 4.86 0 01-1.01-.08z"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    <?php $render_footer_bottom('footer-bottom'); ?>
</footer>

<?php endif; ?>

<?php
$_inquiry_form_id = function_exists('get_field') ? (int) get_field('woo_inquiry_form', 'option') : 0;
if ($_inquiry_form_id) : ?>
<div id="inquiry-dialog" class="inquiry-dialog" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="inquiry-dialog-title">
    <div class="inquiry-dialog-backdrop"></div>
    <div class="inquiry-dialog-box">
        <button class="inquiry-dialog-close" aria-label="<?php esc_attr_e('Mbyll', 'base-theme'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <h3 id="inquiry-dialog-title" class="inquiry-dialog-title"><?php esc_html_e('Porosit produktin', 'base-theme'); ?></h3>
        <p class="inquiry-dialog-product-name"></p>
        <div class="inquiry-dialog-form">
            <?php echo do_shortcode('[contact-form-7 id="' . $_inquiry_form_id . '"]'); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
