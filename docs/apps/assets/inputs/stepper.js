/* stepper - the minus and the plus: a step of the number within its bounds, as the arrow keys
   step it. The buttons stay out of the tab order - the number itself takes the arrows - and
   held down they keep stepping. */
PadInputs.register('stepper', function (frame, cfg, kit) {
  var number = frame.querySelector('input[type=number]');
  if (!number) return;
  kit.show(frame, '.pi-step');

  function step(by) {
    if (number.disabled || number.readOnly) return;
    if (number.value === '') {
      number.value = number.min !== '' ? number.min : '0';
      if (by > 0 && number.min !== '') { kit.changed(number); return; }
    }
    try {
      if (by > 0) number.stepUp(); else number.stepDown();
    } catch (e) {
      var size = parseFloat(number.step) || 1;
      number.value = String((parseFloat(number.value) || 0) + by * size);
    }
    kit.changed(number);
  }

  Array.prototype.forEach.call(frame.querySelectorAll('.pi-step'), function (button) {
    var by = +button.getAttribute('data-step');
    var timer = null;
    function stop() { clearTimeout(timer); clearInterval(timer); timer = null; }
    button.addEventListener('click', function (event) { if (event.detail === 0) step(by); });
    button.addEventListener('pointerdown', function (event) {
      event.preventDefault();
      step(by);
      number.focus();
      timer = setTimeout(function () { timer = setInterval(function () { step(by); }, 80); }, 400);
    });
    ['pointerup', 'pointerleave', 'pointercancel'].forEach(function (type) { button.addEventListener(type, stop); });
  });
});
