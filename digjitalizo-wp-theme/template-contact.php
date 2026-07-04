<?php
/**
 * Template Name: Contact Page
 * Template Post Type: page
 */

get_header();

while (have_posts()) :
    the_post();

    $contact_field = function ($name, $default = '') {
        if (function_exists('get_field')) {
            $value = get_field($name);

            if ($value !== null && $value !== false && $value !== '') {
                return $value;
            }
        }

        return $default;
    };

    $contact_raw_field = function ($name, $default = null) {
        if (function_exists('get_field')) {
            $value = get_field($name);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    };

    $form_title = $contact_field('contact_form_title', __('Na kontaktoni', 'base-theme'));
    $form_intro = $contact_field('contact_form_intro', '');
    $form_post_id = (int) $contact_field('contact_form_post', 0);
    $shop_map_title = $contact_field('contact_shop_map_title', __('Dyqani', 'base-theme'));
    $shop_map_address = trim((string) $contact_field('contact_map_address', ''));
    $depot_map_title = $contact_field('contact_depot_map_title', __('Depoja', 'base-theme'));
    $depot_map_address = trim((string) $contact_field('contact_depot_map_address', ''));
    $form_shortcode = '';

    if ($form_post_id > 0) {
        $form_shortcode = sprintf('[contact-form-7 id="%d"]', $form_post_id);
    }

    $build_map_url = static function ($address) {
        if (!$address) {
            return '';
        }

        return add_query_arg(
            [
                'q'      => $address,
                'output' => 'embed',
            ],
            'https://www.google.com/maps'
        );
    };

    $map_locations = [];

    foreach ([
        ['title' => $shop_map_title, 'address' => $shop_map_address],
        ['title' => $depot_map_title, 'address' => $depot_map_address],
    ] as $location) {
        $map_url = $build_map_url($location['address']);

        if ($map_url) {
            $map_locations[] = [
                'title' => $location['title'],
                'url'   => $map_url,
            ];
        }
    }

    $blog_enabled = (bool) $contact_raw_field('contact_blog_enabled', true);
    $blog_title = $contact_field('contact_blog_title', __('Këshilla dhe lajme nga EMSA', 'base-theme'));
    $blog_source = $contact_field('contact_blog_source', 'latest');
    $blog_post_ids = [];
    $blog_posts_field = $contact_raw_field('contact_blog_posts', []);

    if (is_array($blog_posts_field)) {
        foreach ($blog_posts_field as $post_item) {
            if (is_numeric($post_item)) {
                $blog_post_ids[] = (int) $post_item;
            } elseif ($post_item instanceof WP_Post) {
                $blog_post_ids[] = (int) $post_item->ID;
            }
        }
    }

    $blog_post_ids = array_values(array_unique(array_filter($blog_post_ids)));
    $contact_blog_query = null;

    if ($blog_enabled) {
        $blog_args = [
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 4,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ];

        if ($blog_source === 'manual' && $blog_post_ids) {
            $blog_args['post__in'] = $blog_post_ids;
            $blog_args['orderby'] = 'post__in';
            $blog_args['posts_per_page'] = count($blog_post_ids);
        }

        $contact_blog_query = new WP_Query($blog_args);
    }
    ?>

    <main class="contact-page">
        <div class="container">
            <?php emsaks_render_breadcrumbs([
                [
                    'label' => get_the_title(),
                ],
            ]); ?>

            <div class="contact-layout">
                <section class="contact-info-panel">
                    <h1><?php the_title(); ?></h1>

                    <?php if (trim(get_the_content()) !== '') : ?>
                        <div class="contact-page-content">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>
                </section>

                <?php if ($form_shortcode) : ?>
                    <section class="contact-form-card">
                        <?php if ($form_title) : ?>
                            <h2><?php echo esc_html($form_title); ?></h2>
                        <?php endif; ?>

                        <?php if ($form_intro) : ?>
                            <p class="contact-form-intro"><?php echo esc_html($form_intro); ?></p>
                        <?php endif; ?>

                        <div class="contact-form-embed">
                            <?php echo do_shortcode($form_shortcode); ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <?php if ($map_locations) : ?>
                <section class="contact-maps" aria-label="<?php echo esc_attr__('Lokacionet tona', 'base-theme'); ?>">
                    <?php foreach ($map_locations as $location) : ?>
                        <article class="contact-map">
                            <?php if ($location['title']) : ?>
                                <h2><?php echo esc_html($location['title']); ?></h2>
                            <?php endif; ?>
                            <iframe
                                src="<?php echo esc_url($location['url']); ?>"
                                title="<?php echo esc_attr($location['title']); ?>"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen></iframe>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </div>

        <?php if ($contact_blog_query instanceof WP_Query && $contact_blog_query->have_posts()) : ?>
            <section class="home-blog-section contact-blog-section">
                <div class="container">
                    <?php if ($blog_title) : ?>
                        <h2 class="home-section-title"><?php echo esc_html($blog_title); ?></h2>
                    <?php endif; ?>
                    <div class="home-blog-grid">
                        <?php while ($contact_blog_query->have_posts()) : $contact_blog_query->the_post(); ?>
                            <?php emsaks_render_blog_card(get_the_ID()); ?>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    </main>

<?php
endwhile;

get_footer();
