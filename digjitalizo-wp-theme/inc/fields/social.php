<?php

acf_add_local_field_group([
    'key'    => 'group_social',
    'title'  => 'Social Media',
    'fields' => [
        [
            'key'         => 'field_social_facebook',
            'label'       => 'Facebook',
            'name'        => 'social_facebook',
            'type'        => 'url',
            'placeholder' => 'https://facebook.com/yourpage',
        ],
        [
            'key'         => 'field_social_instagram',
            'label'       => 'Instagram',
            'name'        => 'social_instagram',
            'type'        => 'url',
            'placeholder' => 'https://instagram.com/yourhandle',
        ],
        [
            'key'         => 'field_social_x',
            'label'       => 'X (Twitter)',
            'name'        => 'social_x',
            'type'        => 'url',
            'placeholder' => 'https://x.com/yourhandle',
        ],
        [
            'key'         => 'field_social_linkedin',
            'label'       => 'LinkedIn',
            'name'        => 'social_linkedin',
            'type'        => 'url',
            'placeholder' => 'https://linkedin.com/company/yourcompany',
        ],
        [
            'key'         => 'field_social_youtube',
            'label'       => 'YouTube',
            'name'        => 'social_youtube',
            'type'        => 'url',
            'placeholder' => 'https://youtube.com/@yourchannel',
        ],
        [
            'key'         => 'field_social_tiktok',
            'label'       => 'TikTok',
            'name'        => 'social_tiktok',
            'type'        => 'url',
            'placeholder' => 'https://tiktok.com/@yourhandle',
        ],
        [
            'key'          => 'field_social_whatsapp',
            'label'        => 'WhatsApp Number',
            'name'         => 'social_whatsapp',
            'type'         => 'text',
            'placeholder'  => '+1234567890',
            'instructions' => 'International format without spaces (used for wa.me links).',
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-social']]],
]);
