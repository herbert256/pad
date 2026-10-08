// pad-islands - the runtime of the islands application, for components of any framework.
//
//   islands({ Counter: react(() => import('./islands/Counter.react.jsx')) })
//
// mounts a component on every element <div data-island="Counter" data-props="{^props}"> PAD
// wrote, the JSON of data-props as its props and what PAD rendered inside as serverHtml.
// A component's code - its framework with it - is fetched when its island needs it:
//
//   data-load="eager"      at once (the default)
//   data-load="idle"       once the browser has nothing else to do
//   data-load="visible"    when the island scrolls into view
//
// react(), vue(), svelte(), preact() and solid() pair a component with its framework's
// mount; each adapter is a module of its own, so a page without a Vue island loads no Vue.
// store() is state the islands of every framework share; get() and post() talk to PAD.

const registry = {};

function adapter(load) {
  return (component) => () => Promise.all([load(), component()]).then(([a, c]) => a.island(c.default));
}

export const react  = adapter(() => import('./adapters/react.js'));
export const vue    = adapter(() => import('./adapters/vue.js'));
export const svelte = adapter(() => import('./adapters/svelte.js'));
export const preact = adapter(() => import('./adapters/preact.js'));
export const solid  = adapter(() => import('./adapters/solid.js'));

function props(element) {
  let value = {};
  try { value = JSON.parse(element.getAttribute('data-props') || '{}'); } catch (e) { console.error('pad-islands: no JSON in data-props', element); }
  if (!value || typeof value !== 'object' || Array.isArray(value)) value = { data: value };
  value.serverHtml = element.innerHTML;
  return value;
}

// Every island that mounted, told as a pad:island event and kept: a listener that came
// later - itself an island - reads what it missed with mounted().

const history = [];

function told(name, element, ms) {
  const detail = { name, element, ms, at: new Date() };
  history.push(detail);
  window.dispatchEvent(new CustomEvent('pad:island', { detail }));
}

export const mounted = () => history.slice();

function start(element) {
  if (element.padIsland) return;
  const name = element.getAttribute('data-island');
  const load = registry[name];
  if (!load) return console.warn(`pad-islands: no island named ${name}`);
  element.padIsland = 'loading';
  const began = performance.now();
  load().then((mount) => {
    element.padIsland = mount(element, props(element));
    element.classList.add('is-mounted');
    told(name, element, Math.round(performance.now() - began));
  });
}

function when(element) {
  const how = element.getAttribute('data-load') || 'eager';
  if (how === 'idle')
    return (window.requestIdleCallback || ((go) => setTimeout(go, 200)))(() => start(element));
  if (how === 'visible' && 'IntersectionObserver' in window) {
    const seen = new IntersectionObserver((entries) => {
      if (entries.some((entry) => entry.isIntersecting)) { seen.disconnect(); start(element); }
    }, { rootMargin: '100px' });
    return seen.observe(element);
  }
  start(element);
}

export function islands(list) {
  Object.assign(registry, list);
  document.querySelectorAll('[data-island]').forEach(when);
}

// ------------------------------------------------------------------ shared state

const stores = {};

export function store(name, initial) {
  if (stores[name]) return stores[name];
  let value = initial;
  const listeners = new Set();
  return stores[name] = {
    get: () => value,
    set(next) {
      value = typeof next === 'function' ? next(value) : next;
      listeners.forEach((listener) => listener(value));
    },
    subscribe(listener) {
      listeners.add(listener);
      return () => listeners.delete(listener);
    }
  };
}

// ------------------------------------------------------------------ requests back to PAD

function address(page, params) {
  let url = location.pathname + '?' + page;
  for (const [key, value] of Object.entries(params || {}))
    if (value !== undefined && value !== null && value !== '')
      url += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(value);
  return url;
}

async function send(method, page, params, body) {
  const init = { method, headers: { Accept: 'application/json' }, credentials: 'same-origin' };
  if (body) {
    init.headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]')?.content || '';
    init.body = new URLSearchParams(body);
  }
  const response = await fetch(address(page, { ...params, padFormat: 'json' }), init);
  if (!response.ok) throw Object.assign(new Error(`The server answered ${response.status}`), { status: response.status });
  return response.json();
}

export const get  = (page, params) => send('GET', page, params);
export const post = (page, body) => send('POST', page, {}, body);
