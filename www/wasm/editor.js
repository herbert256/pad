// The static WASM editor shares the frame, application menu, PAD grammar and editor
// layout with Live. Only padRender runs the template: no server endpoint.
import { padInstall, padRender } from './pad-wasm.js?v=4';

const form = document.getElementById('playForm');
const source = document.getElementById('source');
const data = document.getElementById('data');
const phpArea = document.getElementById('php');
const mode = document.getElementById('mode');
const toggle = document.getElementById('dataSwitch');
const output = document.getElementById('output');
const status = document.getElementById('status');
const buttons = [document.getElementById('render'), document.getElementById('asSource')];
const other = document.getElementById('toOther');
const otherBase = other.getAttribute('href');
const escapeHTML = text => String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;');
const encode = value => btoa(unescape(encodeURIComponent(JSON.stringify(value))));
const decode = text => {
  try { return JSON.parse(decodeURIComponent(escape(atob(text)))); }
  catch { return null; }
};

const shared = location.hash.startsWith('#s=') ? decode(location.hash.slice(3)) : null;
if (shared && typeof shared === 'object') {
  source.value = typeof shared.source === 'string' ? shared.source : '';
  data.value = typeof shared.data === 'string' ? shared.data : '';
  phpArea.value = typeof shared.php === 'string' ? shared.php : '';
}

function boxes() {
  return '#s=' + encode({ source: source.value, data: data.value, php: phpArea.value, mode: mode.value });
}

function remember() {
  const hash = boxes();
  history.replaceState(null, '', hash);
  other.href = otherBase + hash;
}

// Keep the same 14:10 starting proportions and let a short/empty data pane give its
// spare space to the template, as Live does.
function fit() {
  const box = mode.value === 'php' ? phpArea : data;
  const want = source.value.split('\n').length + 1;
  const need = Math.max(3, box.value.split('\n').length + 1);
  let keep = 10;
  if (box.value === '') keep = 3;
  else if (need > 10 && want < 14) keep = Math.min(need, 24 - Math.max(want, 5));
  else if (want > 14 && need < 10) keep = need;
  for (const [area, rows] of [[source, 24 - keep], [data, keep], [phpArea, keep]]) {
    area.rows = rows;
    area.style.setProperty('--rows', rows);
    if (area.host) area.host.style.setProperty('--rows', rows);
  }
}

function show(which) {
  mode.value = which;
  if (which === 'php' && phpArea.value.trim() === '') {
    phpArea.value = '<?php\n\n?>';
    if (phpArea.editor) phpArea.editor.setValue(phpArea.value);
  }
  document.querySelectorAll('[data-mode]').forEach(element => { element.hidden = element.dataset.mode !== which; });
  toggle.textContent = which === 'php' ? 'Switch to data' : 'Switch to PHP';
}

show(shared && shared.mode === 'php' ? 'php' : 'data');
toggle.hidden = false;
toggle.addEventListener('click', () => {
  show(mode.value === 'php' ? 'data' : 'php');
  fit();
  remember();
  const area = mode.value === 'php' ? phpArea : data;
  if (area.editor) area.editor.focus(); else area.focus();
});

for (const area of [source, data, phpArea])
  area.addEventListener('input', () => { fit(); remember(); });
fit();
other.href = otherBase + boxes();

let php;
let busy = false;

async function render(asSource = false) {
  if (!php || busy) return;
  remember();
  busy = true;
  buttons.forEach(button => { button.disabled = true; });
  output.setAttribute('aria-busy', 'true');
  status.textContent = 'Rendering…';
  const started = performance.now();
  try {
    const result = await padRender(php, source.value, data.value, { mode: mode.value, php: phpArea.value });
    output.srcdoc = asSource ? '<pre>' + escapeHTML(result.html) + '</pre>' : result.html;
    status.textContent = 'HTTP ' + result.status + ' in ' + Math.round(performance.now() - started) + ' ms';
  } catch (error) {
    output.srcdoc = '<pre>' + escapeHTML(error.message) + '</pre>';
    status.textContent = 'PAD could not render this template.';
    console.error(error);
  } finally {
    busy = false;
    buttons.forEach(button => { button.disabled = false; });
    output.setAttribute('aria-busy', 'false');
  }
}

