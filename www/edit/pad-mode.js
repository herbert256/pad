// PAD for Monaco: the language 'pad' of the editor at /edit/ - a static file, nothing PAD
// parses. What it knows comes from the editor's language call (apps/edit/_lib/language.php):
// the built-in names of editors/lsp/completions.json with their documentation from
// docs/reference, and the application's own tags, functions, snippets, pages and fields.
//
// tokenizer    a Monarch grammar of PAD inside HTML: comments {# #} and {-- --}, {ignore}
//              raw text, constructs (@page@), tag names - the built-in ones apart from the
//              application's -, type prefixes (app:), fields ($x %x !x ?x #x &x ^x), pipe
//              functions, options (name=), properties (first@items), strings, numbers, word
//              operators; HTML tags and attributes with PAD inside their values, and
//              <script>/<style> bodies as JavaScript and CSS
// completion   what PAD would accept where the cursor is - the contexts of
//              editors/lsp/pad-lsp.js (contextAt), and more: after { the tags, after {/ the
//              tags still open (innermost first), after a prefix its names, after $ the
//              fields the page's PHP assigns, after | the pipe functions, after the tag name
//              its options, after name@ the open tags, inside the quotes of {page '...'},
//              {include '...'}, callback='...', {trans '...'} and {collection '...'} the
//              pages, snippets, callbacks, translation keys and collections; @ outside a tag
//              the constructs; < and </ the HTML tags
// hover        the reference documentation, an application file's own comment, or the
//              lines that assign a field
// definition   PAD's own lookup order (pad-lsp.js targets): {mytag} to the nearest
//              _tags/mytag.*, | money to _functions/money.php, a snippet to _include,
//              callback='x' to _callbacks, {page 'x'} to the page, {$total} to the line of
//              the PHP that assigns it
// structure    tag pairs fold, make the outline (Ctrl+Shift+O), and rename together: the
//              name of {items} and of its {/items} are edited as one (linked editing)

