(function () {
  if (window.padToastReady) return;
  window.padToastReady = true;
  function arm(toast) {
    if (toast.padToastArmed) return;
    toast.padToastArmed = true;
    var left = parseFloat(toast.getAttribute('data-pad-timeout')) * 1000, timer = null, since = 0;
    function run() {
      if (timer || !toast.open) return;
      since = Date.now();
      timer = setTimeout(function () { toast.close(); }, Math.max(left, 0));
    }
    function hold() {
      if (!timer) return;
      clearTimeout(timer);
      timer = null;
      left -= Date.now() - since;
    }
    toast.addEventListener('mouseenter', hold);
    toast.addEventListener('focusin', hold);
    toast.addEventListener('mouseleave', function () { if (!toast.contains(document.activeElement)) run(); });
    toast.addEventListener('focusout', function (e) { if (!toast.contains(e.relatedTarget) && !toast.matches(':hover')) run(); });
    run();
  }
  function scan() {
    document.querySelectorAll('dialog.pad-toast[open][data-pad-timeout]').forEach(arm);
  }
  document.addEventListener('keydown', function (e) {
    var toast = e.key === 'Escape' && e.target.closest && e.target.closest('dialog.pad-toast[open]');
    if (toast) toast.close();
  });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan);
  else scan();
  new MutationObserver(scan).observe(document.documentElement, { childList: true, subtree: true });
})();
