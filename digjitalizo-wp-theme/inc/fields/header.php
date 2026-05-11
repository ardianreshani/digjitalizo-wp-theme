<?php

acf_add_local_field_group([
    'key'    => 'group_header',
    'title'  => 'Header',
    'fields' => [
        [
            'key'          => 'field_announcement_enabled',
            'label'        => 'Enable Announcement Bar',
            'name'         => 'announcement_enabled',
            'type'         => 'true_false',
            'ui'           => 1,
            'default_value'=> 0,
        ],
        [
            'key'               => 'field_announcement_text',
            'label'             => 'Announcement Text',
            'name'              => 'announcement_text',
            'type'              => 'text',
            'placeholder'       => 'Free shipping on orders over $50!',
            'conditional_logic' => [[['field' => 'field_announcement_enabled', 'operator' => '==', 'value' => '1']]],
        ],
        [
            'key'               => 'field_announcement_link',
            'label'             => 'Announcement Link',
            'name'              => 'announcement_link',
            'type'              => 'url',
            'placeholder'       => 'https://',
            'instructions'      => 'Optional — leave empty for no link.',
            'conditional_logic' => [[['field' => 'field_announcement_enabled', 'operator' => '==', 'value' => '1']]],
        ],
        [
            'key'               => 'field_announcement_bg',
            'label'             => 'Announcement Background Color',
            'name'              => 'announcement_bg',
            'type'              => 'color_picker',
            'default_value'     => '#1a1a2e',
            'conditional_logic' => [[['field' => 'field_announcement_enabled', 'operator' => '==', 'value' => '1']]],
        ],
        [
            'key'               => 'field_announcement_text_color',
            'label'             => 'Announcement Text Color',
            'name'              => 'announcement_text_color',
            'type'              => 'color_picker',
            'default_value'     => '#ffffff',
            'conditional_logic' => [[['field' => 'field_announcement_enabled', 'operator' => '==', 'value' => '1']]],
        ],
        [
            'key'          => 'field_header_sticky',
            'label'        => 'Sticky Header',
            'name'         => 'header_sticky',
            'type'         => 'true_false',
            'ui'           => 1,
            'default_value'=> 1,
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-header']]],
]);
