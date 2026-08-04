/* TCE client scripts. No dependencies. */
(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var burger = document.getElementById('burger');
  var nav = document.getElementById('primaryNav');
  var backdrop = document.getElementById('navBackdrop');

  function setMenu(open) {
    if (!burger || !nav) return;
    nav.classList.toggle('is-open', open);
    burger.setAttribute('aria-expanded', String(open));
    burger.setAttribute('aria-label', open ? 'Menyunu bağla' : 'Menyunu aç');
    if (backdrop) backdrop.hidden = !open;
    document.body.style.overflow = open ? 'hidden' : '';
  }

  if (burger && nav) {
    burger.addEventListener('click', function () {
      setMenu(!nav.classList.contains('is-open'));
    });

    if (backdrop) {
      backdrop.addEventListener('click', function () { setMenu(false); });
    }

    nav.addEventListener('click', function (event) {
      if (event.target.closest('a')) setMenu(false);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && nav.classList.contains('is-open')) {
        setMenu(false);
        burger.focus();
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 1024) setMenu(false);
    });
  }

  var header = document.getElementById('siteHeader');
  if (header) {
    var onScroll = function () {
      header.classList.toggle('is-stuck', window.scrollY > 8);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  var grid = document.getElementById('projectGrid');
  if (grid) {
    var buttons = document.querySelectorAll('.filter');
    var empty = document.getElementById('projectEmpty');

    Array.prototype.forEach.call(buttons, function (button) {
      button.addEventListener('click', function () {
        var value = button.dataset.filter;

        Array.prototype.forEach.call(buttons, function (b) {
          b.classList.toggle('is-active', b === button);
        });

        var shown = 0;
        Array.prototype.forEach.call(grid.children, function (card) {
          var match = value === '*' || card.dataset.category === value;
          card.classList.toggle('is-hidden', !match);
          if (match) shown += 1;
        });

        if (empty) empty.hidden = shown !== 0;
      });
    });
  }

  var revealTargets = document.querySelectorAll(
    '.section-head, .split__main, .feature, .process__item, .project-card, .service-card, .stat--lg'
  );

  if (!reduced && 'IntersectionObserver' in window && revealTargets.length) {
    Array.prototype.forEach.call(revealTargets, function (el) { el.classList.add('reveal'); });

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    Array.prototype.forEach.call(revealTargets, function (el) { observer.observe(el); });
  }

  var counters = document.querySelectorAll('[data-count]');
  if (!reduced && 'IntersectionObserver' in window && counters.length) {
    var countObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;

        var el = entry.target;
        var target = parseInt(el.dataset.count, 10) || 0;
        var suffix = el.textContent.replace(/[0-9]/g, '');
        var start = performance.now();
        var dur = 1100;

        function tick(now) {
          var p = Math.min((now - start) / dur, 1);
          var eased = 1 - Math.pow(1 - p, 3);
          el.textContent = Math.round(target * eased) + suffix;
          if (p < 1) requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
        countObserver.unobserve(el);
      });
    }, { threshold: 0.5 });

    Array.prototype.forEach.call(counters, function (el) { countObserver.observe(el); });
  }
})();
