(function () {
  if (window.padDrawerReady) return;
  window.padDrawerReady = true;
  var native = 'command' in HTMLButtonElement.prototype;
  document.addEventListener('click', function (e) {
    var target = e.target;
    if (target.matches && target.matches('dialog.pad-drawer-panel[open]')) { target.close(); return; }
    var button = target.closest && target.closest('button.pad-drawer-button, button.pad-drawer-close');
    if (!button || native) return;
    var dialog = document.getElementById(button.getAttribute('commandfor'));
    if (!dialog) return;
    if (button.getAttribute('command') === 'close') dialog.close();
    else if (!dialog.open) dialog.showModal();
  });
  function arrived() {
    var id = '';
    try { id = decodeURIComponent(location.hash.slice(1)); } catch (error) { return; }
    var dialog = id && document.getElementById(id);
    if (dialog && dialog.matches('dialog.pad-drawer-panel') && !dialog.open) dialog.showModal();
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', arrived);
  else arrived();
  window.addEventListener('hashchange', arrived);
  document.addEventListener('close', function (e) {
    var dialog = e.target, id = '';
    if (!dialog.matches || !dialog.matches('dialog.pad-drawer-panel')) return;
    try { id = decodeURIComponent(location.hash.slice(1)); } catch (error) { return; }
    if (id === dialog.id) try { history.replaceState(history.state, '', location.pathname + location.search); } catch (error) {}
  }, true);
})();
