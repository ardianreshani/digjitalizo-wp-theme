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

  // ── Password show/hide toggle ────────────────────────────────────────────
  $(document).on('click', '.auth-toggle-pw', function () {
    const $wrap = $(this).closest('.auth-password-wrap');
    const $input = $wrap.find('input');
    const isPassword = $input.attr('type') === 'password';
    $input.attr('type', isPassword ? 'text' : 'password');
    $wrap.find('.eye-show').toggleClass('hidden', !isPassword);
    $wrap.find('.eye-hide').toggleClass('hidden', isPassword);
  });

})(jQuery);