form.addEventListener('submit', event => {
  event.preventDefault();
  render(event.submitter === buttons[1]);
});
document.addEventListener('keydown', event => {
  if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
    event.preventDefault();
    render();
  }
});

function frame(metadata) {
  function icon(name, size = 16) {
    const template = document.createElement('template');
    // Trusted SVG files of PAD, included by wasm/build.php, never the editor's input.
    template.innerHTML = metadata.icons[name] || '';
    const svg = template.content.querySelector('svg');
    if (!svg) return document.createTextNode('');
    svg.setAttribute('width', size);
    svg.setAttribute('height', size);
    svg.setAttribute('class', 'pad-icon');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('focusable', 'false');
    return svg;
  }

  const menu = document.getElementById('appMenu');
  menu.replaceChildren();
  const columns = new Map();
  for (const headings of metadata.columns || Object.keys(metadata.apps).map(heading => [heading])) {
    const column = document.createElement('div');
    column.className = 'pad-brand-column';
    menu.append(column);
    for (const heading of headings) columns.set(heading, column);
  }
  for (const [heading, apps] of Object.entries(metadata.apps)) {
    const group = document.createElement('div');
    group.className = 'pad-brand-group';
    const head = document.createElement('div');
    head.className = 'pad-brand-head';
    head.textContent = heading;
    group.append(head);
    for (const [path, [name, symbol, about]] of Object.entries(apps)) {
      const link = document.createElement('a');
      link.href = '../' + path + '/';
      if (path === 'wasm') link.setAttribute('aria-current', 'page');
      const label = document.createElement('span');
      const title = document.createElement('b');
      const description = document.createElement('small');
      title.textContent = name;
      description.textContent = about;
      label.append(title, description);
      link.append(icon(symbol), label);
      group.append(link);
    }
    (columns.get(heading) || menu).append(group);
  }
  document.querySelectorAll('[data-icon]').forEach(element => {
    element.replaceChildren(icon(element.dataset.icon, element.closest('.frame-nav') ? 18 : 14));
  });

  const select = document.getElementById('wasmTheme');
  const style = document.createElement('style');
  document.head.append(style);
  for (const [name, theme] of Object.entries(metadata.themes))
    select.add(new Option(theme.name, name));

  function apply(name) {
    const theme = Object.hasOwn(metadata.themes, name) ? metadata.themes[name] : null;
    select.value = theme ? name : '';
    style.dataset.padTheme = select.value;
    style.textContent = theme ? theme.css : '';
    window.padTheme.set(theme ? theme.scheme : 'light');
  }

  let saved = '';
  try { saved = decodeURIComponent(document.cookie.split('; ').find(value => value.startsWith('padTheme='))?.slice(9) || ''); }
  catch { /* The default also works when cookies are unavailable. */ }
  apply(saved);
  select.disabled = false;
  select.addEventListener('change', () => {
    apply(select.value);
    const path = new URL('../', location.href).pathname;
    document.cookie = 'padTheme=' + encodeURIComponent(select.value) + '; Path=' + path
      + '; Max-Age=' + (select.value ? 31622400 : 0) + '; SameSite=Lax'
      + (location.protocol === 'https:' ? '; Secure' : '');
  });
}

