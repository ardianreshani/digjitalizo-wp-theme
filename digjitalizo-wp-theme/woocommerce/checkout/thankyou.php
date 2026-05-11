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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.752.097 1.126.153A2.212 2.212 0 0118 8.653v4.097A2.25 2.25 0 0115.75 15h-.241l.305 1.984A1.75 1.75 0 0114.084 19H5.915a1.75 1.75 0 01-1.73-2.016L4.492 15H4.25A2.25 2.25 0 012 12.75V8.653c0-1.082.775-2.034 1.874-2.198.374-.056.749-.107 1.126-.153V2.75zm4.5 13a.75.75 0 01.75-.75h-.5a.75.75 0 01-.75.75v.75h.5v-.75zm2.25-.75a.75.75 0 01.75.75v.75h.5v-.75a.75.75 0 01-.75-.75h-.5z" clip-rule="evenodd"/>
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
