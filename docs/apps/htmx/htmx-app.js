// The little the pages of this application do in JavaScript of their own - none of it is
// needed for htmx to work:
//
//   the request log    every request htmx makes, with the part PAD answered: htmx tells
//                      them as events (htmx:beforeRequest, htmx:afterRequest)
//   the rules switch   a checkbox data-toggle-rules="<selector>" takes the data-pad-rules of
//                      a form away, so a bad post reaches the server and PAD answers it
//   the theme          light or dark, remembered in localStorage
//
// Loaded in the <head>, before htmx.

(function () {

  'use strict';

  var themeKey = 'pad-htmx-theme';

  function stored() {
    try { return localStorage.getItem(themeKey); } catch (e) { return null; }
  }

  function apply(theme) {
    if (theme === 'light' || theme === 'dark') document.documentElement.setAttribute('data-theme', theme);
  }

  apply(stored());

  document.addEventListener('click', function (event) {
    if (!event.target.closest('[data-theme-toggle]')) return;
    var now = document.documentElement.getAttribute('data-theme') ||
              (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    var next = now === 'dark' ? 'light' : 'dark';
    apply(next);
    try { localStorage.setItem(themeKey, next); } catch (e) { /* private window */ }
  });

  // ---------------------------------------------------------------- the request log

  var started = new WeakMap();
  var count = 0;

  function address(detail) {
    return detail.pathInfo ? (detail.pathInfo.finalRequestPath || detail.pathInfo.requestPath || '') : '';
  }

  function part(detail) {
    var path = address(detail);
    var asked = /[?&]padFragment=([^&]+)/.exec(path);
    if (asked) return 'fragment ' + decodeURIComponent(asked[1]);
    var target = detail.requestConfig && detail.requestConfig.headers && detail.requestConfig.headers['HX-Target'];
    return target ? 'HX-Target ' + target : 'the whole page';
  }

  document.addEventListener('htmx:beforeRequest', function (event) {
    started.set(event.detail.xhr, performance.now());
  });

  document.addEventListener('htmx:afterRequest', function (event) {
    var log = document.querySelector('[data-request-log]');
    if (!log) return;
    var detail = event.detail;
    var xhr = detail.xhr;
    var ms = Math.round(performance.now() - (started.get(xhr) || performance.now()));
    var path = address(detail);
    var row = document.createElement('li');
    var cells = [
      ['method', (detail.requestConfig && detail.requestConfig.verb || 'get').toUpperCase()],
      ['status' + (xhr.status >= 400 ? ' is-bad' : ''), String(xhr.status)],
      ['url', decodeURIComponent(path.substring(path.indexOf('?')))],
      ['part', part(detail)],
      ['num', ms + ' ms'],
      ['num size', (xhr.responseText.length < 1024 ? xhr.responseText.length + ' B' : (xhr.responseText.length / 1024).toFixed(1) + ' kB')]
    ];
    cells.forEach(function (cell) {
      var span = document.createElement('span');
      span.className = cell[0];
      span.textContent = cell[1];
      span.title = cell[1];
      row.appendChild(span);
    });
    log.querySelector('ol').prepend(row);
    log.classList.add('has-rows');
    count++;
    log.querySelector('[data-request-count]').textContent = count + (count === 1 ? ' request' : ' requests');
  });

  // ---------------------------------------------------------------- the rules switch

  function rules(box) {
    document.querySelectorAll(box.getAttribute('data-toggle-rules')).forEach(function (form) {
      if (box.checked && form.hasAttribute('data-pad-rules-off')) {
        form.setAttribute('data-pad-rules', form.getAttribute('data-pad-rules-off'));
        form.removeAttribute('data-pad-rules-off');
      } else if (!box.checked && form.hasAttribute('data-pad-rules')) {
        form.setAttribute('data-pad-rules-off', form.getAttribute('data-pad-rules'));
        form.removeAttribute('data-pad-rules');
      }
    });
  }

  document.addEventListener('change', function (event) {
    if (event.target.matches('[data-toggle-rules]')) rules(event.target);
  });

  // a form htmx swapped in comes with its rules again - the switch still says what holds
  document.addEventListener('htmx:afterSettle', function () {
    document.querySelectorAll('[data-toggle-rules]').forEach(rules);
  });

})();
