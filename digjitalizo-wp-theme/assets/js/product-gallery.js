(function ($) {
    'use strict';

    var $gallery, $mainImg, $thumbs;
    var images   = []; /* [{src, srcset, sizes, alt}] */
    var current  = 0;  /* index of image shown in main view */

    /* ── Init ─────────────────────────────────────────────────────────────── */
    function init() {
        $gallery = $('.pga');
        if (!$gallery.length) return;

        $mainImg = $gallery.find('.pga__main-img');
        $thumbs  = $gallery.find('.pga__thumb');

        /* Build images array from thumbnails (+ main image if no thumbs) */
        if ($thumbs.length) {
            $thumbs.each(function () {
                var $t = $(this);
                images.push({
                    src:    $t.data('full'),
                    srcset: $t.data('srcset') || '',
                    sizes:  $t.data('sizes')  || '',
                    alt:    $t.data('alt')    || '',
                });
            });
        } else {
            images.push({
                src:    $gallery.data('original-src'),
                srcset: $gallery.data('original-srcset') || '',
                sizes:  $gallery.data('original-sizes')  || '',
                alt:    $gallery.data('original-alt')    || '',
            });
        }

        /* Thumbnail click */
        $thumbs.on('click', function () {
            var idx = $thumbs.index(this);
            if (idx === current) return;
            goTo(idx);
        });

        /* Click main image → lightbox */
        $gallery.find('.pga__main').on('click', function () {
            lightboxOpen(current);
        });

        $gallery.find('.pga__nav--prev').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            navMain(-1);
        });

        $gallery.find('.pga__nav--next').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            navMain(1);
        });

        buildLightbox();

        /* Variation image swap */
        $('form.variations_form')
            .on('found_variation', onFoundVariation)
            .on('reset_data',      onResetData);
    }

    /* ── Gallery navigation ───────────────────────────────────────────────── */
    function goTo(idx) {
        current = idx;
        var img = images[idx];
        setMain(img.src, img.srcset, img.sizes, img.alt);
        $thumbs.removeClass('is-active').eq(idx).addClass('is-active');
    }

    function navMain(dir) {
        if (images.length < 2) return;

        var next = (current + dir + images.length) % images.length;
        goTo(next);
    }

    function setMain(src, srcset, sizes, alt) {
        $mainImg.attr('src', src || '');
        if (srcset) {
            $mainImg.attr('srcset', srcset).attr('sizes', sizes || '');
        } else {
            $mainImg.removeAttr('srcset').removeAttr('sizes');
        }
        $mainImg.attr('alt', alt || '');
    }

    /* ── Variation events ─────────────────────────────────────────────────── */
    function onFoundVariation(e, variation) {
        if (!variation.image || !variation.image.src) return;

        var img    = variation.image;
        var src    = img.full_src || img.src;
        var srcset = img.srcset   || '';
        var sizes  = img.sizes    || '';
        var alt    = img.alt      || '';

        /* Activate matching thumbnail if found */
        var matched = -1;
        $thumbs.each(function (i) {
            if ($(this).data('full') === src || $(this).data('full') === img.src) {
                matched = i;
                return false;
            }
        });

        if (matched >= 0) {
            current = matched;
            $thumbs.removeClass('is-active').eq(matched).addClass('is-active');
        } else {
            $thumbs.removeClass('is-active');
            current = 0;
        }

        setMain(src, srcset, sizes, alt);
    }

    function onResetData() {
        setMain(
            $gallery.data('original-src'),
            $gallery.data('original-srcset'),
            $gallery.data('original-sizes'),
            $gallery.data('original-alt')
        );
        current = 0;
        $thumbs.removeClass('is-active').first().addClass('is-active');
    }

    /* ── Lightbox ─────────────────────────────────────────────────────────── */
    var $lb, $lbImg, $lbClose, $lbPrev, $lbNext, $lbCounter;
    var lbActive = false;
    var lbIndex  = 0;
    var lbImages = []; /* images visible in lightbox — may differ from gallery during variation swap */

    function buildLightbox() {
        $lb = $([
            '<div class="pga-lb" role="dialog" aria-modal="true" aria-label="Image viewer" aria-hidden="true">',
            '  <button class="pga-lb__close" aria-label="Close">&times;</button>',
            '  <button class="pga-lb__prev"  aria-label="Previous">&#8249;</button>',
            '  <button class="pga-lb__next"  aria-label="Next">&#8250;</button>',
            '  <div class="pga-lb__stage">',
            '    <img class="pga-lb__img" src="" alt="">',
            '  </div>',
            '  <div class="pga-lb__counter"></div>',
            '</div>',
        ].join('')).appendTo('body');

        $lbImg     = $lb.find('.pga-lb__img');
        $lbClose   = $lb.find('.pga-lb__close');
        $lbPrev    = $lb.find('.pga-lb__prev');
        $lbNext    = $lb.find('.pga-lb__next');
        $lbCounter = $lb.find('.pga-lb__counter');

        $lbClose.on('click', lightboxClose);

        /* Click the dark backdrop (not the image) to close */
        $lb.on('click', function (e) {
            if ($(e.target).is($lb) || $(e.target).is($lb.find('.pga-lb__stage'))) lightboxClose();
        });

        $lbPrev.on('click', function () { lightboxNav(-1); });
        $lbNext.on('click', function () { lightboxNav(1); });

        $(document).on('keydown', function (e) {
            if (!lbActive) return;
            if (e.key === 'Escape')     lightboxClose();
            if (e.key === 'ArrowLeft')  lightboxNav(-1);
            if (e.key === 'ArrowRight') lightboxNav(1);
        });
    }

    function lightboxOpen(startIdx) {
        /* Use current gallery images (may have a variation-specific image as main) */
        lbImages = images.slice();

        /* If variation changed the main without adding it to `images`, inject it */
        var mainSrc = $mainImg.attr('src');
        if (lbImages.length && lbImages[0].src !== mainSrc) {
            /* Variation-specific image: show it first */
            lbImages.unshift({
                src:    mainSrc,
                srcset: $mainImg.attr('srcset') || '',
                sizes:  $mainImg.attr('sizes')  || '',
                alt:    $mainImg.attr('alt')    || '',
            });
            startIdx = 0;
        }

        lbIndex  = startIdx;
        lbActive = true;
        $lb.attr('aria-hidden', 'false').addClass('is-open');
        $('body').css('overflow', 'hidden');
        lightboxShow(lbIndex);
    }

    function lightboxShow(idx) {
        var img = lbImages[idx];
        $lbImg.attr({
            src:    img.src,
            srcset: img.srcset || '',
            sizes:  img.sizes  || '',
            alt:    img.alt    || '',
        });
        if (!img.srcset) $lbImg.removeAttr('srcset').removeAttr('sizes');

        $lbPrev.toggle(lbImages.length > 1);
        $lbNext.toggle(lbImages.length > 1);
        $lbCounter.text(lbImages.length > 1 ? (idx + 1) + ' / ' + lbImages.length : '');
    }

    function lightboxNav(dir) {
        lbIndex = (lbIndex + dir + lbImages.length) % lbImages.length;
        lightboxShow(lbIndex);
    }

    function lightboxClose() {
        lbActive = false;
        $lb.attr('aria-hidden', 'true').removeClass('is-open');
        $('body').css('overflow', '');
    }

    $(init);

}(jQuery));
