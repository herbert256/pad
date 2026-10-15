// Notes in the browser - what the pages do without it they keep doing, this makes them
// quicker:
//
//   search   while a search is typed the list is asked for again - fetch('?index&q=...&
//            padFragment=list') - and only the list is swapped: PAD answers {fragment 'list'}
//   preview  the editor's Preview tab posts the text to ?preview, with the session's token in
//            X-CSRF-Token, and shows the Markdown as the note page will
//   files    a file dropped on the editor's drop zone becomes its attachment
//   keys     Ctrl+S (Cmd+S) saves the note being edited, / goes to the search box
//
// Every request goes to the application's own address - on a server, or to PHP in the browser
// when the application was packed with pad pack.

(function () {

  'use strict';

  const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  // ---- search ------------------------------------------------------------------------

  const search = document.querySelector('.search input[name=q]');
  const results = document.getElementById('results');
  let timer = null, asked = 0;

  if (search && results) {
    search.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(find, 180);
    });
  }

  async function find() {
    const mine = ++asked;
    const params = new URLSearchParams();
    params.set('index', '');
    if (search.value.trim()) params.set('q', search.value.trim());
    const tag = results.dataset.tag;
    if (tag) params.set('tag', tag);
    const query = '?' + params.toString().replace(/^index=/, 'index');
    results.setAttribute('aria-busy', 'true');
    try {
      const response = await fetch(query + '&padFragment=list', { headers: { 'X-Requested-With': 'fetch' } });
      if (!response.ok || mine !== asked) return;
      results.innerHTML = await response.text();
      const found = results.querySelectorAll('article.note').length;
      const count = document.querySelector('.list-head .count');
      if (count) count.textContent = count.textContent.replace(/^\d+/, found);
      const heading = document.querySelector('.list-head h1');
      if (heading && !tag) heading.textContent = search.value.trim() ? `Found for “${search.value.trim()}”` : 'All notes';
      try { history.replaceState(null, '', query); } catch (e) { /* a page shown from the disk keeps its own address */ }
    } finally {
      if (mine === asked) results.removeAttribute('aria-busy');
    }
  }

  document.addEventListener('keydown', event => {
    if (event.key === '/' && search && !/^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName)) {
      event.preventDefault();
      search.focus();
      search.select();
    }
  });

  // ---- the editor ----------------------------------------------------------------------

  const editor = document.querySelector('.editor form');
  if (!editor) return;

  const text = editor.querySelector('textarea[name=body]');
  const preview = document.getElementById('preview');
  const tabs = document.querySelectorAll('.tabs button');

  for (const tab of tabs) tab.addEventListener('click', () => show(tab.dataset.tab));

  async function show(which) {
    for (const tab of tabs) tab.setAttribute('aria-selected', String(tab.dataset.tab === which));
    text.hidden = which !== 'write';
    preview.hidden = which !== 'preview';
    if (which !== 'preview') { text.focus(); return; }
    preview.innerHTML = '<p class="muted">Rendering…</p>';
    const response = await fetch('?preview&padInclude', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': token },
      body: new URLSearchParams({ body: text.value })
    });
    preview.innerHTML = response.ok ? await response.text() : `<p class="error">The preview failed (HTTP ${response.status}).</p>`;
  }

  document.addEventListener('keydown', event => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
      event.preventDefault();
      editor.requestSubmit();
    }
  });

  const drop = editor.querySelector('.drop');
  const input = drop && drop.querySelector('input[type=file]');
  if (drop && input) {
    for (const type of ['dragenter', 'dragover']) drop.addEventListener(type, event => { event.preventDefault(); drop.classList.add('over'); });
    for (const type of ['dragleave', 'drop']) drop.addEventListener(type, () => drop.classList.remove('over'));
    drop.addEventListener('drop', event => {
      event.preventDefault();
      if (!event.dataTransfer.files.length) return;
      const files = new DataTransfer();
      files.items.add(event.dataTransfer.files[0]);
      input.files = files.files;
      drop.querySelector('.chosen').textContent = `Will attach ${event.dataTransfer.files[0].name}`;
    });
    input.addEventListener('change', () => {
      drop.querySelector('.chosen').textContent = input.files[0] ? `Will attach ${input.files[0].name}` : '';
    });
  }

})();
