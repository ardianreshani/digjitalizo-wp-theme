<?php
defined('ABSPATH') || exit;

$order = wc_get_order($order_id);
if (!$order) return;

$order_items        = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', ['completed', 'processing']));
$downloads          = $order->get_downloadable_items();
$show_customer      = $order->get_user_id() === get_current_user_id();

if ($show_downloads) {
    wc_get_template('order/order-downloads.php', ['downloads' => $downloads, 'show_title' => true]);
}
?>

<section class="woocommerce-order-details order-detail-section">
    <?php do_action('woocommerce_order_details_before_order_table', $order); ?>

    <h3 class="view-order-section-title"><?php esc_html_e('Produktet e porositura', 'base-theme'); ?></h3>

    <!-- Items list -->
    <div class="order-items-card">
        <?php
        do_action('woocommerce_order_details_before_order_table_items', $order);
        foreach ($order_items as $item_id => $item) :
            $product          = $item->get_product();
            $is_visible       = $product && $product->is_visible();
            $product_link     = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);
            $img_id           = $product ? $product->get_image_id() : 0;
            $img_url          = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : wc_placeholder_img_src('thumbnail');
            $qty              = $item->get_quantity();
            $refunded_qty     = $order->get_qty_refunded_for_item($item_id);
            $qty_display      = $refunded_qty
                ? '<del>' . $qty . '</del> <ins>' . ($qty - ($refunded_qty * -1)) . '</ins>'
                : $qty;
        ?>
        <div class="order-item-row">
            <div class="order-item-img-wrap">
                <img src="<?php echo esc_url($img_url); ?>"
                     alt="<?php echo esc_attr($item->get_name()); ?>"
                     class="order-item-img">
            </div>
            <div class="order-item-info">
                <div class="order-item-name">
                    <?php if ($product_link) : ?>
                        <a href="<?php echo esc_url($product_link); ?>"><?php echo esc_html(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, $is_visible)); ?></a>
                    <?php else : ?>
                        <?php echo esc_html(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, $is_visible)); ?>
                    <?php endif; ?>
                </div>
                <?php wc_display_item_meta($item); ?>
                <?php if ($show_purchase_note && $product && $product->get_purchase_note()) : ?>
                <div class="order-item-note"><?php echo wpautop(do_shortcode(wp_kses_post($product->get_purchase_note()))); ?></div>
                <?php endif; ?>
            </div>
            <div class="order-item-qty">
                &times;&nbsp;<?php echo wp_kses_post($qty_display); ?>
            </div>
            <div class="order-item-total">
                <?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
            </div>
        </div>
        <?php endforeach;
        do_action('woocommerce_order_details_after_order_table_items', $order); ?>
    </div>

    <!-- Totals -->
    <div class="order-totals-card">
        <?php foreach ($order->get_order_item_totals() as $key => $total) : ?>
        <div class="order-total-row <?php echo esc_attr($key); ?>">
            <span class="order-total-label"><?php echo esc_html($total['label']); ?></span>
            <span class="order-total-value"><?php echo wp_kses_post($total['value']); ?></span>
        </div>
        <?php endforeach; ?>
        <?php if ($order->get_customer_note()) : ?>
        <div class="order-total-row customer-note">
            <span class="order-total-label"><?php esc_html_e('Shënim:', 'base-theme'); ?></span>
            <span class="order-total-value">
                <?php echo wp_kses(nl2br(wc_wptexturize_order_note($order->get_customer_note())), ['br' => []]); ?>
            </span>
        </div>
        <?php endif; ?>
    </div>

    <?php do_action('woocommerce_order_details_after_order_table', $order); ?>
</section>

<?php
do_action('woocommerce_after_order_details', $order);

if ($show_customer) {
    wc_get_template('order/order-details-customer.php', ['order' => $order]);
}
