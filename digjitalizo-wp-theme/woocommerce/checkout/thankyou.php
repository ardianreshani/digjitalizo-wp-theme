<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_thankyou', $order->get_id());
?>

	<div class="thankyou-page">

    <?php if ($order) : ?>

    <!-- Success banner -->
    <div class="thankyou-banner">
        <h2>
            <?php printf(
                esc_html__('Faleminderit për blerjen, %s!', 'base-theme'),
                esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name())
            ); ?>
        </h2>
        <p><?php esc_html_e('Së shpejti do të merrni një konfirmim dhe faturë me email.', 'base-theme'); ?></p>
    </div>

    <!-- Order details card -->
    <div class="thankyou-card">
        <div class="thankyou-card-title">
            <?php printf(
                esc_html__('Numri i porosisë: %s', 'base-theme'),
                '<strong>' . esc_html($order->get_order_number()) . '</strong>'
            ); ?>
        </div>

        <!-- 3-col meta -->
        <div class="thankyou-meta-grid">
            <div class="thankyou-meta-col">
                <p class="thankyou-meta-col-title"><?php esc_html_e('Adresa e faturimit', 'base-theme'); ?></p>
                <p>
                    <?php echo esc_html(trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name())); ?><br>
                    <?php if ($a = $order->get_billing_address_1()) echo esc_html($a) . '<br>'; ?>
                    <?php echo esc_html(trim($order->get_billing_postcode() . ' ' . $order->get_billing_city())); ?><br>
                    <?php if ($e = $order->get_billing_email()) echo esc_html($e) . '<br>'; ?>
                    <?php echo esc_html($order->get_billing_phone()); ?>
                </p>
            </div>
            <div class="thankyou-meta-col">
                <p class="thankyou-meta-col-title"><?php esc_html_e('Adresa e dorëzimit', 'base-theme'); ?></p>
                <p>
                    <?php echo esc_html(trim($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name())); ?><br>
                    <?php if ($sa = $order->get_shipping_address_1()) echo esc_html($sa) . '<br>'; ?>
                    <?php echo esc_html(trim($order->get_shipping_postcode() . ' ' . $order->get_shipping_city())); ?>
                </p>
                <?php if ($order->get_shipping_method()) : ?>
                <p class="thankyou-meta-col-title mt-3"><?php esc_html_e('Mënyra e transportit', 'base-theme'); ?></p>
                <p><?php echo esc_html($order->get_shipping_method()); ?></p>
                <?php endif; ?>
            </div>
            <div class="thankyou-meta-col">
                <p class="thankyou-meta-col-title"><?php esc_html_e('Mënyra e pagesës', 'base-theme'); ?></p>
                <p><?php echo esc_html($order->get_payment_method_title()); ?></p>
            </div>
        </div>

        <!-- Order summary -->
        <div class="border-t border-[#f0f0f0] pt-4 mb-4">
            <p class="thankyou-meta-col-title mb-3"><?php esc_html_e('Përmbledhje e porosisë', 'base-theme'); ?></p>

            <div class="thankyou-summary-row">
                <span><?php printf(esc_html__('Produkte (%d)', 'base-theme'), $order->get_item_count()); ?></span>
                <span><?php echo wc_price($order->get_subtotal()); ?></span>
            </div>
	            <?php if ($order->get_shipping_method()) : ?>
	            <div class="thankyou-summary-row">
	                <span><?php esc_html_e('Kostot e transportit', 'base-theme'); ?></span>
	                <span><?php echo wc_price($order->get_shipping_total()); ?></span>
	            </div>
	            <?php endif; ?>
	            <?php foreach ($order->get_coupon_codes() as $coupon_code) : ?>
	                <div class="thankyou-summary-row discount">
	                    <span><?php printf(esc_html__('Kodi i përdorur: %s', 'base-theme'), '<strong>' . esc_html(strtoupper($coupon_code)) . '</strong>'); ?></span>
	                    <span>-<?php echo wc_price($order->get_discount_total()); ?></span>
	                </div>
	            <?php endforeach; ?>
            <?php if (wc_tax_enabled() && $order->get_total_tax() > 0) : ?>
            <div class="thankyou-summary-row">
                <span><?php esc_html_e('TVSH', 'base-theme'); ?></span>
                <span><?php echo wc_price($order->get_total_tax()); ?></span>
            </div>
            <?php endif; ?>
            <div class="thankyou-summary-row total">
                <span><?php esc_html_e('Total', 'base-theme'); ?></span>
                <span><?php echo $order->get_formatted_order_total(); ?></span>
            </div>
        </div>

        <!-- Ordered products -->
        <div class="border-t border-[#f0f0f0] pt-4">
            <p class="thankyou-meta-col-title mb-3"><?php esc_html_e('Produkte të porositura', 'base-theme'); ?></p>
            <?php foreach ($order->get_items() as $item_id => $item) :
                $product = $item->get_product();
                if (!$product) continue;
            ?>
            <div class="thankyou-product-row">
                <img class="thankyou-product-img"
                     src="<?php echo esc_url(wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail')); ?>"
                     alt="<?php echo esc_attr($product->get_name()); ?>">
	                <div class="thankyou-product-info">
	                    <div class="thankyou-product-name"><?php echo esc_html($item->get_name()); ?></div>
	                </div>
	                <div class="thankyou-product-meta">
	                    <?php echo esc_html($item->get_quantity()); ?> x <?php echo wc_price($product->get_price()); ?>
	                </div>
	                <div class="thankyou-product-price">
                    <?php if ($product->is_on_sale()) : ?>
                        <span class="was"><?php echo wc_price($product->get_regular_price() * $item->get_quantity()); ?></span>
                    <?php endif; ?>
                    <?php echo wc_price($item->get_total()); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Print -->
        <div class="text-center">
            <button onclick="window.print()" class="print-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                <?php esc_html_e('Printo faturën', 'base-theme'); ?>
            </button>
        </div>
    </div>

    <?php else : ?>
    <div class="thankyou-banner">
        <h2><?php esc_html_e('Faleminderit për porosinë tuaj!', 'base-theme'); ?></h2>
        <p><?php esc_html_e('Porosia juaj u pranua. Konfirmimi do të dërgohet me email.', 'base-theme'); ?></p>
    </div>
    <?php endif; ?>

    <?php do_action('woocommerce_thankyou', $order->get_id()); ?>

</div>
