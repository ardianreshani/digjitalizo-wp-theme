<?php
/**
 * Template Name: Home Page
 * Template Post Type: page
 *
 * Homepage scaffold prepared for ACF fields and multilingual content.
 */

get_header();

while (have_posts()) :
    the_post();

    $home_field = function ($name, $default = '') {
        if (function_exists('get_field')) {
            $value = get_field($name);

            if ($value !== null && $value !== false && $value !== '') {
                return $value;
            }
        }

        return $default;
    };

    $home_raw_field = function ($name, $default = null) {
        if (function_exists('get_field')) {
            $value = get_field($name);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    };

    $slides = $home_field('home_slider', []);

    if (empty($slides) || !is_array($slides)) {
        $slides = [[
            'title'             => $home_field('home_hero_title', get_the_title()),
            'short_description' => $home_field('home_hero_subtitle', ''),
            'button_text'       => $home_field('home_hero_button_text', __('Shop products', 'base-theme')),
            'button_url'        => $home_field('home_hero_button_url', function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')),
            'image'             => $home_field('home_hero_image', null),
        ]];
    }

    $home_product_ids_from_field = function ($products_field) {
        $product_ids = [];

        if (!empty($products_field) && is_array($products_field)) {
            foreach ($products_field as $product) {
                if (is_numeric($product)) {
                    $product_ids[] = (int) $product;
                } elseif ($product instanceof WP_Post) {
                    $product_ids[] = (int) $product->ID;
                } elseif (is_object($product) && method_exists($product, 'get_id')) {
                    $product_ids[] = (int) $product->get_id();
                }
            }
        }

        return array_values(array_unique(array_filter($product_ids)));
    };

    $home_products_from_ids = function ($product_ids) {
        $products = [];

        if (!class_exists('WooCommerce')) {
            return $products;
        }

        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);

            if ($product && $product->is_visible()) {
                $products[] = $product;
            }
        }

        return $products;
    };

    $home_post_ids_from_field = function ($posts_field) {
        $post_ids = [];

        if (!empty($posts_field) && is_array($posts_field)) {
            foreach ($posts_field as $post_item) {
                if (is_numeric($post_item)) {
                    $post_ids[] = (int) $post_item;
                } elseif ($post_item instanceof WP_Post) {
                    $post_ids[] = (int) $post_item->ID;
                }
            }
        }

        return array_values(array_unique(array_filter($post_ids)));
    };

    $render_home_product_slider = function ($section_class, $title, $products) {
        if (empty($products)) {
            return;
        }
        ?>
        <section class="home-recommended-products <?php echo esc_attr($section_class); ?>" data-product-slider>
            <div class="container">
                <?php if ($title) : ?>
                    <h2 class="home-section-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <div class="home-products-slider swiper">
                    <ul class="swiper-wrapper">
                        <?php foreach ($products as $product) :
                            emsaks_render_product_loop_card($product, 'slider');
                            ?>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if (count($products) > 1) : ?>
                    <div class="home-products-arrows">
                        <button type="button" class="home-products-arrow home-products-prev" aria-label="<?php echo esc_attr__('Produktet paraprake', 'base-theme'); ?>">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M15 5 8 12l7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button type="button" class="home-products-arrow home-products-next" aria-label="<?php echo esc_attr__('Produktet tjera', 'base-theme'); ?>">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    };
    ?>

    <main class="home-page">

        <!-- 1. Hero Slider -->
        <section class="home-slider" data-home-slider>
            <div class="container">
                <div class="home-slider-frame swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($slides as $index => $slide) :
                            $title       = $slide['title'] ?? '';
                            $description = $slide['short_description'] ?? '';
                            $button_text = $slide['button_text'] ?? '';
                            $button_url  = $slide['button_url'] ?? '';
                            $image       = $slide['image'] ?? null;
                            $image_url   = '';
                            $image_alt   = $title;

                            if (is_array($image) && !empty($image['url'])) {
                                $image_url = $image['url'];
                                $image_alt = !empty($image['alt']) ? $image['alt'] : $image_alt;
                            } elseif (is_string($image) && $image !== '') {
                                $image_url = $image;
                            }
                            ?>
                            <article class="home-slide swiper-slide">
                                <div class="home-slide-copy">
                                    <?php if ($title) : ?>
                                        <?php if ($index === 0) : ?>
                                            <h1><?php echo esc_html($title); ?></h1>
                                        <?php else : ?>
                                            <h2><?php echo esc_html($title); ?></h2>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($description) : ?>
                                        <p><?php echo esc_html($description); ?></p>
                                    <?php endif; ?>

                                    <?php if ($button_text && $button_url) : ?>
                                        <a href="<?php echo esc_url($button_url); ?>" class="home-slide-button">
                                            <?php echo esc_html($button_text); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <?php if ($image_url) : ?>
                                    <div class="home-slide-media">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($slides) > 1) : ?>
                        <div class="home-slider-dots swiper-pagination"></div>

                        <div class="home-slider-arrows">
                            <button type="button" class="home-slider-arrow home-slider-prev" aria-label="<?php echo esc_attr__('Previous slide', 'base-theme'); ?>">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M15 5 8 12l7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" class="home-slider-arrow home-slider-next" aria-label="<?php echo esc_attr__('Next slide', 'base-theme'); ?>">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- 2. Recommended Products -->
        <?php
        $recommended_enabled = (bool) $home_raw_field('home_recommended_enabled', true);
        $recommended_title = $home_field('home_recommended_title', __('Të rekomanduara', 'base-theme'));
        $recommended_source = $home_field('home_recommended_source', 'featured');
        $recommended_products_field = $home_raw_field('home_recommended_products', []);
        $recommended_product_ids = [];
        $recommended_products = [];

        if (!empty($recommended_products_field) && is_array($recommended_products_field)) {
            foreach ($recommended_products_field as $recommended_product) {
                if (is_numeric($recommended_product)) {
                    $recommended_product_ids[] = (int) $recommended_product;
                } elseif ($recommended_product instanceof WP_Post) {
                    $recommended_product_ids[] = (int) $recommended_product->ID;
                } elseif (is_object($recommended_product) && method_exists($recommended_product, 'get_id')) {
                    $recommended_product_ids[] = (int) $recommended_product->get_id();
                }
            }
        }

        if ($recommended_enabled && class_exists('WooCommerce')) {
            if ($recommended_source === 'manual' && !empty($recommended_product_ids)) {
                foreach ($recommended_product_ids as $product_id) {
                    $product = wc_get_product($product_id);

                    if ($product && $product->is_visible()) {
                        $recommended_products[] = $product;
                    }
                }
            }

            if (empty($recommended_products)) {
                $recommended_query = new WP_Query([
                    'post_type'           => 'product',
                    'post_status'         => 'publish',
                    'posts_per_page'      => 10,
                    'no_found_rows'       => true,
                    'ignore_sticky_posts' => true,
                    'tax_query'           => [
                        [
                            'taxonomy' => 'product_visibility',
                            'field'    => 'name',
                            'terms'    => 'featured',
                            'operator' => 'IN',
                        ],
                    ],
                ]);

                foreach ($recommended_query->posts as $recommended_post) {
                    $product = wc_get_product($recommended_post->ID);

                    if ($product && $product->is_visible()) {
                        $recommended_products[] = $product;
                    }
                }

                wp_reset_postdata();
            }
        }

        if ($recommended_enabled && !empty($recommended_products)) {
            $render_home_product_slider('', $recommended_title, $recommended_products);
        }
        ?>

        <!-- 3. Benefits Bar -->
        <?php echo do_shortcode('[emsaks_benefits]'); ?>

        <!-- 4. Special Offers -->
        <?php
        $special_offers_enabled = (bool) $home_raw_field('home_special_offers_enabled', true);
        $special_offers_title = $home_field('home_special_offers_title', __('Oferta speciale', 'base-theme'));
        $special_offer_ids = $home_product_ids_from_field($home_raw_field('home_special_offers_products', []));
        $special_offer_products = $special_offers_enabled ? $home_products_from_ids($special_offer_ids) : [];

        if ($special_offers_enabled && !empty($special_offer_products)) {
            $render_home_product_slider('home-special-offers', $special_offers_title, $special_offer_products);
        }
        ?>

        <!-- 5. Featured Categories (conditional) -->
        <?php
        $featured_categories_title = $home_field('home_featured_categories_title', '');
        $featured_categories = $home_field('home_featured_categories', []);
        ?>
        <?php if ($featured_categories_title || !empty($featured_categories)) : ?>
            <section class="home-featured-categories">
                <div class="container">
                    <?php if ($featured_categories_title) : ?>
                        <h2 class="home-section-title"><?php echo esc_html($featured_categories_title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($featured_categories) && is_array($featured_categories)) : ?>
                        <div class="home-category-grid">
                            <?php foreach ($featured_categories as $category_item) :
                                $category_title = $category_item['title'] ?? '';
                                $category_url   = $category_item['url'] ?? '#';
                                $category_image = $category_item['image'] ?? null;
                                $category_image_url = '';
                                $category_image_alt = $category_title;

                                if (is_array($category_image) && !empty($category_image['url'])) {
                                    $category_image_url = $category_image['url'];
                                    $category_image_alt = !empty($category_image['alt']) ? $category_image['alt'] : $category_image_alt;
                                }
                                ?>
                                <a href="<?php echo esc_url($category_url); ?>" class="home-category-card">
                                    <?php if ($category_image_url) : ?>
                                        <img src="<?php echo esc_url($category_image_url); ?>" alt="<?php echo esc_attr($category_image_alt); ?>">
                                    <?php endif; ?>
                                    <?php if ($category_title) : ?>
                                        <span><?php echo esc_html($category_title); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- 6. Image Link Cards -->
        <?php
        $home_link_cards = $home_raw_field('home_link_cards', []);

        if (empty($home_link_cards) || !is_array($home_link_cards)) {
            $legacy_cards = [
                $home_raw_field('home_card_1', []),
                $home_raw_field('home_card_2', []),
            ];

            $home_link_cards = array_values(array_filter($legacy_cards, function ($card) {
                return is_array($card) && (!empty($card['title']) || !empty($card['text']) || !empty($card['image']));
            }));
        }

        $home_link_cards = array_values(array_filter((array) $home_link_cards, function ($card) {
            return is_array($card) && (
                !empty($card['title'])
                || !empty($card['text'])
                || !empty($card['image'])
                || !empty($card['link'])
                || !empty($card['button_url'])
            );
        }));
        $home_link_cards = array_slice($home_link_cards, 0, 2);
        ?>
        <?php if (!empty($home_link_cards)) : ?>
            <section class="home-link-cards">
                <div class="container">
                    <div class="home-link-cards-grid">
                        <?php foreach ($home_link_cards as $card_index => $card_data) :
                            $card_title = $card_data['title'] ?? '';
                            $card_text  = $card_data['text'] ?? '';
                            $card_image = $card_data['image'] ?? null;
                            $card_link  = $card_data['button'] ?? $card_data['link'] ?? [];

                            $card_link_url    = '';
                            $card_link_title  = $card_data['button_text'] ?? __('Mëso më shumë', 'base-theme');
                            $card_link_target = '';

                            if (is_array($card_link)) {
                                $card_link_url    = $card_link['url'] ?? '';
                                $card_link_title  = !empty($card_link['title']) ? $card_link['title'] : $card_link_title;
                                $card_link_target = $card_link['target'] ?? '';
                            } elseif (is_string($card_link)) {
                                $card_link_url = $card_link;
                            }

                            if (!$card_link_url && !empty($card_data['button_url'])) {
                                $card_link_url = $card_data['button_url'];
                            }

                            $card_image_url = '';
                            $card_image_alt = $card_title;

                            if (is_array($card_image) && !empty($card_image['url'])) {
                                $card_image_url = $card_image['url'];
                                $card_image_alt = !empty($card_image['alt']) ? $card_image['alt'] : $card_image_alt;
                            } elseif (is_numeric($card_image)) {
                                $card_image_url = wp_get_attachment_image_url((int) $card_image, 'large');
                                $card_image_alt = get_post_meta((int) $card_image, '_wp_attachment_image_alt', true) ?: $card_image_alt;
                            } elseif (is_string($card_image) && $card_image !== '') {
                                $card_image_url = $card_image;
                            }

                            $card_class = $card_index === 0 ? 'home-link-card-large' : 'home-link-card-small';
                            ?>
                            <article class="home-link-card <?php echo esc_attr($card_class); ?>">
                                <?php if ($card_image_url) : ?>
                                    <img src="<?php echo esc_url($card_image_url); ?>" alt="<?php echo esc_attr($card_image_alt); ?>">
                                <?php endif; ?>
                                <div class="home-link-card-overlay"></div>
                                <div class="home-link-card-content">
                                    <?php if ($card_title) : ?>
                                        <h2><?php echo esc_html($card_title); ?></h2>
                                    <?php endif; ?>
                                    <?php if ($card_text) : ?>
                                        <p><?php echo nl2br(esc_html($card_text)); ?></p>
                                    <?php endif; ?>
                                    <?php if ($card_link_url) : ?>
                                        <a href="<?php echo esc_url($card_link_url); ?>" class="home-link-card-button" <?php echo $card_link_target ? 'target="' . esc_attr($card_link_target) . '" rel="noopener"' : ''; ?>>
                                            <?php echo esc_html($card_link_title); ?>
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>

                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- 7. Blog Section -->
        <?php
        $home_blog_enabled = (bool) $home_raw_field('home_blog_enabled', true);
        $home_blog_title = $home_field('home_blog_title', __('Këshilla dhe lajme nga EMSA', 'base-theme'));
        $home_blog_source = $home_field('home_blog_source', 'latest');
        $home_blog_post_ids = $home_post_ids_from_field($home_raw_field('home_blog_posts', []));

        if ($home_blog_enabled) {
            $home_blog_args = [
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 4,
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ];

            if ($home_blog_source === 'manual' && !empty($home_blog_post_ids)) {
                $home_blog_args['post__in'] = $home_blog_post_ids;
                $home_blog_args['orderby'] = 'post__in';
                $home_blog_args['posts_per_page'] = count($home_blog_post_ids);
            }

            $home_blog_query = new WP_Query($home_blog_args);
            ?>
            <?php if ($home_blog_query->have_posts()) : ?>
                <section class="home-blog-section">
                    <div class="container">
                        <?php if ($home_blog_title) : ?>
                            <h2 class="home-section-title"><?php echo esc_html($home_blog_title); ?></h2>
                        <?php endif; ?>
                        <div class="home-blog-grid">
                            <?php while ($home_blog_query->have_posts()) : $home_blog_query->the_post(); ?>
                                <?php emsaks_render_blog_card(get_the_ID()); ?>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </section>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        <?php } ?>

        <?php if (trim(get_the_content())) : ?>
            <section class="home-content">
                <div class="container">
                    <?php the_content(); ?>
                </div>
            </section>
        <?php endif; ?>

    </main>

<?php endwhile; ?>


<?php get_footer(); ?>
