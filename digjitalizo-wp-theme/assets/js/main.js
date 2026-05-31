(function ($) {
  'use strict';

  // ── Mini cart drawer ───────────────────────────────────────────────────────
  const $drawer  = $('.mini-cart-drawer');
  const $overlay = $('.mini-cart-overlay');

  function openCart() {
    $drawer.addClass('open').attr('aria-hidden', 'false');
    $overlay.addClass('active');
    $('body').css('overflow', 'hidden');
  }

  function closeCart() {
    $drawer.removeClass('open').attr('aria-hidden', 'true');
    $overlay.removeClass('active');
    $('body').css('overflow', '');
  }

  $(document).on('click', '.mini-cart-toggle', openCart);
  $(document).on('click', '.mini-cart-close', closeCart);
  $(document).on('click', '.mini-cart-overlay', closeCart);
  $(document).on('keydown', function (e) { if (e.key === 'Escape') closeCart(); });
  $(document.body).on('added_to_cart', function () { openCart(); });
  $(document.body).on('wc_cart_button_updated', function (e, $button) {
    if (!$button || !$button.length || !$button.hasClass('product-loop-card-button')) {
      return;
    }

    const $cartLink = $button.siblings('.added_to_cart').first();

    if (!$cartLink.length || $cartLink.find('svg').length) {
      return;
    }

    const $icon = $button.find('svg').first().clone();
    if ($icon.length) {
      $cartLink.append($icon);
    }
  });

  // ── Sticky header reveal on scroll ────────────────────────────────────────
  const $siteHeader = $('.site-header');
  let lastHeaderScroll = window.pageYOffset || document.documentElement.scrollTop || 0;
  let headerScrollTicking = false;

  function updateStickyHeader() {
    if (!$siteHeader.length) {
      return;
    }

    const currentScroll = window.pageYOffset || document.documentElement.scrollTop || 0;
    const revealThreshold = Math.max(130, Math.round(window.innerHeight * 0.25));
    const searchIsFocused = $(document.activeElement).closest('.header-search').length > 0;
    const drawerIsOpen = $('.kategorit-panel.is-open, .mini-cart-drawer.open, .mobile-primary-menu.is-open, .header-account.is-open').length > 0;
    const headerWasSticky = $siteHeader.hasClass('is-sticky');

    if (currentScroll <= 12) {
      $siteHeader.removeClass('is-sticky is-hidden');
    } else if (searchIsFocused || drawerIsOpen) {
      if (currentScroll > revealThreshold || headerWasSticky) {
        $siteHeader.addClass('is-sticky').removeClass('is-hidden');
      }
    } else if (currentScroll > revealThreshold || headerWasSticky) {
      $siteHeader.addClass('is-sticky');

      if (!headerWasSticky || currentScroll > lastHeaderScroll + 2) {
        $siteHeader.addClass('is-hidden');
      } else if (currentScroll < lastHeaderScroll - 2) {
        $siteHeader.removeClass('is-hidden');
      }
    }

    lastHeaderScroll = currentScroll;
  }

  $(window).on('scroll', function () {
    if (headerScrollTicking) {
      return;
    }

    window.requestAnimationFrame(function () {
      updateStickyHeader();
      headerScrollTicking = false;
    });

    headerScrollTicking = true;
  });

  updateStickyHeader();

  // ── Mobile primary menu ───────────────────────────────────────────────────
  function closeMobileMenu() {
    $('.mobile-primary-menu').removeClass('is-open').attr('aria-hidden', 'true');
    $('.header-mobile-menu-toggle').attr('aria-expanded', 'false');
  }

  $(document).on('click', '.header-mobile-menu-toggle', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $menu = $('.mobile-primary-menu');
    const isOpen = $menu.hasClass('is-open');

    if (isOpen) {
      closeMobileMenu();
      return;
    }

    $siteHeader.removeClass('is-hidden');
    $menu.addClass('is-open').attr('aria-hidden', 'false');
    $(this).attr('aria-expanded', 'true');
  });

  $(document).on('click', '.mobile-primary-menu-close, .mobile-primary-menu a', function () {
    closeMobileMenu();
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('.mobile-primary-menu, .header-mobile-menu-toggle').length) {
      return;
    }

    closeMobileMenu();
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeMobileMenu();
    }
  });

  // ── Home page slider ──────────────────────────────────────────────────────
  $('[data-home-slider]').each(function () {
    if (typeof window.Swiper === 'undefined') {
      return;
    }

    const slider = this;
    const slideCount = $(slider).find('.swiper-slide').length;

    if (slideCount < 2) {
      return;
    }

    new window.Swiper($(slider).find('.home-slider-frame')[0], {
      slidesPerView: 1,
      loop: true,
      speed: 500,
      autoHeight: false,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      pagination: {
        el: $(slider).find('.swiper-pagination')[0],
        clickable: true,
        bulletClass: 'home-slider-dot',
        bulletActiveClass: 'is-active',
      },
      navigation: {
        prevEl: $(slider).find('.home-slider-prev')[0],
        nextEl: $(slider).find('.home-slider-next')[0],
      },
    });
  });

  // ── Brands / Partners carousel ───────────────────────────────────────────
  $('[data-brands-slider]').each(function () {
    if (typeof window.Swiper === 'undefined') {
      return;
    }

    const brandsSwiper = $(this).find('.brands-swiper')[0];
    const slideCount = $(brandsSwiper).find('.swiper-slide').length;
    const shouldRotate = slideCount > 5;

    new window.Swiper(brandsSwiper, {
      slidesPerView: 2,
      spaceBetween: 12,
      loop: shouldRotate,
      autoplay: shouldRotate ? {
        delay: 2500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      } : false,
      breakpoints: {
        480: { slidesPerView: 3, spaceBetween: 14 },
        768: { slidesPerView: 4, spaceBetween: 16 },
        1024: { slidesPerView: 5, spaceBetween: 18 },
      },
    });
  });

  // ── Home product sliders ──────────────────────────────────────────────────
  $('[data-product-slider]').each(function () {
    if (typeof window.Swiper === 'undefined') {
      return;
    }

    const slider = this;
    const slideCount = $(slider).find('.swiper-slide').length;

    if (slideCount < 2) {
      return;
    }

    new window.Swiper($(slider).find('.home-products-slider')[0], {
      slidesPerView: 1.2,
      spaceBetween: 14,
      watchOverflow: true,
      navigation: {
        prevEl: $(slider).find('.home-products-prev')[0],
        nextEl: $(slider).find('.home-products-next')[0],
      },
      breakpoints: {
        480: {
          slidesPerView: 2,
          spaceBetween: 16,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 18,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 20,
        },
        1280: {
          slidesPerView: 5,
          spaceBetween: 20,
        },
      },
    });
  });

  // ── Eagle product description overlay on mobile scroll ───────────────────
  const productOverlayQuery = window.matchMedia('(max-width: 767px)');
  let productOverlayObserver = null;

  function resetProductOverlayCards() {
    $('.product-loop-card--has-overlay').removeClass('is-overlay-visible');
  }

  function setupProductOverlayObserver() {
    const cards = document.querySelectorAll('.product-loop-card--has-overlay');

    if (productOverlayObserver) {
      productOverlayObserver.disconnect();
      productOverlayObserver = null;
    }

    resetProductOverlayCards();

    if (!cards.length || !productOverlayQuery.matches || typeof window.IntersectionObserver === 'undefined') {
      return;
    }

    productOverlayObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        $(entry.target).toggleClass('is-overlay-visible', entry.isIntersecting);
      });
    }, {
      threshold: 0.65,
      rootMargin: '-18% 0px -18% 0px',
    });

    cards.forEach(function (card) {
      productOverlayObserver.observe(card);
    });
  }

  setupProductOverlayObserver();

  if (productOverlayQuery.addEventListener) {
    productOverlayQuery.addEventListener('change', setupProductOverlayObserver);
  } else if (productOverlayQuery.addListener) {
    productOverlayQuery.addListener(setupProductOverlayObserver);
  }

  // ── Header account dropdown ───────────────────────────────────────────────
  function closeAccountMenu() {
    $('.header-account').removeClass('is-open');
    $('.header-account-toggle').attr('aria-expanded', 'false');
    $('.header-account-menu').attr('aria-hidden', 'true');
  }

  $(document).on('click', '.header-account-toggle', function (e) {
    e.preventDefault();
    const $account = $(this).closest('.header-account');
    const isOpen = $account.hasClass('is-open');

    closeAccountMenu();

    if (!isOpen) {
      $account.addClass('is-open');
      $(this).attr('aria-expanded', 'true');
      $account.find('.header-account-menu').attr('aria-hidden', 'false');
    }
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('.header-account').length) {
      return;
    }

    closeAccountMenu();
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeAccountMenu();
    }
  });

  // ── Header product search ─────────────────────────────────────────────────
  const $searchForm = $('.header-search');
  const $searchInput = $('.header-search-input');
  const $searchResults = $('.header-search-results');
  const searchText = window.themeData && window.themeData.search ? window.themeData.search : {};
  const minSearchChars = parseInt(searchText.minChars, 10) || 2;
  let searchTimer;
  let searchRequest;

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, function (char) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
      }[char];
    });
  }

  function openSearchResults() {
    $searchForm.addClass('is-searching');
    $searchInput.attr('aria-expanded', 'true');
  }

  function closeSearchResults() {
    $searchForm.removeClass('is-searching');
    $searchInput.attr('aria-expanded', 'false');
  }

  function renderSearchMessage(message) {
    $searchResults.html('<div class="header-search-message">' + escapeHtml(message) + '</div>');
    openSearchResults();
  }

  function renderSearchResults(products, allResultsUrl) {
    if (!products.length) {
      renderSearchMessage(searchText.noResults || 'No products found.');
      return;
    }

    const productItems = products.map(function (product) {
      return [
        '<a class="header-search-result" href="' + escapeHtml(product.url) + '">',
          '<img class="header-search-result-img" src="' + escapeHtml(product.image) + '" alt="">',
          '<span class="header-search-result-body">',
            '<span class="header-search-result-title">' + escapeHtml(product.title) + '</span>',
            '<span class="header-search-result-price">' + (product.price_html || '') + '</span>',
          '</span>',
        '</a>',
      ].join('');
    }).join('');

    const viewAll = allResultsUrl
      ? '<a class="header-search-view-all" href="' + escapeHtml(allResultsUrl) + '">' + escapeHtml(searchText.viewAll || 'View all results') + '</a>'
      : '';

    $searchResults.html(productItems + viewAll);
    openSearchResults();
  }

  function requestProductSearch(term) {
    if (!window.themeData || !window.themeData.ajaxUrl) {
      return;
    }

    if (searchRequest) {
      searchRequest.abort();
    }

    renderSearchMessage(searchText.loading || 'Searching...');

    searchRequest = $.get(window.themeData.ajaxUrl, {
      action: 'emsaks_product_search',
      security: window.themeData.searchNonce || '',
      term: term,
    }).done(function (response) {
      if (!response || !response.success || !response.data) {
        renderSearchMessage(searchText.error || 'Search is unavailable right now.');
        return;
      }

      renderSearchResults(response.data.products || [], response.data.url || '');
    }).fail(function (xhr, status) {
      if (status === 'abort') {
        return;
      }

      renderSearchMessage(searchText.error || 'Search is unavailable right now.');
    });
  }

  $searchInput.on('input', function () {
    const term = $(this).val().trim();
    clearTimeout(searchTimer);

    if (term.length < minSearchChars) {
      closeSearchResults();
      $searchResults.empty();
      return;
    }

    searchTimer = setTimeout(function () {
      requestProductSearch(term);
    }, 300);
  });

  $searchInput.on('focus', function () {
    if ($searchResults.children().length) {
      openSearchResults();
    }
  });

  $(document).on('click', function (e) {
    if (!$searchForm.length || $searchForm[0].contains(e.target)) {
      return;
    }

    closeSearchResults();
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeSearchResults();
    }
  });

	  // Update cart count + subtotal after WC AJAX
	  $(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function () {
	    const fragments = window.WC_Fragments || {};
	    const count = fragments.cart_count !== undefined
	      ? fragments.cart_count
	      : $('.cart-count').first().text();
	    $('.cart-count').text(count);
	  });

  // ── Quantity steppers (mini-cart + cart page) ──────────────────────────────
	  function updateMinicartQty($input, newQty) {
	    const key = $input.data('key');
	    if (!key || !newQty || newQty < 1) return;
	    const ajaxUrl = window.themeData && window.themeData.ajaxUrl
	      ? window.themeData.ajaxUrl
	      : '/wp-admin/admin-ajax.php';

	    $.post(ajaxUrl, {
	      action: 'emsaks_update_cart_item_quantity',
	      security: window.themeData ? window.themeData.cartNonce : '',
	      cart_item_key: key,
	      quantity: newQty,
	    }).done(function (response) {
	      if (!response || !response.success || !response.data || !response.data.fragments) {
	        window.location.reload();
	        return;
	      }

	      $.each(response.data.fragments, function (selector, html) {
	        $(selector).replaceWith(html);
	      });

	      $(document.body).trigger('wc_fragments_refreshed');
	    }).fail(function () {
	      window.location.reload();
	    });
  }

  // Mini cart qty buttons
  $(document).on('click', '.mini-cart-body .qty-minus', function () {
    const $input = $(this).siblings('.qty-input');
    const val = parseInt($input.val(), 10);
    if (val > 1) {
      $input.val(val - 1).trigger('change');
    }
  });

  $(document).on('click', '.mini-cart-body .qty-plus', function () {
    const $input = $(this).siblings('.qty-input');
    const max = parseInt($input.attr('max'), 10) || 99;
    const val = parseInt($input.val(), 10);
    if (val < max) {
      $input.val(val + 1).trigger('change');
    }
  });

  $(document).on('change', '.mini-cart-body .qty-input', function () {
    const $input = $(this);
    const val = parseInt($input.val(), 10);
    const max = parseInt($input.attr('max'), 10) || 99;
    updateMinicartQty($input, Math.min(Math.max(val || 1, 1), max));
  });

  // Cart page qty steppers
  $(document).on('click', '.cart-table .qty-minus', function () {
    const $input = $(this).siblings('input[type=number]');
    const val = parseInt($input.val(), 10);
    if (val > 1) {
      $input.val(val - 1).trigger('change');
    } else if (val === 1) {
      $input.val(0).trigger('change');
    }
  });

  $(document).on('click', '.cart-table .qty-plus', function () {
    const $input = $(this).siblings('input[type=number]');
    const max = parseInt($input.attr('max'), 10) || 99;
    const val = parseInt($input.val(), 10);
    if (val < max) {
      $input.val(val + 1).trigger('change');
    }
  });

  // Auto-submit cart form on qty change (WC standard pattern)
  $(document).on('change', '.cart-table input[type=number]', function () {
    $('[name="update_cart"]').prop('disabled', false);
    $('.woocommerce-cart-form').submit();
  });

	  // ── Payment method toggle (checkout) ──────────────────────────────────────
	  $(document).on('change', 'input[name="payment_method"]', function () {
	    const id = $(this).val();
	    $('.payment-method-content').removeClass('active');
	    $('#payment_box_' + id).addClass('active');
	  });

	  // ── Custom checkout uses one visible customer/address form ────────────────
	  function syncCheckoutHiddenFields() {
	    $('[name="shipping_first_name"]').val($('[name="billing_first_name"]').val() || '');
	    $('[name="shipping_last_name"]').val($('[name="billing_last_name"]').val() || '');
	    $('[name="billing_country"]').val($('[name="shipping_country"]').val() || $('[name="billing_country"]').val() || '');
	    $('[name="billing_address_1"]').val($('[name="shipping_address_1"]').val() || $('[name="billing_address_1"]').val() || '');
	    $('[name="billing_postcode"]').val($('[name="shipping_postcode"]').val() || $('[name="billing_postcode"]').val() || '');
	    $('[name="billing_city"]').val($('[name="shipping_city"]').val() || $('[name="billing_city"]').val() || '');
	    $('[name="billing_state"]').val($('[name="shipping_state"]').val() || $('[name="billing_state"]').val() || '');
	  }

	  function syncCheckoutHiddenFieldsEarly(e) {
	    if (!e.target || !e.target.matches('[name="billing_first_name"], [name="billing_last_name"], [name="shipping_country"], [name="shipping_address_1"], [name="shipping_postcode"], [name="shipping_city"], [name="shipping_state"]')) {
	      return;
	    }

	    syncCheckoutHiddenFields();
	  }

	  document.addEventListener('change', syncCheckoutHiddenFieldsEarly, true);
	  document.addEventListener('input', syncCheckoutHiddenFieldsEarly, true);

	  let checkoutUpdateTimer;
	  function queueCheckoutUpdate(target, delay) {
	    clearTimeout(checkoutUpdateTimer);
	    checkoutUpdateTimer = setTimeout(function () {
	      $(document.body).trigger('update_checkout', { current_target: target });
	    }, delay || 250);
	  }

	  $(document).on('input change', '[name="billing_first_name"], [name="billing_last_name"], [name="shipping_country"], [name="shipping_address_1"], [name="shipping_postcode"], [name="shipping_city"], [name="shipping_state"]', syncCheckoutHiddenFields);
	  $(document).on('change', '[name="shipping_country"], [name="shipping_state"]', function () {
	    syncCheckoutHiddenFields();
	    queueCheckoutUpdate(this, 50);
	  });
	  $(document).on('input change', '[name="shipping_address_1"], [name="shipping_postcode"], [name="shipping_city"]', function () {
	    syncCheckoutHiddenFields();
	    queueCheckoutUpdate(this, 500);
	  });
	  $(document).on('change', 'form.checkout input.shipping_method', function () {
	    syncCheckoutHiddenFields();
	    queueCheckoutUpdate(this, 50);
	  });
	  $(document).on('submit', 'form.checkout', syncCheckoutHiddenFields);
	  syncCheckoutHiddenFields();

	  // ── Checkout coupon proxy: keep WooCommerce's native hidden coupon form ──
	  function submitCheckoutCouponProxy() {
	    const code = $('#checkout_coupon_proxy').val();
	    const $nativeForm = $('form.checkout_coupon');

	    if (!code || !$nativeForm.length) return;

	    $nativeForm.find('input[name="coupon_code"]').val(code);
	    $nativeForm.trigger('submit');
	  }

	  $(document).on('click', '#checkout_coupon_proxy_apply', submitCheckoutCouponProxy);
	  $(document).on('keydown', '#checkout_coupon_proxy', function (e) {
	    if (e.key === 'Enter') {
	      e.preventDefault();
	      submitCheckoutCouponProxy();
	    }
	  });

  // ── WooCommerce fragment refresh updates ──────────────────────────────────
  $(document.body).on('wc_fragments_refreshed', function () {
    const subtotal = $('.mini-cart-subtotal').text();
    if (subtotal) {
      $('.mini-cart-total .mini-cart-subtotal').text(subtotal);
    }
  });

  // ── Single product qty +/- ───────────────────────────────────────────────
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.qty-btn--minus, .qty-btn--plus');
    if (!btn) return;

    var wrap  = btn.closest('.qty-wrap');
    if (!wrap) return;
    var input = wrap.querySelector('.qty-input');
    if (!input) return;

    var step = parseFloat(input.getAttribute('step')) || 1;
    var min  = parseFloat(input.getAttribute('min'));
    var max  = parseFloat(input.getAttribute('max'));
    if (isNaN(min)) min = 0;
    if (isNaN(max) || max <= 0) max = Infinity;

    var val  = parseFloat(input.value) || 0;
    var next = btn.classList.contains('qty-btn--minus')
      ? Math.max(min, val - step)
      : Math.min(max, val + step);

    if (next !== val) {
      input.value = next;
      input.dispatchEvent(new Event('input', { bubbles: true }));
    }
  });

  // ── Password show/hide toggle ────────────────────────────────────────────
  $(document).on('click', '.auth-toggle-pw', function () {
    const $wrap = $(this).closest('.auth-password-wrap');
    const $input = $wrap.find('input');
    const isPassword = $input.attr('type') === 'password';
    $input.attr('type', isPassword ? 'text' : 'password');
    $wrap.find('.eye-show').toggleClass('hidden', !isPassword);
    $wrap.find('.eye-hide').toggleClass('hidden', isPassword);
  });

  // ── Out-of-stock inquiry dialog ───────────────────────────────────────────
  (function () {
    const $dialog = $('#inquiry-dialog');
    if (!$dialog.length) return;

    const $productName = $dialog.find('.inquiry-dialog-product-name');

    function openDialog(name) {
      $productName.text(name || '');
      $dialog.find('input[name="product-name"]').val(name || '');
      $dialog.addClass('is-open').attr('aria-hidden', 'false');
      $('body').addClass('inquiry-dialog-open');
      $dialog.find('.inquiry-dialog-close').trigger('focus');
    }

    function closeDialog() {
      $dialog.removeClass('is-open').attr('aria-hidden', 'true');
      $('body').removeClass('inquiry-dialog-open');
    }

    $(document).on('click', '[data-inquiry-trigger]', function () {
      openDialog($(this).data('product-name'));
    });

    $dialog.on('click', '.inquiry-dialog-close, .inquiry-dialog-backdrop', closeDialog);

    $(document).on('keydown', function (e) {
      if (e.key === 'Escape' && $dialog.hasClass('is-open')) closeDialog();
    });
  })();

})(jQuery);