(function () {

  'use strict';

  var OPERATORS  = ['eq', 'ne', 'gt', 'lt', 'ge', 'le', 'and', 'or', 'xor', 'not', 'range', 'in', 'like', 'matches'];
  var EXPRESSION = ['if', 'elseif', 'while', 'until', 'case', 'when', 'set', 'echo', 'increment', 'decrement', 'assert'];
  var STRUCTURAL = ['if', 'case', 'while', 'until', 'data', 'content', 'ignore', 'form', 'cache', 'block', 'slot',
                    'push', 'fragment', 'live', 'markdown', 'spaceless', 'tree', 'branch', 'flash', 'trace', 'tidy',
                    'record', 'check', 'code', 'sandbox', 'ifchanged', 'first', 'last'];

  var HTML_TAGS = ['a', 'abbr', 'article', 'aside', 'audio', 'b', 'blockquote', 'body', 'br', 'button', 'canvas',
                   'caption', 'code', 'col', 'colgroup', 'dd', 'details', 'dialog', 'div', 'dl', 'dt', 'em',
                   'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head',
                   'header', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'label', 'legend', 'li', 'link', 'main',
                   'meta', 'nav', 'ol', 'optgroup', 'option', 'output', 'p', 'picture', 'pre', 'progress', 'script',
                   'section', 'select', 'small', 'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup',
                   'svg', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title',
                   'tr', 'u', 'ul', 'video'];
  var HTML_VOID = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source', 'track', 'wbr'];

  var NAME = '[A-Za-z_][A-Za-z0-9_]*';
  var TAG_RE = /\{(~?\/?~?)([A-Za-z_][A-Za-z0-9_]*(?::[A-Za-z_][A-Za-z0-9_]*)?)([^{}]*)/g;

  // ------------------------------------------------------------------------------------
  // Tag pairs - the scan behind {/ completion, folding, the outline and linked editing.
  // ------------------------------------------------------------------------------------

  // The comments the engine drops before it scans, blanked to spaces of the same length so
  // every offset stays where it was.
  function blankComments(text) {
    var blank = function (m) { return m.replace(/[^\n]/g, ' '); };
    return text
      .replace(/\{#(?![A-Za-z_][A-Za-z0-9_]*\s*[}|])[\s\S]*?#\}/g, blank)
      .replace(/\{--\s[\s\S]*?--\}/g, blank)
      .replace(/(\{~?ignore\b[^}]*\})([\s\S]*?)(\{~?\/ignore~?\})/g, function (m, a, b, c) { return a + blank(b) + c; });
  }

  function isSingle(base, name, rest) {
    return base.pad.single.indexOf(name) >= 0 || base.pad.branch.indexOf(name) >= 0 ||
           /\/\s*$/.test(rest) || name.charAt(0) === '$';
  }

  // Every tag of the text: opening and closing ones paired where they meet, innermost first,
  // as PAD pairs them - a closing tag closes the nearest open tag of its name.
  function scanPairs(text, base) {
    var clean = blankComments(text);
    var stack = [], pairs = [], m;
    TAG_RE.lastIndex = 0;
    while ((m = TAG_RE.exec(clean)) !== null) {
      var name = m[2], closing = m[1].indexOf('/') >= 0;
      var nameAt = m.index + 1 + m[1].length;
      if (closing) {
        for (var i = stack.length - 1; i >= 0; i--) {
          if (stack[i].name === name) {
            var open = stack[i];
            stack.length = i;
            pairs.push({ name: name, open: open, close: { index: m.index, nameAt: nameAt, end: m.index + m[0].length + 1 } });
            break;
          }
        }
      } else if (!isSingle(base, name, m[3])) {
        stack.push({ name: name, index: m.index, nameAt: nameAt, head: m[0] + '}', end: m.index + m[0].length + 1 });
      }
    }
    return { pairs: pairs, open: stack };
  }

  function openTagsBefore(text, base) {
    return scanPairs(text, base).open.map(function (t) { return t.name; });
  }

  // ------------------------------------------------------------------------------------
  // Where a name lives: the application's names nearest the file first - the file's own
  // directory (cut above the first _xxx directory) up to the root, then _common.
  // ------------------------------------------------------------------------------------

  function chainOf(path, app) {
    var parts = (path || '').split('/');
    parts.pop();
    var cut = parts.findIndex(function (p) { return p.charAt(0) === '_'; });
    var context = cut < 0 ? parts : parts.slice(0, cut);
    var chain = [];
    for (var n = context.length; n >= 0; n--) chain.push(context.slice(0, n).join('/'));
    if (app && app.common) chain.push('_common');
    return chain;
  }

  function appNames(hooks, model, kind) {
    var app = hooks.appLang();
    var file = hooks.fileOf(model);
    if (!app || !file) return [];
    var chain = chainOf(file.root === 'app' ? file.path : '', app);
    var seen = {}, out = [];
    chain.forEach(function (dir) {
      app.names.forEach(function (n) {
        if (n.kind === kind && n.dir === dir && !seen[n.name]) { seen[n.name] = true; out.push(n); }
      });
    });
    return out;
  }

  function appFile(n) {
    return n.dir === '_common' ? { app: '_common', path: n.file } : { app: null, path: n.file };
  }

  // ------------------------------------------------------------------------------------
  // The word under the cursor, in PAD's terms - pad-lsp.js contextAt.
  // ------------------------------------------------------------------------------------

  function contextAt(text, offset) {
    var isWord = function (c) { return /[A-Za-z0-9_]/.test(c || ''); };
    var s = offset, e = offset;
    if (!isWord(text[s]) && /[$%!?@{|]/.test(text[s] || '') && isWord(text[s + 1])) { s++; e++; }
    while (s > 0 && isWord(text[s - 1])) s--;
    while (isWord(text[e])) e++;
    if (s === e) return null;
    var word = text.slice(s, e);
    if (!/^[A-Za-z_]/.test(word)) return null;

    var before = text[s - 1] || '', after = text[e] || '';
    if (before === '@' && after === '@') return { role: 'construct', word: word, start: s - 1, end: e + 1 };

    var open = text.lastIndexOf('{', s);
    var inTag = open >= 0 && text.slice(open, s).indexOf('}') < 0;
    var head = inTag ? text.slice(open + 1, s) : '';

    if ('$%!?'.indexOf(before) >= 0 && before) return { role: 'field', word: word, start: s - 1, end: e };
    if (after === '@' && inTag) return { role: 'property', word: word, start: s, end: e + 1 };
    if (!inTag) return null;

    if (after === ':' && /^(?:~?\/?~?)$/.test(head)) return { role: 'prefix', word: word, start: s, end: e + 1 };
    if (after === ':' && /\|\s*$/.test(head)) return { role: 'prefix', word: word, start: s, end: e + 1 };

    var prefixed = head.match(new RegExp('(' + NAME + '):$'));
    if (prefixed) {
      var lead = head.slice(0, -prefixed[0].length);
      if (/^(?:~?\/?~?)$/.test(lead)) return { role: 'tag', word: word, prefix: prefixed[1], start: s - prefixed[0].length, end: e };
      if (/\|\s*$/.test(lead)) return { role: 'function', word: word, prefix: prefixed[1], start: s - prefixed[0].length, end: e };
    }

    if (/^(?:~?\/?~?)$/.test(head)) return { role: 'tag', word: word, start: s, end: e };
    if (/\|\s*$/.test(head)) return { role: 'function', word: word, start: s, end: e };
    if (/callback\s*=\s*['"]$/.test(head)) return { role: 'callback', word: word, start: s, end: e };
    if (/^~?(?:page|redirect)\s+['"]$/.test(head)) return { role: 'page', word: text.slice(s, e), start: s, end: e };
    if (/^~?include\s+['"]$/.test(head)) return { role: 'include', word: word, start: s, end: e };
    if (/['"]$/.test(head)) return null;
    if (/[\s,]$/.test(head)) return { role: 'option', word: word, start: s, end: e };
    return null;
  }

  // ------------------------------------------------------------------------------------
  // The language
  // ------------------------------------------------------------------------------------

  function monarch(base) {
    var tags = base.pad.tags.concat(base.pad.branch);
    var START = /\{(?=~?\/?~?[A-Za-z_$%!?#&^@])/;
    return {
      defaultToken: '',
      ignoreCase: false,
      tags: tags,
      functions: base.pad.functions,
      properties: base.pad.properties,
      options: base.pad.options,
      prefixes: base.pad.prefixes,
      operators: OPERATORS,

      tokenizer: {
        root: [
          [/\{#(?![A-Za-z_]\w*\s*[}|])/, 'comment.pad', '@commentHash'],
          [/\{--(?=\s)/, 'comment.pad', '@commentDash'],
          [/(\{~?)(ignore)([^}]*)(~?\})/, ['delimiter.pad', 'keyword.tag.pad', 'attribute.name.pad', { token: 'delimiter.pad', next: '@ignored' }]],
          [/@(?:page|content|start|end|else|tidy)@/, 'keyword.construct.pad'],
          [START, { token: 'delimiter.pad', next: '@tagStart' }],
          [/<!--/, 'comment.html', '@htmlComment'],
          [/<!DOCTYPE/i, 'metatag.html', '@doctype'],
          [/(<)(script)(?=[\s>])/i, ['delimiter.html', { token: 'tag.html', next: '@script' }]],
          [/(<)(style)(?=[\s>])/i, ['delimiter.html', { token: 'tag.html', next: '@style' }]],
          [/(<\/?)([\w\-:.]+)/, ['delimiter.html', { token: 'tag.html', next: '@htmlTag' }]],
          [/&(?:#\d+|#x[0-9a-fA-F]+|\w+);/, 'string.escape.html'],
          [/[^<{@&]+/, ''],
          [/./, '']
        ],

        commentHash: [[/#\}/, 'comment.pad', '@pop'], [/[^#]+/, 'comment.pad'], [/#/, 'comment.pad']],
        commentDash: [[/--\}/, 'comment.pad', '@pop'], [/[^-]+/, 'comment.pad'], [/-/, 'comment.pad']],
        ignored: [
          [/(\{~?\/)(ignore)(~?\})/, ['delimiter.pad', 'keyword.tag.pad', { token: 'delimiter.pad', next: '@pop' }]],
          [/[^{]+/, 'string.raw.pad'],
          [/\{/, 'string.raw.pad']
        ],
        htmlComment: [[/-->/, 'comment.html', '@pop'], [/[^-]+/, 'comment.html'], [/./, 'comment.html']],
        doctype: [[/[^>]+/, 'metatag.content.html'], [/>/, 'metatag.html', '@pop']],

        tagStart: [
          [/~/, 'delimiter.pad'],
          [/\//, 'delimiter.pad'],
          [/([A-Za-z_]\w*)(:)(?=[A-Za-z_$])/, [{ cases: { '@prefixes': 'type.prefix.pad', '@default': 'type.prefix.custom.pad' } }, 'delimiter.pad']],
          [/[$%!?#&^]+[\w.]*/, { token: 'variable.pad', switchTo: '@tagBody' }],
          [/([A-Za-z_]\w*)(@)(\w*)/, [{ cases: { '@properties': 'property.pad', '@default': 'variable.pad' } }, 'delimiter.pad', { token: 'tag.ref.pad', switchTo: '@tagBody' }]],
          [/[A-Za-z_]\w*/, { cases: { '@tags': { token: 'keyword.tag.pad', switchTo: '@tagBody' }, '@default': { token: 'tag.custom.pad', switchTo: '@tagBody' } } }],
          [/@/, { token: 'variable.placeholder.pad', switchTo: '@tagBody' }],
          [/./, { token: '@rematch', switchTo: '@tagBody' }]
        ],

        tagBody: [
          [/~?\}/, 'delimiter.pad', '@pop'],
          [START, 'delimiter.pad', '@tagStart'],
          [/'/, 'string.pad', '@stringSingle'],
          [/"/, 'string.pad', '@stringDouble'],
          [/(\|)(\s*)([A-Za-z_]\w*)(:)([A-Za-z_]\w*)/, ['operator.pipe.pad', '', 'type.prefix.pad', 'delimiter.pad', 'function.custom.pad']],
          [/(\|)(\s*)([A-Za-z_]\w*)/, ['operator.pipe.pad', '', { cases: { '@functions': 'function.pad', '@default': 'function.custom.pad' } }]],
          [/\|/, 'operator.pipe.pad'],
          [/[$%!?#&^]+[A-Za-z_][\w.]*/, 'variable.pad'],
          [/([A-Za-z_]\w*)(@)(\w*)/, [{ cases: { '@properties': 'property.pad', '@default': 'variable.pad' } }, 'delimiter.pad', 'tag.ref.pad']],
          [/@/, 'variable.placeholder.pad'],
          [/([A-Za-z_][\w-]*)(\s*)(=)(?![=>])/, ['attribute.name.pad', '', 'operator.pad']],
          [/\d+(?:\.\d+)?/, 'number.pad'],
          [/[A-Za-z_]\w*/, { cases: { '@operators': 'keyword.operator.pad', '@options': 'attribute.name.pad', '@default': 'identifier.pad' } }],
          [/[=<>!+\-*\/%.?:,()\[\]]/, 'operator.pad'],
          [/\s+/, ''],
          [/./, '']
        ],

        stringSingle: [
          [START, 'delimiter.pad', '@tagStart'],
          [/[^'\\{]+/, 'string.pad'],
          [/\\./, 'string.escape.pad'],
          [/'/, 'string.pad', '@pop'],
          [/./, 'string.pad']
        ],
        stringDouble: [
          [START, 'delimiter.pad', '@tagStart'],
          [/[^"\\{]+/, 'string.pad'],
          [/\\./, 'string.escape.pad'],
          [/"/, 'string.pad', '@pop'],
          [/./, 'string.pad']
        ],

        htmlTag: [
          [START, 'delimiter.pad', '@tagStart'],
          [/\/?>/, 'delimiter.html', '@pop'],
          [/"/, 'attribute.value.html', '@attrDouble'],
          [/'/, 'attribute.value.html', '@attrSingle'],
          [/[\w\-:@.]+/, 'attribute.name.html'],
          [/=/, 'delimiter.html'],
          [/\s+/, ''],
          [/./, '']
        ],
        attrDouble: [[START, 'delimiter.pad', '@tagStart'], [/[^"{]+/, 'attribute.value.html'], [/"/, 'attribute.value.html', '@pop'], [/\{/, 'attribute.value.html']],
        attrSingle: [[START, 'delimiter.pad', '@tagStart'], [/[^'{]+/, 'attribute.value.html'], [/'/, 'attribute.value.html', '@pop'], [/\{/, 'attribute.value.html']],

        script: [
          [START, 'delimiter.pad', '@tagStart'],
          [/"/, 'attribute.value.html', '@attrDouble'],
          [/'/, 'attribute.value.html', '@attrSingle'],
          [/[\w\-:]+/, 'attribute.name.html'],
          [/=/, 'delimiter.html'],
          [/>/, { token: 'delimiter.html', next: '@scriptBody', nextEmbedded: 'text/javascript' }],
          [/(<\/)(script\s*)(>)/i, ['delimiter.html', 'tag.html', { token: 'delimiter.html', next: '@pop' }]],
          [/\s+/, '']
        ],
        scriptBody: [
          [/<\/script/i, { token: '@rematch', next: '@pop', nextEmbedded: '@pop' }],
          [/[^<]+/, ''],
          [/</, '']
        ],
        style: [
          [START, 'delimiter.pad', '@tagStart'],
          [/"/, 'attribute.value.html', '@attrDouble'],
          [/'/, 'attribute.value.html', '@attrSingle'],
          [/[\w\-:]+/, 'attribute.name.html'],
          [/=/, 'delimiter.html'],
          [/>/, { token: 'delimiter.html', next: '@styleBody', nextEmbedded: 'text/css' }],
          [/(<\/)(style\s*)(>)/i, ['delimiter.html', 'tag.html', { token: 'delimiter.html', next: '@pop' }]],
          [/\s+/, '']
        ],
        styleBody: [
          [/<\/style/i, { token: '@rematch', next: '@pop', nextEmbedded: '@pop' }],
          [/[^<]+/, ''],
          [/</, '']
        ]
      }
    };
  }

  // The colours of the VS Code kit (editors/vscode/pad/package.json) for the dark theme,
  // and their darker kin for the light one.
  function themes(monaco) {
    var dark = [
      { token: 'keyword.tag.pad', foreground: 'C695C6', fontStyle: 'bold' },
      { token: 'tag.custom.pad', foreground: 'E0A3E0', fontStyle: 'italic' },
      { token: 'keyword.construct.pad', foreground: 'F28B6E', fontStyle: 'bold' },
      { token: 'type.prefix.pad', foreground: 'C695C6', fontStyle: 'italic' },
      { token: 'type.prefix.custom.pad', foreground: 'C695C6', fontStyle: 'italic underline' },
      { token: 'delimiter.pad', foreground: '5FB4B4' },
      { token: 'variable.pad', foreground: 'F7EC6E' },
      { token: 'variable.placeholder.pad', foreground: 'EC5F66' },
      { token: 'property.pad', foreground: '6699CC', fontStyle: 'bold' },
      { token: 'tag.ref.pad', foreground: 'A8C8E8' },
      { token: 'function.pad', foreground: '7FC8F8' },
      { token: 'function.custom.pad', foreground: '7FC8F8', fontStyle: 'italic' },
      { token: 'operator.pipe.pad', foreground: 'C695C6', fontStyle: 'bold' },
      { token: 'keyword.operator.pad', foreground: 'C695C6' },
      { token: 'operator.pad', foreground: 'C695C6' },
      { token: 'attribute.name.pad', foreground: 'F9AE58' },
      { token: 'identifier.pad', foreground: 'D8DEE9' },
      { token: 'string.pad', foreground: '99C794' },
      { token: 'string.escape.pad', foreground: '5FB4B4' },
      { token: 'string.raw.pad', foreground: 'A6ACB9' },
      { token: 'number.pad', foreground: 'F99157' },
      { token: 'comment.pad', foreground: '7A8794', fontStyle: 'italic' }
    ];
    var light = [
      { token: 'keyword.tag.pad', foreground: '8B3D8B', fontStyle: 'bold' },
      { token: 'tag.custom.pad', foreground: 'A0469F', fontStyle: 'italic' },
      { token: 'keyword.construct.pad', foreground: 'C2410C', fontStyle: 'bold' },
      { token: 'type.prefix.pad', foreground: '8B3D8B', fontStyle: 'italic' },
      { token: 'type.prefix.custom.pad', foreground: '8B3D8B', fontStyle: 'italic underline' },
      { token: 'delimiter.pad', foreground: '0E7C7B' },
      { token: 'variable.pad', foreground: '9A6700' },
      { token: 'variable.placeholder.pad', foreground: 'C0392B' },
      { token: 'property.pad', foreground: '2F5D8A', fontStyle: 'bold' },
      { token: 'tag.ref.pad', foreground: '3B6EA5' },
      { token: 'function.pad', foreground: '0B6BCB' },
      { token: 'function.custom.pad', foreground: '0B6BCB', fontStyle: 'italic' },
      { token: 'operator.pipe.pad', foreground: '8B3D8B', fontStyle: 'bold' },
      { token: 'keyword.operator.pad', foreground: '8B3D8B' },
      { token: 'operator.pad', foreground: '8B3D8B' },
      { token: 'attribute.name.pad', foreground: 'B25E00' },
      { token: 'identifier.pad', foreground: '333B45' },
      { token: 'string.pad', foreground: '2E7D32' },
      { token: 'string.escape.pad', foreground: '0E7C7B' },
      { token: 'string.raw.pad', foreground: '5C6670' },
      { token: 'number.pad', foreground: 'B4530A' },
      { token: 'comment.pad', foreground: '8A939C', fontStyle: 'italic' }
    ];
    monaco.editor.defineTheme('pad-dark', {
      base: 'vs-dark', inherit: true, rules: dark,
      colors: { 'editor.background': '#1B2028', 'editorGutter.background': '#1B2028', 'editor.lineHighlightBackground': '#232A35',
                'editorLineNumber.foreground': '#4C5767', 'editorIndentGuide.background1': '#2A313C' }
    });
    monaco.editor.defineTheme('pad-light', {
      base: 'vs', inherit: true, rules: light,
      colors: { 'editor.background': '#FCFCFD', 'editor.lineHighlightBackground': '#F2F4F7' }
    });
  }

  // ------------------------------------------------------------------------------------
  // Completion
  // ------------------------------------------------------------------------------------

  function docOf(base, kind, name) {
    var d = base.pad.docs[kind] && base.pad.docs[kind][name];
    return d ? { summary: d.summary, text: d.text } : null;
  }

  function item(monaco, label, kind, range, opts) {
    opts = opts || {};
    var out = { label: label, kind: kind, insertText: opts.insert !== undefined ? opts.insert : label, range: opts.range || range };
    if (opts.detail) out.detail = opts.detail;
    if (opts.doc) out.documentation = { value: opts.doc };
    if (opts.snippet) out.insertTextRules = monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet;
    if (opts.sort) out.sortText = opts.sort;
    if (opts.filter) out.filterText = opts.filter;
    if (opts.command) out.command = opts.command;
    return out;
  }

  var PAIR_SNIPPETS = {
    'if': 'if ${1:\\$field} ${2:eq} ${3:1}}\n\t$0\n{/if}',
    'case': 'case ${1:\\$field}}\n\t{when \'${2:value}\'} $3\n\t{else} $0\n{/case}',
    'while': 'while ${1:\\$i} ${2:le} ${3:10}}\n\t$0\n\t{increment ${1:\\$i}}\n{/while}',
    'data': 'data \'${1:name}\'}\n\t${0:["a", "b"]}\n{/data}',
    'ignore': 'ignore}\n$0\n{/ignore}',
    'form': 'form \'${1:name}\', error}\n\t{input \'${2:email}\', type=\'${3:email}\', label=\'${4:E-mail}\', rules=\'${5:required|email}\'}\n\t<button>${6:Send}</button>\n{/form}',
    'cache': 'cache \'${1:name}\', ttl=${2:300}}\n\t$0\n{/cache}',
    'block': 'block \'${1:title}\'}$0{/block}',
    'fragment': 'fragment \'${1:name}\'}\n\t$0\n{/fragment}',
    'markdown': 'markdown}\n$0\n{/markdown}',
    'live': 'live \'${1:region}\'}\n\t$0\n{/live}',
    'push': 'push \'${1:scripts}\'}\n\t$0\n{/push}',
    'tree': 'tree \'${1:menu}\', children=\'${2:items}\'}\n\t<li>{${3:\\$title}}{branch}<ul>{recurse}</ul>{/branch}</li>\n{/tree}',
    'sequence': 'sequence \'${1:1..10}\', name=\'${2:n}\'}\n\t{\\$${2:n}}$0\n{/sequence}'
  };

  function completions(monaco, hooks, model, position) {
    var base = hooks.base();
    if (!base) return { suggestions: [] };
    var K = monaco.languages.CompletionItemKind;

    var start = Math.max(1, position.lineNumber - 400);
    var before = model.getValueInRange({ startLineNumber: start, startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column });
    var word = model.getWordUntilPosition(position);
    var range = { startLineNumber: position.lineNumber, endLineNumber: position.lineNumber, startColumn: word.startColumn, endColumn: word.endColumn };
    var lineText = model.getLineContent(position.lineNumber);
    var nextChar = lineText.charAt(position.column - 1);
    var closeRange = nextChar === '}' ? { startLineNumber: range.startLineNumber, endLineNumber: range.endLineNumber, startColumn: range.startColumn, endColumn: position.column + 1 } : range;
    var out = [];
    var m;

    var open = before.lastIndexOf('{');
    var head = open >= 0 ? before.slice(open + 1) : null;
    var inTag = head !== null && head.indexOf('}') < 0 && (head === '' || /^[~\/A-Za-z_$%!?#&^@]/.test(head));

    if (!inTag) {
      // Outside a tag: the constructs after @, HTML tags after < and </.
      if ((m = before.match(/@(\w*)$/)) && !/[\w.]@\w*$/.test(before)) {
        var at = { startLineNumber: position.lineNumber, endLineNumber: position.lineNumber, startColumn: position.column - m[0].length, endColumn: position.column };
        base.pad.constructs.forEach(function (c) {
          var d = docOf(base, 'construct', c);
          out.push(item(monaco, '@' + c + '@', K.Constant, at, { detail: 'PAD construct', doc: d && d.text }));
        });
        return { suggestions: out };
      }
      if ((m = before.match(/<\/([\w-]*)$/))) {
        var openHtml = htmlOpen(before.slice(0, -m[0].length));
        openHtml.reverse().forEach(function (t, i) {
          out.push(item(monaco, t, K.Property, range, { insert: t + '>', detail: 'close <' + t + '>', sort: ('000' + i).slice(-3) }));
        });
        return { suggestions: out };
      }
      if ((m = before.match(/<([\w-]*)$/))) {
        HTML_TAGS.forEach(function (t) {
          var snip = HTML_VOID.indexOf(t) >= 0 ? t + '$0>' : t + '>$0</' + t + '>';
          out.push(item(monaco, t, K.Property, range, { insert: snip, snippet: true, detail: 'HTML' }));
        });
        return { suggestions: out };
      }
      return { suggestions: [] };
    }

    var file = hooks.fileOf(model);

    // {/  - the tags still open, innermost first.
    if ((m = head.match(/^~?\/~?([\w:]*)$/))) {
      var full = model.getValueInRange({ startLineNumber: 1, startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column });
      var openTags = openTagsBefore(full.slice(0, full.length - m[0].length - 1), base);
      openTags.reverse().forEach(function (t, i) {
        out.push(item(monaco, t, K.Snippet, closeRange, { insert: t + '}', detail: 'close {' + t + '}', sort: ('000' + i).slice(-3) }));
      });
      return { suggestions: out };
    }

    // {prefix:  - the names of that prefix.
    if ((m = head.match(/^~?\/?~?([A-Za-z_]\w*):(\w*)$/))) {
      prefixNames(monaco, hooks, model, base, m[1], range).forEach(function (s) { out.push(s); });
      return { suggestions: out };
    }

    // { or {name - every tag there is, and the pair snippets.
    if ((m = head.match(/^~?(\w*)$/))) {
      base.pad.tags.forEach(function (t) {
        var d = docOf(base, 'tag', t);
        out.push(item(monaco, t, K.Keyword, range, { detail: 'PAD tag', doc: d && d.text, sort: '1' + t }));
        if (PAIR_SNIPPETS[t])
          out.push(item(monaco, t + ' … {/' + t + '}', K.Snippet, closeRange, { insert: PAIR_SNIPPETS[t], snippet: true, filter: t, detail: 'pair', sort: '0' + t }));
      });
      base.pad.branch.forEach(function (t) { out.push(item(monaco, t, K.Keyword, range, { detail: 'branch', sort: '1' + t })); });
      appNames(hooks, model, 'tag').forEach(function (n) {
        out.push(item(monaco, n.name, K.Class, range, { detail: 'tag - ' + n.file, doc: n.doc, sort: '0' + n.name }));
      });
      appNames(hooks, model, 'include').forEach(function (n) {
        out.push(item(monaco, n.name, K.File, range, { detail: 'snippet - ' + n.file, sort: '2' + n.name }));
      });
      appNames(hooks, model, 'data').forEach(function (n) {
        out.push(item(monaco, n.name, K.Struct, range, { detail: 'data - ' + n.file, sort: '2' + n.name }));
      });
      base.pad.prefixes.forEach(function (p) {
        var d = docOf(base, 'prefix', p);
        out.push(item(monaco, p + ':', K.Module, range, { detail: 'type prefix', doc: d && d.text, sort: '3' + p,
                                                          command: { id: 'editor.action.triggerSuggest', title: '' } }));
      });
      base.pad.properties.forEach(function (p) {
        var d = docOf(base, 'property', p);
        out.push(item(monaco, p + '@', K.Property, range, { detail: 'property@tag', doc: d && d.text, sort: '4' + p,
                                                            command: { id: 'editor.action.triggerSuggest', title: '' } }));
      });
      fieldItems(monaco, hooks, model, range, '$', '5').forEach(function (s) { out.push(s); });
      return { suggestions: out };
    }

    // A quoted name: pages, snippets, callbacks, translation keys, collections, mails.
    if ((m = head.match(/^~?(page|redirect|include|example|trans|collection|script|mail)\b[^'"]*['"]([\w\/.-]*)$/)) ||
        (m = head.match(/(callback|template)\s*=\s*['"]([\w\/.-]*)$/))) {
      var kind = m[1];
      var qRange = { startLineNumber: position.lineNumber, endLineNumber: position.lineNumber, startColumn: position.column - m[2].length, endColumn: position.column };
      var app = hooks.appLang();
      if ((kind === 'page' || kind === 'redirect' || kind === 'example') && app)
        app.pages.forEach(function (p) { out.push(item(monaco, p, K.File, qRange, { detail: 'page' })); });
      var map = { include: 'include', trans: 'lang', collection: 'collection', script: 'script', callback: 'callback', template: 'mail', mail: 'mail' };
      if (map[kind])
        appNames(hooks, model, map[kind]).forEach(function (n) { out.push(item(monaco, n.name, K.Reference, qRange, { detail: map[kind] + ' - ' + n.file, doc: n.doc })); });
      return { suggestions: out };
    }

    // | name - pipe functions.
    if ((m = head.match(/\|\s*(\w*)$/))) {
      base.pad.functions.forEach(function (f) {
        var d = docOf(base, 'function', f);
        out.push(item(monaco, f, K.Function, range, { detail: d ? d.summary : 'pipe function', doc: d && d.text, sort: '1' + f }));
      });
      appNames(hooks, model, 'function').forEach(function (n) {
        out.push(item(monaco, n.name, K.Function, range, { detail: 'function - ' + n.file, doc: n.doc, sort: '0' + n.name }));
      });
      return { suggestions: out };
    }

    // name@  - the tags it can be about: the ones open here.
    if ((m = head.match(/(\w+)@(\w*)$/)) && base.pad.properties.indexOf(m[1]) >= 0) {
      var full2 = model.getValueInRange({ startLineNumber: 1, startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column });
      openTagsBefore(full2.slice(0, full2.length - head.length - 1), base).reverse().forEach(function (t, i) {
        out.push(item(monaco, t, K.Reference, range, { detail: 'open tag', sort: ('000' + i).slice(-3) }));
      });
      return { suggestions: out };
    }

    // $name, %name, !name ... - the fields.
    if ((m = head.match(/([$%!?#&^])([\w.]*)$/))) {
      return { suggestions: fieldItems(monaco, hooks, model, range, '', '0') };
    }

    // After the tag name: an expression tag gets fields, operators and properties; any
    // other its options.
    if ((m = head.match(/^~?(?:\w+:)?(\w+)\b[\s\S]*[\s,(](\w*)$/))) {
      var tag = m[1];
      if (EXPRESSION.indexOf(tag) >= 0) {
        OPERATORS.forEach(function (o) { out.push(item(monaco, o, K.Operator, range, { detail: 'operator', sort: '1' + o })); });
        base.pad.properties.forEach(function (p) { out.push(item(monaco, p + '@', K.Property, range, { detail: 'property@tag', sort: '2' + p })); });
      }
      base.pad.options.forEach(function (o) {
        var d = docOf(base, 'option', o);
        out.push(item(monaco, o, K.EnumMember, range, { detail: 'option', doc: d && d.text, sort: (EXPRESSION.indexOf(tag) >= 0 ? '3' : '0') + o }));
      });
      appNames(hooks, model, 'option').forEach(function (n) {
        out.push(item(monaco, n.name, K.EnumMember, range, { detail: 'option - ' + n.file, doc: n.doc, sort: '0' + n.name }));
      });
      return { suggestions: out };
    }

    return { suggestions: out };
  }

  function prefixNames(monaco, hooks, model, base, prefix, range) {
    var K = monaco.languages.CompletionItemKind;
    var out = [];
    var add = function (list, kind, detail) {
      list.forEach(function (n) {
        var name = typeof n === 'string' ? n : n.name;
        out.push(item(monaco, name, kind, range, { detail: detail + (n.file ? ' - ' + n.file : ''), doc: n.doc }));
      });
    };
    switch (prefix) {
      case 'pad': add(base.pad.tags, K.Keyword, 'PAD tag'); break;
      case 'app': add(appNames(hooks, model, 'tag').filter(function (n) { return n.dir !== '_common'; }), K.Class, 'tag'); break;
      case 'common': add(appNames(hooks, model, 'tag').filter(function (n) { return n.dir === '_common'; }), K.Class, 'tag'); break;
      case 'include': add(appNames(hooks, model, 'include'), K.File, 'snippet'); break;
      case 'function': add(appNames(hooks, model, 'function'), K.Function, 'function'); add(base.pad.functions, K.Function, 'pipe function'); break;
      case 'property': add(base.pad.properties, K.Property, 'property'); break;
      case 'sequence': add(base.pad.sequences, K.Class, 'sequence type'); break;
      case 'action': add(base.pad.actions, K.Method, 'sequence action'); break;
      case 'make': case 'keep': case 'remove': case 'flag': add(base.pad.sequences, K.Class, 'sequence type'); break;
      case 'local': add(appNames(hooks, model, 'data'), K.File, '_data file'); break;
      case 'data': add(appNames(hooks, model, 'data'), K.Struct, 'data'); break;
      case 'script': add(appNames(hooks, model, 'script'), K.File, 'script'); break;
      case 'field': case 'level': case 'array': return fieldItems(monaco, hooks, model, range, '', '0');
      default: break;
    }
    return out;
  }

  function fieldItems(monaco, hooks, model, range, sigil, sort) {
    var K = monaco.languages.CompletionItemKind;
    var seen = {}, out = [];
    var add = function (name, detail, doc) {
      if (seen[name]) return;
      seen[name] = true;
      out.push(item(monaco, sigil + name, K.Variable, range, { detail: detail, doc: doc, sort: sort + name }));
    };
    (hooks.fields(model) || []).forEach(function (f) { add(f.name, f.file + ':' + f.line, '```php\n' + f.text + '\n```'); });
    var text = model.getValue(), m;
    var re = /\{(?:set\s+)?[^{}]*?[$%]([A-Za-z_]\w*)\s*=(?![=>])|\{data\s+['"](\w+)['"]/g;
    while ((m = re.exec(text)) !== null) add(m[1] || m[2], 'this template');
    ['padGo', 'padGoExt', 'padHost', 'padRoot', 'padApp', 'padPage'].forEach(function (n) { add(n, 'PAD'); });
    return out;
  }

  function htmlOpen(text) {
    var stack = [], m, re = /<(\/?)([A-Za-z][\w-]*)[^>]*?(\/?)>/g;
    var clean = text.replace(/<!--[\s\S]*?-->/g, '');
    while ((m = re.exec(clean)) !== null) {
      var t = m[2].toLowerCase();
      if (HTML_VOID.indexOf(t) >= 0 || m[3]) continue;
      if (m[1]) { var i = stack.lastIndexOf(t); if (i >= 0) stack.length = i; }
      else stack.push(t);
    }
    return stack;
  }

  // ------------------------------------------------------------------------------------
  // Hover and definition
  // ------------------------------------------------------------------------------------

  function targets(hooks, model, ctx) {
    var base = hooks.base();
    var w = ctx.word, list;
    var byKind = function (kind) { return appNames(hooks, model, kind).filter(function (n) { return n.name === w; }); };
    switch (ctx.role) {
      case 'tag':
        if (ctx.prefix === 'include') return { files: byKind('include') };
        if (ctx.prefix === 'app' || ctx.prefix === 'common' || !ctx.prefix) {
          list = byKind('tag');
          if (ctx.prefix === 'app') list = list.filter(function (n) { return n.dir !== '_common'; });
          if (ctx.prefix === 'common') list = list.filter(function (n) { return n.dir === '_common'; });
          if (list.length) return { files: list };
        }
        if (ctx.prefix === 'field' || ctx.prefix === 'level' || ctx.prefix === 'array') return { fields: fieldsOf(hooks, model, w) };
        if (ctx.prefix === 'local' || ctx.prefix === 'data') return { files: byKind('data') };
        if (ctx.prefix === 'function') return { files: byKind('function') };
        if (!ctx.prefix && base.pad.tags.indexOf(w) >= 0) return { doc: docOf(base, 'tag', w) };
        if (ctx.prefix && ctx.prefix !== 'pad') return { doc: docOf(base, 'tag', w) || docOf(base, 'prefix', ctx.prefix) };
        if (ctx.prefix === 'pad') return { doc: docOf(base, 'tag', w) };
        list = byKind('include').concat(byKind('data'));
        if (list.length) return { files: list };
        return { fields: fieldsOf(hooks, model, w) };
      case 'prefix': return { doc: docOf(base, 'prefix', w) };
      case 'function':
        list = byKind('function');
        return list.length ? { files: list } : { doc: docOf(base, 'function', w) };
      case 'property': return { doc: docOf(base, 'property', w) };
      case 'option':
        list = byKind('option');
        return list.length ? { files: list } : { doc: docOf(base, 'option', w) };
      case 'construct': return { doc: docOf(base, 'construct', w) };
      case 'callback': return { files: byKind('callback') };
      case 'include': return { files: byKind('include') };
      case 'page':
        var app = hooks.appLang();
        return { page: app && app.pages.indexOf(w) >= 0 ? w : null };
      case 'field': return { fields: fieldsOf(hooks, model, w) };
    }
    return {};
  }

  function fieldsOf(hooks, model, name) {
    var out = (hooks.fields(model) || []).filter(function (f) { return f.name === name; });
    var text = model.getValue(), re = new RegExp('\\{[^{}]*?[$%]' + name + '\\s*=(?![=>])|\\{data\\s+[\'"]' + name + '[\'"]', 'g'), m;
    while ((m = re.exec(text)) !== null) {
      var pos = model.getPositionAt(m.index);
      out.push({ name: name, file: null, line: pos.lineNumber, text: model.getLineContent(pos.lineNumber).trim() });
    }
    return out;
  }

  function hover(monaco, hooks, model, position) {
    var base = hooks.base();
    if (!base) return null;
    var text = model.getValue();
    var ctx = contextAt(text, model.getOffsetAt(position));
    if (!ctx) return null;
    var t = targets(hooks, model, ctx), value = '';
    if (t.doc) value = t.doc.text;
    else if (t.files && t.files.length) {
      var f = t.files[0];
      value = '**' + (ctx.role === 'function' ? '| ' : '{') + ctx.word + (ctx.role === 'function' ? '' : '}') + '** - `' + f.file + '`' +
              (f.dir === '_common' ? ' (_common)' : '') + (f.doc ? '\n\n' + f.doc : '');
    } else if (t.fields && t.fields.length) {
      value = t.fields.slice(0, 4).map(function (f) {
        return '`' + (f.file || 'this template') + ':' + f.line + '`\n```' + (f.file ? 'php' : '') + '\n' + f.text + '\n```';
      }).join('\n');
    } else if (t.page) value = '**page** `' + t.page + '`';
    if (!value) return null;
    var s = model.getPositionAt(ctx.start), e = model.getPositionAt(ctx.end);
    return { range: new monaco.Range(s.lineNumber, s.column, e.lineNumber, e.column), contents: [{ value: value }] };
  }

  function definition(monaco, hooks, model, position) {
    var base = hooks.base();
    if (!base) return null;
    var ctx = contextAt(model.getValue(), model.getOffsetAt(position));
    if (!ctx) return null;
    var t = targets(hooks, model, ctx);
    var file = hooks.fileOf(model);
    var go = function (app, root, path, line) {
      return hooks.modelFor(app, root, path).then(function (target) {
        if (!target) return null;
        return { uri: target.uri, range: new monaco.Range(line || 1, 1, line || 1, 1) };
      });
    };
    if (t.files && t.files.length) {
      var f = t.files[0], where = appFile(f);
      return go(where.app || file.app, 'app', where.path, 1).then(function (l) { return l ? [l] : null; });
    }
    if (t.fields && t.fields.length) {
      var hit = t.fields[0];
      if (!hit.file) return [{ uri: model.uri, range: new monaco.Range(hit.line, 1, hit.line, 1) }];
      return go(file.app, 'app', hit.file, hit.line).then(function (l) { return l ? [l] : null; });
    }
    if (t.page) {
      var tryExt = ['.pad', '.html', '.php'];
      for (var i = 0; i < tryExt.length; i++)
        if (hooks.exists('app', t.page + tryExt[i])) return go(file.app, 'app', t.page + tryExt[i], 1).then(function (l) { return l ? [l] : null; });
    }
    return null;
  }

  // ------------------------------------------------------------------------------------
  // Structure: folding, outline, linked editing, the closing tag typed for you
  // ------------------------------------------------------------------------------------

  function folding(monaco, hooks, model) {
    var base = hooks.base();
    if (!base) return [];
    var text = model.getValue(), ranges = [];
    scanPairs(text, base).pairs.forEach(function (p) {
      var a = model.getPositionAt(p.open.index).lineNumber, b = model.getPositionAt(p.close.index).lineNumber;
      if (b > a) ranges.push({ start: a, end: b - 1 >= a ? b - 1 : b });
    });
    var re = /\{--\s[\s\S]*?--\}|\{#[\s\S]*?#\}|<!--[\s\S]*?-->/g, m;
    while ((m = re.exec(text)) !== null) {
      var s = model.getPositionAt(m.index).lineNumber, e = model.getPositionAt(m.index + m[0].length).lineNumber;
      if (e > s) ranges.push({ start: s, end: e, kind: monaco.languages.FoldingRangeKind.Comment });
    }
    return ranges;
  }

  function symbols(monaco, hooks, model) {
    var base = hooks.base();
    if (!base) return [];
    var pairs = scanPairs(model.getValue(), base).pairs.slice().sort(function (a, b) { return a.open.index - b.open.index; });
    var K = monaco.languages.SymbolKind;
    var roots = [], stack = [];
    pairs.forEach(function (p) {
      var s = model.getPositionAt(p.open.index), e = model.getPositionAt(p.close.end - 1);
      var label = p.open.head.replace(/\s+/g, ' ');
      if (label.length > 60) label = label.slice(0, 57) + '…}';
      var sym = { name: label, detail: '', kind: base.pad.tags.indexOf(p.name) >= 0 ? K.Namespace : K.Struct, tags: [],
                  range: new monaco.Range(s.lineNumber, s.column, e.lineNumber, e.column),
                  selectionRange: new monaco.Range(s.lineNumber, s.column, s.lineNumber, s.column + p.name.length + 1), children: [] };
      while (stack.length && stack[stack.length - 1].end <= p.open.index) stack.pop();
      if (stack.length) stack[stack.length - 1].sym.children.push(sym); else roots.push(sym);
      stack.push({ end: p.close.end, sym: sym });
    });
    return roots;
  }

  function linked(monaco, hooks, model, position) {
    var base = hooks.base();
    if (!base) return null;
    var offset = model.getOffsetAt(position);
    var pairs = scanPairs(model.getValue(), base).pairs;
    for (var i = 0; i < pairs.length; i++) {
      var p = pairs[i], len = p.name.length;
      var inOpen = offset >= p.open.nameAt && offset <= p.open.nameAt + len;
      var inClose = offset >= p.close.nameAt && offset <= p.close.nameAt + len;
      if (inOpen || inClose) {
        var r = function (at) { var s = model.getPositionAt(at), e = model.getPositionAt(at + len); return new monaco.Range(s.lineNumber, s.column, e.lineNumber, e.column); };
        return { ranges: [r(p.open.nameAt), r(p.close.nameAt)], wordPattern: /[A-Za-z_][A-Za-z0-9_:]*/ };
      }
    }
    return null;
  }

  // When the } of a structural tag is typed - {if ...}, {data ...}, {form ...} - and the
  // rest of the template does not close one, its {/tag} is put in after the cursor.
  // The } is either typed into the text or, when the editor closed the brace already, typed
  // over: the one is a change of the text, the other only a key.
  function closeOnType(monaco, hooks, editor) {
    editor.onDidChangeModelContent(function (e) {
      var model = editor.getModel();
      if (!model || model.getLanguageId() !== 'pad' || e.isUndoing || e.isRedoing || e.changes.length !== 1) return;
      var ch = e.changes[0];
      if (ch.text !== '}') return;
      closeAt(monaco, hooks, editor, { lineNumber: ch.range.startLineNumber, column: ch.range.startColumn + 1 });
    });
    editor.onKeyUp(function (e) {
      var model = editor.getModel();
      if (!model || model.getLanguageId() !== 'pad' || e.browserEvent.key !== '}') return;
      var pos = editor.getPosition();
      if (model.getLineContent(pos.lineNumber).charAt(pos.column - 2) === '}') closeAt(monaco, hooks, editor, pos);
    });
  }

  var closing = false;

  function closeAt(monaco, hooks, editor, pos) {
    var model = editor.getModel();
    if (!hooks.settings().autoClose) return;
    var base = hooks.base();
    if (!base) return;
    var line = model.getLineContent(pos.lineNumber).slice(0, pos.column - 1);
    var m = line.match(/\{~?([A-Za-z_]\w*)\b[^{}]*\}$/);
    if (!m || STRUCTURAL.indexOf(m[1]) < 0) return;
    var rest = model.getValueInRange({ startLineNumber: pos.lineNumber, startColumn: pos.column, endLineNumber: model.getLineCount(), endColumn: model.getLineMaxColumn(model.getLineCount()) });
    var prior = model.getValueInRange({ startLineNumber: 1, startColumn: 1, endLineNumber: pos.lineNumber, endColumn: pos.column });
    var opens = (prior.match(new RegExp('\\{~?' + m[1] + '\\b', 'g')) || []).length;
    var closes = (prior + rest).match(new RegExp('\\{~?/~?' + m[1] + '~?\\}', 'g')) || [];
    if (closes.length >= opens || closing) return;
    closing = true;
    setTimeout(function () {
      closing = false;
      var at = editor.getPosition();
      var here = new monaco.Selection(at.lineNumber, at.column, at.lineNumber, at.column);
      editor.executeEdits('pad-close', [{ range: new monaco.Range(at.lineNumber, at.column, at.lineNumber, at.column), text: '{/' + m[1] + '}' }], [here]);
      editor.setSelection(here);
    }, 0);
  }

  // ------------------------------------------------------------------------------------

  function register(monaco, hooks) {
    var base = hooks.base();
    monaco.languages.register({ id: 'pad', extensions: ['.pad'], aliases: ['PAD', 'pad'] });
    monaco.languages.setMonarchTokensProvider('pad', monarch(base));
    monaco.languages.setLanguageConfiguration('pad', {
      comments: { blockComment: ['{--', '--}'] },
      brackets: [['{', '}'], ['(', ')'], ['[', ']']],
      autoClosingPairs: [
        { open: '{', close: '}' }, { open: '(', close: ')' }, { open: '[', close: ']' },
        { open: "'", close: "'", notIn: ['string'] }, { open: '"', close: '"', notIn: ['string'] },
        { open: '<!--', close: '-->', notIn: ['comment', 'string'] }
      ],
      surroundingPairs: [{ open: '{', close: '}' }, { open: "'", close: "'" }, { open: '"', close: '"' }, { open: '<', close: '>' }],
      wordPattern: /(-?\d*\.\d\w*)|([^\`\~\!\@\#\$\%\^\&\*\(\)\=\+\[\{\]\}\\\|\;\:\'\"\,\.\<\>\/\?\s]+)/g,
      indentationRules: {
        increaseIndentPattern: /\{~?(?:if|while|until|case|data|content|ignore|trace|tidy|record|array|check|sequence|code|form|cache|block|slot|push|fragment|live|markdown|tree|branch)\b[^{}]*\}[^{}]*$|<(?!(?:area|base|br|col|embed|hr|img|input|link|meta|source|track|wbr)\b)(\w[\w-]*)(?:[^>"']|"[^"]*"|'[^']*')*?(?!\/)>[^<]*$/,
        decreaseIndentPattern: /^\s*(?:\{~?\/[A-Za-z_][A-Za-z0-9_:]*~?\}|\{~?(?:else|elseif|when)\b[^{}]*\}|<\/[\w-]+>)/
      }
    });
    themes(monaco);

    monaco.languages.registerCompletionItemProvider('pad', {
      triggerCharacters: ['{', '/', '|', '$', ':', '@', '<', ' ', "'", '"', '%', ','],
      provideCompletionItems: function (model, position) { return completions(monaco, hooks, model, position); }
    });
    monaco.languages.registerHoverProvider('pad', { provideHover: function (model, position) { return hover(monaco, hooks, model, position); } });
    monaco.languages.registerDefinitionProvider('pad', { provideDefinition: function (model, position) { return definition(monaco, hooks, model, position); } });
    monaco.languages.registerFoldingRangeProvider('pad', { provideFoldingRanges: function (model) { return folding(monaco, hooks, model); } });
    monaco.languages.registerDocumentSymbolProvider('pad', { displayName: 'PAD tags', provideDocumentSymbols: function (model) { return symbols(monaco, hooks, model); } });
    monaco.languages.registerLinkedEditingRangeProvider('pad', { provideLinkedEditingRanges: function (model, position) { return linked(monaco, hooks, model, position); } });
  }

  window.PadMode = {
    register: register,
    closeOnType: closeOnType,
    scanPairs: scanPairs,
    contextAt: contextAt,
    chainOf: chainOf
  };

})();
