<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

$shipping_packages = [];

if (WC()->cart->needs_shipping()) {
    $base_country = WC()->countries->get_base_country();

    if ($base_country && !WC()->customer->get_shipping_country()) {
        WC()->customer->set_shipping_country($checkout->get_value('shipping_country') ?: $base_country);
    }

    WC()->cart->calculate_shipping();
    $shipping_packages = WC()->shipping()->get_packages();
    $chosen_methods    = WC()->session->get('chosen_shipping_methods', []);

    foreach ($shipping_packages as $i => $package) {
        if (empty($chosen_methods[$i]) && !empty($package['rates'])) {
            $chosen_methods[$i] = array_key_first($package['rates']);
        }
    }

    WC()->session->set('chosen_shipping_methods', $chosen_methods);
}
?>

<form name="checkout" method="post"
      class="checkout woocommerce-checkout"
      action="<?php echo esc_url(wc_get_checkout_url()); ?>"
      enctype="multipart/form-data">

    <div class="woocommerce-notices-wrapper"></div>

    <div class="checkout-layout">

        <!-- ── Left: form ─────────────────────────────────────────────── -->
        <div class="checkout-form-column">

            <?php if ($checkout->get_checkout_fields()) : ?>

            <?php do_action('woocommerce_checkout_before_customer_details'); ?>

            <!-- 1. Klienti -->
            <?php if ($checkout->get_checkout_fields('billing')) : ?>
            <?php
            $billing_fields = $checkout->get_checkout_fields('billing');
            $labels = [
                'billing_first_name' => 'Emri',
                'billing_last_name'  => 'Mbiemri',
                'billing_phone'      => 'Numri i telefonit',
                'billing_email'      => 'E-mail',
            ];
            $bill_same = !$checkout->get_value('billing_address_1')
                      || ($checkout->get_value('billing_address_1') === $checkout->get_value('shipping_address_1'));
            ?>
            <div class="checkout-section" id="customer_details">
                <h2 class="checkout-section-title"><?php esc_html_e('Klienti', 'base-theme'); ?></h2>

                <div class="account-row mb-4">
                <?php foreach (['billing_first_name', 'billing_last_name'] as $key) :
                    if (!isset($billing_fields[$key])) continue;
                    $field = $billing_fields[$key]; ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($labels[$key] . (!empty($field['required']) ? ' *' : '')); ?></label>
                        <input type="text" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>"
                               placeholder="<?php echo esc_attr($labels[$key]); ?>"
                               value="<?php echo esc_attr($checkout->get_value($key)); ?>"
                               <?php echo !empty($field['required']) ? 'required' : ''; ?>>
                    </div>
                <?php endforeach; ?>
                </div>

                <div class="account-row mb-6">
                <?php foreach (['billing_phone', 'billing_email'] as $key) :
                    if (!isset($billing_fields[$key])) continue;
                    $field = $billing_fields[$key]; ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($labels[$key] . (!empty($field['required']) ? ' *' : '')); ?></label>
                        <input type="<?php echo $key === 'billing_email' ? 'email' : 'text'; ?>"
                               id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>"
                               placeholder="<?php echo esc_attr($labels[$key]); ?>"
                               value="<?php echo esc_attr($checkout->get_value($key)); ?>"
                               <?php echo !empty($field['required']) ? 'required' : ''; ?>>
                    </div>
                <?php endforeach; ?>
                </div>

                <?php if (WC()->cart->needs_shipping_address()) : ?>
                <div class="border-t border-[#e6e6e6] pt-5">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" id="billing_same_as_shipping" <?php checked($bill_same); ?> class="accent-brand">
                        <span class="text-sm"><?php esc_html_e('Adresa e faturës është e njëjtë me adresën e transportit', 'base-theme'); ?></span>
                    </label>
                </div>

                <!-- Hidden mirror fields — always submitted, mirror shipping when checkbox is checked -->
                <div id="billing_mirror_fields">
                    <input type="hidden" id="billing_country"   name="billing_country"   value="<?php echo esc_attr($checkout->get_value('billing_country')   ?: WC()->countries->get_base_country()); ?>">
                    <input type="hidden" id="billing_address_1" name="billing_address_1" value="<?php echo esc_attr($checkout->get_value('billing_address_1') ?: ''); ?>">
                    <input type="hidden" id="billing_postcode"  name="billing_postcode"  value="<?php echo esc_attr($checkout->get_value('billing_postcode')  ?: ''); ?>">
                    <input type="hidden" id="billing_city"      name="billing_city"      value="<?php echo esc_attr($checkout->get_value('billing_city')      ?: ''); ?>">
                    <input type="hidden" id="billing_state"     name="billing_state"     value="<?php echo esc_attr($checkout->get_value('billing_state')     ?: ''); ?>">
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- 2. Adresa e faturës — separate box, shown only when checkbox is unchecked -->
            <?php if (WC()->cart->needs_shipping_address()) : ?>
            <div id="billing_fields_wrap" class="checkout-section" <?php echo $bill_same ? 'style="display:none"' : ''; ?>>
                <h2 class="checkout-section-title"><?php esc_html_e('Adresa e faturës', 'base-theme'); ?></h2>
                <div class="account-row">
                    <div class="form-field sm:col-span-2">
                        <label for="billing_country_sel"><?php esc_html_e('Shteti', 'base-theme'); ?></label>
                        <select id="billing_country_sel" class="country_select" data-billing-field="billing_country">
                            <?php foreach (WC()->countries->get_countries() as $ckey => $cname) : ?>
                                <option value="<?php echo esc_attr($ckey); ?>"
                                    <?php selected($checkout->get_value('billing_country') ?: WC()->countries->get_base_country(), $ckey); ?>>
                                    <?php echo esc_html($cname); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-field sm:col-span-2">
                        <label for="billing_address_1_vis"><?php esc_html_e('Adresa', 'base-theme'); ?> *</label>
                        <input type="text" id="billing_address_1_vis" data-billing-field="billing_address_1"
                               placeholder="<?php esc_attr_e('Adresa', 'base-theme'); ?>"
                               value="<?php echo esc_attr($checkout->get_value('billing_address_1') ?: ''); ?>">
                    </div>
                    <div class="form-field">
                        <label for="billing_postcode_vis"><?php esc_html_e('Kodi postar', 'base-theme'); ?> *</label>
                        <input type="text" id="billing_postcode_vis" data-billing-field="billing_postcode"
                               placeholder="<?php esc_attr_e('Kodi postar', 'base-theme'); ?>"
                               value="<?php echo esc_attr($checkout->get_value('billing_postcode') ?: ''); ?>">
                    </div>
                    <div class="form-field">
                        <label for="billing_city_vis"><?php esc_html_e('Qyteti', 'base-theme'); ?> *</label>
                        <input type="text" id="billing_city_vis" data-billing-field="billing_city"
                               placeholder="<?php esc_attr_e('Qyteti', 'base-theme'); ?>"
                               value="<?php echo esc_attr($checkout->get_value('billing_city') ?: ''); ?>">
                    </div>
                </div>
            </div>

            <!-- 3. Adresa e transportit -->
            <div class="checkout-section" id="shipping_address">
                <h2 class="checkout-section-title"><?php esc_html_e('Adresa e transportit', 'base-theme'); ?></h2>
                <input type="hidden" name="ship_to_different_address" value="1">
                <input type="hidden" name="shipping_first_name" value="<?php echo esc_attr($checkout->get_value('shipping_first_name') ?: $checkout->get_value('billing_first_name')); ?>">
                <input type="hidden" name="shipping_last_name"  value="<?php echo esc_attr($checkout->get_value('shipping_last_name')  ?: $checkout->get_value('billing_last_name')); ?>">
                <input type="hidden" id="shipping_state" name="shipping_state" value="<?php echo esc_attr($checkout->get_value('shipping_state')); ?>">

                <div class="account-row">
                    <div class="form-field sm:col-span-2">
                        <label for="shipping_country"><?php esc_html_e('Shteti', 'base-theme'); ?></label>
                        <select id="shipping_country" name="shipping_country" class="country_select update_totals_on_change">
                            <?php foreach (WC()->countries->get_shipping_countries() as $ckey => $cname) : ?>
                                <option value="<?php echo esc_attr($ckey); ?>"
                                    <?php selected($checkout->get_value('shipping_country') ?: WC()->countries->get_base_country(), $ckey); ?>>
                                    <?php echo esc_html($cname); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-field sm:col-span-2">
                        <label for="shipping_address_1"><?php esc_html_e('Adresa', 'base-theme'); ?> *</label>
                        <input type="text" id="shipping_address_1" name="shipping_address_1"
                               placeholder="<?php esc_attr_e('Adresa', 'base-theme'); ?>"
                               value="<?php echo esc_attr($checkout->get_value('shipping_address_1')); ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="shipping_postcode"><?php esc_html_e('Kodi postar', 'base-theme'); ?> *</label>
                        <input type="text" id="shipping_postcode" name="shipping_postcode" class="update_totals_on_change"
                               placeholder="<?php esc_attr_e('Kodi postar', 'base-theme'); ?>"
                               value="<?php echo esc_attr($checkout->get_value('shipping_postcode')); ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="shipping_city"><?php esc_html_e('Qyteti', 'base-theme'); ?> *</label>
                        <input type="text" id="shipping_city" name="shipping_city" class="update_totals_on_change"
                               placeholder="<?php esc_attr_e('Qyteti', 'base-theme'); ?>"
                               value="<?php echo esc_attr($checkout->get_value('shipping_city')); ?>" required>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php do_action('woocommerce_checkout_after_customer_details'); ?>

            <!-- Shipping methods -->
            <?php if (WC()->cart->needs_shipping()) : ?>
            <div class="checkout-section">
                <h2 class="checkout-section-title text-xs font-semibold uppercase tracking-widest text-muted mb-3">
                    <?php esc_html_e('Mënyra e transportit', 'base-theme'); ?>
                </h2>
                <?php echo function_exists('emsaks_render_checkout_shipping_methods') ? emsaks_render_checkout_shipping_methods() : ''; ?>
            </div>
            <?php endif; ?>

	            <!-- Order note -->
	            <div class="checkout-section checkout-note">
	                <h2 class="checkout-section-title"><?php esc_html_e('Shkruaj porosi', 'base-theme'); ?></h2>
	                <label for="order_comments" class="sr-only">
	                    <?php esc_html_e('Shënime për porosinë', 'base-theme'); ?>
	                </label>
	                <textarea id="order_comments" name="order_comments" rows="3"
	                          class="w-full resize-none"
	                          placeholder="<?php esc_attr_e('Nëse dëshironi të lini një koment, ju mund ta shkruani këtu (opsionale):', 'base-theme'); ?>"><?php echo esc_textarea((string) $checkout->get_value('order_comments')); ?></textarea>
	            </div>

	            <!-- Payment -->
	            <div class="checkout-section checkout-payment-section">
	                <h2 class="checkout-section-title"><?php esc_html_e('Mënyra e pagesës', 'base-theme'); ?></h2>
	                <?php woocommerce_checkout_payment(); ?>
	            </div>

            <?php endif; ?>

        </div><!-- left col -->

        <!-- ── Right: order summary ────────────────────────────────── -->
        <div class="checkout-summary-column">
            <?php do_action('woocommerce_checkout_before_order_review'); ?>

            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php woocommerce_order_review(); ?>
            </div>

            <?php do_action('woocommerce_checkout_after_order_review'); ?>
        </div>

    </div><!-- .checkout-layout -->

