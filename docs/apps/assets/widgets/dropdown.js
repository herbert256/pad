(function () {
  if (window.padDropdownReady) return;
  window.padDropdownReady = true;
  var anchored = window.CSS && CSS.supports && CSS.supports('position-area: bottom');
  function items(menu) {
    return Array.prototype.filter.call(menu.querySelectorAll('a[href], button:not([disabled])'), function (item) {
      return item.offsetParent !== null || item.getClientRects().length;
    });
  }
  function place(menu) {
    var button = menu.parentNode.querySelector('.pad-dropdown-button');
    if (anchored || !button) return;
    var box = button.getBoundingClientRect();
    menu.style.inset = 'auto';
    menu.style.margin = '0';
    var left = menu.classList.contains('pad-dropdown-end') ? box.right - menu.offsetWidth : box.left;
    var top = box.bottom + 6;
    if (top + menu.offsetHeight > innerHeight && box.top - menu.offsetHeight - 6 > 0) top = box.top - menu.offsetHeight - 6;
    menu.style.left = Math.max(8, Math.min(left, innerWidth - menu.offsetWidth - 8)) + 'px';
    menu.style.top = Math.max(8, top) + 'px';
  }
  document.addEventListener('toggle', function (e) {
    var menu = e.target;
    if (menu.classList && menu.classList.contains('pad-dropdown-menu') && e.newState === 'open') place(menu);
  }, true);
  document.addEventListener('keydown', function (e) {
    var target = e.target;
    if (!target.closest) return;
    var button = target.closest('.pad-dropdown-button');
    if (button && e.key === 'ArrowDown') {
      var opens = button.parentNode.querySelector('.pad-dropdown-menu');
      if (!opens) return;
      e.preventDefault();
      if (!opens.matches(':popover-open')) opens.showPopover({ source: button });
      var first = items(opens)[0];
      if (first) first.focus();
      return;
    }
    var menu = target.closest('.pad-dropdown-menu');
    if (!menu || ['ArrowDown', 'ArrowUp', 'Home', 'End'].indexOf(e.key) < 0) return;
    var list = items(menu), at = list.indexOf(target);
    if (!list.length) return;
    e.preventDefault();
    if (e.key === 'Home') at = 0;
    else if (e.key === 'End') at = list.length - 1;
    else if (e.key === 'ArrowDown') at = (at + 1) % list.length;
    else at = (at - 1 + list.length) % list.length;
    list[at].focus();
  });
  document.addEventListener('click', function (e) {
    var item = e.target.closest && e.target.closest('.pad-dropdown-menu a[href], .pad-dropdown-menu button');
    var menu = item && item.closest('.pad-dropdown-menu');
    if (menu && menu.matches(':popover-open')) menu.hidePopover();
  });
})();
