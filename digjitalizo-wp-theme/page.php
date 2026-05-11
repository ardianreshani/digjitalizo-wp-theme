<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<?php
// WC shop/archive/product pages are NOT pages — they use their own templates.
// This file only handles WordPress pages, which includes WC's cart/checkout/account pages.
// Those pages contain shortcodes and need the_content() but with appropriate wrappers.
$is_cart_page     = function_exists('is_cart')         && is_cart();
$is_checkout_page = function_exists('is_checkout')     && is_checkout();
$is_account_page  = function_exists('is_account_page') && is_account_page();
?>

<?php if ($is_cart_page || $is_checkout_page) : ?>

    <div class="container py-8">
        <?php the_content(); ?>
    </div>

<?php elseif ($is_account_page) : ?>

    <main class="woo-main container py-12">
        <?php the_content(); ?>
    </main>

<?php else : ?>

    <div class="container py-10">
        <div class="max-w-3xl">
            <?php the_content(); ?>
        </div>
    </div>

<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
