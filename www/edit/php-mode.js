// PHP in the PAD editor: what Monaco's own PHP mode (colouring only) gets on top, from the
// editor's language call (apps/edit/_lib/language.php).
//
// completion      the PAD functions a page's PHP calls - padRedirect, db, padArrGet ... - with
//                 their real signatures and the documentation of docs/reference/HELPERS.md
//                 and CLAUDE.md; the functions of the application's own _lib files; every
//                 internal PHP function; the variables of the file and the superglobals; in
//                 a _config/ file the $pad settings with their defaults; the verbs of db()
//                 at the start of its query string; and a few PAD snippets
// signature help  the parameters of the call the cursor stands in, the current one marked
// hover           the same documentation, or the php.net page of an internal function
// definition      a function of the application's _lib to the line that declares it

(function () {

  'use strict';

  var SUPERGLOBALS = ['_GET', '_POST', '_REQUEST', '_SERVER', '_SESSION', '_COOKIE', '_FILES', '_ENV', 'GLOBALS'];

  var PAGE_GLOBALS = ['padPage', 'padApp', 'padGo', 'padGoExt', 'padHost', 'padRoot', 'padExpose', 'padContentType',
                      'padFragmentOnly', 'padCsp', 'padOutputType'];

  var SNIPPETS = [
    { label: 'padExpose', insert: '\\$padExpose = [ \'${1:orders}\' ];', doc: 'The page variables a page answers as JSON or CSV' },
    { label: 'padRedirect', insert: 'padRedirect ( \'${1:index}\' );', doc: 'Send the visitor to a page of this application' },
    { label: 'padPosted', insert: 'if ( padPosted ( \'${1:form}\' ) ) {\n\n  \\$errors = padValidate ( [ \'${2:email}\' => \'${3:required|email}\' ] );\n\n  if ( ! \\$errors ) {\n    $0\n  }\n\n}', doc: 'Handle a {form} that came back' },
    { label: 'db ARRAY', insert: '\\$${1:rows} = db ( "ARRAY * FROM ${2:table}${3: WHERE id={0}}", [ ${4:\\$id} ] );', doc: 'Rows of a query' },
    { label: 'db RECORD', insert: '\\$${1:row} = db ( "RECORD * FROM ${2:table} WHERE id={0}", [ ${3:\\$id} ] );', doc: 'One row' },
    { label: 'foreach', insert: 'foreach ( \\$${1:rows} as \\$${2:row} ) {\n  $0\n}', doc: 'foreach loop' },
    { label: 'function', insert: '// ${3:What it does.}\n\nfunction ${1:name} ( ${2} ) {\n\n  $0\n\n}', doc: 'A function, PAD style' }
  ];

  function index(base) {
    if (index.cache && index.cache.base === base) return index.cache;
    var map = {};
    base.php.internal.forEach(function (f) { map[f.name.toLowerCase()] = { name: f.name, sig: f.sig, doc: '', internal: true }; });
    base.php.pad.forEach(function (f) { map[f.name.toLowerCase()] = { name: f.name, sig: f.sig, doc: f.doc, group: f.group, pad: true }; });
    index.cache = { base: base, map: map };
    return index.cache;
  }

  function lookup(hooks, model, name) {
    var base = hooks.base();
    if (!base) return null;
    var lower = name.toLowerCase();
    var lib = libFunctions(hooks, model).filter(function (f) { return f.name.toLowerCase() === lower; })[0];
    if (lib) return { name: lib.name, sig: lib.sig, doc: lib.doc, lib: lib };
    return index(base).map[lower] || null;
  }

  function libFunctions(hooks, model) {
    var app = hooks.appLang();
    var file = hooks.fileOf(model);
    if (!app || !file) return [];
    var chain = window.PadMode.chainOf(file.root === 'app' ? file.path : '', app);
    return app.lib.filter(function (f) { return chain.indexOf(f.dir) >= 0; });
  }

  function docText(f) {
    var text = '```php\n' + f.sig + '\n```';
    if (f.doc) text += '\n\n' + f.doc;
    if (f.group) text += '\n\n*' + f.group + '*';
    if (f.lib) text += '\n\n`' + f.lib.file + ':' + f.lib.line + '`';
    if (f.internal) text += '\n\n[php.net/' + f.name + '](https://www.php.net/' + f.name + ')';
    return text;
  }

  function inString(before) {
    var line = before.split('\n').pop();
    var single = 0, double = 0;
    for (var i = 0; i < line.length; i++) {
      var c = line.charAt(i);
      if (c === '\\') { i++; continue; }
      if (c === "'" && !double) single ^= 1;
      else if (c === '"' && !single) double ^= 1;
    }
    return single || double;
  }

  function completions(monaco, hooks, model, position) {
    var base = hooks.base();
    if (!base) return { suggestions: [] };
    var K = monaco.languages.CompletionItemKind;
    var Snip = monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet;
    var word = model.getWordUntilPosition(position);
    var range = { startLineNumber: position.lineNumber, endLineNumber: position.lineNumber, startColumn: word.startColumn, endColumn: word.endColumn };
    var before = model.getValueInRange({ startLineNumber: Math.max(1, position.lineNumber - 50), startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column });
    var file = hooks.fileOf(model);
    var out = [], m;

    // db ( "  - the verbs, at the start of the query.
    if ((m = before.match(/\b(?:db|padDb)\s*\(\s*["']([A-Za-z]*)$/))) {
      base.php.verbs.forEach(function (v) {
        out.push({ label: v, kind: K.Keyword, insertText: v + ' ', range: range, detail: 'db() verb', sortText: '0' + v });
      });
      return { suggestions: out };
    }

    // $name - variables.
    if ((m = before.match(/\$([A-Za-z_]\w*)?$/))) {
      var vr = { startLineNumber: position.lineNumber, endLineNumber: position.lineNumber, startColumn: position.column - (m[1] || '').length, endColumn: position.column };
      var seen = {};
      var add = function (name, detail, doc, sort) {
        if (seen[name]) return;
        seen[name] = true;
        out.push({ label: '$' + name, kind: K.Variable, insertText: name, filterText: name, range: vr, detail: detail,
                   documentation: doc ? { value: doc } : undefined, sortText: sort + name });
      };
      var text = model.getValue(), here = model.getOffsetAt(position), re = /\$([A-Za-z_]\w*)/g, v;
      while ((v = re.exec(text)) !== null) if (v.index + v[0].length !== here) add(v[1], 'in this file', '', '0');
      if (file && /(^|\/)_config\//.test(file.path))
        base.php.config.forEach(function (c) { add(c.name, '= ' + c.value, '```php\n$' + c.name + ' = ' + c.value + ';\n```\n\n' + c.doc, '1'); });
      SUPERGLOBALS.forEach(function (g) { add(g, 'superglobal', '', '2'); });
      PAGE_GLOBALS.forEach(function (g) { add(g, 'PAD', '', '3'); });
      if (file && !/(^|\/)_config\//.test(file.path))
        base.php.config.forEach(function (c) { add(c.name, 'PAD setting', '```php\n$' + c.name + ' = ' + c.value + ';\n```\n\n' + c.doc, '4'); });
      return { suggestions: out };
    }

    if (inString(before)) return { suggestions: [] };
    if (/(->|::)\w*$/.test(before)) return { suggestions: [] };

    libFunctions(hooks, model).forEach(function (f) {
      out.push({ label: f.name, kind: K.Function, insertText: f.name + ' ( $0 )', insertTextRules: Snip, range: range,
                 detail: f.sig, documentation: { value: docText({ name: f.name, sig: f.sig, doc: f.doc, lib: f }) }, sortText: '0' + f.name });
    });
    base.php.pad.forEach(function (f) {
      out.push({ label: f.name, kind: K.Function, insertText: f.name + ' ( $0 )', insertTextRules: Snip, range: range,
                 detail: f.sig, documentation: { value: docText(f) }, sortText: '1' + f.name });
    });
    if (word.word.length >= 1) {
      base.php.internal.forEach(function (f) {
        out.push({ label: f.name, kind: K.Function, insertText: f.name + '($0)', insertTextRules: Snip, range: range,
                   detail: f.sig, sortText: '3' + f.name });
      });
    }
    base.php.keywords.forEach(function (k) { out.push({ label: k, kind: K.Keyword, insertText: k, range: range, sortText: '2' + k }); });
    SNIPPETS.forEach(function (s) {
      out.push({ label: s.label, kind: K.Snippet, insertText: s.insert, insertTextRules: Snip, range: range,
                 detail: 'PAD snippet', documentation: { value: s.doc }, sortText: '0' + s.label });
    });
    return { suggestions: out };
  }

  // The call the cursor stands in: the name before the nearest unclosed ( and how many
  // commas at its own depth come after it.
  function callAt(before) {
    var depth = 0, commas = 0, quote = '';
    for (var i = before.length - 1; i >= 0 && before.length - i < 4000; i--) {
      var c = before.charAt(i);
      if (quote) { if (c === quote && before.charAt(i - 1) !== '\\') quote = ''; continue; }
      if (c === '"' || c === "'") { quote = c; continue; }
      if (c === ')' || c === ']') depth++;
      else if (c === '(' || c === '[') {
        if (depth === 0) {
          if (c === '[') return null;
          var m = before.slice(0, i).match(/([A-Za-z_]\w*)\s*$/);
          return m ? { name: m[1], active: commas } : null;
        }
        depth--;
      } else if (c === ',' && depth === 0) commas++;
      else if (c === ';' || c === '{' || c === '}') return null;
    }
    return null;
  }

  function splitParms(sig) {
    var open = sig.indexOf('('), close = sig.lastIndexOf(')');
    var inner = sig.slice(open + 1, close).trim();
    if (!inner) return [];
    var parts = [], depth = 0, cur = '';
    for (var i = 0; i < inner.length; i++) {
      var c = inner.charAt(i);
      if (c === '(' || c === '[') depth++;
      if (c === ')' || c === ']') depth--;
      if (c === ',' && depth === 0) { parts.push(cur.trim()); cur = ''; continue; }
      cur += c;
    }
    if (cur.trim()) parts.push(cur.trim());
    return parts;
  }

  function signature(monaco, hooks, model, position) {
    var before = model.getValueInRange({ startLineNumber: Math.max(1, position.lineNumber - 30), startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column });
    var call = callAt(before);
    if (!call) return null;
    var f = lookup(hooks, model, call.name);
    if (!f) return null;
    var parms = splitParms(f.sig);
    return {
      value: {
        signatures: [{ label: f.sig, documentation: f.doc ? { value: f.doc } : undefined,
                       parameters: parms.map(function (p) { return { label: p }; }) }],
        activeSignature: 0,
        activeParameter: Math.min(call.active, Math.max(0, parms.length - 1))
      },
      dispose: function () {}
    };
  }

  function hover(monaco, hooks, model, position) {
    var word = model.getWordAtPosition(position);
    if (!word) return null;
    var line = model.getLineContent(position.lineNumber);
    if (line.charAt(word.startColumn - 2) === '$') {
      var base = hooks.base();
      var c = base && base.php.config.filter(function (x) { return x.name === word.word; })[0];
      if (!c) return null;
      return { range: new monaco.Range(position.lineNumber, word.startColumn - 1, position.lineNumber, word.endColumn),
               contents: [{ value: '```php\n$' + c.name + ' = ' + c.value + ';\n```\n\n' + c.doc }] };
    }
    if (!/^\s*\(/.test(line.slice(word.endColumn - 1))) return null;
    var f = lookup(hooks, model, word.word);
    if (!f) return null;
    return { range: new monaco.Range(position.lineNumber, word.startColumn, position.lineNumber, word.endColumn), contents: [{ value: docText(f) }] };
  }

  function definition(monaco, hooks, model, position) {
    var word = model.getWordAtPosition(position);
    if (!word) return null;
    var f = libFunctions(hooks, model).filter(function (x) { return x.name === word.word; })[0];
    if (!f) return null;
    var file = hooks.fileOf(model);
    var app = f.dir === '_common' ? '_common' : file.app;
    return hooks.modelFor(app, 'app', f.file).then(function (target) {
      return target ? [{ uri: target.uri, range: new monaco.Range(f.line, 1, f.line, 1) }] : null;
    });
  }

  function register(monaco, hooks) {
    monaco.languages.registerCompletionItemProvider('php', {
      triggerCharacters: ['$', '"', "'", '>', ':'],
      provideCompletionItems: function (model, position) { return completions(monaco, hooks, model, position); }
    });
    monaco.languages.registerSignatureHelpProvider('php', {
      signatureHelpTriggerCharacters: ['(', ','],
      signatureHelpRetriggerCharacters: [','],
      provideSignatureHelp: function (model, position) { return signature(monaco, hooks, model, position); }
    });
    monaco.languages.registerHoverProvider('php', { provideHover: function (model, position) { return hover(monaco, hooks, model, position); } });
    monaco.languages.registerDefinitionProvider('php', { provideDefinition: function (model, position) { return definition(monaco, hooks, model, position); } });
  }

  window.PhpMode = { register: register };

})();
