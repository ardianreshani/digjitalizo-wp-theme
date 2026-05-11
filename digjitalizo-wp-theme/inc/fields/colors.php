<?php

acf_add_local_field_group([
    'key'    => 'group_colors',
    'title'  => 'Branding & Colors',
    'fields' => [
        [
            'key'           => 'field_color_primary',
            'label'         => 'Primary Color',
            'name'          => 'color_primary',
            'type'          => 'color_picker',
            'default_value' => '#1a1a2e',
            'instructions'  => 'Main brand color — used for header background, headings.',
        ],
        [
            'key'           => 'field_color_secondary',
            'label'         => 'Secondary Color',
            'name'          => 'color_secondary',
            'type'          => 'color_picker',
            'default_value' => '#16213e',
            'instructions'  => 'Supporting color — backgrounds, cards.',
        ],
        [
            'key'           => 'field_color_accent',
            'label'         => 'Accent Color',
            'name'          => 'color_accent',
            'type'          => 'color_picker',
            'default_value' => '#e94560',
            'instructions'  => 'Highlight color — badges, sale tags, icons.',
        ],
        [
            'key'           => 'field_color_btn_bg',
            'label'         => 'Button Background',
            'name'          => 'color_btn_bg',
            'type'          => 'color_picker',
            'default_value' => '#e94560',
        ],
        [
            'key'           => 'field_color_btn_text',
            'label'         => 'Button Text',
            'name'          => 'color_btn_text',
            'type'          => 'color_picker',
            'default_value' => '#ffffff',
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-colors']]],
]);
