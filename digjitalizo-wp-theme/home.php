<?php
get_header();

$posts_page_id = (int) get_option('page_for_posts');
$title = $posts_page_id ? get_the_title($posts_page_id) : __('Këshilla & lajme nga EMSA', 'base-theme');
$description = $posts_page_id ? get_post_field('post_excerpt', $posts_page_id) : '';

emsaks_render_blog_archive_page($title, $description);

get_footer();
