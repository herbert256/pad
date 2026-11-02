/* stars - the button that takes a rating back, for a field that is not required: a radio
   group once chosen cannot be emptied otherwise. It shows while a star is chosen. */
PadInputs.register('stars', function (frame, cfg, kit) {
  var radios = frame.querySelectorAll('.pi-star-input');
  var clear = frame.querySelector('.pi-stars-clear');
  if (!radios.length || !clear || !cfg.clear) return;
  var name = kit.label(frame);
  clear.setAttribute('aria-label', 'Clear ' + (name || 'the rating'));
  function chosen() {
    for (var i = 0; i < radios.length; i++) if (radios[i].checked) return radios[i];
    return null;
  }
  function sync() { clear.hidden = !chosen(); }
  frame.addEventListener('change', sync);
  clear.addEventListener('click', function () {
    var one = chosen();
    if (!one) return;
    one.checked = false;
    sync();
    kit.changed(radios[0]);
    radios[0].focus();
  });
  sync();
});