</form>
<script>
(function(){
    var cb          = document.getElementById('billing_same_as_shipping');
    var mirrorWrap  = document.getElementById('billing_mirror_fields');
    var billingWrap = document.getElementById('billing_fields_wrap');
    if (!cb || !mirrorWrap || !billingWrap) return;

    /* Copy shipping → billing hidden mirror fields */
    function syncMirror() {
        var map = {
            billing_country:   document.getElementById('shipping_country'),
            billing_address_1: document.getElementById('shipping_address_1'),
            billing_postcode:  document.getElementById('shipping_postcode'),
            billing_city:      document.getElementById('shipping_city'),
            billing_state:     document.getElementById('shipping_state'),
        };
        Object.keys(map).forEach(function(id) {
            var src = map[id];
            var dst = document.getElementById(id);
            if (src && dst) dst.value = src.value;
        });
    }

    /* Copy visible billing form → billing hidden mirror fields */
    function syncFromVisible() {
        billingWrap.querySelectorAll('[data-billing-field]').forEach(function(el) {
            var dst = document.getElementById(el.dataset.billingField);
            if (dst) dst.value = el.value;
        });
    }

    function toggleBilling() {
        var same = cb.checked;

        /* Mirror fields always stay enabled — they're the ones with name="" that submit */
        /* Visible billing form */
        billingWrap.style.display = same ? 'none' : '';
        billingWrap.querySelectorAll('input, select').forEach(function(el){ el.disabled = same; });

        if (same) syncMirror(); else syncFromVisible();
    }

    /* Watch shipping fields to keep mirror in sync */
    ['shipping_country','shipping_address_1','shipping_postcode','shipping_city','shipping_state'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', function(){ if (cb.checked) syncMirror(); });
        if (el) el.addEventListener('input',  function(){ if (cb.checked) syncMirror(); });
    });

    /* Watch visible billing fields to keep hidden fields in sync */
    billingWrap.querySelectorAll('[data-billing-field]').forEach(function(el){
        el.addEventListener('change', syncFromVisible);
        el.addEventListener('input',  syncFromVisible);
    });

    cb.addEventListener('change', toggleBilling);
    toggleBilling(); /* set initial state */
})();
</script>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
