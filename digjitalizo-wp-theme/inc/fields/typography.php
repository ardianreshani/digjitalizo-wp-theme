<?php

acf_add_local_field_group([
    'key'    => 'group_typography',
    'title'  => 'Typography',
    'fields' => [
        [
            'key'          => 'field_google_fonts_url',
            'label'        => 'Google Fonts URL',
            'name'         => 'google_fonts_url',
            'type'         => 'url',
            'instructions' => 'Paste the full embed URL from Google Fonts (e.g. https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap)',
            'placeholder'  => 'https://fonts.googleapis.com/css2?family=...',
        ],
        [
            'key'          => 'field_font_heading',
            'label'        => 'Heading Font',
            'name'         => 'font_heading',
            'type'         => 'text',
            'instructions' => 'Font family name exactly as in Google Fonts (e.g. Inter, Playfair Display)',
            'placeholder'  => 'Inter',
            'default_value'=> 'Inter',
        ],
        [
            'key'          => 'field_font_body',
            'label'        => 'Body Font',
            'name'         => 'font_body',
            'type'         => 'text',
            'placeholder'  => 'Inter',
            'default_value'=> 'Inter',
        ],
        [
            'key'          => 'field_font_size_base',
            'label'        => 'Base Font Size',
            'name'         => 'font_size_base',
            'type'         => 'text',
            'default_value'=> '16px',
            'placeholder'  => '16px',
            'instructions' => 'Base font size for body text.',
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-typography']]],
]);
