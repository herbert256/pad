/* datepicker - the month over the date field: a dialog with the month's days as a grid, the
   way the WAI-ARIA date picker dialog is built. The field stays what the form posts, and the
   day chosen is written into it as 2026-10-31 - it says it changed, as a typed day does. The
   arrow keys walk the days, Page Up and Page Down the months (with Shift the years), Home
   and End the week; Enter or Space takes the day, Escape closes, and the focus goes back to
   the button that opened it. Days outside min= and max= cannot be chosen. The names of the
   months and the days are the browser's, in the page's language. */
PadInputs.register('datepicker', function (frame, cfg, kit) {
  var field = frame.querySelector('input[type=date]');
  var opener = frame.querySelector('.pi-date-open');
  var anchor = frame.querySelector('.pi-anchor');
  if (!field || !opener || !anchor) return;

  var lang = document.documentElement.lang || undefined;
  var first = cfg.weekstart === 'sunday' ? 0 : 1;
  var name = kit.label(frame);
  var monthName = new Intl.DateTimeFormat(lang, { month: 'long', year: 'numeric', timeZone: 'UTC' });
  var dayName = new Intl.DateTimeFormat(lang, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
  var shortDay = new Intl.DateTimeFormat(lang, { weekday: 'short', timeZone: 'UTC' });
  var longDay = new Intl.DateTimeFormat(lang, { weekday: 'long', timeZone: 'UTC' });

  // A day is a number of days since 1970-01-01, in UTC: no clock change in between.
  function parse(text) {
    var one = /^(\d{4})-(\d{2})-(\d{2})$/.exec(text || '');
    if (!one) return null;
    var day = Date.UTC(+one[1], +one[2] - 1, +one[3]) / 86400000;
    return iso(day) === text ? day : null;
  }
  function iso(day) {
    var d = new Date(day * 86400000);
    return String(d.getUTCFullYear()).padStart(4, '0') + '-' + String(d.getUTCMonth() + 1).padStart(2, '0') + '-' + String(d.getUTCDate()).padStart(2, '0');
  }
  function parts(day) {
    var d = new Date(day * 86400000);
    return { y: d.getUTCFullYear(), m: d.getUTCMonth(), d: d.getUTCDate(), w: d.getUTCDay() };
  }
  function addMonths(day, n) {
    var p = parts(day);
    var last = new Date(Date.UTC(p.y, p.m + n + 1, 0)).getUTCDate();
    return Date.UTC(p.y, p.m + n, Math.min(p.d, last)) / 86400000;
  }
  function today() {
    var now = new Date();
    return Date.UTC(now.getFullYear(), now.getMonth(), now.getDate()) / 86400000;
  }

  var dialog = kit.el('div', 'pi-popup pi-calendar');
  dialog.id = kit.id((field.id || 'date') + '-calendar');
  dialog.setAttribute('role', 'dialog');
  dialog.setAttribute('aria-modal', 'false');
  dialog.setAttribute('aria-label', 'Choose ' + (name || 'a day'));
  dialog.hidden = true;

  var head = kit.el('div', 'pi-cal-head');
  var back = kit.el('button', 'pi-button pi-cal-step', '‹');
  back.type = 'button';
  back.setAttribute('aria-label', 'Previous month');
  var title = kit.el('div', 'pi-cal-title');
  title.id = dialog.id + '-title';
  title.setAttribute('aria-live', 'polite');
  var ahead = kit.el('button', 'pi-button pi-cal-step', '›');
  ahead.type = 'button';
  ahead.setAttribute('aria-label', 'Next month');
  head.appendChild(back);
  head.appendChild(title);
  head.appendChild(ahead);

  var grid = kit.el('table', 'pi-cal-grid');
  grid.setAttribute('role', 'grid');
  grid.setAttribute('aria-labelledby', title.id);
  var thead = kit.el('thead');
  var names = kit.el('tr');
  for (var i = 0; i < 7; i++) {
    var sample = Date.UTC(2024, 0, 7 + first + i);  // 2024-01-07 is a Sunday
    var th = kit.el('th', '', shortDay.format(sample));
    th.setAttribute('scope', 'col');
    th.setAttribute('abbr', longDay.format(sample));
    names.appendChild(th);
  }
  thead.appendChild(names);
  var tbody = kit.el('tbody');
  grid.appendChild(thead);
  grid.appendChild(tbody);

  var foot = kit.el('div', 'pi-cal-foot');
  var now = kit.el('button', 'pi-button', 'Today');
  now.type = 'button';
  var close = kit.el('button', 'pi-button', 'Close');
  close.type = 'button';
  foot.appendChild(now);
  foot.appendChild(close);

  dialog.appendChild(head);
  dialog.appendChild(grid);
  dialog.appendChild(foot);
  anchor.appendChild(dialog);
  opener.setAttribute('aria-controls', dialog.id);
  opener.hidden = false;

  var focus = today();

  function allowed(day) {
    var low = parse(field.min), high = parse(field.max);
    return (low === null || day >= low) && (high === null || day <= high);
  }

  function within(day) {
    var low = parse(field.min), high = parse(field.max);
    if (low !== null && day < low) return low;
    if (high !== null && day > high) return high;
    return day;
  }

  function draw(moveFocus) {
    var p = parts(focus);
    var start = Date.UTC(p.y, p.m, 1) / 86400000;
    var lead = (parts(start).w - first + 7) % 7;
    var chosen = parse(field.value), now = today();
    title.textContent = monthName.format(start * 86400000);
    tbody.textContent = '';
    var length = new Date(Date.UTC(p.y, p.m + 1, 0)).getUTCDate();
    var rows = Math.ceil((lead + length) / 7);
    var day = start - lead;
    for (var row = 0; row < rows; row++) {
      var tr = kit.el('tr');
      for (var col = 0; col < 7; col++, day++) {
        var td = kit.el('td');
        var q = parts(day);
        if (q.m === p.m) {
          var button = kit.el('button', 'pi-cal-day', String(q.d));
          button.type = 'button';
          button.tabIndex = day === focus ? 0 : -1;
          button.setAttribute('data-day', String(day));
          button.setAttribute('aria-label', dayName.format(day * 86400000));
          if (day === chosen) { button.setAttribute('aria-pressed', 'true'); td.setAttribute('aria-selected', 'true'); }
          if (day === now) button.setAttribute('aria-current', 'date');
          if (!allowed(day)) button.disabled = true;
          td.appendChild(button);
        }
        tr.appendChild(td);
      }
      tbody.appendChild(tr);
    }
    var low = parse(field.min), high = parse(field.max);
    back.disabled = low !== null && start - 1 < low;
    ahead.disabled = high !== null && start + length > high;
    now.disabled = !allowed(today());
    if (moveFocus) {
      var target = tbody.querySelector('[data-day="' + focus + '"]');
      if (target) target.focus();
    }
  }

  function show() {
    var chosen = parse(field.value);
    focus = within(chosen !== null ? chosen : today());
    dialog.hidden = false;
    opener.setAttribute('aria-expanded', 'true');
    draw(true);
  }

  function hide(giveBack) {
    if (dialog.hidden) return;
    dialog.hidden = true;
    opener.setAttribute('aria-expanded', 'false');
    if (giveBack) opener.focus();
  }

  function take(day) {
    if (!allowed(day)) return;
    field.value = iso(day);
    kit.changed(field);
    hide(true);
  }

  function move(day) {
    focus = within(day);
    draw(true);
  }

  opener.addEventListener('click', function () { if (dialog.hidden) show(); else hide(true); });
  back.addEventListener('click', function () { focus = within(addMonths(focus, -1)); draw(false); });
  ahead.addEventListener('click', function () { focus = within(addMonths(focus, 1)); draw(false); });
  now.addEventListener('click', function () { take(today()); });
  close.addEventListener('click', function () { hide(true); });

  tbody.addEventListener('click', function (event) {
    var button = event.target.closest('.pi-cal-day');
    if (button && !button.disabled) take(+button.getAttribute('data-day'));
  });

  tbody.addEventListener('keydown', function (event) {
    var key = event.key, to = null;
    var w = (parts(focus).w - first + 7) % 7;
    if (key === 'ArrowLeft') to = focus - 1;
    else if (key === 'ArrowRight') to = focus + 1;
    else if (key === 'ArrowUp') to = focus - 7;
    else if (key === 'ArrowDown') to = focus + 7;
    else if (key === 'Home') to = focus - w;
    else if (key === 'End') to = focus + 6 - w;
    else if (key === 'PageUp') to = addMonths(focus, event.shiftKey ? -12 : -1);
    else if (key === 'PageDown') to = addMonths(focus, event.shiftKey ? 12 : 1);
    else if (key === 'Enter' || key === ' ') { event.preventDefault(); take(focus); return; }
    if (to === null) return;
    event.preventDefault();
    move(to);
  });

  dialog.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') { event.preventDefault(); event.stopPropagation(); hide(true); }
  });

  kit.outside(anchor, function () { hide(false); });
});
