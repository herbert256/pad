(function () {
  if (window.padContextmenuReady) return;
  window.padContextmenuReady = true;
  var anchored = window.CSS && CSS.supports && CSS.supports('position-area: bottom');
  var back = null, keyed = 0;
  var ITEM = 'a[href], a[aria-disabled], button';
  function items(menu) {
    return Array.prototype.filter.call(menu.querySelectorAll(ITEM), function (item) {
      return !item.disabled && item.getAttribute('aria-disabled') !== 'true' && (item.offsetParent !== null || item.getClientRects().length);
    });
  }
  function dress(box) {
    var menu = box.querySelector('.pad-contextmenu-menu'), button = box.querySelector('.pad-contextmenu-button');
    if (!menu || menu.getAttribute('role') === 'menu') return menu;
    menu.setAttribute('role', 'menu');
    if (button) {
      button.setAttribute('aria-haspopup', 'menu');
      menu.setAttribute('aria-label', button.getAttribute('aria-label') || '');
    }
    Array.prototype.forEach.call(menu.children, function (child) {
      if (child.matches(ITEM)) { child.setAttribute('role', 'menuitem'); child.tabIndex = -1; return; }
      child.setAttribute('role', child.matches('hr') ? 'separator' : 'none');
      Array.prototype.forEach.call(child.querySelectorAll(ITEM), function (item) { item.setAttribute('role', 'menuitem'); item.tabIndex = -1; });
    });
    return menu;
  }
  function area(box) {
    var parent = box.parentElement, chosen = box.getAttribute('data-pad-area');
    if (!parent || !chosen) return parent;
    try { return parent.closest(chosen); } catch (error) { return null; }
  }
  function owner(target) {
    var found = null, part = null;
    Array.prototype.forEach.call(document.querySelectorAll('.pad-contextmenu'), function (box) {
      var at = area(box);
      if (at && at.contains(target) && (!part || part.contains(at))) { found = box; part = at; }
    });
    return found;
  }
  function editable(target) {
    return target.closest && target.closest('input, textarea, select, [contenteditable]:not([contenteditable="false"])');
  }
  function place(menu, x, y) {
    var width = menu.offsetWidth, height = menu.offsetHeight;
    menu.style.left = Math.max(8, Math.min(x, innerWidth - width - 8)) + 'px';
    menu.style.top = Math.max(8, y + height > innerHeight - 8 ? y - height : y) + 'px';
  }
  function open(box, x, y) {
    var menu = dress(box);
    if (!menu) return;
    if (menu.matches(':popover-open')) menu.hidePopover();
    back = document.activeElement !== document.body ? document.activeElement : null;
    menu.classList.add('pad-contextmenu-at');
    menu.style.inset = 'auto';
    menu.style.margin = '0';
    menu.showPopover();
    place(menu, x, y);
  }
  function under(menu) {
    var button = menu.parentNode.querySelector('.pad-contextmenu-button');
    if (anchored || !button) return;
    var box = button.getBoundingClientRect();
    menu.style.inset = 'auto';
    menu.style.margin = '0';
    place(menu, box.right - menu.offsetWidth, box.bottom + 6);
  }
  document.addEventListener('toggle', function (e) {
    var menu = e.target;
    if (!menu.classList || !menu.classList.contains('pad-contextmenu-menu')) return;
    if (e.newState === 'open') {
      dress(menu.parentNode);
      if (!menu.classList.contains('pad-contextmenu-at')) {
        back = menu.parentNode.querySelector('.pad-contextmenu-button');
        under(menu);
      }
      var first = items(menu)[0];
      if (first && !menu.contains(document.activeElement)) first.focus();
      return;
    }
    menu.classList.remove('pad-contextmenu-at');
    ['inset', 'margin', 'left', 'top'].forEach(function (name) { menu.style.removeProperty(name); });
    var focus = document.activeElement;
    if (back && back.isConnected && (!focus || focus === document.body || menu.contains(focus))) back.focus({ preventScroll: true });
    back = null;
  }, true);
  document.addEventListener('contextmenu', function (e) {
    var target = e.target;
    if (!target.closest) return;
    var inside = target.closest('.pad-contextmenu-menu');
    if (inside) { if (inside.matches(':popover-open')) e.preventDefault(); return; }
    if (Date.now() - keyed < 800) { e.preventDefault(); return; }
    if (e.shiftKey || editable(target)) return;
    var box = owner(target);
    if (!box) return;
    e.preventDefault();
    if (e.clientX || e.clientY) open(box, e.clientX, e.clientY);
    else { var r = target.getBoundingClientRect(); open(box, r.left, r.bottom); }
  });
  document.addEventListener('keydown', function (e) {
    var target = e.target;
    if (!target.closest) return;
    if (e.key === 'ContextMenu' || (e.key === 'F10' && e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey)) {
      if (target.closest('.pad-contextmenu-menu') || editable(target)) return;
      var box = owner(target);
      if (!box) return;
      e.preventDefault();
      keyed = Date.now();
      var r = target.getBoundingClientRect();
      open(box, r.left, r.bottom);
      return;
    }
    var button = target.closest('.pad-contextmenu-button');
    if (button && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
      var opens = button.parentNode.querySelector('.pad-contextmenu-menu');
      if (!opens) return;
      e.preventDefault();
      if (!opens.matches(':popover-open')) opens.showPopover({ source: button });
      var list0 = items(opens);
      if (list0.length) list0[e.key === 'ArrowUp' ? list0.length - 1 : 0].focus();
      return;
    }
    var menu = target.closest('.pad-contextmenu-menu');
    if (!menu || !menu.matches(':popover-open')) return;
    if (e.key === 'Tab') { e.preventDefault(); menu.hidePopover(); return; }
    var list = items(menu), at = list.indexOf(target);
    if (!list.length || e.ctrlKey || e.altKey || e.metaKey) return;
    if (e.key === 'Home') at = 0;
    else if (e.key === 'End') at = list.length - 1;
    else if (e.key === 'ArrowDown') at = (at + 1) % list.length;
    else if (e.key === 'ArrowUp') at = (at - 1 + list.length) % list.length;
    else if (e.key === ' ' && target.matches('a')) { e.preventDefault(); target.click(); return; }
    else if (e.key.length === 1 && e.key !== ' ') {
      var letter = e.key.toLowerCase(), n = list.length;
      for (var i = 1; i <= n; i++) {
        var one = list[(at + i) % n];
        if (one.textContent.trim().toLowerCase().indexOf(letter) === 0) { at = (at + i) % n; break; }
      }
    }
    else return;
    e.preventDefault();
    list[at].focus();
  });
  document.addEventListener('click', function (e) {
    var item = e.target.closest && e.target.closest('.pad-contextmenu-menu a[href], .pad-contextmenu-menu a[aria-disabled], .pad-contextmenu-menu button');
    var menu = item && item.closest('.pad-contextmenu-menu');
    if (!menu || !menu.matches(':popover-open')) return;
    if (item.getAttribute('aria-disabled') === 'true') { e.preventDefault(); return; }
    menu.hidePopover();
  });
})();
