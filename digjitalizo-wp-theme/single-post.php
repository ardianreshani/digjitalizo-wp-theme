<?php
get_header();

while (have_posts()) :
    the_post();

    $post_id = get_the_ID();
    $image = get_the_post_thumbnail_url($post_id, 'full');
    $categories = wp_get_post_categories($post_id);
    ?>
    <main class="single-blog-page">
        <div class="container">
            <article <?php post_class('single-blog-article'); ?>>
                <?php emsaks_render_breadcrumbs([
                    [
                        'label' => get_the_title(),
                    ],
                ]); ?>

                <header class="single-blog-header">
                    <span class="blog-card-badge"><?php echo esc_html(emsaks_get_post_category_label($post_id)); ?></span>
                    <h1><?php the_title(); ?></h1>
                    <div class="single-blog-meta">
                        <span><?php echo esc_html(get_the_date('j F Y', $post_id)); ?></span>
                        <span aria-hidden="true">•</span>
                        <span><?php echo esc_html(emsaks_get_reading_time($post_id)); ?></span>
                    </div>
                </header>

                <?php if ($image) : ?>
                    <figure class="single-blog-featured">
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    </figure>
                <?php endif; ?>

                <div class="single-blog-content">
                    <?php the_content(); ?>
                </div>
            </article>

            <?php
            $related_args = [
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 3,
                'post__not_in'        => [$post_id],
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ];

            if (!empty($categories)) {
                $related_args['category__in'] = $categories;
            }

            $related_posts = new WP_Query($related_args);
            ?>
            <?php if ($related_posts->have_posts()) : ?>
                <section class="single-blog-related">
                    <h2><?php esc_html_e('Këshilla & lajme nga EMSA', 'base-theme'); ?></h2>
                    <div class="blog-grid blog-grid-related">
                        <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                            <?php emsaks_render_blog_card(get_the_ID()); ?>
                        <?php endwhile; ?>
                    </div>
                </section>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
    </main>
<?php endwhile; ?>

<?php get_footer(); ?>
