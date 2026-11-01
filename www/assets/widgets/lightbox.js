(function () {
  if (window.padLightboxReady) return;
  window.padLightboxReady = true;
  function target() {
    var id = '';
    try { id = decodeURIComponent(location.hash.slice(1)); } catch (error) { return null; }
    return id ? document.getElementById(id) : null;
  }
  function focus() {
    var element = target();
    if (!element || !element.closest('.pad-lightbox')) return;
    if (element.classList.contains('pad-lightbox-view')) element.querySelector('.pad-lightbox-close').focus({ preventScroll: true });
    else if (element.classList.contains('pad-lightbox-thumb')) element.focus({ preventScroll: true });
  }
  document.addEventListener('keydown', function (e) {
    var view = target();
    if (!view || !view.classList.contains('pad-lightbox-view')) return;
    var link = e.key === 'Escape' ? '.pad-lightbox-close' : e.key === 'ArrowLeft' ? '.pad-lightbox-prev' : e.key === 'ArrowRight' ? '.pad-lightbox-next' : '';
    var go = link && view.querySelector(link);
    if (!go) return;
    e.preventDefault();
    go.click();
  });
  window.addEventListener('hashchange', focus);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', focus);
  else focus();
})();
