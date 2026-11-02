/* Decipher - an effect of PAD with a script (pad/subs/widgets/effects/types/decipher.php). Written once
   per page with the CSP nonce. A {decipher}...{/decipher} lays a layer of changing signs over
   its text when it comes into view - after the delay in its data-pad-decipher - and again when
   the pointer comes on it or the focus into it; the signs settle into the text's letters one
   after the other, left to right, and the layer goes. The text is never touched: it stays in
   the page, read by a screen reader and selected as it is, only made see-through while the
   layer stands; the layer is aria-hidden and takes no clicks, and its text is set as text.
   Effects a fragment or a live region brings later are watched as well. A visitor who asks
   for reduced motion gets no signs. PadDecipher.run ( element ) plays one by hand. */

(function () {

  if (window.PadDecipher) return;

  var still = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;

  if(still&&still.addEventListener)still.addEventListener('change',function(){if(still.matches)document.querySelectorAll('[data-pad-decipher]').forEach(function(el){if(el.padDecipherStop)el.padDecipherStop();});});

  var signs = {
    symbols: '!<>-_/[]{}=+*^?#%&$@~',
    letters: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
    digits:  '0123456789',
    binary:  '01'
  };

  function config(el) {
    try { return JSON.parse(el.getAttribute('data-pad-decipher') || '{}') || {}; } catch (e) { return {}; }
  }

  // The letters of a text as a reader sees them - an accent or an emoji with its modifiers one.

  function letters(text) {
    if (window.Intl && Intl.Segmenter)
      return Array.from(new Intl.Segmenter().segment(text), function (part) { return part.segment; });
    return Array.from(text);
  }

  // The text a reader sees, as the server counts it: the <style> an effect or a widget inside
  // writes, and a <script>, are no part of it.

  function plain(node) {
    var copy = node.cloneNode(true);
    Array.prototype.forEach.call(copy.querySelectorAll('style, script'), function (one) { one.remove(); });
    return copy.textContent;
  }

  function run(el) {

    if (!el || !el.isConnected || el.padDecipherRunning || (still && still.matches)) return;

    var text = el.querySelector('.pe-text');
    if (!text) return;

    var cfg   = config(el);
    var set   = signs[cfg.glyphs] || signs.symbols;
    var parts = letters(plain(text).replace(/\s+/g, ' ').trim());
    var time  = Math.max(200, Math.min(10000, (+cfg.duration || 1.2) * 1000));
    var layer = document.createElement('span');

    layer.className = 'pe-glyphs';
    layer.setAttribute('aria-hidden', 'true');
    el.appendChild(layer);
    el.classList.add('pe-run');
    el.padDecipherRunning = true;

    var start = null, shown = -1, raf = 0;
    function stop(){if(raf)cancelAnimationFrame(raf);layer.remove();el.classList.remove('pe-run');el.padDecipherRunning=false;el.padDecipherStop=null;}
    el.padDecipherStop=stop;

    function frame(now) {

      if (start === null) start = now;

      var t = (now - start) / time;

      if (t >= 1 || !el.isConnected || (still && still.matches)) {
        stop();
        return;
      }

      // A new set of signs every 50 ms; letter i settles once a quarter of the time has gone
      // and its share of the rest.

      var step = Math.floor((now - start) / 50);

      if (step !== shown) {
        shown = step;
        var out = '';
        for (var i = 0; i < parts.length; i++) {
          if (/^\s+$/.test(parts[i]) || t >= .25 + .75 * (i + 1) / parts.length) out += parts[i];
          else out += set.charAt(Math.floor(Math.random() * set.length));
        }
        layer.textContent = out;
      }

      raf=requestAnimationFrame(frame);

    }

    raf=requestAnimationFrame(frame);

  }

  // Each plays once when it is first well in view, after its delay.

  var seen = window.IntersectionObserver ? new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      seen.unobserve(entry.target);
      entry.target.padDecipherTimer=setTimeout(function () { entry.target.padDecipherTimer=null;run(entry.target); }, Math.max(0, Math.min(60, +config(entry.target).delay || 0)) * 1000);
    });
  }, { threshold: .6 }) : null;

  function watch(root) {
    if (!root.querySelectorAll) return;
    var list = Array.prototype.slice.call(root.querySelectorAll('.pad-effect-decipher[data-pad-decipher]'));
    if (root.matches && root.matches('.pad-effect-decipher[data-pad-decipher]')) list.push(root);
    list.forEach(function (el) {
      if (el.padDecipherWatched) return;
      el.padDecipherWatched = true;
      if (seen) seen.observe(el);
    });
  }

  function again(e) {
    var el = e.target.closest && e.target.closest('.pad-effect-decipher[data-pad-decipher]');
    if (el && !(e.relatedTarget && el.contains(e.relatedTarget))) run(el);
  }

  document.addEventListener('mouseover', again);
  document.addEventListener('focusin', again);

  function ready() {
    watch(document);
    if (window.MutationObserver)
      new MutationObserver(function (changes) {
        changes.forEach(function(change){
          Array.prototype.forEach.call(change.removedNodes,function(node){if(node.nodeType!==1)return;var list=Array.prototype.slice.call(node.querySelectorAll('[data-pad-decipher]'));if(node.matches('[data-pad-decipher]'))list.push(node);list.forEach(function(el){if(el.isConnected)return;if(seen)seen.unobserve(el);if(el.padDecipherStop)el.padDecipherStop();if(el.padDecipherTimer){clearTimeout(el.padDecipherTimer);el.padDecipherTimer=null;}el.padDecipherWatched=false;});});
          Array.prototype.forEach.call(change.addedNodes,function(node){if(node.nodeType===1)watch(node);});
        });
      }).observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ready);
  else ready();

  window.PadDecipher = { run: run };

})();
