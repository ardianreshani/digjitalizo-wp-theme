<?php
defined('ABSPATH') || exit;

$label = !empty($args['product_name'])
    ? sprintf(esc_html__('%s quantity', 'woocommerce'), wp_strip_all_tags($args['product_name']))
    : esc_html__('Quantity', 'woocommerce');
?>
<div class="qty-wrap">

    <button type="button" class="qty-btn qty-btn--minus" aria-label="<?php esc_attr_e('Decrease quantity', 'woocommerce'); ?>">
        <svg width="14" height="2" viewBox="0 0 14 2" fill="none" aria-hidden="true">
            <rect width="14" height="2" rx="1" fill="currentColor"/>
        </svg>
    </button>

    <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo esc_attr($label); ?></label>
    <input
        type="<?php echo esc_attr($type); ?>"
        <?php echo $readonly ? 'readonly="readonly"' : ''; ?>
        id="<?php echo esc_attr($input_id); ?>"
        class="qty-input <?php echo esc_attr(join(' ', (array) $classes)); ?>"
        name="<?php echo esc_attr($input_name); ?>"
        value="<?php echo esc_attr($input_value); ?>"
        aria-label="<?php esc_attr_e('Product quantity', 'woocommerce'); ?>"
        min="<?php echo esc_attr($min_value); ?>"
        <?php if (0 < $max_value) : ?>max="<?php echo esc_attr($max_value); ?>"<?php endif; ?>
        <?php if (!$readonly) : ?>
            step="<?php echo esc_attr($step); ?>"
            placeholder="<?php echo esc_attr($placeholder); ?>"
            inputmode="<?php echo esc_attr($inputmode); ?>"
            autocomplete="<?php echo esc_attr($autocomplete ?? 'on'); ?>"
        <?php endif; ?>
    />

    <button type="button" class="qty-btn qty-btn--plus" aria-label="<?php esc_attr_e('Increase quantity', 'woocommerce'); ?>">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
            <rect x="6" width="2" height="14" rx="1" fill="currentColor"/>
            <rect width="14" height="2" rx="1" y="6" fill="currentColor"/>
        </svg>
    </button>

</div>
