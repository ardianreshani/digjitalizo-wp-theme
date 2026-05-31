<?php

if (!function_exists('acf_add_options_page')) {
    return;
}

// ─── Register Options Pages ───────────────────────────────────────────────────
acf_add_options_page([
    'page_title' => 'Theme Settings',
    'menu_title' => 'Theme Settings',
    'menu_slug'  => 'theme-settings',
    'capability' => 'manage_options',
    'icon_url'   => 'dashicons-admin-customizer',
    'position'   => 60,
    'redirect'   => true,
]);

$sub_pages = [
    ['Branding & Colors', 'theme-colors'],
    ['Typography',        'theme-typography'],
    ['Company Info',      'theme-company'],
    ['Social Media',      'theme-social'],
    ['Header',            'theme-header'],
    ['Footer',            'theme-footer'],
    ['WooCommerce',       'theme-woocommerce'],
    ['Templates',         'theme-templates'],
    ['Media Uploads',     'theme-media-uploads'],
];

foreach ($sub_pages as [$title, $slug]) {
    acf_add_options_sub_page([
        'page_title'  => $title,
        'menu_title'  => $title,
        'parent_slug' => 'theme-settings',
        'menu_slug'   => $slug,
    ]);
}

// ─── Load Field Groups ────────────────────────────────────────────────────────
$fields = [
    'colors',
    'typography',
    'company',
    'social',
    'header',
    'footer',
    'woocommerce',
    'global-options',
    'media-uploads',
];

foreach ($fields as $file) {
    require_once get_template_directory() . "/inc/fields/{$file}.php";
}
