/* slider - the value beside the range, and the value a screen reader says, as it moves. */
PadInputs.register('slider', function (frame, cfg) {
  var range = frame.querySelector('input[type=range]');
  var out = frame.querySelector('.pi-slider-out');
  if (!range) return;
  function say() {
    var text = range.value + (cfg.unit || '');
    if (out) out.textContent = text;
    range.setAttribute('aria-valuetext', text);
  }
  range.addEventListener('input', say);
  say();
});
