/**
 * SR Mac Shop — advanced image magnifier (lens + flyout + wheel zoom + touch).
 * Attach to .image-magnifier roots after DOM ready.
 */
(function () {
  'use strict';

  var MIN_ZOOM = 2;
  var MAX_ZOOM = 4;
  var DEFAULT_ZOOM_CARD = 2.5;
  var DEFAULT_ZOOM_PRODUCT = 3;

  function clamp(n, a, b) {
    return Math.max(a, Math.min(b, n));
  }

  function parseZoom(el, fallback) {
    var z = parseFloat(el.getAttribute('data-magnifier-zoom') || '');
    if (!isFinite(z)) return fallback;
    return clamp(z, MIN_ZOOM, MAX_ZOOM);
  }

  /**
   * @param {HTMLElement} root
   * @param {{ variant?: 'card'|'product', zoom?: number }} [opts]
   */
  function attachMagnifier(root, opts) {
    if (!root || root.dataset.magnifierAttached === '1') return;

    opts = opts || {};
    var variant = opts.variant || (root.classList.contains('image-magnifier--product') ? 'product' : 'card');
    var zoomLevel =
      typeof opts.zoom === 'number'
        ? clamp(opts.zoom, MIN_ZOOM, MAX_ZOOM)
        : parseZoom(root, variant === 'product' ? DEFAULT_ZOOM_PRODUCT : DEFAULT_ZOOM_CARD);

    var frame = root.querySelector('.image-magnifier__frame');
    var img = frame && frame.querySelector('img');
    var lens = root.querySelector('.image-magnifier__lens');
    var panel = root.querySelector('.image-magnifier__panel');
    var fill = root.querySelector('.image-magnifier__panel-fill');
    var zoomUi = root.querySelector('.image-magnifier__zoom-ui');

    if (!img || !lens || !panel || !fill) return;

    root.dataset.magnifierAttached = '1';

    var raf = 0;
    var lastEv = null;
    var active = false;

    function naturalDims() {
      var w = img.naturalWidth;
      var h = img.naturalHeight;
      if (w > 0 && h > 0) return { w: w, h: h };
      var r = img.getBoundingClientRect();
      return { w: r.width || 1, h: r.height || 1 };
    }

    function updateZoomDisplay() {
      if (!zoomUi) return;
      var label = zoomUi.querySelector('[data-zoom-label]');
      if (label) label.textContent = zoomLevel.toFixed(1) + '×';
    }

    function show(on) {
      active = on;
      lens.hidden = !on;
      panel.hidden = !on;
      panel.setAttribute('aria-hidden', on ? 'false' : 'true');
      if (!on && raf) {
        cancelAnimationFrame(raf);
        raf = 0;
      }
    }

    function apply(ev) {
      var rect = img.getBoundingClientRect();
      if (rect.width < 8 || rect.height < 8) return;

      var nx = ev.clientX - rect.left;
      var ny = ev.clientY - rect.top;
      nx = clamp(nx, 0, rect.width);
      ny = clamp(ny, 0, rect.height);

      var nat = naturalDims();
      var scaleX = nat.w / rect.width;
      var scaleY = nat.h / rect.height;

      var ix = nx * scaleX;
      var iy = ny * scaleY;

      var lensW = variant === 'product' ? Math.min(120, rect.width * 0.22) : Math.min(72, rect.width * 0.28);
      var lensH = lensW;

      var lx = clamp(nx - lensW / 2, 0, rect.width - lensW);
      var ly = clamp(ny - lensH / 2, 0, rect.height - lensH);

      lens.style.width = lensW + 'px';
      lens.style.height = lensH + 'px';
      lens.style.left = lx + 'px';
      lens.style.top = ly + 'px';

      var pw =
        variant === 'product'
          ? Math.min(310, Math.floor(window.innerWidth * 0.46))
          : 176;
      var ph = pw;

      var src = (img.currentSrc || img.src || '').replace(/"/g, '\\"');
      fill.style.backgroundImage = src ? 'url("' + src + '")' : 'none';
      fill.style.backgroundSize = nat.w * zoomLevel + 'px ' + nat.h * zoomLevel + 'px';
      fill.style.backgroundPosition =
        -(ix * zoomLevel - pw / 2) + 'px ' + -(iy * zoomLevel - ph / 2) + 'px';

      if (variant === 'card') {
        var px = rect.right + 12;
        if (px + pw > window.innerWidth - 8) {
          px = rect.left - pw - 12;
        }
        if (px < 8) {
          px = clamp(rect.left, 8, window.innerWidth - pw - 8);
        }
        var py = clamp(ev.clientY - ph / 2, 8, window.innerHeight - ph - 8);
        panel.style.left = px + 'px';
        panel.style.top = py + 'px';
        panel.style.position = 'fixed';
      } else {
        var pxx = rect.right + 16;
        if (pxx + pw > window.innerWidth - 12) {
          pxx = rect.left - pw - 16;
        }
        if (pxx < 12) {
          pxx = clamp(rect.left, 12, window.innerWidth - pw - 12);
        }
        var pyy = clamp(rect.top + rect.height / 2 - ph / 2, 12, window.innerHeight - ph - 12);
        panel.style.left = pxx + 'px';
        panel.style.top = pyy + 'px';
        panel.style.position = 'fixed';
      }
    }

    function loop() {
      raf = 0;
      if (!active || !lastEv) return;
      apply(lastEv);
    }

    function onMove(ev) {
      lastEv = ev;
      if (!raf) raf = requestAnimationFrame(loop);
    }

    function onEnter(ev) {
      show(true);
      lastEv = ev;
      apply(ev);
    }

    function onLeave() {
      lastEv = null;
      show(false);
    }

    frame.addEventListener('mouseenter', onEnter);
    frame.addEventListener('mousemove', onMove);
    frame.addEventListener('mouseleave', onLeave);

    frame.addEventListener(
      'wheel',
      function (e) {
        if (!active || variant !== 'product') return;
        e.preventDefault();
        var delta = e.deltaY > 0 ? -0.15 : 0.15;
        zoomLevel = clamp(zoomLevel + delta, MIN_ZOOM, MAX_ZOOM);
        root.setAttribute('data-magnifier-zoom', String(zoomLevel));
        updateZoomDisplay();
        if (lastEv) apply(lastEv);
      },
      { passive: false }
    );

    if (zoomUi) {
      zoomUi.querySelectorAll('[data-zoom-step]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var step = parseFloat(btn.getAttribute('data-zoom-step') || '0');
          zoomLevel = clamp(zoomLevel + step, MIN_ZOOM, MAX_ZOOM);
          root.setAttribute('data-magnifier-zoom', String(zoomLevel));
          updateZoomDisplay();
          if (lastEv) apply(lastEv);
        });
      });
    }

    function touchToMouse(te) {
      var t = te.touches[0];
      return { clientX: t.clientX, clientY: t.clientY };
    }

    frame.addEventListener('touchstart', function (e) {
      if (e.touches.length !== 1) return;
      e.preventDefault();
      show(true);
      lastEv = touchToMouse(e);
      apply(lastEv);
    }, { passive: false });

    frame.addEventListener('touchmove', function (e) {
      if (e.touches.length !== 1) return;
      e.preventDefault();
      lastEv = touchToMouse(e);
      if (!raf) raf = requestAnimationFrame(loop);
    }, { passive: false });

    frame.addEventListener('touchend', function () {
      show(false);
    });

    img.addEventListener('load', function () {
      if (lastEv) apply(lastEv);
    });

    updateZoomDisplay();
  }

  function initAll() {
    document.querySelectorAll('.image-magnifier[data-magnifier-auto]').forEach(function (el) {
      var v = el.classList.contains('image-magnifier--product') ? 'product' : 'card';
      attachMagnifier(el, { variant: v });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  window.SrMacMagnifier = { attach: attachMagnifier, initAll: initAll };
})();
