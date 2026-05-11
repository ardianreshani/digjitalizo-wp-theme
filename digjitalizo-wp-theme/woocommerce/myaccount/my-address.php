<?php
defined('ABSPATH') || exit;

$customer_id = get_current_user_id();

if (!wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
    $get_addresses = apply_filters('woocommerce_my_account_get_addresses', [
        'billing'  => __('Adresa e faturimit', 'base-theme'),
        'shipping' => __('Adresa e dërgimit', 'base-theme'),
    ], $customer_id);
} else {
    $get_addresses = apply_filters('woocommerce_my_account_get_addresses', [
        'billing' => __('Adresa e faturimit', 'base-theme'),
    ], $customer_id);
}
?>

<div class="account-section-header">
    <h2 class="account-section-title"><?php esc_html_e('Adresat e mia', 'base-theme'); ?></h2>
    <p class="account-section-sub"><?php echo esc_html(apply_filters('woocommerce_my_account_my_address_description', __('Adresat e mëposhtme do të përdoren si parazgjedhje gjatë blerjes.', 'base-theme'))); ?></p>
</div>

<div class="address-grid">
    <?php foreach ($get_addresses as $name => $address_title) :
        $address = wc_get_account_formatted_address($name);
        $edit_url = wc_get_endpoint_url('edit-address', $name, wc_get_page_permalink('myaccount'));
    ?>
    <div class="address-card">
        <div class="address-card-head">
            <h3 class="address-card-title">
                <?php if ($name === 'billing') : ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                </svg>
                <?php else : ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                </svg>
                <?php endif; ?>
                <?php echo esc_html($address_title); ?>
            </h3>
            <a href="<?php echo esc_url($edit_url); ?>" class="address-card-edit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                </svg>
                <?php echo $address ? esc_html__('Ndrysho', 'base-theme') : esc_html__('Shto', 'base-theme'); ?>
            </a>
        </div>

        <address class="address-card-body">
            <?php if ($address) :
                echo wp_kses_post($address);
                do_action('woocommerce_my_account_after_my_address', $name);
            else : ?>
            <p class="address-card-empty">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-[#ccc] mb-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                </svg>
                <?php esc_html_e('Nuk keni vendosur ende këtë adresë.', 'base-theme'); ?>
            </p>
            <?php endif; ?>
        </address>

        <?php if (!$address) : ?>
        <a href="<?php echo esc_url($edit_url); ?>" class="address-card-add-btn">
            <?php esc_html_e('Shto adresë', 'base-theme'); ?>
        </a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
