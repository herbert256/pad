/* colorpicker - the swatches and the code field beside the colour field, all three in step:
   a swatch chosen, a code typed - #rgb or #rrggbb - or a colour picked sets the other two,
   and the colour field, the one the form posts, says it changed. The swatch of the colour
   is pressed. The swatches are buttons in a group, the arrow keys move along them. */
PadInputs.register('colorpicker', function (frame, cfg, kit) {
  var colour = frame.querySelector('input[type=color]');
  var code = frame.querySelector('.pi-color-code');
  var row = frame.querySelector('.pi-swatches');
  if (!colour) return;
  var buttons = [];

  function full(text) {
    var one = String(text).trim().toLowerCase().match(/^#?([0-9a-f]{3}|[0-9a-f]{6})$/);
    if (!one) return null;
    var hex = one[1];
    return '#' + (hex.length === 3 ? hex.replace(/./g, '$&$&') : hex);
  }

  function mark() {
    buttons.forEach(function (button) {
      button.setAttribute('aria-pressed', button.getAttribute('data-colour') === colour.value ? 'true' : 'false');
    });
  }

  function set(value, from) {
    if (value === null) return;
    if (colour.value !== value) {
      colour.value = value;
      kit.changed(colour);
    }
    if (code && from !== code) code.value = value;
    mark();
  }

  (cfg.swatches || []).forEach(function (one, i) {
    var button = kit.el('button', 'pi-swatch');
    button.type = 'button';
    button.setAttribute('data-colour', one);
    button.setAttribute('aria-label', one);
    button.title = one;
    button.tabIndex = i === 0 ? 0 : -1;
    button.style.background = one;
    button.addEventListener('click', function () { set(one); });
    buttons.push(button);
    row.appendChild(button);
  });

  // One tab stop for the row: the arrows move between the swatches, as in a toolbar.
  row.addEventListener('keydown', function (event) {
    var at = buttons.indexOf(document.activeElement);
    if (at < 0) return;
    var to = { ArrowRight: at + 1, ArrowDown: at + 1, ArrowLeft: at - 1, ArrowUp: at - 1, Home: 0, End: buttons.length - 1 }[event.key];
    if (to === undefined) return;
    event.preventDefault();
    to = (to + buttons.length) % buttons.length;
    buttons[at].tabIndex = -1;
    buttons[to].tabIndex = 0;
    buttons[to].focus();
  });

  // A colour picked in the browser's own picker; the code being typed is left as it is typed.
  colour.addEventListener('input', function () { if (code && document.activeElement !== code) code.value = colour.value; mark(); });

  if (code) {
    code.addEventListener('input', function () { set(full(code.value), code); });
    code.addEventListener('change', function () { var value = full(code.value); code.value = value || colour.value; set(value); });
  }

  if (buttons.length) row.hidden = false;
  if (code) code.hidden = false;
  mark();
});
