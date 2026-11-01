/* multiselect - chips for the choices made, and a filter box over the list of the choices:
   the WAI-ARIA listbox that chooses many (aria-multiselectable). The select the server wrote
   stays what the form posts, hidden: a choice made or let go here is made in it and said with
   its input and change events, and the chips and the list show what it holds - a page's
   script that changes it, a form reset, are shown too. The filter box takes the select's id,
   so the label, the server's message and the browser's checker of the rules go by it (the
   select's data-pad-shows), and its required: a hidden select the browser cannot focus
   refused the send without a word. The focus stays in the filter box, the option the arrow
   keys stand on its aria-activedescendant: Down or Up open the list and walk it - with Shift
   they choose the option walked to - Space or Enter choose the option stood on or let it go,
   Enter the one option a filter leaves; Escape closes the list, then empties the filter;
   Backspace in the empty filter takes the last chip out, and every chip has a button that
   takes it out. Typing filters the list - in any case, accents or not. What changes is said
   to a screen reader, politely. Nothing moves: no transition, the list scrolled at once. A
   field disabled - by its attribute or by a <fieldset disabled> round it - opens nothing and
   changes nothing, as the native select. */
PadInputs.register('multiselect', function (frame, cfg, kit) {
  var select = frame.querySelector('select[multiple]');
  var said = frame.querySelector('.pi-ms-said');
  if (!select) return;

  var options = Array.prototype.slice.call(select.options);
  var label = kit.label(frame) || 'Choices';

  var anchor = kit.el('div', 'pi-anchor pi-ms-anchor');
  var box = kit.el('div', 'pi-ms');
  var chips = kit.el('ul', 'pi-chips');
  var filter = kit.el('input', 'pi-ms-filter');
  var list = kit.el('ul', 'pi-popup pi-ms-list');

  filter.type = 'text';
  filter.setAttribute('autocomplete', 'off');
  filter.spellcheck = false;
  if (cfg.filter) filter.placeholder = cfg.filter;

  // The id, the name a screen reader says and the server's message go to the filter box.
  if (select.id) {
    filter.id = select.id;
    select.id = kit.id(select.id + '-select');
  } else filter.id = kit.id('multiselect');
  select.setAttribute('data-pad-shows', filter.id);
  ['aria-label', 'aria-labelledby', 'aria-describedby', 'aria-invalid'].forEach(function (name) {
    if (!select.hasAttribute(name)) return;
    filter.setAttribute(name, select.getAttribute(name));
    select.removeAttribute(name);
  });

  var needed = select.required;
  select.required = false;
  if (needed) filter.setAttribute('aria-required', 'true');
  filter.disabled = select.disabled;

  list.id = kit.id(filter.id + '-list');
  list.setAttribute('role', 'listbox');
  list.setAttribute('aria-multiselectable', 'true');
  list.setAttribute('aria-label', label);
  list.hidden = true;

  filter.setAttribute('role', 'combobox');
  filter.setAttribute('aria-autocomplete', 'list');
  filter.setAttribute('aria-expanded', 'false');
  filter.setAttribute('aria-controls', list.id);
  chips.setAttribute('aria-label', label);

  function fold(text) {
    return String(text).normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
  }

  var folded = options.map(function (option) { return fold(option.text); });

  var items = options.map(function (option, i) {
    var li = kit.el('li', 'pi-ms-option');
    li.id = list.id + '-' + i;
    li.setAttribute('role', 'option');
    if (option.disabled) li.setAttribute('aria-disabled', 'true');
    var check = kit.el('span', 'pi-ms-check');
    check.setAttribute('aria-hidden', 'true');
    li.appendChild(check);
    li.appendChild(document.createTextNode(option.text));
    list.appendChild(li);
    return li;
  });

  box.appendChild(chips);
  box.appendChild(filter);
  anchor.appendChild(box);
  anchor.appendChild(list);
  select.parentNode.insertBefore(anchor, select.nextSibling);
  select.hidden = true;

  var shown = [], current = -1, timer = null;

  // A field disabled - by its attribute, or by a <fieldset disabled> round it, which leaves
  // select.disabled false - opens nothing and changes nothing, as the native select: asked
  // each time, as a script may enable the fieldset again.
  function locked() {
    return select.matches(':disabled');
  }

  function say(text) {
    clearTimeout(timer);
    timer = setTimeout(function () { if (said) said.textContent = text; }, 150);
  }

  // The chips and the marks of the list, from what the select holds.
  function render() {
    var count = 0;
    chips.textContent = '';
    options.forEach(function (option, i) {
      items[i].setAttribute('aria-selected', option.selected ? 'true' : 'false');
      if (!option.selected) return;
      count++;
      var li = kit.el('li', 'pi-chip');
      li.appendChild(kit.el('span', 'pi-chip-text', option.text));
      var x = kit.el('button', 'pi-chip-x', '×');
      x.type = 'button';
      x.disabled = locked() || option.disabled;
      x.setAttribute('aria-label', 'Remove ' + option.text);
      x.addEventListener('click', function () { choose(i, false); filter.focus(); });
      li.appendChild(x);
      chips.appendChild(li);
    });
    chips.hidden = !count;
    filter.setCustomValidity(needed && !count ? label + ' is required' : '');
    return count;
  }

  function choose(i, on) {
    var option = options[i];
    if (!option || option.disabled || locked() || option.selected === on) return;
    option.selected = on;
    render();
    say(option.text + (on ? ' chosen' : ' removed'));
    kit.changed(select);
  }

  function activate(i) {
    if (current >= 0) items[current].classList.remove('is-active');
    current = i;
    if (i < 0) { filter.removeAttribute('aria-activedescendant'); return; }
    items[i].classList.add('is-active');
    filter.setAttribute('aria-activedescendant', items[i].id);
    items[i].scrollIntoView({ block: 'nearest' });
  }

  function fill() {
    var typed = fold(filter.value.trim());
    shown = [];
    items.forEach(function (li, i) {
      var hit = !typed || folded[i].indexOf(typed) >= 0;
      li.hidden = !hit;
      if (hit) shown.push(i);
    });
    activate(-1);
  }

  function open() {
    if (locked()) return;
    fill();
    if (!shown.length) { close(); say('No choices hold what is typed'); return; }
    list.hidden = false;
    filter.setAttribute('aria-expanded', 'true');
    say(shown.length === 1 ? '1 choice' : shown.length + ' choices');
  }

  function close() {
    list.hidden = true;
    filter.setAttribute('aria-expanded', 'false');
    activate(-1);
  }

  function move(step) {
    if (!shown.length) return;
    var at = shown.indexOf(current);
    at = at < 0 ? (step > 0 ? 0 : shown.length - 1) : (at + step + shown.length) % shown.length;
    activate(shown[at]);
  }

  filter.addEventListener('input', open);

  filter.addEventListener('keydown', function (event) {
    var key = event.key;
    if (key === 'ArrowDown' || key === 'ArrowUp') {
      event.preventDefault();
      if (list.hidden) { open(); if (event.altKey) return; }
      move(key === 'ArrowDown' ? 1 : -1);
      if (event.shiftKey && current >= 0) choose(current, true);
    } else if (key === 'Enter' || (key === ' ' && current >= 0 && !list.hidden)) {
      if (list.hidden) return;
      event.preventDefault();
      var i = current >= 0 ? current : (shown.length === 1 ? shown[0] : -1);
      if (i < 0) return;
      activate(i);
      choose(i, !options[i].selected);
    } else if (key === 'Escape') {
      if (!list.hidden) { event.preventDefault(); close(); }
      else if (filter.value !== '') { event.preventDefault(); filter.value = ''; fill(); }
    } else if (key === 'Backspace' && filter.value === '') {
      var last = -1;
      options.forEach(function (option, at) { if (option.selected && !option.disabled) last = at; });
      if (last >= 0) { event.preventDefault(); choose(last, false); }
    } else if (key === 'Tab') close();
  });

  filter.addEventListener('click', function () { if (list.hidden) open(); });

  // A click in the frame of the chips puts the focus in the filter box, as in a text field.
  box.addEventListener('click', function (event) {
    if (event.target !== box && event.target !== chips) return;
    filter.focus();
    if (list.hidden) open();
  });

  // A pointer chooses without the filter box losing the focus.
  list.addEventListener('mousedown', function (event) { event.preventDefault(); });
  list.addEventListener('click', function (event) {
    var li = event.target.closest('.pi-ms-option');
    if (!li) return;
    var i = items.indexOf(li);
    activate(i);
    choose(i, !options[i].selected);
    filter.focus();
  });

  kit.outside(anchor, close);
  filter.addEventListener('blur', function () { setTimeout(function () { if (document.activeElement !== filter) close(); }, 0); });

  select.addEventListener('change', render);
  if (select.form) select.form.addEventListener('reset', function () { setTimeout(render, 0); });

  render();
});
