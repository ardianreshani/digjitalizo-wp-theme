<?php
get_header();

if (is_category()) {
    $title = single_cat_title('', false);
    $description = category_description();
} elseif (is_tag()) {
    $title = single_tag_title('', false);
    $description = tag_description();
} else {
    $title = get_the_archive_title();
    $description = get_the_archive_description();
}

emsaks_render_blog_archive_page($title, $description);

get_footer();
