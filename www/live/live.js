// The PAD live application's editor: no framework, nothing PAD needs to parse - a static file.
// The same as the playground's (www/playground/playground.js), but Run saves the page and the
// output frame shows it as this server answers it - same origin, a real request - with its
// address under "open alone".
// The panes live in the URL hash (#s= base64 of a JSON object), so the address bar is always
// a link to the example on screen; a link opened later fills the panes back in. The button
// beside the data's label switches the data between the text pane - JSON, YAML, XML, CSV,
// recognised on sight - and the PHP pane; the form's mode says which one render.php takes.
// The sample runs on load, and the form again on Ctrl+Enter (Cmd+Enter on a Mac); an
// example from a link waits for Run.
//
// When Monaco loads - from the address the form's data-monaco names - each box becomes an
// editor with colours: the template in the PAD colouring of the edit application
// (www/edit/pad-mode.js, fed with PAD's built-in names from data-names), the data text in
// the colours of the format it reads as - JSON, YAML, XML, plain for CSV - the way PAD's
// padContentType tells them apart, and the PHP code as PHP. The text fields stay the form's
// fields, written on every change; without Monaco they are the boxes.

(function () {

  var form   = document.getElementById('liveForm');
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

  // The link to the playground beside the buttons carries the same hash: it opens the
  // boxes there as they stand here, and waits there for its button as any link does.

  var other     = document.getElementById('toOther');
  var otherBase = other ? other.getAttribute('href').split('#')[0] : '';

  function boxes() {
    return '#s=' + encode({ source: source.value, data: data.value, php: php.value, mode: mode.value });
  }

  function relink(hash) {
    if (other) other.href = otherBase + hash;
  }

  function remember() {
    var hash = boxes();
    history.replaceState(null, '', hash);
    relink(hash);
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
    toggle.textContent = which === 'php' ? 'Switch to data' : 'Switch to PHP';
  }

  // The template and the data or PHP box share their rows - fourteen and ten as written. A
  // box without data or PHP is small, its placeholder on a line or two, and the template
  // takes the rows it gives up: an example of a template alone, most of those opened from
  // their icon, stood in a box of fourteen rows above ten empty ones. And when the text of
  // one box does not fit while the other has rows to spare, the one that needs them takes
  // them - a long template above a few lines of data, or the other way round - keeping a
  // line to spare in each; when neither fits both keep their own rows. Fitted again on every
  // change and on the switch; a box - the text field or the editor over it - takes its share
  // of the column's height from --rows (live.css), the page filling the window.

  var rows  = { source: source.rows, data: data.rows };
  var total = rows.source + rows.data;
  var small = 3;
  var least = 5;

  function rowsOf(area, count) {
    area.rows = count;
    area.style.setProperty('--rows', count);
    if (area.host) area.host.style.setProperty('--rows', count);
  }

  function lines(text) {
    return text === '' ? 0 : text.split('\n').length;
  }

  function fit() {
    var box  = mode.value === 'php' ? php : data;
    var want = lines(source.value) + 1;
    var need = Math.max(small, lines(box.value) + 1);
    var keep = rows.data;
    if (box.value === '')
      keep = small;
    else if (need > rows.data && want < rows.source)
      keep = Math.min(need, total - Math.max(want, least));
    else if (want > rows.source && need < rows.data)
      keep = need;
    rowsOf(source, total - keep);
    rowsOf(data,   keep);
    rowsOf(php,    keep);
  }

  var shared = location.hash.indexOf('#s=') === 0 ? decode(location.hash.slice(3)) : null;

  if (shared) {
    source.value = shared.source || '';
    data.value   = shared.data   || '';
    if (typeof shared.php === 'string') php.value = shared.php;
  }

  show(shared && shared.mode === 'php' ? 'php' : 'data');
  fit();
  toggle.hidden = false;
  relink(boxes());

  toggle.addEventListener('click', function () {
    show(mode.value === 'php' ? 'data' : 'php');
    fit();
    remember();
    var box = mode.value === 'php' ? php : data;
    if (box.editor) box.editor.focus(); else box.focus();
  });

  source.addEventListener('input', function () { fit(); remember(); });
  data.addEventListener('input', function () { fit(); remember(); });
  php.addEventListener('input', function () { fit(); remember(); });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
      event.preventDefault();
      render();
    }
  });

  // The address of the page the frame shows, under "open alone": the frame is this site's,
  // so its address can be read once it has loaded.

  var frame = document.getElementById('liveOut');
  var open  = document.getElementById('liveOpen');

  frame.addEventListener('load', function () {
    var at = '';
    try { at = frame.contentWindow.location.href; } catch (e) { at = ''; }
    open.hidden = !at || at === 'about:blank';
    if (!open.hidden) open.href = at;
  });

  // An example from a link is shown, not run: its template - and its PHP code - is whoever
  // made the link's, and it runs on this machine - a {curl} in it fetches what it likes and
  // sends what it read where it likes, PHP code does anything at all. A page on another site
  // only had to open such a link for it to run. It waits for Run, which the visitor presses
  // having read it; the sample runs at once.

  // ------------------------------------------------------------------------------------
  // The boxes as editors
  // ------------------------------------------------------------------------------------

  // The type PAD would read the data text as (lib/checks.php, padContentType), for its
  // colours only: render.php reads it with PAD's own sniffing.

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

  function loadMonaco(at) {
    return new Promise(function (resolve, reject) {
      var timer = setTimeout(function () { reject(new Error('Monaco did not load in time')); }, 25000);
      var script = document.createElement('script');
      script.src = at.replace(/\/$/, '') + '/loader.js';
      script.onerror = function () { clearTimeout(timer); reject(new Error('Monaco could not be loaded from ' + at)); };
      script.onload = function () {
        try {
          window.require.config({ paths: { vs: at.replace(/\/$/, '') } });
          window.require(['vs/editor/editor.main'], function () { clearTimeout(timer); resolve(window.monaco); },
                         function (error) { clearTimeout(timer); reject(error); });
        } catch (error) { clearTimeout(timer); reject(error); }
      };
      document.head.appendChild(script);
    });
  }

  function editors(monaco) {
    var names = JSON.parse(form.getAttribute('data-names') || '{}');
    var base  = { pad: names };
    names.docs = {};

    // The edit application's language, with nothing of an application behind it: no files,
    // no fields of a PHP file, no pages - the names of PAD itself complete and colour.

    window.PadMode.register(monaco, {
      base:     function () { return base; },
      appLang:  function () { return null; },
      fileOf:   function () { return null; },
      fields:   function () { return []; },
      modelFor: function () { return Promise.resolve(null); },
      exists:   function () { return false; },
      settings: function () { return {}; }
    });

    // The editors are light or dark with the frame: the colour scheme of the page - light, or
    // the theme chosen in the bar, which comes with the page as it is answered again.

    var theme  = function () { return /dark/.test(getComputedStyle(document.documentElement).colorScheme) ? 'pad-dark' : 'pad-light'; };

    // An editor takes the place of a text field: the field is hidden for good, the editor's
    // box takes over its data-mode - the switch shows and hides the box - and every change
    // is written back into the field, with the input event the address bar listens to.

    function over(area, language) {
      var host = document.createElement('div');
      host.className = 'editorBox';
      host.style.setProperty('--rows', area.rows);
      if (area.hasAttribute('data-mode')) {
        host.setAttribute('data-mode', area.getAttribute('data-mode'));
        host.hidden = area.hidden;
        area.removeAttribute('data-mode');
      }
      area.hidden = true;
      area.host = host;
      area.parentNode.insertBefore(host, area);
      var label = document.querySelector('label[for="' + area.id + '"]');
      var editor = monaco.editor.create(host, {
        value: area.value, language: language, theme: theme(), ariaLabel: label ? label.textContent : area.id,
        automaticLayout: true, minimap: { enabled: false }, fontSize: 13, tabSize: 2, insertSpaces: true,
        scrollBeyondLastLine: false, lineNumbersMinChars: 3, renderLineHighlight: 'none',
        bracketPairColorization: { enabled: true }, fontFamily: 'ui-monospace, Menlo, monospace',
        padding: { top: 6 }, overviewRulerLanes: 0, placeholder: area.getAttribute('placeholder') || ''
      });

      // An empty box has no line numbers: the 1 of its one empty line read as a value in it.

      var numbers = function () { editor.updateOptions({ lineNumbers: editor.getValue() === '' ? 'off' : 'on' }); };
      numbers();
      editor.onDidChangeModelContent(function () {
        area.value = editor.getValue();
        area.dispatchEvent(new Event('input'));
        numbers();
      });
      editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter, render);
      if (label) label.addEventListener('click', function () { editor.focus(); });
      area.editor = editor;
      return editor;
    }

    over(source, 'pad');
    over(php, 'php');

    // The data text's colours follow its format as it is typed.

    var dataEditor = over(data, dataLanguage(data.value)), timer = null;
    dataEditor.onDidChangeModelContent(function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        var model = dataEditor.getModel(), language = dataLanguage(model.getValue());
        if (model.getLanguageId() !== language) monaco.editor.setModelLanguage(model, language);
      }, 300);
    });
  }

  var monacoAt = form.getAttribute('data-monaco');

  if (monacoAt && window.PadMode)
    loadMonaco(monacoAt).then(editors, function (error) {
      if (window.console) console.warn('live: the boxes stay plain - ' + error.message);
    });

  if (shared) {
    var hint = document.querySelector('.hint');
    if (hint) hint.textContent = mode.value === 'php'
      ? 'An example from a link, with PHP code: read both, then Run (Ctrl+Enter) runs them on this machine.'
      : 'An example from a link: read it, then Run (Ctrl+Enter) runs it on this machine.';
  } else
    render();

})();
