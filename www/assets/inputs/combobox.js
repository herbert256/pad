/* combobox - the WAI-ARIA combobox with a list that autocompletes: the field gets
   role="combobox" and the list under it role="listbox", the choice the arrow keys stand on is
   the field's aria-activedescendant - the focus stays in the field, typing goes on. The list
   holds the choices that contain what is typed - in any case, accents or not - at most 50,
   those that begin with it first, the matching part marked; it opens as the field is typed
   in, with Alt+Down or Down, and closes with Escape, a choice taken, or the focus leaving.
   Escape in a closed list empties the field. How many choices there are is said to a screen
   reader. The browser's own list of the datalist is left out where this one is. */
PadInputs.register('combobox', function (frame, cfg, kit) {
  var field = frame.querySelector('.pi-anchor input[type=text]');
  var anchor = frame.querySelector('.pi-anchor');
  var said = frame.querySelector('.pi-combo-said');
  var choices = cfg.choices || [];
  if (!field || !anchor || !choices.length) return;

  field.removeAttribute('list');

  var list = kit.el('ul', 'pi-popup pi-combo-list');
  list.id = kit.id((field.id || 'combo') + '-list');
  list.setAttribute('role', 'listbox');
  list.setAttribute('aria-label', kit.label(frame) || 'Choices');
  list.hidden = true;
  anchor.appendChild(list);

  field.setAttribute('role', 'combobox');
  field.setAttribute('aria-autocomplete', 'list');
  field.setAttribute('aria-expanded', 'false');
  field.setAttribute('aria-controls', list.id);

  function fold(text) {
    return String(text).normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
  }

  var folded = choices.map(fold);
  var shown = [], active = -1, timer = null;

  function say(text) {
    clearTimeout(timer);
    timer = setTimeout(function () { if (said) said.textContent = text; }, 400);
  }

  function option(text, at, typed) {
    var li = kit.el('li', 'pi-combo-option');
    li.id = list.id + '-' + at;
    li.setAttribute('role', 'option');
    li.setAttribute('aria-selected', 'false');
    var found = typed ? fold(text).indexOf(typed) : -1;
    if (found >= 0 && fold(text).length === text.length) {
      li.appendChild(document.createTextNode(text.slice(0, found)));
      li.appendChild(kit.el('mark', '', text.slice(found, found + typed.length)));
      li.appendChild(document.createTextNode(text.slice(found + typed.length)));
    } else li.textContent = text;
    return li;
  }

  function fill() {
    var typed = fold(field.value.trim());
    var starts = [], holds = [];
    folded.forEach(function (one, i) {
      if (!typed || one.indexOf(typed) === 0) starts.push(i);
      else if (one.indexOf(typed) > 0) holds.push(i);
    });
    shown = starts.concat(holds).slice(0, 50);
    list.textContent = '';
    shown.forEach(function (i, at) { list.appendChild(option(choices[i], at, typed)); });
    active = -1;
    field.removeAttribute('aria-activedescendant');
    return typed;
  }

  function open() {
    fill();
    if (!shown.length) { close(); say('No choices hold what is typed'); return; }
    list.hidden = false;
    field.setAttribute('aria-expanded', 'true');
    say(shown.length === 1 ? '1 choice' : shown.length + ' choices');
  }

  function close() {
    list.hidden = true;
    field.setAttribute('aria-expanded', 'false');
    field.removeAttribute('aria-activedescendant');
    active = -1;
  }

  function activate(at) {
    var items = list.children;
    if (active >= 0 && items[active]) { items[active].classList.remove('is-active'); items[active].setAttribute('aria-selected', 'false'); }
    active = (at + shown.length) % shown.length;
    items[active].classList.add('is-active');
    items[active].setAttribute('aria-selected', 'true');
    field.setAttribute('aria-activedescendant', items[active].id);
    items[active].scrollIntoView({ block: 'nearest' });
  }

  // A choice taken says the field changed, as typing does - and that is no typing that opens
  // the list again.
  var taking = false;

  function take(at) {
    field.value = choices[shown[at]];
    close();
    taking = true;
    kit.changed(field);
    taking = false;
  }

  field.addEventListener('input', function () { if (!taking) open(); });

  field.addEventListener('keydown', function (event) {
    var key = event.key;
    if (key === 'ArrowDown') {
      event.preventDefault();
      if (list.hidden) { open(); if (!event.altKey && shown.length) activate(0); }
      else if (shown.length) activate(active + 1);
    } else if (key === 'ArrowUp') {
      event.preventDefault();
      if (list.hidden) { open(); if (shown.length) activate(shown.length - 1); }
      else if (shown.length) activate(active - 1);
    } else if (key === 'Enter') {
      if (!list.hidden && active >= 0) { event.preventDefault(); take(active); }
    } else if (key === 'Escape') {
      if (!list.hidden) { event.preventDefault(); close(); }
      else if (field.value !== '') { event.preventDefault(); field.value = ''; taking = true; kit.changed(field); taking = false; }
    } else if (key === 'Tab') close();
  });

  // A pointer chooses without the field losing the focus.
  list.addEventListener('mousedown', function (event) { event.preventDefault(); });
  list.addEventListener('click', function (event) {
    var li = event.target.closest('.pi-combo-option');
    if (li) { take(Array.prototype.indexOf.call(list.children, li)); field.focus(); }
  });

  kit.outside(anchor, close);
  field.addEventListener('blur', function () { setTimeout(function () { if (document.activeElement !== field) close(); }, 0); });
});
