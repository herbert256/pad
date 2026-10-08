// What every page of the application shares, loaded before Alpine:
//
//   pad.get(page, params)        the page's data: ?page&...&padFormat=json, parsed
//   pad.post(page, body)         a form-encoded post with the session's CSRF token in the
//                                X-CSRF-Token header (from <meta name="csrf-token">)
//   the request log              every request of pad.get and pad.post, listed under the
//                                example - the components never touch it
//   the theme                    light or dark, remembered in localStorage
//
// An error answer is thrown with its status and parsed body, so a component can show it.

(function () {

  'use strict';

  var themeKey = 'pad-alpine-theme';

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

  // ---------------------------------------------------------------- requests back to PAD

  function address(page, params) {
    var url = location.pathname + '?' + page;
    Object.keys(params || {}).forEach(function (key) {
      if (params[key] !== undefined && params[key] !== null && params[key] !== '')
        url += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
    });
    return url;
  }

  function token() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
  }

  var count = 0;

  function log(method, url, status, ms, bytes) {
    var box = document.querySelector('[data-request-log]');
    if (!box) return;
    var row = document.createElement('li');
    [['method', method], ['status' + (status >= 400 ? ' is-bad' : ''), String(status)],
     ['url', decodeURIComponent(url.substring(url.indexOf('?')))], ['part', 'JSON'],
     ['num', ms + ' ms'], ['num size', bytes < 1024 ? bytes + ' B' : (bytes / 1024).toFixed(1) + ' kB']].forEach(function (cell) {
      var span = document.createElement('span');
      span.className = cell[0];
      span.textContent = span.title = cell[1];
      row.appendChild(span);
    });
    box.querySelector('ol').prepend(row);
    box.classList.add('has-rows');
    count++;
    box.querySelector('[data-request-count]').textContent = count + (count === 1 ? ' request' : ' requests');
  }

  function send(method, page, params, body) {
    var url = address(page, Object.assign({}, params, { padFormat: 'json' }));
    var init = { method: method, headers: { 'Accept': 'application/json' }, credentials: 'same-origin' };
    if (body) {
      init.headers['X-CSRF-Token'] = token();
      init.body = new URLSearchParams(body);
    }
    var started = performance.now();
    return fetch(url, init).then(function (response) {
      return response.text().then(function (text) {
        log(method, url, response.status, Math.round(performance.now() - started), new Blob([text]).size);
        var data = null;
        try { data = JSON.parse(text); } catch (e) { /* no JSON */ }
        if (!response.ok) {
          var error = new Error('The server answered ' + response.status);
          error.status = response.status;
          error.body = data;
          throw error;
        }
        return data;
      });
    });
  }

  window.pad = {
    get: function (page, params) { return send('GET', page, params); },
    post: function (page, body) { return send('POST', page, {}, body); },
    url: address,
    token: token
  };

})();
