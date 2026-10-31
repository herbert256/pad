/* otp - a box per character over the one-time code. The text field the server wrote becomes
   the hidden field the form posts; the boxes - a group named by the field's label - take one
   character each, and the first box takes the field's id, so the label and the messages are
   its. A character typed moves the focus on, Backspace back - emptying the box before it when
   it is empty itself - the arrow keys, Home and End move along, and a code pasted or offered
   by the phone (autocomplete="one-time-code", on the first box) fills the boxes from where it
   lands - the boxes have no maxlength, so a code a phone puts in the first box whole is
   spread over them rather than cut to its first character. A character the code cannot hold
   is not taken. */
PadInputs.register('otp', function (frame, cfg, kit) {
  var whole = frame.querySelector('.pi-otp-whole');
  if (!whole) return;
  var length = cfg.length || 6;
  var allowed = cfg.letters ? /[A-Za-z0-9]/ : /[0-9]/;
  var id = whole.id;
  var label = frame.querySelector('label[for="' + (window.CSS && CSS.escape ? CSS.escape(id) : id) + '"]');
  var invalid = whole.getAttribute('aria-invalid');
  var described = whole.getAttribute('aria-describedby');

  var group = kit.el('div', 'pi-otp-boxes');
  group.setAttribute('role', 'group');
  if (label) {
    if (!label.id) label.id = kit.id(id + '-label');
    group.setAttribute('aria-labelledby', label.id);
  } else group.setAttribute('aria-label', kit.label(frame));

  var boxes = [];
  for (var i = 0; i < length; i++) {
    var box = document.createElement('input');
    box.type = 'text';
    box.className = 'pi-otp-box';
    box.setAttribute('inputmode', cfg.letters ? 'text' : 'numeric');
    box.setAttribute('autocomplete', i === 0 ? 'one-time-code' : 'off');
    box.setAttribute('aria-label', 'Character ' + (i + 1) + ' of ' + length);
    box.spellcheck = false;
    if (cfg.letters) box.setAttribute('autocapitalize', 'characters');
    if (whole.disabled) box.disabled = true;
    boxes.push(box);
    group.appendChild(box);
  }

  // The first box is the field the label and the messages point at.
  whole.removeAttribute('id');
  boxes[0].id = id;
  if (invalid) boxes.forEach(function (box) { box.setAttribute('aria-invalid', invalid); });
  if (described) boxes[0].setAttribute('aria-describedby', described);
  whole.removeAttribute('aria-invalid');
  whole.removeAttribute('aria-describedby');
  whole.setAttribute('data-pad-shows', id);
  whole.type = 'hidden';
  whole.parentNode.insertBefore(group, whole);

  function clean(text) {
    return Array.from(String(text)).filter(function (c) { return allowed.test(c); }).map(function (c) { return c.toUpperCase(); });
  }

  function post() {
    whole.value = boxes.map(function (box) { return box.value; }).join('');
    kit.changed(whole);
  }

  function fill(from, text) {
    var chars = clean(text);
    for (var i = 0; i < chars.length && from + i < length; i++) boxes[from + i].value = chars[i];
    var next = Math.min(length - 1, from + chars.length);
    boxes[next].focus();
    boxes[next].select();
    post();
  }

  clean(whole.value).slice(0, length).forEach(function (c, i) { boxes[i].value = c; });

  boxes.forEach(function (box, i) {
    box.addEventListener('focus', function () { box.select(); });

    box.addEventListener('input', function () {
      var chars = clean(box.value);
      box.value = '';
      if (chars.length > 1) { fill(i, chars.join('')); return; }
      if (chars.length === 1) {
        box.value = chars[0];
        if (i < length - 1) boxes[i + 1].focus();
      }
      post();
    });

    box.addEventListener('keydown', function (event) {
      var to = null;
      if (event.key === 'Backspace' && box.value === '' && i > 0) {
        boxes[i - 1].value = '';
        to = i - 1;
        post();
      } else if (event.key === 'ArrowLeft') to = i - 1;
      else if (event.key === 'ArrowRight') to = i + 1;
      else if (event.key === 'Home') to = 0;
      else if (event.key === 'End') to = length - 1;
      else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey && !allowed.test(event.key)) { event.preventDefault(); return; }
      if (to === null) return;
      event.preventDefault();
      to = Math.max(0, Math.min(length - 1, to));
      boxes[to].focus();
    });

    box.addEventListener('paste', function (event) {
      var text = (event.clipboardData || window.clipboardData).getData('text');
      if (!text) return;
      event.preventDefault();
      fill(i, text);
    });
  });
});
