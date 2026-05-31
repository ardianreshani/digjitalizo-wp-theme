<?php

acf_add_local_field_group([
    'key'                   => 'group_about_page',
    'title'                 => 'About page',
    'fields'                => [
        [
            'key'          => 'field_about_sections',
            'label'        => 'About sections',
            'name'         => 'about_sections',
            'type'         => 'repeater',
            'layout'       => 'block',
            'button_label' => 'Add section',
            'collapsed'    => 'field_about_section_title',
            'sub_fields'   => [
                [
                    'key'           => 'field_about_section_image_position',
                    'label'         => 'Image position',
                    'name'          => 'image_position',
                    'type'          => 'button_group',
                    'choices'       => [
                        'right' => 'Right',
                        'left'  => 'Left',
                    ],
                    'default_value' => 'right',
                    'layout'        => 'horizontal',
                    'wrapper'       => ['width' => '30'],
                ],
                [
                    'key'            => 'field_about_section_image',
                    'label'          => 'Image',
                    'name'           => 'image',
                    'type'           => 'image',
                    'return_format'  => 'array',
                    'preview_size'   => 'medium',
                    'library'        => 'all',
                    'wrapper'        => ['width' => '70'],
                ],
                [
                    'key'     => 'field_about_section_title',
                    'label'   => 'Title',
                    'name'    => 'title',
                    'type'    => 'text',
                    'wrapper' => ['width' => '40'],
                ],
                [
                    'key'     => 'field_about_section_content',
                    'label'   => 'Content',
                    'name'    => 'content',
                    'type'    => 'wysiwyg',
                    'tabs'    => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                    'wrapper' => ['width' => '60'],
                ],
            ],
        ],
    ],
    'location'              => [[
        [
            'param'    => 'page_template',
            'operator' => '==',
            'value'    => 'template-about.php',
        ],
    ]],
    'menu_order'            => 0,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'active'                => true,
]);
