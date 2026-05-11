<?php

acf_add_local_field_group([
    'key'    => 'group_woocommerce',
    'title'  => 'WooCommerce',
    'fields' => [
        // ─── Shop Layout ─────────────────────────────────────────────────────
        [
            'key'     => 'field_woo_shop_columns',
            'label'   => 'Shop Columns',
            'name'    => 'woo_shop_columns',
            'type'    => 'select',
            'choices' => ['2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns'],
            'default_value' => '3',
            'return_format' => 'value',
        ],
        [
            'key'           => 'field_woo_products_per_page',
            'label'         => 'Products Per Page',
            'name'          => 'woo_products_per_page',
            'type'          => 'number',
            'default_value' => 12,
            'min'           => 4,
            'max'           => 96,
        ],

        // ─── Product Gallery ──────────────────────────────────────────────────
        [
            'key'           => 'field_woo_gallery_enabled',
            'label'         => 'Custom Product Gallery',
            'name'          => 'woo_gallery_enabled',
            'type'          => 'true_false',
            'default_value' => 1,
            'ui'            => 1,
            'instructions'  => 'Enable the custom theme gallery (thumbnail strip + variation image swap). Disable to use WooCommerce default.',
        ],
        [
            'key'              => 'field_woo_gallery_thumb_pos',
            'label'            => 'Thumbnail Position',
            'name'             => 'woo_gallery_thumb_pos',
            'type'             => 'select',
            'choices'          => [
                'bottom' => 'Below main image',
                'left'   => 'Left of main image',
                'right'  => 'Right of main image',
            ],
            'default_value'    => 'bottom',
            'return_format'    => 'value',
            'conditional_logic' => [[
                ['field' => 'field_woo_gallery_enabled', 'operator' => '==', 'value' => '1'],
            ]],
        ],

        // ─── Variable Products ────────────────────────────────────────────────
        [
            'key'           => 'field_woo_attr_display',
            'label'         => 'Attribute Display',
            'name'          => 'woo_attr_display',
            'type'          => 'select',
            'choices'       => [
                'buttons'  => 'Buttons / Swatches',
                'dropdown' => 'Dropdown (WooCommerce default)',
            ],
            'default_value' => 'buttons',
            'return_format' => 'value',
            'instructions'  => 'How variable product attributes (Size, Color, etc.) are displayed on the product page.',
        ],

        // ─── Cart ─────────────────────────────────────────────────────────────
        [
            'key'     => 'field_woo_cart_behavior',
            'label'   => 'After Add to Cart',
            'name'    => 'woo_cart_behavior',
            'type'    => 'select',
            'choices' => [
                'minicart' => 'Open mini-cart drawer',
                'redirect' => 'Redirect to cart page',
                'nothing'  => 'Do nothing',
            ],
            'default_value' => 'minicart',
            'return_format' => 'value',
        ],

        // ─── Free Shipping ────────────────────────────────────────────────────
        [
            'key'          => 'field_woo_free_shipping_threshold',
            'label'        => 'Free Shipping Threshold',
            'name'         => 'woo_free_shipping_threshold',
            'type'         => 'number',
            'placeholder'  => '50',
            'instructions' => 'Enter the minimum order amount for free shipping. Leave empty to hide the message.',
        ],
        [
            'key'          => 'field_woo_free_shipping_message',
            'label'        => 'Free Shipping Message',
            'name'         => 'woo_free_shipping_message',
            'type'         => 'text',
            'default_value'=> 'Add {amount} more for free shipping!',
            'instructions' => 'Use {amount} as placeholder for the remaining amount.',
        ],

        // ─── Trust Badges ─────────────────────────────────────────────────────
        [
            'key'          => 'field_woo_trust_badges',
            'label'        => 'Trust Badges',
            'name'         => 'woo_trust_badges',
            'type'         => 'repeater',
            'instructions' => 'Shown on cart and checkout pages.',
            'min'          => 0,
            'max'          => 6,
            'layout'       => 'table',
            'button_label' => 'Add Badge',
            'sub_fields'   => [
                [
                    'key'           => 'field_badge_icon',
                    'label'         => 'Icon',
                    'name'          => 'badge_icon',
                    'type'          => 'image',
                    'return_format' => 'array',
                    'preview_size'  => 'thumbnail',
                    'wrapper'       => ['width' => '30'],
                ],
                [
                    'key'     => 'field_badge_label',
                    'label'   => 'Label',
                    'name'    => 'badge_label',
                    'type'    => 'text',
                    'placeholder' => 'Secure Payment',
                    'wrapper' => ['width' => '70'],
                ],
            ],
        ],

        // ─── Checkout ─────────────────────────────────────────────────────────
        [
            'key'          => 'field_woo_checkout_promo',
            'label'        => 'Checkout Promo Text',
            'name'         => 'woo_checkout_promo',
            'type'         => 'text',
            'placeholder'  => 'You\'re one step away from your order!',
            'instructions' => 'Shown above the checkout form.',
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-woocommerce']]],
]);
