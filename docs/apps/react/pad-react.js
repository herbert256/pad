// PadReact - the small runtime the pages of this application share. Plain JavaScript, no
// JSX, loaded in the <head> after React:
//
//   PadReact.island ( 'Cart', Cart )         mounts Cart on every <div data-island="Cart">,
//                                            the JSON of its data-props as the props
//   PadReact.get    ( 'examples/search', { q: 'lamp' } )
//                                            the page's data: ?examples/search&q=lamp&padFormat=json
//   PadReact.post   ( 'examples/cart', { op: 'add', id: 3 } )
//                                            a post with the session's CSRF token in a header
//   PadReact.fragment ( 'examples/chart', 'chart', { kind: 'line' } )
//                                            one {fragment} of a page, as HTML
//   PadReact.useData, PadReact.useDebounced  hooks around the same
//
// Every request is told to the page as a 'pad:request' event - the request log of the
// examples listens for it. The theme switch of the top bar lives here too, and the version
// on the components' addresses. Loaded before Babel, after React.

(function () {

  'use strict';

  // ------------------------------------------------------------------ the theme

  var themeKey = 'pad-react-theme';

  function storedTheme () {
    try { return localStorage.getItem(themeKey); } catch (e) { return null; }
  }

  function applyTheme (theme) {
    if (theme === 'light' || theme === 'dark')
      document.documentElement.setAttribute('data-theme', theme);
    else
      document.documentElement.removeAttribute('data-theme');
  }

  applyTheme(storedTheme());

  document.addEventListener('click', function (event) {
    var button = event.target.closest && event.target.closest('[data-theme-toggle]');
    if (!button) return;
    var dark = document.documentElement.getAttribute('data-theme') === 'dark' ||
               (!document.documentElement.getAttribute('data-theme') && matchMedia('(prefers-color-scheme: dark)').matches);
    var next = dark ? 'light' : 'dark';
    applyTheme(next);
    try { localStorage.setItem(themeKey, next); } catch (e) { /* private window */ }
  });

  // ------------------------------------------------------------------ fresh components

  // A browser keeps a script it fetched a while without asking the server again, and Babel
  // fetches every text/babel file through that cache - a component that changed could run
  // as its old copy. The wrapper writes the version of www/react/ into a meta tag, and every
  // component's address gets it before Babel reads them: this file is loaded before Babel,
  // so its listener runs first.

  document.addEventListener('DOMContentLoaded', function () {
    var meta = document.querySelector('meta[name="pad-react-version"]');
    if (!meta || !meta.content) return;
    document.querySelectorAll('script[type="text/babel"][src]').forEach(function (script) {
      var src = script.getAttribute('src');
      script.setAttribute('src', src + (src.indexOf('?') < 0 ? '?' : '&') + 'v=' + encodeURIComponent(meta.content));
    });
  });

  // ------------------------------------------------------------------ islands

  // The props of an island: the JSON PAD wrote into data-props ({^field} in the template). A
  // value that is no object - a list - arrives as props.data. serverHtml is what PAD
  // rendered inside the island, there before React took the element over.

  function islandProps (element) {
    var raw = element.getAttribute('data-props');
    var props = {};
    if (raw) {
      try {
        var parsed = JSON.parse(raw);
        props = (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) ? parsed : { data: parsed };
      } catch (e) {
        console.error('PadReact: the data-props of this island is no JSON', element, e);
      }
    }
    props.serverHtml = element.innerHTML;
    return props;
  }

  function island (name, Component) {
    var found = document.querySelectorAll('[data-island="' + name + '"]');
    if (!found.length) console.warn('PadReact: no element with data-island="' + name + '" on this page');
    found.forEach(function (element) {
      if (element.padRoot) return;
      var props = islandProps(element);
      element.padRoot = ReactDOM.createRoot(element);
      element.padRoot.render(React.createElement(Component, props));
      element.classList.add('is-mounted');
    });
  }

  // ------------------------------------------------------------------ requests back to PAD

  // The address of a page of this application, relative to the entry point the browser is on:
  // url('examples/search', { q: 'lamp' }) is ?examples/search&q=lamp.

  function url (page, params) {
    var address = location.pathname + '?' + page;
    Object.keys(params || {}).forEach(function (key) {
      var value = params[key];
      if (value === undefined || value === null || value === '') return;
      address += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(Array.isArray(value) ? value.join(',') : value);
    });
    return address;
  }

  function csrfToken () {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
  }

  var requests = [];

  function told (entry) {
    requests.unshift(entry);
    requests.length = Math.min(requests.length, 40);
    window.dispatchEvent(new CustomEvent('pad:request', { detail: entry }));
  }

  // One request: the answer's text, its status, and the time it took - told to the log
  // whatever came back. A status that is no success is thrown, with the parsed body when
  // there is one.

  function send (method, address, options) {
    options = options || {};
    var headers = { 'Accept': options.accept || 'application/json' };
    var init = { method: method, headers: headers, credentials: 'same-origin', signal: options.signal };

    if (method === 'POST') {
      if (options.token !== false) headers['X-CSRF-Token'] = csrfToken();
      init.body = new URLSearchParams(options.body || {});
    }

    var started = performance.now();

    return fetch(address, init).then(function (response) {
      return response.text().then(function (text) {
        var entry = { method: method, url: address, status: response.status, ms: Math.round(performance.now() - started),
                      bytes: new Blob([text]).size, at: new Date() };
        told(entry);
        if (!response.ok) {
          var error = new Error('The server answered ' + response.status + ' ' + response.statusText);
          error.status = response.status;
          try { error.body = JSON.parse(text); } catch (e) { error.body = text; }
          throw error;
        }
        return text;
      });
    });
  }

  function json (text) {
    return JSON.parse(text);
  }

  function get (page, params, options) {
    return send('GET', url(page, Object.assign({}, params, { padFormat: 'json' })), options).then(json);
  }

  function post (page, body, options) {
    return send('POST', url(page, { padFormat: 'json' }), Object.assign({}, options, { body: body })).then(json);
  }

  function fragment (page, name, params, options) {
    return send('GET', url(page, Object.assign({}, params, { padFragment: name })), Object.assign({ accept: 'text/html' }, options));
  }

  // ------------------------------------------------------------------ hooks

  // useData('examples/search', { q }, first): the page's data for these params, asked again
  // whenever they change - the previous request aborted. With a first answer - the one PAD
  // put in the props - nothing is asked until the params change.

  function useData (page, params, first) {
    var key = JSON.stringify(params || {});
    var initial = first !== undefined;
    var state = React.useState({ data: first, loading: !initial, error: null });
    var skip = React.useRef(initial);
    var setState = state[1];

    React.useEffect(function () {
      if (skip.current) { skip.current = false; return; }
      var controller = new AbortController();
      setState(function (now) { return { data: now.data, loading: true, error: null }; });
      get(page, params, { signal: controller.signal })
        .then(function (data) { setState({ data: data, loading: false, error: null }); })
        .catch(function (error) {
          if (error.name !== 'AbortError') setState(function (now) { return { data: now.data, loading: false, error: error }; });
        });
      return function () { controller.abort(); };
    }, [page, key]);

    return state[0];
  }

  function useDebounced (value, ms) {
    var state = React.useState(value);
    React.useEffect(function () {
      var timer = setTimeout(function () { state[1](value); }, ms);
      return function () { clearTimeout(timer); };
    }, [value, ms]);
    return state[0];
  }

  window.PadReact = {
    island: island, url: url, csrfToken: csrfToken,
    get: get, post: post, fragment: fragment,
    useData: useData, useDebounced: useDebounced,
    requests: requests
  };

})();
