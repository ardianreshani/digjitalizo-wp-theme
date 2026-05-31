<?php

acf_add_local_field_group([
    'key'                   => 'group_contact_page',
    'title'                 => 'Contact page',
    'fields'                => [
        [
            'key'           => 'field_contact_form_title',
            'label'         => 'Form title',
            'name'          => 'contact_form_title',
            'type'          => 'text',
            'default_value' => 'Na kontaktoni',
            'wrapper'       => ['width' => '50'],
        ],
        [
            'key'           => 'field_contact_form_intro',
            'label'         => 'Form intro text',
            'name'          => 'contact_form_intro',
            'type'          => 'textarea',
            'rows'          => 3,
            'default_value' => 'Për çdo informacion shtesë, jemi në dispozicion përmes formularit online.',
            'wrapper'       => ['width' => '50'],
        ],
        [
            'key'           => 'field_contact_form_post',
            'label'         => 'Contact Form 7 form',
            'name'          => 'contact_form_post',
            'type'          => 'post_object',
            'post_type'     => ['wpcf7_contact_form'],
            'return_format' => 'id',
            'ui'            => 1,
            'instructions'  => 'Select the CF7 form if you want to manage it from the dropdown.',
            'wrapper'       => ['width' => '50'],
        ],
        [
            'key'           => 'field_contact_map_address',
            'label'         => 'Google Maps address',
            'name'          => 'contact_map_address',
            'type'          => 'text',
            'instructions'  => 'Example: Brigada 123, Suhareke, Kosove. Used to generate the embedded map.',
        ],
    ],
    'location'              => [[
        [
            'param'    => 'page_template',
            'operator' => '==',
            'value'    => 'template-contact.php',
        ],
    ]],
    'menu_order'            => 0,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'active'                => true,
]);
