/* rangeslider - the two ranges of a lower and an upper number laid on one track, the part
   between them filled. Each stays the native range the form posts - a WAI-ARIA slider of its
   own, reached with Tab and moved with the arrow keys, Page Up and Page Down, Home and End,
   named by its label (From, To) in the group of the legend - and neither passes the other:
   the lower stops at the upper, the upper at the lower. What each holds is said with its
   unit (aria-valuetext) and shown above the track as one line; a press on the track moves
   the nearer thumb there. A reset of the form brings the two it started with back. */
PadInputs.register('rangeslider', function (frame, cfg, kit) {
  var track = frame.querySelector('.pi-range-track');
  var from = frame.querySelector('.pi-range-from input[type=range]');
  var to = frame.querySelector('.pi-range-to input[type=range]');
  var said = frame.querySelector('.pi-range-said');
  if (!track || !from || !to) return;

  var unit = cfg.unit || '';
  var ends = [from, to];
  var outs = ends.map(function (range) {
    var out = range.id ? frame.querySelector('output[for="' + (window.CSS && CSS.escape ? CSS.escape(range.id) : range.id) + '"]') : null;
    if (out) out.hidden = false;
    return out;
  });

  var rail = kit.el('div', 'pi-range-rail');
  rail.setAttribute('aria-hidden', 'true');
  rail.appendChild(kit.el('div', 'pi-range-fill'));
  track.insertBefore(rail, track.firstChild);
  if (said) {
    said.setAttribute('aria-hidden', 'true');
    said.hidden = false;
  }

  function low() { return parseFloat(from.min) || 0; }
  function high() { var max = parseFloat(from.max); return isNaN(max) ? 100 : max; }
  function part(range) {
    var span = high() - low();
    return span > 0 ? Math.min(1, Math.max(0, (parseFloat(range.value) - low()) / span)) : 0;
  }
  function shown(range) { return range.value + unit; }

  // The fill and what is said follow the two; the lower thumb lies on top in the upper half
  // of the track, so two thumbs at the top can still be parted - and the upper in the lower.
  function sync() {
    var a = part(from), b = part(to);
    track.style.setProperty('--pi-lo', String(Math.min(a, b)));
    track.style.setProperty('--pi-hi', String(Math.max(a, b)));
    ends.forEach(function (range, i) {
      range.setAttribute('aria-valuetext', shown(range));
      if (outs[i]) outs[i].textContent = shown(range);
    });
    if (said) said.textContent = shown(from) + ' – ' + shown(to);
    from.classList.toggle('is-top', a > 0.5);
  }

  from.addEventListener('input', function () {
    if (parseFloat(from.value) > parseFloat(to.value)) from.value = to.value;
    sync();
  });
  to.addEventListener('input', function () {
    if (parseFloat(to.value) < parseFloat(from.value)) to.value = from.value;
    sync();
  });

  // A press on the track: the nearer thumb goes there, on a step of the range.
  rail.addEventListener('pointerdown', function (event) {
    if (from.disabled || to.disabled) return;
    var box = rail.getBoundingClientRect();
    var at = box.width > 0 ? Math.min(1, Math.max(0, (event.clientX - box.left) / box.width)) : 0;
    var step = parseFloat(from.step) || 0;
    var value = low() + at * (high() - low());
    if (step > 0) value = low() + Math.round((value - low()) / step) * step;
    var near = Math.abs(value - parseFloat(from.value)) <= Math.abs(value - parseFloat(to.value)) && value <= parseFloat(to.value) ? from : to;
    if (near === to && value < parseFloat(from.value)) near = from;
    near.value = String(value);
    near.dispatchEvent(new Event('input', { bubbles: true }));
    near.dispatchEvent(new Event('change', { bubbles: true }));
    near.focus();
    event.preventDefault();
  });

  // Two ends that come crossed - refilled from a post made without the script - are put in
  // order: the first step of the upper end jumped it onto the lower.
  function order() {
    if (parseFloat(from.value) > parseFloat(to.value)) {
      var lower = to.value;
      to.value = from.value;
      from.value = lower;
    }
    sync();
  }

  if (from.form) from.form.addEventListener('reset', function () { setTimeout(order, 0); });

  order();
});
