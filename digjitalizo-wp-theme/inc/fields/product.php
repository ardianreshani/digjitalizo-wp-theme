<?php

acf_add_local_field_group([
    'key'                   => 'group_product_extra_content',
    'title'                 => 'Product extra content',
    'fields'                => [
        [
            'key'          => 'field_product_extra_sections',
            'label'        => 'Extra content sections',
            'name'         => 'product_extra_sections',
            'type'         => 'repeater',
            'layout'       => 'block',
            'button_label' => 'Add section',
            'collapsed'    => 'field_product_extra_section_title',
            'instructions' => 'Optional full-width product description rows shown under the product details table. Image position alternates automatically: first left, second right.',
            'sub_fields'   => [
                [
                    'key'            => 'field_product_extra_section_image',
                    'label'          => 'Image',
                    'name'           => 'image',
                    'type'           => 'image',
                    'return_format'  => 'array',
                    'preview_size'   => 'medium',
                    'library'        => 'all',
                    'wrapper'        => ['width' => '40'],
                ],
                [
                    'key'     => 'field_product_extra_section_title',
                    'label'   => 'Title',
                    'name'    => 'title',
                    'type'    => 'text',
                    'wrapper' => ['width' => '60'],
                ],
                [
                    'key'          => 'field_product_extra_section_content',
                    'label'        => 'Content',
                    'name'         => 'content',
                    'type'         => 'wysiwyg',
                    'tabs'         => 'all',
                    'toolbar'      => 'basic',
                    'media_upload' => 0,
                ],
            ],
        ],
    ],
    'location'              => [[
        [
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'product',
        ],
    ]],
    'menu_order'            => 20,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'active'                => true,
]);
