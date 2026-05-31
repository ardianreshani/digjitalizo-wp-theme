(function () {
  'use strict';

  // ── Accordion: open/close filter sections ────────────────────────────────────
  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('.filter-section-toggle');
    if (!toggle) return;

    var panelId = toggle.getAttribute('aria-controls');
    var panel = panelId ? document.getElementById(panelId) : null;
    if (!panel) return;

    var isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    panel.classList.toggle('is-open', !isOpen);
  });

  // ── Category tree: expand/collapse subcategories ────────────────────────────
  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('.filter-cat-expand');
    if (!toggle) return;

    var row = toggle.closest('.filter-cat-row');
    var children = row && row.nextElementSibling;
    if (!children || !children.classList.contains('filter-cat-children')) return;

    var isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    children.classList.toggle('is-open', !isOpen);
  });

  // ── Mobile filter drawer ──────────────────────────────────────────────────────
  var sidebar  = document.getElementById('archiveSidebar');
  var overlay  = document.getElementById('filterOverlay');
  var filtroBtn = document.getElementById('filtroBtn');
  var closeBtn  = document.getElementById('sidebarClose');

  function openFilter() {
    if (!sidebar) return;
    sidebar.classList.add('is-open');
    if (overlay) {
      overlay.classList.add('active');
      overlay.removeAttribute('aria-hidden');
    }
    document.body.style.overflow = 'hidden';
    if (filtroBtn) filtroBtn.setAttribute('aria-expanded', 'true');
  }

  function closeFilter() {
    if (!sidebar) return;
    sidebar.classList.remove('is-open');
    if (overlay) {
      overlay.classList.remove('active');
      overlay.setAttribute('aria-hidden', 'true');
    }
    document.body.style.overflow = '';
    if (filtroBtn) filtroBtn.setAttribute('aria-expanded', 'false');
  }

  if (filtroBtn) filtroBtn.addEventListener('click', openFilter);
  if (overlay)   overlay.addEventListener('click', closeFilter);
  if (closeBtn)  closeBtn.addEventListener('click', closeFilter);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeFilter();
  });

  // ── Price slider (dual range) ─────────────────────────────────────────────────
  var priceWraps = document.querySelectorAll('.filter-price-wrap');

  priceWraps.forEach(function (wrap) {
    var absMin   = parseFloat(wrap.dataset.absMin);
    var absMax   = parseFloat(wrap.dataset.absMax);
    var rangeMin = wrap.querySelector('.price-range-min');
    var rangeMax = wrap.querySelector('.price-range-max');
    var inputMin = wrap.querySelector('.price-input-min');
    var inputMax = wrap.querySelector('.price-input-max');
    var fill     = wrap.querySelector('.price-slider-fill');
    var applyBtn = wrap.querySelector('.price-apply-btn');

    if (!rangeMin || !rangeMax) return;

    function updateFill() {
      if (!fill) return;
      var range = absMax - absMin;
      if (range <= 0) return;
      var leftPct  = ((parseFloat(rangeMin.value) - absMin) / range) * 100;
      var rightPct = ((absMax - parseFloat(rangeMax.value)) / range) * 100;
      fill.style.left  = leftPct + '%';
      fill.style.right = rightPct + '%';
    }

    rangeMin.addEventListener('input', function () {
      var minVal = parseFloat(rangeMin.value);
      var maxVal = parseFloat(rangeMax.value);
      if (minVal >= maxVal) rangeMin.value = maxVal - 1;
      if (inputMin) inputMin.value = rangeMin.value;
      updateFill();
    });

    rangeMax.addEventListener('input', function () {
      var minVal = parseFloat(rangeMin.value);
      var maxVal = parseFloat(rangeMax.value);
      if (maxVal <= minVal) rangeMax.value = minVal + 1;
      if (inputMax) inputMax.value = rangeMax.value;
      updateFill();
    });

    if (inputMin) {
      inputMin.addEventListener('change', function () {
        var val = Math.max(absMin, Math.min(parseFloat(inputMin.value) || absMin, parseFloat(rangeMax.value) - 1));
        inputMin.value = val;
        rangeMin.value = val;
        updateFill();
      });
    }

    if (inputMax) {
      inputMax.addEventListener('change', function () {
        var val = Math.min(absMax, Math.max(parseFloat(inputMax.value) || absMax, parseFloat(rangeMin.value) + 1));
        inputMax.value = val;
        rangeMax.value = val;
        updateFill();
      });
    }

    if (applyBtn) {
      applyBtn.addEventListener('click', function () {
        var url = new URL(window.location.href);
        url.searchParams.set('min_price', Math.round(parseFloat(rangeMin.value)));
        url.searchParams.set('max_price', Math.round(parseFloat(rangeMax.value)));
        url.searchParams.delete('paged');
        window.location.href = url.toString();
      });
    }

    // Init fill position on page load
    updateFill();
  });

})();
