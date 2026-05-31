<?php get_header(); ?>

<main class="theme-single-page">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('theme-single-content'); ?>>
                <h1 class="theme-single-title"><?php the_title(); ?></h1>
                <div class="theme-single-body">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
