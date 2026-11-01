// The PAD playground's client side: no framework, nothing PAD needs to parse - a static file.
// The panes live in the URL hash (#s= base64 of a JSON object), so the address bar is always
// a link to the example on screen; a link opened later fills the panes back in. The button
// beside the data's label switches the data between the JSON pane and the PHP pane - the
// form's mode says which one render.php takes. The sample renders on load, and the form
// again on Ctrl+Enter (Cmd+Enter on a Mac); an example from a link waits for Render.

(function () {

  var form   = document.getElementById('playForm');
  var source = document.getElementById('source');
  var data   = document.getElementById('data');
  var php    = document.getElementById('php');
  var mode   = document.getElementById('mode');
  var toggle = document.getElementById('dataSwitch');

  function encode(value) {
    return btoa(unescape(encodeURIComponent(JSON.stringify(value))));
  }

  function decode(text) {
    try { return JSON.parse(decodeURIComponent(escape(atob(text)))); } catch (e) { return null; }
  }

  function remember() {
    history.replaceState(null, '', '#s=' + encode({ source: source.value, data: data.value, php: php.value, mode: mode.value }));
  }

  function render() {
    remember();
    if (form.requestSubmit) form.requestSubmit(); else form.submit();
  }

  // Shows the pane and the label of the one mode, hides the other's.

  function show(which) {
    mode.value = which;
    document.querySelectorAll('[data-mode]').forEach(function (element) {
      element.hidden = element.getAttribute('data-mode') !== which;
    });
    toggle.textContent = which === 'php' ? 'Switch to JSON' : 'Switch to PHP';
  }

  var shared = location.hash.indexOf('#s=') === 0 ? decode(location.hash.slice(3)) : null;

  if (shared) {
    source.value = shared.source || '';
    data.value   = shared.data   || '';
    if (typeof shared.php === 'string') php.value = shared.php;
  }

  show(shared && shared.mode === 'php' ? 'php' : 'json');
  toggle.hidden = false;

  toggle.addEventListener('click', function () {
    show(mode.value === 'php' ? 'json' : 'php');
    remember();
    (mode.value === 'php' ? php : data).focus();
  });

  source.addEventListener('input', remember);
  data.addEventListener('input', remember);
  php.addEventListener('input', remember);

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
      event.preventDefault();
      render();
    }
  });

  // An example from a link is shown, not run: its template - and its PHP code - is whoever
  // made the link's, and it runs on this machine - a {curl} in it fetches what it likes and
  // sends what it read where it likes, PHP code does anything at all. A page on another site
  // only had to open such a link for it to run. It waits for Render, which the visitor
  // presses having read it; the sample renders at once.

  if (shared) {
    var hint = document.querySelector('.hint');
    if (hint) hint.textContent = mode.value === 'php'
      ? 'An example from a link, with PHP code: read both, then Render (Ctrl+Enter) runs them on this machine.'
      : 'An example from a link: read it, then Render (Ctrl+Enter) runs it on this machine.';
  } else
    render();

})();
