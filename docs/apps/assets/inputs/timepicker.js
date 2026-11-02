/* timepicker - the times to choose from as a list under the time field: a listbox the arrow
   keys walk, Home and End to its ends, a typed digit to the first time it begins with; Enter
   takes the time, Escape closes and gives the focus back. The list starts at the field's
   time, or the last one before it. The field stays what the form posts; the browser's own
   list of the datalist is left out where this one is. */
PadInputs.register('timepicker', function (frame, cfg, kit) {
  var field = frame.querySelector('input[type=time]');
  var opener = frame.querySelector('.pi-time-open');
  var anchor = frame.querySelector('.pi-anchor');
  var slots = cfg.slots || [];
  if (!field || !opener || !anchor || !slots.length) return;

  field.removeAttribute('list');

  var list = kit.el('ul', 'pi-popup pi-time-list');
  list.id = kit.id((field.id || 'time') + '-times');
  list.setAttribute('role', 'listbox');
  list.setAttribute('aria-label', kit.label(frame) || 'Times');
  list.tabIndex = -1;
  list.hidden = true;

  var options = slots.map(function (slot) {
    var li = kit.el('li', 'pi-time-option', slot);
    li.id = kit.id(list.id);
    li.setAttribute('role', 'option');
    li.setAttribute('data-time', slot);
    li.setAttribute('aria-selected', 'false');
    list.appendChild(li);
    return li;
  });

  anchor.appendChild(list);
  opener.setAttribute('aria-controls', list.id);
  opener.hidden = false;

  var active = -1;

  function activate(at) {
    if (active >= 0) options[active].classList.remove('is-active');
    active = Math.max(0, Math.min(options.length - 1, at));
    options[active].classList.add('is-active');
    list.setAttribute('aria-activedescendant', options[active].id);
    options[active].scrollIntoView({ block: 'nearest' });
  }

  // A list that runs past midnight - 22:00 to 06:00 - is in the order of its times from its
  // first: 01:00 comes after 23:30 there.
  var wraps = slots.some(function (slot, i) { return i > 0 && slot < slots[i - 1]; });
  function since(time) {
    var minutes = +time.slice(0, 2) * 60 + +time.slice(3, 5);
    return wraps ? (minutes - (+slots[0].slice(0, 2) * 60 + +slots[0].slice(3, 5)) + 1440) % 1440 : minutes;
  }

  function show() {
    var value = (field.value || '').slice(0, 5);
    var at = 0;
    options.forEach(function (one, i) {
      var time = one.getAttribute('data-time');
      one.setAttribute('aria-selected', time === value ? 'true' : 'false');
      if (value && since(time) <= since(value)) at = i;
    });
    list.hidden = false;
    opener.setAttribute('aria-expanded', 'true');
    activate(at);
    list.focus();
  }

  function hide(giveBack) {
    if (list.hidden) return;
    list.hidden = true;
    opener.setAttribute('aria-expanded', 'false');
    if (giveBack) opener.focus();
  }

  function take(at) {
    field.value = options[at].getAttribute('data-time');
    kit.changed(field);
    hide(true);
  }

  opener.addEventListener('click', function () { if (list.hidden) show(); else hide(true); });

  list.addEventListener('click', function (event) {
    var li = event.target.closest('.pi-time-option');
    if (li) take(options.indexOf(li));
  });

  list.addEventListener('keydown', function (event) {
    var key = event.key;
    if (key === 'ArrowDown') activate(active + 1);
    else if (key === 'ArrowUp') activate(active - 1);
    else if (key === 'Home') activate(0);
    else if (key === 'End') activate(options.length - 1);
    else if (key === 'PageDown') activate(active + 6);
    else if (key === 'PageUp') activate(active - 6);
    else if (key === 'Enter' || key === ' ') take(active);
    else if (key === 'Escape') hide(true);
    else if (key === 'Tab') { hide(false); return; }
    else if (/^\d$/.test(key)) {
      for (var i = 1; i <= options.length; i++) {
        var at = (active + i) % options.length;
        if (options[at].getAttribute('data-time').charAt(0) === key || options[at].getAttribute('data-time').replace(/^0/, '').charAt(0) === key) { activate(at); break; }
      }
    } else return;
    event.preventDefault();
  });

  kit.outside(anchor, function () { hide(false); });
});
