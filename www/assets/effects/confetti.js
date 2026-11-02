/* Confetti - the one effect of PAD with a script (pad/subs/widgets/effects/types/confetti.php). Written
   once per page with the CSP nonce. A {confetti/} bursts once when it scrolls into view, a
   {confetti}...{/confetti} pair every time something in it is clicked - its button; the ones a
   fragment or a live region brings later as well. The pieces fly on a canvas laid over the
   window for a few seconds, hidden from a screen reader, taking no clicks; seed= in the tag's
   data-pad-confetti makes them the same pieces on every burst and every load. A visitor who
   asks for reduced motion gets no burst. PadConfetti.burst ( element ) bursts one by hand. */

(function () {

  if (window.PadConfetti) return;

  var still = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;

  var active=new Set();
  if(still&&still.addEventListener)still.addEventListener('change',function(){if(still.matches)Array.from(active).forEach(function(stop){stop();});});

  // mulberry32: a small seeded random number, the same numbers for the same seed.

  function seeded(seed) {
    return function () {
      seed = (seed + 0x6D2B79F5) | 0;
      var t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
      t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
      return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
  }

  function config(el) {
    try { return JSON.parse(el.getAttribute('data-pad-confetti') || '{}') || {}; } catch (e) { return {}; }
  }

  // The colours as the canvas needs them: a custom property, light-dark() or a name resolved
  // by the browser on an element inside the effect, where its custom properties hold.

  function colours(el, list) {
    var probe = document.createElement('span'), out = [];
    probe.style.display = 'none';
    el.appendChild(probe);
    list.forEach(function (colour) {
      probe.style.color = '';
      probe.style.color = colour;
      var got = getComputedStyle(probe).color;
      if (got) out.push(got);
    });
    probe.remove();
    return out.length ? out : ['#2563eb', '#db2777', '#0d9488', '#f59e0b'];
  }

  function burst(el) {

    if (!el || !el.isConnected || (still && still.matches)) return;

    var cfg    = config(el);
    var random = seeded((cfg.seed >>> 0) || 1);
    var count  = Math.max(1, Math.min(500, cfg.pieces | 0 || 120));
    var spread = Math.max(10, Math.min(360, +cfg.spread || 70)) * Math.PI / 180;
    var paint  = colours(el, cfg.colors && cfg.colors.length ? cfg.colors
                   : ['var(--pad-effect-accent)', 'var(--pad-effect-accent2)', 'var(--pad-effect-accent3)', '#f59e0b', '#22c55e']);

    var box    = el.getBoundingClientRect();
    var width  = window.innerWidth, height = window.innerHeight;
    var ratio  = window.devicePixelRatio || 1;
    var canvas = document.createElement('canvas');

    canvas.setAttribute('aria-hidden', 'true');
    canvas.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:2147483000';
    canvas.width  = Math.round(width * ratio);
    canvas.height = Math.round(height * ratio);
    document.body.appendChild(canvas);

    var ctx = canvas.getContext('2d');
    if(!ctx){canvas.remove();return;}
    ctx.scale(ratio, ratio);

    var x0 = box.left + box.width / 2, y0 = box.top + box.height / 2, pieces = [];

    for (var i = 0; i < count; i++) {
      var angle = -Math.PI / 2 + (random() - .5) * spread;
      var speed = 7 + random() * 9;
      pieces.push({
        x: x0, y: y0,
        vx: Math.cos(angle) * speed, vy: Math.sin(angle) * speed,
        w: 6 + random() * 6, h: 4 + random() * 5,
        turn: random() * Math.PI, spin: (random() - .5) * .35,
        wobble: random() * Math.PI * 2,
        round: random() < .3,
        colour: paint[Math.floor(random() * paint.length)]
      });
    }

    var start = null, last = null, life = 3200, raf = 0;
    if(!el.padConfettiBursts)el.padConfettiBursts=new Set();
    function stop(){if(raf)cancelAnimationFrame(raf);canvas.remove();active.delete(stop);el.padConfettiBursts.delete(stop);}
    active.add(stop);el.padConfettiBursts.add(stop);

    function frame(now) {
      if(!el.isConnected || (still && still.matches)){stop();return;}
      if (start === null) start = last = now;
      var step = Math.min(3, (now - last) / 16.67), age = now - start;
      last = now;
      ctx.clearRect(0, 0, width, height);
      var fade = age > life * .7 ? Math.max(0, 1 - (age - life * .7) / (life * .3)) : 1, alive = false;
      pieces.forEach(function (p) {
        p.vx *= Math.pow(.985, step);
        p.vy  = p.vy * Math.pow(.985, step) + .32 * step;
        p.wobble += .12 * step;
        p.x += (p.vx + Math.sin(p.wobble) * .6) * step;
        p.y += p.vy * step;
        p.turn += p.spin * step;
        if (p.y < height + 20) alive = true;
        ctx.save();
        ctx.globalAlpha = fade;
        ctx.translate(p.x, p.y);
        ctx.rotate(p.turn);
        ctx.fillStyle = p.colour;
        if (p.round) { ctx.beginPath(); ctx.arc(0, 0, p.h / 2, 0, Math.PI * 2); ctx.fill(); }
        else ctx.fillRect(-p.w / 2, -p.h / 2 * Math.abs(Math.cos(p.wobble)), p.w, p.h * Math.abs(Math.cos(p.wobble)) + .5);
        ctx.restore();
      });
      if (alive && age < life) raf=requestAnimationFrame(frame);
      else stop();
    }

    raf=requestAnimationFrame(frame);

  }

  // The single tags burst once, the first time they come into view.

  var seen = window.IntersectionObserver ? new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      seen.unobserve(entry.target);
      burst(entry.target);
    });
  }, { threshold: .6 }) : null;

  function watch(root) {
    if (!root.querySelectorAll) return;
    var list = Array.prototype.slice.call(root.querySelectorAll('.pad-effect-confetti[data-pad-confetti]'));
    if (root.matches && root.matches('.pad-effect-confetti[data-pad-confetti]')) list.push(root);
    list.forEach(function (el) {
      if (el.padConfettiWatched || config(el).trigger !== 'view') return;
      el.padConfettiWatched = true;
      if (seen) seen.observe(el); else burst(el);
    });
  }

  document.addEventListener('click', function (e) {
    var el = e.target.closest && e.target.closest('.pad-effect-confetti[data-pad-confetti]');
    if (el && config(el).trigger === 'click') burst(el);
  });

  function ready() {
    watch(document);
    if (window.MutationObserver)
      new MutationObserver(function (changes) {
        changes.forEach(function(change){
          Array.prototype.forEach.call(change.removedNodes,function(node){if(node.nodeType!==1)return;var list=Array.prototype.slice.call(node.querySelectorAll('[data-pad-confetti]'));if(node.matches('[data-pad-confetti]'))list.push(node);list.forEach(function(el){if(el.isConnected)return;if(seen)seen.unobserve(el);if(el.padConfettiBursts)Array.from(el.padConfettiBursts).forEach(function(stop){stop();});el.padConfettiWatched=false;});});
          Array.prototype.forEach.call(change.addedNodes,function(node){if(node.nodeType===1)watch(node);});
        });
      }).observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ready);
  else ready();

  window.PadConfetti = { burst: burst };

})();
