// The PAD playground's client side: no framework, nothing PAD needs to parse - a static file.
// Both panes live in the URL hash (#s= base64 of a JSON object), so the address bar is always
// a link to the example on screen; a link opened later fills the panes back in. The form
// renders once on load and again on Ctrl+Enter (Cmd+Enter on a Mac).

(function () {

  var form   = document.getElementById('playForm');
  var source = document.getElementById('source');
  var data   = document.getElementById('data');

  function encode(value) {
    return btoa(unescape(encodeURIComponent(JSON.stringify(value))));
  }

  function decode(text) {
    try { return JSON.parse(decodeURIComponent(escape(atob(text)))); } catch (e) { return null; }
  }

  function remember() {
    history.replaceState(null, '', '#s=' + encode({ source: source.value, data: data.value }));
  }

  function render() {
    remember();
    if (form.requestSubmit) form.requestSubmit(); else form.submit();
  }

  var shared = location.hash.indexOf('#s=') === 0 ? decode(location.hash.slice(3)) : null;

  if (shared) {
    source.value = shared.source || '';
    data.value   = shared.data   || '';
  }

  source.addEventListener('input', remember);
  data.addEventListener('input', remember);

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
      event.preventDefault();
      render();
    }
  });

  render();

})();
