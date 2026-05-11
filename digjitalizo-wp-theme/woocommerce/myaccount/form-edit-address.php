<?php
defined('ABSPATH') || exit;

$page_title = 'billing' === $load_address
    ? esc_html__('Adresa e faturimit', 'base-theme')
    : esc_html__('Adresa e dërgimit', 'base-theme');

do_action('woocommerce_before_edit_account_address_form');
?>

<?php if (!$load_address) :
    wc_get_template('myaccount/my-address.php');
else : ?>

<div class="account-section-header">
    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount') . 'edit-address/'); ?>" class="account-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
        <?php esc_html_e('Kthehu te adresat', 'base-theme'); ?>
    </a>
    <h2 class="account-section-title mt-2">
        <?php echo esc_html(apply_filters('woocommerce_my_account_edit_address_title', $page_title, $load_address)); ?>
    </h2>
</div>

<form method="post" novalidate class="account-address-form">

    <div class="woocommerce-address-fields">
        <?php do_action("woocommerce_before_edit_address_form_{$load_address}"); ?>

        <div class="wc-address-fields-card mb-5">
            <div class="wc-address-fields-grid">
                <?php foreach ($address as $key => $field) :
                    woocommerce_form_field($key, $field, wc_get_post_data_by_key($key, $field['value']));
                endforeach; ?>
            </div>
        </div>

        <?php do_action("woocommerce_after_edit_address_form_{$load_address}"); ?>

        <div class="mt-5">
            <?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
            <input type="hidden" name="action" value="edit_address">
            <button type="submit" name="save_address"
                    value="<?php esc_attr_e('Ruaj adresën', 'base-theme'); ?>"
                    class="btn py-3 px-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <?php esc_html_e('Ruaj adresën', 'base-theme'); ?>
            </button>
        </div>
    </div>

</form>

<?php endif; ?>

<?php do_action('woocommerce_after_edit_account_address_form'); ?>
