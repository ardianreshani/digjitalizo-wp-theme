<?php
/**
 * Template Name: About Page
 * Template Post Type: page
 */

get_header();

while (have_posts()) :
    the_post();

    $sections = function_exists('get_field') ? get_field('about_sections') : [];
    ?>

    <main class="about-page">
        <div class="container">
            <?php emsaks_render_breadcrumbs([
                [
                    'label' => get_the_title(),
                ],
            ]); ?>

            <header class="about-header">
                <h1><?php the_title(); ?></h1>
            </header>

            <?php if (!empty($sections) && is_array($sections)) : ?>
                <div class="about-sections">
                    <?php foreach ($sections as $section) :
                        $image_position = ($section['image_position'] ?? 'right') === 'left' ? 'left' : 'right';
                        $title = trim((string) ($section['title'] ?? ''));
                        $content = trim((string) ($section['content'] ?? ''));
                        $image = $section['image'] ?? null;
                        $image_url = '';
                        $image_alt = $title ?: get_the_title();

                        if (is_array($image) && !empty($image['url'])) {
                            $image_url = $image['url'];
                            $image_alt = !empty($image['alt']) ? $image['alt'] : $image_alt;
                        } elseif (is_numeric($image)) {
                            $image_url = wp_get_attachment_image_url((int) $image, 'large');
                            $attachment_alt = get_post_meta((int) $image, '_wp_attachment_image_alt', true);
                            $image_alt = $attachment_alt ?: $image_alt;
                        } elseif (is_string($image) && $image !== '') {
                            $image_url = $image;
                        }

                        if (!$title && !$content && !$image_url) {
                            continue;
                        }
                        ?>
                        <section class="about-row about-row--image-<?php echo esc_attr($image_position); ?>">
                            <div class="about-row-copy">
                                <?php if ($title) : ?>
                                    <h2><?php echo esc_html($title); ?></h2>
                                <?php endif; ?>

                                <?php if ($content) : ?>
                                    <div class="about-row-content">
                                        <?php echo wp_kses_post(apply_filters('the_content', $content)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($image_url) : ?>
                                <figure class="about-row-image">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                                </figure>
                            <?php endif; ?>
                        </section>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php
endwhile;

get_footer();
