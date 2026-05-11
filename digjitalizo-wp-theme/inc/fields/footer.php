<?php

acf_add_local_field_group([
    'key'    => 'group_footer',
    'title'  => 'Footer',
    'fields' => [
        [
            'key'          => 'field_footer_copyright',
            'label'        => 'Copyright Text',
            'name'         => 'footer_copyright',
            'type'         => 'text',
            'placeholder'  => '© 2025 Your Company. All rights reserved.',
            'instructions' => 'Use {year} to insert the current year automatically.',
        ],
        [
            'key'          => 'field_footer_columns',
            'label'        => 'Footer Columns',
            'name'         => 'footer_columns',
            'type'         => 'repeater',
            'min'          => 0,
            'max'          => 4,
            'layout'       => 'block',
            'button_label' => 'Add Column',
            'sub_fields'   => [
                [
                    'key'   => 'field_footer_col_title',
                    'label' => 'Column Title',
                    'name'  => 'col_title',
                    'type'  => 'text',
                ],
                [
                    'key'   => 'field_footer_col_content',
                    'label' => 'Content',
                    'name'  => 'col_content',
                    'type'  => 'wysiwyg',
                    'tabs'  => 'all',
                    'media_upload' => 0,
                    'toolbar' => 'basic',
                ],
            ],
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-footer']]],
]);
