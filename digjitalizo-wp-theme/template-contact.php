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

    $form_title = $contact_field('contact_form_title', __('Na kontaktoni', 'base-theme'));
    $form_intro = $contact_field('contact_form_intro', '');
    $form_post_id = (int) $contact_field('contact_form_post', 0);
    $map_address = trim((string) $contact_field('contact_map_address', ''));
    $form_shortcode = '';

    if ($form_post_id > 0) {
        $form_shortcode = sprintf('[contact-form-7 id="%d"]', $form_post_id);
    }

    $map_url = '';

    if ($map_address) {
        $map_url = add_query_arg(
            [
                'q'      => $map_address,
                'output' => 'embed',
            ],
            'https://www.google.com/maps'
        );
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

            <?php if ($map_url) : ?>
                <section class="contact-map" aria-label="<?php echo esc_attr__('Lokacioni në Google Maps', 'base-theme'); ?>">
                    <iframe
                        src="<?php echo esc_url($map_url); ?>"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen></iframe>
                </section>
            <?php endif; ?>
        </div>
    </main>

<?php
endwhile;

get_footer();
