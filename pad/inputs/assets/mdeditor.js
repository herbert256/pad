/* mdeditor - the bar of buttons over the textarea and the preview beside it. A button wraps
   the selection - or the word the caret is in - in the Markdown of bold, italic, code or a
   link, or puts a heading, list or quote mark before each line it covers; the change is typed
   into the textarea, so Undo takes it back. The bar is one tab stop, the arrow keys move along
   it. Ctrl or Cmd with B, I and K do the same in the textarea.

   The preview is a reading of the common Markdown of the text, made again a moment after
   every change: every character of the text is escaped before any of it is made markup, so
   the text can hold no tag of its own, and a link goes only to http, https, mailto or an
   address of the site - javascript: and data: links are written as their text. */
PadInputs.register('mdeditor', function (frame, cfg, kit) {
  var area = frame.querySelector('textarea');
  var bar = frame.querySelector('.pi-md-bar');
  var preview = frame.querySelector('.pi-md-preview');
  if (!area || !bar || !preview) return;

  function esc(text) {
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function plain(text) {
    return String(text).replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&');
  }

  function safeUrl(url) {
    url = url.trim();
    if (/^(https?:|mailto:)/i.test(url)) return url;
    if (/^[a-z][a-z0-9+.-]*:/i.test(url.replace(/[\u0000- ]/g, ''))) return null;
    return url;
  }

  function inline(text) {
    var codes = [];
    text = text.replace(/`([^`\n]+)`/g, function (all, code) {
      codes.push('<code>' + esc(code) + '</code>');
      return '\u0000' + (codes.length - 1) + '\u0000';
    });
    text = esc(text);
    text = text.replace(/!\[([^\]]*)\]\(([^)\s]+)\)/g, '$1');
    text = text.replace(/\[([^\]]+)\]\(([^)\s]+)(?:\s+&quot;[^&]*&quot;)?\)/g, function (all, label, url) {
      var safe = safeUrl(plain(url));
      return safe === null ? label : '<a href="' + esc(safe) + '" rel="nofollow noopener">' + label + '</a>';
    });
    text = text.replace(/&lt;((?:https?:\/\/|mailto:)[^\s&]+)&gt;/g, function (all, url) {
      return '<a href="' + esc(plain(url)) + '" rel="nofollow noopener">' + url + '</a>';
    });
    text = text.replace(/\*\*(?=\S)([\s\S]*?\S)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/(^|[^\w])__(?=\S)([\s\S]*?\S)__(?!\w)/g, '$1<strong>$2</strong>');
    text = text.replace(/\*(?=[^\s*])([^*]*?[^\s*])\*/g, '<em>$1</em>');
    text = text.replace(/(^|[^\w])_(?=[^\s_])([^_]*?[^\s_])_(?!\w)/g, '$1<em>$2</em>');
    text = text.replace(/~~(?=\S)([\s\S]*?\S)~~/g, '<del>$1</del>');
    text = text.replace(/ {2,}\n/g, '<br>\n');
    return text.replace(/\u0000(\d+)\u0000/g, function (all, n) { return codes[+n]; });
  }

  var listMark = /^\s*([-*+]|\d+[.)])\s+/;
  var blockStart = /^(#{1,6}\s|```|~~~|\s*>|\s*([-*+]|\d+[.)])\s+)/;

  function render(source) {
    var lines = source.replace(/\r\n?/g, '\n').split('\n');
    var html = '', i = 0;
    while (i < lines.length) {
      var line = lines[i];
      if (/^\s*$/.test(line)) { i++; continue; }
      var fence = /^(```|~~~)/.exec(line);
      if (fence) {
        var code = [];
        for (i++; i < lines.length && lines[i].indexOf(fence[1]) !== 0; i++) code.push(lines[i]);
        i++;
        html += '<pre><code>' + esc(code.join('\n')) + '</code></pre>';
        continue;
      }
      var heading = /^(#{1,6})\s+(.*?)\s*#*\s*$/.exec(line);
      if (heading) {
        html += '<h' + heading[1].length + '>' + inline(heading[2]) + '</h' + heading[1].length + '>';
        i++;
        continue;
      }
      if (/^\s*([-*_])(\s*\1){2,}\s*$/.test(line)) { html += '<hr>'; i++; continue; }
      if (/^\s*>/.test(line)) {
        var quoted = [];
        while (i < lines.length && /^\s*>/.test(lines[i])) quoted.push(lines[i++].replace(/^\s*>\s?/, ''));
        html += '<blockquote>' + render(quoted.join('\n')) + '</blockquote>';
        continue;
      }
      var mark = listMark.exec(line);
      if (mark) {
        var ordered = /\d/.test(mark[1]), items = [];
        while (i < lines.length && listMark.test(lines[i])) {
          items.push(lines[i++].replace(listMark, ''));
          while (i < lines.length && /^\s{2,}\S/.test(lines[i]) && !listMark.test(lines[i])) items[items.length - 1] += '\n' + lines[i++].trim();
        }
        html += (ordered ? '<ol>' : '<ul>') + items.map(function (item) { return '<li>' + inline(item) + '</li>'; }).join('') + (ordered ? '</ol>' : '</ul>');
        continue;
      }
      var para = [line];
      for (i++; i < lines.length && !/^\s*$/.test(lines[i]) && !blockStart.test(lines[i]); i++) para.push(lines[i]);
      html += '<p>' + inline(para.join('\n')) + '</p>';
    }
    return html;
  }

  var timer = null;
  function update() {
    clearTimeout(timer);
    timer = setTimeout(function () {
      preview.innerHTML = area.value.trim() === '' ? '<p class="pi-muted">Nothing to preview yet.</p>' : render(area.value);
    }, 120);
  }

  // A change typed into the textarea, so Undo takes it back where the browser keeps it.
  function type(start, end, text, selectFrom, selectTo) {
    area.focus();
    area.setSelectionRange(start, end);
    var done = false;
    try { done = document.execCommand && document.execCommand('insertText', false, text); } catch (e) { done = false; }
    if (!done) {
      area.setRangeText(text, start, end, 'end');
      kit.changed(area);
    }
    area.setSelectionRange(start + selectFrom, start + selectTo);
  }

  // The selection, or the word the caret stands in.
  function selection() {
    var start = area.selectionStart, end = area.selectionEnd, value = area.value;
    if (start === end) {
      while (start > 0 && /\w/.test(value.charAt(start - 1))) start--;
      while (end < value.length && /\w/.test(value.charAt(end))) end++;
    }
    return { start: start, end: end, text: value.slice(start, end) };
  }

  function wrap(before, after, empty) {
    var s = selection();
    var inner = s.text || empty;
    type(s.start, s.end, before + inner + after, before.length, before.length + inner.length);
  }

  function lines(prefix) {
    var value = area.value;
    var start = value.lastIndexOf('\n', area.selectionStart - 1) + 1;
    var end = value.indexOf('\n', area.selectionEnd);
    if (end < 0) end = value.length;
    var n = 0;
    var text = value.slice(start, end).split('\n').map(function (line) { n++; return (typeof prefix === 'function' ? prefix(n) : prefix) + line; }).join('\n');
    type(start, end, text, 0, text.length);
  }

  function link() {
    var s = selection();
    var label = s.text || 'link text';
    var text = '[' + label + '](https://)';
    type(s.start, s.end, text, label.length + 3, text.length - 1);
  }

  function code() {
    var s = selection();
    if (s.text.indexOf('\n') >= 0) type(s.start, s.end, '```\n' + s.text + '\n```', 4, 4 + s.text.length);
    else wrap('`', '`', 'code');
  }

  var tools = [
    ['Bold', 'B', function () { wrap('**', '**', 'bold'); }, 'b'],
    ['Italic', 'I', function () { wrap('*', '*', 'italic'); }, 'i'],
    ['Heading', 'H', function () { lines('## '); }],
    ['Link', '\u{1F517}', link, 'k'],
    ['Bulleted list', '•', function () { lines('- '); }],
    ['Numbered list', '1.', function () { lines(function (n) { return n + '. '; }); }],
    ['Quote', '“', function () { lines('> '); }],
    ['Code', '</>', code]
  ];

  var buttons = tools.map(function (tool, i) {
    var button = kit.el('button', 'pi-button pi-md-tool', tool[1]);
    button.type = 'button';
    button.setAttribute('aria-label', tool[0]);
    button.title = tool[0] + (tool[3] ? ' (Ctrl+' + tool[3].toUpperCase() + ')' : '');
    button.tabIndex = i === 0 ? 0 : -1;
    button.addEventListener('mousedown', function (event) { event.preventDefault(); });
    button.addEventListener('click', tool[2]);
    bar.appendChild(button);
    return button;
  });

  bar.addEventListener('keydown', function (event) {
    var at = buttons.indexOf(document.activeElement);
    if (at < 0) return;
    var to = { ArrowRight: at + 1, ArrowLeft: at - 1, Home: 0, End: buttons.length - 1 }[event.key];
    if (to === undefined) return;
    event.preventDefault();
    to = (to + buttons.length) % buttons.length;
    buttons[at].tabIndex = -1;
    buttons[to].tabIndex = 0;
    buttons[to].focus();
  });

  area.addEventListener('keydown', function (event) {
    if (!(event.ctrlKey || event.metaKey) || event.altKey || event.shiftKey) return;
    var key = event.key.toLowerCase();
    for (var i = 0; i < tools.length; i++)
      if (tools[i][3] === key) { event.preventDefault(); tools[i][2](); return; }
  });

  area.addEventListener('input', update);

  bar.hidden = false;
  preview.hidden = false;
  frame.classList.add('has-preview');
  update();
});
