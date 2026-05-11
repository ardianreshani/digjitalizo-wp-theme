<?php
defined('ABSPATH') || exit;

$status_classes = [
    'pending'    => 'order-status--pending',
    'processing' => 'order-status--processing',
    'on-hold'    => 'order-status--on-hold',
    'completed'  => 'order-status--completed',
    'cancelled'  => 'order-status--cancelled',
    'refunded'   => 'order-status--refunded',
    'failed'     => 'order-status--failed',
];

$status     = $order->get_status();
$status_cls = $status_classes[$status] ?? '';
$notes      = $order->get_customer_order_notes();
$actions    = array_filter(
    wc_get_account_orders_actions($order),
    fn($key) => 'view' !== $key,
    ARRAY_FILTER_USE_KEY
);
?>

<!-- Back link + header -->
<div class="account-section-header">
    <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'))); ?>" class="account-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
        <?php esc_html_e('Kthehu te porositë', 'base-theme'); ?>
    </a>

    <div class="view-order-header">
        <div class="view-order-header-left">
            <h2 class="account-section-title">
                <?php printf(esc_html__('Porosia #%s', 'base-theme'), esc_html($order->get_order_number())); ?>
            </h2>
            <div class="view-order-meta">
                <span class="order-status-badge <?php echo esc_attr($status_cls); ?>">
                    <?php echo esc_html(wc_get_order_status_name($status)); ?>
                </span>
                <span class="view-order-date">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5"/>
                    </svg>
                    <?php echo esc_html(wc_format_datetime($order->get_date_created(), get_option('date_format'))); ?>
                </span>
            </div>
        </div>

        <?php if (!empty($actions)) : ?>
        <div class="view-order-actions">
            <?php foreach ($actions as $key => $action) : ?>
            <a href="<?php echo esc_url($action['url']); ?>"
               class="order-action-btn order-action-btn--<?php echo esc_attr($key); ?>"
               aria-label="<?php echo esc_attr($action['aria-label'] ?? $action['name']); ?>">
                <?php echo esc_html($action['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($notes) : ?>
<div class="view-order-notes">
    <h3 class="view-order-section-title"><?php esc_html_e('Përditësimet e porosisë', 'base-theme'); ?></h3>
    <div class="order-notes-list">
        <?php foreach ($notes as $note) : ?>
        <div class="order-note">
            <span class="order-note-date">
                <?php echo esc_html(date_i18n(get_option('date_format') . ', H:i', strtotime($note->comment_date))); ?>
            </span>
            <p class="order-note-text"><?php echo wp_kses_post(wpautop(wptexturize($note->comment_content))); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php do_action('woocommerce_view_order', $order_id); ?>
