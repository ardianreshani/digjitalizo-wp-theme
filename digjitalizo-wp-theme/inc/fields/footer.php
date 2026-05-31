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
            'default_value'=> '©{year} EMSA SH.P.K.',
            'placeholder'  => '©{year} EMSA SH.P.K.',
            'instructions' => 'Use {year} to insert the current year automatically.',
        ],
    ],
    'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'theme-footer']]],
]);
