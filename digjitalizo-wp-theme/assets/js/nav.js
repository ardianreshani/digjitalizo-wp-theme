(function () {
  'use strict';

  var btns     = document.querySelectorAll('#kategoritBtn, [data-kategorit-open]');
  var panel    = document.getElementById('kategorit-panel');
  var closeBtn = document.getElementById('kategoritClose');

  if (!btns.length || !panel) return;

  function setExpanded(isExpanded) {
    btns.forEach(function (btn) {
      btn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
    });
  }

  function openPanel() {
    var siteHeader = document.querySelector('.site-header');
    if (siteHeader) {
      siteHeader.classList.remove('is-hidden');
    }

    panel.classList.add('is-open');
    panel.setAttribute('aria-hidden', 'false');
    setExpanded(true);
  }

  function closePanel() {
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden', 'true');
    setExpanded(false);
    setTimeout(resetToRoot, 310);
  }

  function resetToRoot() {
    panel.querySelectorAll('.cat-level').forEach(function (l) {
      l.classList.remove('is-active');
    });
    var root = document.getElementById('cat-level-root');
    if (root) root.classList.add('is-active');
  }

  // Toggle buttons — stopPropagation so the document listener below doesn't also fire
  btns.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      if (panel.classList.contains('is-open')) {
        closePanel();
      } else {
        openPanel();
      }
    });
  });

  // X close button inside the panel
  if (closeBtn) {
    closeBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      closePanel();
    });
  }

  // Clicks inside the panel stay inside — don't bubble to document
  panel.addEventListener('click', function (e) {
    e.stopPropagation();

    // Drill into sub-level
    var drillBtn = e.target.closest('.cat-item-drill');
    if (drillBtn) {
      var targetEl = document.getElementById(drillBtn.dataset.target);
      if (!targetEl) return;
      var current = panel.querySelector('.cat-level.is-active');
      if (current) current.classList.remove('is-active');
      targetEl.classList.add('is-active');
      return;
    }

    // Back navigation
    var backBtn = e.target.closest('.cat-back-btn');
    if (backBtn) {
      var targetEl = document.getElementById(backBtn.dataset.target);
      if (!targetEl) return;
      var current = panel.querySelector('.cat-level.is-active');
      if (current) current.classList.remove('is-active');
      targetEl.classList.add('is-active');
    }
  });

  // Click anywhere outside the panel closes it
  document.addEventListener('click', function () {
    if (panel.classList.contains('is-open')) {
      closePanel();
    }
  });

  // Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && panel.classList.contains('is-open')) {
      closePanel();
      btns[0].focus();
    }
  });

})();
