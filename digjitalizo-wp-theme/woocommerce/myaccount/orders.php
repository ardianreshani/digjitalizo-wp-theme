<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders);

$status_classes = [
    'pending'    => 'order-status--pending',
    'processing' => 'order-status--processing',
    'on-hold'    => 'order-status--on-hold',
    'completed'  => 'order-status--completed',
    'cancelled'  => 'order-status--cancelled',
    'refunded'   => 'order-status--refunded',
    'failed'     => 'order-status--failed',
];
?>

<div class="account-section-header">
    <h2 class="account-section-title"><?php esc_html_e('Porositë e mia', 'base-theme'); ?></h2>
</div>

<?php if ($has_orders) : ?>

<div class="orders-list">
    <?php foreach ($customer_orders->orders as $customer_order) :
        $order      = wc_get_order($customer_order);
        $item_count = $order->get_item_count() - $order->get_item_count_refunded();
        $status     = $order->get_status();
        $status_cls = $status_classes[$status] ?? '';
    ?>
    <div class="order-card <?php echo esc_attr('order-status-' . $status); ?>">

        <div class="order-card-head">
            <div class="order-card-meta">
                <span class="order-card-number">
                    #<?php echo esc_html($order->get_order_number()); ?>
                </span>
                <span class="order-card-date">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5"/>
                    </svg>
                    <?php echo esc_html(wc_format_datetime($order->get_date_created(), get_option('date_format'))); ?>
                </span>
            </div>

            <span class="order-status-badge <?php echo esc_attr($status_cls); ?>">
                <?php echo esc_html(wc_get_order_status_name($status)); ?>
            </span>
        </div>

        <div class="order-card-body">
            <!-- Product thumbnails -->
            <div class="order-card-thumbs">
                <?php foreach (array_slice($order->get_items(), 0, 3) as $item) :
                    $product = $item->get_product();
                    if (!$product) continue;
                    $img_url = wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail');
                ?>
                <img src="<?php echo esc_url($img_url); ?>"
                     alt="<?php echo esc_attr($item->get_name()); ?>"
                     class="order-card-thumb"
                     title="<?php echo esc_attr($item->get_name()); ?>">
                <?php endforeach; ?>
                <?php if ($item_count > 3) : ?>
                <span class="order-card-more">+<?php echo esc_html($item_count - 3); ?></span>
                <?php endif; ?>
            </div>

            <div class="order-card-total">
                <div class="text-xs text-[#777] mb-0.5"><?php esc_html_e('Total', 'base-theme'); ?></div>
                <div class="font-bold text-base text-[#191c1d]"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></div>
                <div class="text-xs text-[#777]"><?php printf(_n('%d produkt', '%d produkte', $item_count, 'base-theme'), $item_count); ?></div>
            </div>
        </div>

        <div class="order-card-actions">
            <?php $actions = wc_get_account_orders_actions($order);
            foreach ($actions as $key => $action) : ?>
            <a href="<?php echo esc_url($action['url']); ?>"
               class="order-action-btn order-action-btn--<?php echo esc_attr($key); ?>"
               aria-label="<?php echo esc_attr($action['aria-label'] ?? $action['name']); ?>">
                <?php if ($key === 'view') : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                <?php elseif ($key === 'cancel') : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                <?php endif; ?>
                <?php echo esc_html($action['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
    <?php endforeach; ?>
</div>

<?php do_action('woocommerce_before_account_orders_pagination'); ?>

<?php if (1 < $customer_orders->max_num_pages) : ?>
<div class="account-pagination">
    <?php if (1 !== $current_page) : ?>
    <a class="account-page-btn" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">
        &larr; <?php esc_html_e('Para', 'base-theme'); ?>
    </a>
    <?php endif; ?>
    <?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
    <a class="account-page-btn" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">
        <?php esc_html_e('Tjetër', 'base-theme'); ?> &rarr;
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php else : ?>

<div class="account-empty-state">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-14 h-14 text-[#ccc] mb-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
    </svg>
    <p class="text-[#555] mb-4"><?php esc_html_e('Nuk keni bërë ende asnjë porosi.', 'base-theme'); ?></p>
    <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>"
       class="btn">
        <?php esc_html_e('Shfletoni produktet', 'base-theme'); ?>
    </a>
</div>

<?php endif; ?>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>
