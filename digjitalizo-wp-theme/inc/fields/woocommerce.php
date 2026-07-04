<?php

acf_add_local_field_group([
    'key'    => 'group_woocommerce',
    'title'  => 'WooCommerce',
    'fields' => [
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
        [
            'key'               => 'field_woo_gallery_lightbox_thumbnails',
            'label'             => 'Lightbox Thumbnails',
            'name'              => 'woo_gallery_lightbox_thumbnails',
            'type'              => 'true_false',
            'default_value'     => 1,
            'ui'                => 1,
            'instructions'      => 'Show a thumbnail strip below the large image when the product gallery lightbox is open.',
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

        // ─── Out-of-Stock Inquiry ─────────────────────────────────────────────
        [
            'key'           => 'field_woo_inquiry_form',
            'label'         => 'Out-of-Stock Inquiry Form',
            'name'          => 'woo_inquiry_form',
            'type'          => 'post_object',
            'post_type'     => ['wpcf7_contact_form'],
            'return_format' => 'id',
            'allow_null'    => 1,
            'instructions'  => 'Select the Contact Form 7 form to show when a product is out of stock. Leave empty to hide the inquiry button.',
        ],

        // ─── Categories ───────────────────────────────────────────────────────
        [
            'key'           => 'field_woo_show_empty_categories',
            'label'         => 'Show empty categories',
            'name'          => 'woo_show_empty_categories',
            'type'          => 'true_false',
            'ui'            => 1,
            'default_value' => 0,
            'instructions'  => 'When enabled, categories with no products are still shown in the archive tabs and navigation.',
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
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-woocommerce']]],
]);
