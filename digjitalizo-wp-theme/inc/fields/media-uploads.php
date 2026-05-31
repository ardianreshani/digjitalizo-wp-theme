<?php

acf_add_local_field_group([
    'key'    => 'group_media_uploads',
    'title'  => 'Media Uploads',
    'fields' => [
        [
            'key'           => 'field_enable_svg_uploads',
            'label'         => 'Enable SVG Uploads',
            'name'          => 'enable_svg_uploads',
            'type'          => 'true_false',
            'ui'            => 1,
            'default_value' => 0,
            'instructions'  => 'Allow administrators to upload SVG files to the Media Library.',
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-media-uploads']]],
]);
