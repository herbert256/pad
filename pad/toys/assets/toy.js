/* The runtime of the PAD toys (pad/toys/_lib/toy.php writes it once per page). A toy's script
   registers itself - PadToys.register('life', function (k, cfg) { ... }) - and is started on
   every stage of its kind: the ones on the page, and the ones a fragment, a live region or a
   script brings later. k is the toy's kit: its stage, status line, counter line and bar, a
   random number seeded by seed= (the same on every load) or by the browser, a canvas whose
   palette is resolved for the page's light or dark scheme, the pointer over it in the
   canvas's own units, labelled controls, and the animation loop.

   The loop is the kit's, not the toy's, so every toy behaves alike: it runs a frame function
   on each animation frame while the toy plays, holds while the page is hidden or the toy is
   scrolled out of view - and goes on where it was when it comes back - and does not start by
   itself when the visitor asks for reduced motion, or when the tag says paused. The root
   carries the state as data-state: playing, paused or held. */

(function () {
  'use strict';

  if (window.PadToys) return;

  var kinds = {}, kits = [];

  // The colours of the palette, custom properties --pad-toy-<role> (toys/_lib/toy.php).
  var ROLES = ['ink', 'muted', 'line', 'surface', 'canvas', 'grid', 'accent', 'accent2', 'accent3', 'good', 'bad', 'mark', 'night', 'glow'];
  var media = function (q) { return window.matchMedia ? window.matchMedia(q) : { matches: false }; };
  var reduced = media('(prefers-reduced-motion: reduce)');

  // mulberry32: a small, fast generator whose numbers follow from the seed alone.

  function random(seed) {
    var a = seed >>> 0;
    return function () {
      a = (a + 0x6D2B79F5) >>> 0;
      var t = a;
      t = Math.imul(t ^ (t >>> 15), t | 1);
      t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
      return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
  }

  function el(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text !== undefined && text !== null) e.textContent = text;
    return e;
  }

  var uid = 0;

  function Kit(root, cfg) {
    this.root = root;
    this.cfg = cfg;
    this.stage = root.querySelector('.pt-stage');
    this.bar = root.querySelector('.pt-bar');
    this.infoEl = root.querySelector('.pt-info');
    this.statusEl = root.querySelector('.pt-status');
    this.seed = (typeof cfg.seed === 'number') ? cfg.seed : Math.floor(Math.random() * 2147483647);
    this.rand = random(this.seed);
    this.playing = false;
    this.held = false;
    this.frameFn = null;
    this.drawFn = null;
    this.themeFns = [];
    this.raf = 0;
    this.last = 0;
    this.seen = true;
    this.colours = {};
    this.playBtn = null;
    this.probe = el('span', 'pt-hide');
    this.probe.setAttribute('aria-hidden', 'true');
    root.appendChild(this.probe);
    this.keymap = {};
    this.listen();
    this.setState('paused');
  }

  Kit.prototype = {
    el: el,

    // Random numbers from the toy's own generator: what the seed makes the same.
    int: function (n) { return Math.floor(this.rand() * n); },
    range: function (a, b) { return a + this.rand() * (b - a); },
    pick: function (list) { return list[this.int(list.length)]; },
    chance: function (p) { return this.rand() < p; },
    gauss: function () {
      var u = 1 - this.rand(), v = this.rand();
      return Math.sqrt(-2 * Math.log(u)) * Math.cos(2 * Math.PI * v);
    },
    shuffle: function (list) {
      for (var i = list.length - 1; i > 0; i--) {
        var j = this.int(i + 1), t = list[i];
        list[i] = list[j];
        list[j] = t;
      }
      return list;
    },

    // The status line is a live region: it says what changed - started, paused, sorted - and
    // is not written every frame. Counters that change all the time go to the info line.
    status: function (text) { if (this.statusEl.textContent !== text) this.statusEl.textContent = text; },
    info: function (text) { if (this.infoEl.textContent !== text) this.infoEl.textContent = text; },

    // A colour of the palette - 'accent', '--pad-toy-accent' or any CSS colour - as the
    // canvas can paint it: the custom properties are light-dark(...) text, so a probe element
    // resolves them to the colour of the page's scheme, kept until the scheme changes.
    color: function (name) {
      if (!this.colours[name]) {
        this.probe.style.color = ROLES.indexOf(name) >= 0 ? 'var(--pad-toy-' + name + ')' : (name.indexOf('--') === 0 ? 'var(' + name + ')' : name);
        this.colours[name] = getComputedStyle(this.probe).color;
      }
      return this.colours[name];
    },
    rgb: function (name) {
      var m = this.color(name).match(/[\d.]+/g) || [0, 0, 0];
      return [+m[0], +m[1], +m[2]];
    },
    alpha: function (name, a) {
      var c = this.rgb(name);
      return 'rgba(' + c[0] + ',' + c[1] + ',' + c[2] + ',' + a + ')';
    },
    // Whether the toy draws on a dark canvas - the page's dark scheme.
    dark: function () {
      var c = this.rgb('canvas');
      return (0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]) < 128;
    },
    // Called again after the scheme changed, with the colours resolved anew.
    theme: function (fn) { this.themeFns.push(fn); },
    retheme: function () {
      this.colours = {};
      this.themeFns.forEach(function (fn) { fn(); });
      if (!this.playing || this.held) this.redraw();
    },

    // A canvas on the stage, w by h in its own units. It scales down with a narrow page and
    // keeps its proportions; the pointer is handed over in those units. A canvas drawn as
    // pixels - an image, a grid of cells - is one unit a pixel; any other is drawn sharp on
    // a screen of more pixels per unit. role=img and the label say what it shows.
    canvas: function (w, h, label, pixels) {
      var c = el('canvas', 'pt-canvas'), ratio = pixels ? 1 : Math.min(2, window.devicePixelRatio || 1);
      c.width = Math.round(w * ratio);
      c.height = Math.round(h * ratio);
      c.style.width = w + 'px';
      c.setAttribute('role', 'img');
      c.setAttribute('aria-label', label);
      c.tabIndex = 0;
      if (pixels) c.classList.add('is-pixels');
      c.unitW = w;
      c.unitH = h;
      this.stage.appendChild(c);
      var ctx = c.getContext('2d');
      if (ratio !== 1) ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
      c.ratio = ratio;
      return ctx;
    },
    describe: function (canvas, label) { canvas.setAttribute('aria-label', label); },

    // The pointer over a canvas - mouse, pen or finger - in the canvas's units: down(p, e),
    // move(p, e, pressed) and up(p, e), p = { x, y, button, shift }.
    pointer: function (canvas, on) {
      var pressed = false;
      function at(e) {
        var r = canvas.getBoundingClientRect();
        return { x: (e.clientX - r.left) * canvas.unitW / r.width, y: (e.clientY - r.top) * canvas.unitH / r.height,
                 button: e.button, shift: e.shiftKey, alt: e.altKey, type: e.pointerType };
      }
      canvas.addEventListener('pointerdown', function (e) {
        pressed = true;
        try { canvas.setPointerCapture(e.pointerId); } catch (x) {}
        if (on.down && on.down(at(e), e) !== false) e.preventDefault();
      });
      canvas.addEventListener('pointermove', function (e) { if (on.move) on.move(at(e), e, pressed); });
      function end(e) {
        if (!pressed) return;
        pressed = false;
        if (on.up) on.up(at(e), e);
      }
      canvas.addEventListener('pointerup', end);
      canvas.addEventListener('pointercancel', end);
      canvas.addEventListener('pointerleave', function (e) { if (on.leave) on.leave(at(e), e); });
      canvas.addEventListener('contextmenu', function (e) { if (on.context !== false) e.preventDefault(); });
    },

    // Keys the toy reads while it has the focus - added to the ones it has; Space plays and
    // pauses every toy (listen, in the constructor).
    keys: function (map) { for (var key in map) this.keymap[key] = map[key]; },
    listen: function () {
      var kit = this;
      this.root.addEventListener('keydown', function (e) {
        if (e.altKey || e.ctrlKey || e.metaKey) return;
        var t = e.target, tag = t && t.tagName;
        if (tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA' || (tag === 'BUTTON' && (e.key === ' ' || e.key === 'Enter'))) return;
        var fn = kit.keymap[e.key];
        if (!fn && e.key === ' ' && kit.frameFn) fn = function () { kit.toggle(); };
        if (fn && fn(e) !== false) e.preventDefault();
      });
    },

    // The controls, in the bar: a row of them, then buttons, sliders, a list and a box, each
    // with the label a screen reader says.
    row: function () {
      var r = el('div', 'pt-row');
      this.bar.appendChild(r);
      this.rowEl = r;
      return r;
    },
    place: function (node) { (this.rowEl || this.row()).appendChild(node); return node; },
    button: function (label, fn, title) {
      var b = el('button', 'pt-button', label);
      b.type = 'button';
      if (title) { b.title = title; b.setAttribute('aria-label', title); }
      b.addEventListener('click', fn);
      return this.place(b);
    },
    slider: function (label, min, max, step, value, fn, show) {
      var id = 'pt-' + (++uid), wrap = el('label', 'pt-slider'), name = el('span', 'pt-label', label),
          input = el('input'), out = el('output', 'pt-value');
      input.type = 'range';
      input.min = min;
      input.max = max;
      input.step = step;
      input.value = value;
      input.id = id;
      out.htmlFor = id;
      show = show || function (v) { return v; };
      out.textContent = show(+input.value);
      input.addEventListener('input', function () { out.textContent = show(+input.value); fn(+input.value); });
      wrap.appendChild(name);
      wrap.appendChild(input);
      wrap.appendChild(out);
      this.place(wrap);
      input.set = function (v) { input.value = v; out.textContent = show(+input.value); };
      return input;
    },
    select: function (label, options, value, fn) {
      var wrap = el('label', 'pt-select'), name = el('span', 'pt-label', label), s = el('select');
      options.forEach(function (o) {
        var opt = el('option', '', Array.isArray(o) ? o[1] : o);
        opt.value = Array.isArray(o) ? o[0] : o;
        s.appendChild(opt);
      });
      s.value = value;
      s.addEventListener('change', function () { fn(s.value); });
      wrap.appendChild(name);
      wrap.appendChild(s);
      return this.place(wrap);
    },
    check: function (label, value, fn) {
      var wrap = el('label', 'pt-check'), box = el('input');
      box.type = 'checkbox';
      box.checked = !!value;
      box.addEventListener('change', function () { fn(box.checked); });
      wrap.appendChild(box);
      wrap.appendChild(el('span', 'pt-label', label));
      this.place(wrap);
      return box;
    },

    // The animation loop: fn(dt, now) on every frame while the toy plays and is in view - dt
    // the seconds since the frame before, at most a tenth, so a toy coming back from a pause
    // does not jump. draw() repaints a toy that is not playing, after the scheme changed.
    loop: function (fn, draw) { this.frameFn = fn; this.drawFn = draw || null; },
    redraw: function () { if (this.drawFn) this.drawFn(); },
    setState: function (s) { this.root.setAttribute('data-state', s); },
    tick: function (now) {
      this.raf = 0;
      if (!this.playing || this.held) return;
      var dt = this.last ? Math.min(0.1, (now - this.last) / 1000) : 1 / 60;
      this.last = now;
      if (this.frameFn) this.frameFn(dt, now);
      this.schedule();
    },
    schedule: function () {
      var kit = this;
      if (!this.raf && this.playing && !this.held) this.raf = requestAnimationFrame(function (now) { kit.tick(now); });
    },
    // Held while the page is hidden or the toy out of view, without changing what the
    // visitor chose: it goes on where it was once it is seen again.
    hold: function () {
      var held = document.hidden || !this.seen;
      if (held === this.held) return;
      this.held = held;
      if (held && this.raf) { cancelAnimationFrame(this.raf); this.raf = 0; }
      this.last = 0;
      this.setState(this.playing ? (held ? 'held' : 'playing') : 'paused');
      if (!held) this.schedule();
    },
    play: function () {
      if (this.playing) return;
      this.playing = true;
      this.last = 0;
      this.setState(this.held ? 'held' : 'playing');
      if (this.playBtn) { this.playBtn.textContent = 'Pause'; this.playBtn.setAttribute('aria-pressed', 'true'); }
      if (this.onPlay) this.onPlay();
      this.schedule();
    },
    pause: function () {
      if (!this.playing) return;
      this.playing = false;
      if (this.raf) { cancelAnimationFrame(this.raf); this.raf = 0; }
      this.setState('paused');
      if (this.playBtn) { this.playBtn.textContent = 'Play'; this.playBtn.setAttribute('aria-pressed', 'false'); }
      if (this.onPause) this.onPause();
    },
    toggle: function () { if (this.playing) this.pause(); else this.play(); },
    // The Play / Pause button every toy has; the toy starts playing unless reduced motion is
    // asked for or the tag says paused, and the status says why it waits.
    playButton: function () {
      var kit = this;
      this.playBtn = this.button('Play', function () { kit.toggle(); });
      this.playBtn.classList.add('pt-play');
      this.playBtn.setAttribute('aria-pressed', 'false');
      return this.playBtn;
    },
    begin: function (playingText) {
      if (reduced.matches) this.status('Paused, as reduced motion is asked for - press Play to start');
      else if (this.cfg.paused) this.status('Press Play to start');
      else {
        this.play();
        if (playingText) this.status(playingText);
      }
      if (this.playing) return;
      if (this.stillFn) this.stillFn();
      else this.redraw();
    },
    // What a toy that waits shows: the figure it would draw, finished - a still picture in
    // place of an empty canvas.
    still: function (fn) { this.stillFn = fn; }
  };

  function start(root) {
    if (root.padToyKit) return;
    var init = kinds[root.getAttribute('data-pad-toy')];
    if (!init) return;
    var cfg = {};
    try { cfg = JSON.parse(root.getAttribute('data-config') || '{}') || {}; } catch (e) { cfg = {}; }
    var kit = new Kit(root, cfg);
    root.padToyKit = kit;
    root.classList.add('is-ready');
    kits.push(kit);
    if (seer) seer.observe(root);
    init(kit, cfg);
  }

  // The toys of the page, or of a part of it - and of the open shadow roots in it: a toy in a
  // web component's {shadow}, which the document's own querySelectorAll does not see into.
  // A shadow root found is watched for toys that come and go, as the page is.
  function scan(where) {
    where = where || document;
    var list = where.querySelectorAll('.pad-toy[data-pad-toy]');
    for (var i = 0; i < list.length; i++) start(list[i]);
    var all = where.querySelectorAll('*');
    for (var j = 0; j < all.length; j++)
      if (all[j].shadowRoot) { watch(all[j].shadowRoot); scan(all[j].shadowRoot); }
  }

  // A toy that leaves the page - a fragment swapped out - is let go: its loop stops. One in a
  // shadow root is on the page while its host is: isConnected, where the document's contains
  // is false for every node of a shadow tree.
  function prune() {
    kits = kits.filter(function (kit) {
      if (kit.root.isConnected) return true;
      kit.pause();
      if (seer) seer.unobserve(kit.root);
      return false;
    });
  }

  var seer = window.IntersectionObserver ? new IntersectionObserver(function (entries) {
    entries.forEach(function (en) {
      var kit = en.target.padToyKit;
      if (kit) { kit.seen = en.isIntersecting; kit.hold(); }
    });
  }) : null;

  document.addEventListener('visibilitychange', function () { kits.forEach(function (kit) { kit.hold(); }); });

  if (reduced.addEventListener)
    reduced.addEventListener('change', function () { if (reduced.matches) kits.forEach(function (kit) { kit.pause(); }); });

  // The scheme changes with the system, or with the frame's own switch (data-theme on <html>,
  // the padtheme event): every toy resolves its palette anew.
  function retheme() { kits.forEach(function (kit) { kit.retheme(); }); }
  var darkQuery = media('(prefers-color-scheme: dark)');
  if (darkQuery.addEventListener) darkQuery.addEventListener('change', retheme);
  document.addEventListener('padtheme', function () { setTimeout(retheme, 0); });

  window.PadToys = {
    register: function (name, init) { kinds[name] = init; scan(); },
    scan: scan,
    random: random,
    el: el
  };

  document.addEventListener('DOMContentLoaded', function () { scan(); });

  var watcher = window.MutationObserver ? new MutationObserver(function (records) {
    var added = false, removed = false;
    for (var i = 0; i < records.length; i++) {
      if (records[i].type === 'attributes') { retheme(); continue; }
      if (records[i].removedNodes.length) removed = true;
      for (var j = 0; j < records[i].addedNodes.length; j++) {
        var n = records[i].addedNodes[j];
        if (n.nodeType === 1 && (n.matches('.pad-toy') || n.querySelector('.pad-toy') || n.shadowRoot)) added = true;
      }
    }
    if (added) scan();
    if (removed && kits.length) prune();
  }) : null;

  if (watcher) watcher.observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-theme'] });

  var watched = [];
  function watch(root) {
    if (!watcher || watched.indexOf(root) >= 0) return;
    watched.push(root);
    watcher.observe(root, { childList: true, subtree: true });
  }
})();