function script(src) {
  return new Promise((resolve, reject) => {
    const element = document.createElement('script');
    element.src = src;
    element.onload = resolve;
    element.onerror = () => reject(new Error('Could not load ' + src));
    document.head.append(element);
  });
}

  function dataLanguage(text) {
    var t = text.replace(/^\uFEFF/, '').trim();
    var first = t.charAt(0);
    if (first === '(' && t.charAt(t.length - 1) === ')') return 'plaintext';
    if (first === '{' || first === '[' || /^[\w.$]+\s*\(\s*[{\[]/.test(t)) return 'json';
    if (first === '<') return t.slice(0, 5) === '<html' ? 'html' : 'xml';
    if (t.slice(0, 5) === '%YAML' || t.slice(0, 3) === '---') return 'yaml';
    var lines = t.split(/\r?\n/).filter(function (one) { return one.trim() !== '' && one.trim().charAt(0) !== '#'; });
    if (lines.length > 1) {
      var line = lines[0].trim(), next = lines[1].trim(), deeper = /^[ \t]/.test(lines[1]);
      if (/^-(\s|$)/.test(line) || (/^[\w"'][^:,]*:(\s|$)/.test(line) &&
          (line.indexOf(',') < 0 || deeper || /^(-(\s|$)|[\w"'][^:]*:(\s|$))/.test(next))))
        return 'yaml';
    }
    return 'plaintext';
  }


async function editors(metadata) {
  const at = 'https://cdn.jsdelivr.net/npm/monaco-editor@0.57.0/min/vs';
  await Promise.all([script(metadata.grammar), script(at + '/loader.js')]);
  const monaco = await new Promise((resolve, reject) => {
    window.require.config({ paths: { vs: at } });
    window.require(['vs/editor/editor.main'], () => resolve(window.monaco), reject);
  });
  const base = { pad: { ...metadata.names, docs: {} } };
  window.PadMode.register(monaco, {
    base: () => base, appLang: () => null, fileOf: () => null, fields: () => [],
    modelFor: () => Promise.resolve(null), exists: () => false, settings: () => ({})
  });
  const theme = () => /dark/.test(getComputedStyle(document.documentElement).colorScheme) ? 'pad-dark' : 'pad-light';
  document.addEventListener('padtheme', () => monaco.editor.setTheme(theme()));

  for (const [area, language] of [[source, 'pad'], [data, dataLanguage(data.value)], [phpArea, 'php']]) {
    const host = document.createElement('div');
    host.className = 'editorBox';
    host.style.setProperty('--rows', area.rows);
    if (area.hasAttribute('data-mode')) {
      host.dataset.mode = area.dataset.mode;
      host.hidden = area.hidden;
      area.removeAttribute('data-mode');
    }
    area.before(host);
    const label = document.querySelector('label[for="' + area.id + '"]');
    const editor = monaco.editor.create(host, {
      value: area.value, language, theme: theme(), ariaLabel: label.textContent,
      automaticLayout: true, minimap: { enabled: false }, fontSize: 13, tabSize: 2, insertSpaces: true,
      scrollBeyondLastLine: false, lineNumbersMinChars: 3, renderLineHighlight: 'none',
      bracketPairColorization: { enabled: true }, fontFamily: 'ui-monospace, Menlo, monospace',
      padding: { top: 6 }, overviewRulerLanes: 0, placeholder: area.placeholder || ''
    });
    const numbers = () => editor.updateOptions({ lineNumbers: editor.getValue() === '' ? 'off' : 'on' });
    numbers();
    editor.onDidChangeModelContent(() => {
      area.value = editor.getValue();
      area.dispatchEvent(new Event('input'));
      numbers();
      if (area === data) {
        const language = dataLanguage(area.value);
        if (editor.getModel().getLanguageId() !== language) monaco.editor.setModelLanguage(editor.getModel(), language);
      }
    });
    editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter, () => render());
    label.addEventListener('click', () => editor.focus());
    area.host = host;
    area.editor = editor;
    area.hidden = true;
  }
}

try {
  const response = await fetch('pad-bundle.json', { cache: 'no-cache' });
  if (!response.ok) throw new Error('pad-bundle.json is missing - build it with: php wasm/build.php');
  const bundle = await response.json();
  if (bundle.editor) {
    frame(bundle.editor);
    editors(bundle.editor).catch(error => console.warn('WASM: the boxes stay plain - ' + error.message));
  }
  const [{ PHP, loadPHPRuntime }, loader] = await Promise.all([
    import('https://cdn.jsdelivr.net/npm/@php-wasm/universal@3.1.56/+esm'),
    import('https://cdn.jsdelivr.net/npm/@php-wasm/web-8-4@3.1.56/asyncify/php_8_4.js')
  ]);
  const runtime = new PHP(await loadPHPRuntime(loader, { phpWasmAsyncMode: 'asyncify', processId: 1 }));
  await padInstall(runtime, bundle);
  php = runtime;
  buttons.forEach(button => { button.disabled = false; });
  // A shared example waits for an explicit render, just as the server editor does.
  if (shared) status.textContent = 'Ready';
  else await render();
} catch (error) {
  status.textContent = 'PAD could not start here: ' + error.message;
  console.error(error);
}
