// regex:/pattern/flags - a PCRE pattern as JavaScript knows it: its delimiters taken off,
// the flags JavaScript has kept. null when it cannot be read, or JavaScript would read it
// another way - the server judges that one alone: an escape of PCRE's own (\A, \z, \R,
// \x{41} ...) is a plain letter here, [[:alnum:]] a set of its characters, \p a p without
// u, and under u PHP takes \w, \d and \b of every script, JavaScript of ASCII alone - José
// was refused. Without u PCRE reads the bytes of a value: one beyond ASCII is the server's
// too.
function pattern(text) {
  var open = text.charAt(0);
  var close = { '(': ')', '{': '}', '[': ']', '<': '>' }[open] || open;
  var end = text.lastIndexOf(close);
  if (end <= 0 || /[A-Za-z0-9\\\s]/.test(open)) return null;
  var flags = text.slice(end + 1);
  if (/[^imsuxXUAD]/.test(flags) || /[xXUAD]/.test(flags)) return null;
  var body = text.slice(1, end);
  var bare = body.replace(/\\\\/g, '');
  if (/\\([AzZQERhHKGeNXCagoLlUuvV]|x\{)/.test(bare) || /\[:\^?[a-z]+:\]/.test(bare)) return null;
  // A ] right after [ or [^ is a literal in PCRE, where JavaScript reads [] as the empty
  // class and [^] as any character - /^[^]]+$/ could never match and blocked the form.
  if (/(^|[^\\])\[\^?\]/.test(bare)) return null;
  if (flags.indexOf('u') < 0 ? /\\[pP]/.test(bare) : /\\[wWdDbB]/.test(bare)) return null;
  if (braces(body)) return null;
  try { return new RegExp(body, flags.replace(/[^imsu]/g, '')); } catch (e) { return null; }
}

// Whether the pattern has a brace JavaScript does not read as a quantifier: PCRE2 10.43
// and later read {,4} as {0,4} and allow blanks inside the braces - {2, 3}, { 4 } - where
// JavaScript without u reads them as text, so regex:/^[0-9]{,4}$/ refused 12 the server
// takes and the form could not be sent. A brace in a class or escaped, and one that is
// text in both - {x}, the {L} of \p{L} - is no quantifier in either.
function braces(body) {
  var inClass = false;
  for (var i = 0; i < body.length; i++) {
    var c = body.charAt(i);
    if (c === '\\') {
      i++;
    } else if (inClass) {
      if (c === ']') inClass = false;
    } else if (c === '[') {
      inClass = true;
      if (body.charAt(i + 1) === '^') i++;
      if (body.charAt(i + 1) === ']') i++;
    } else if (c === '{' && /^\{[ \t]*(\d+[ \t]*(,[ \t]*\d*)?|,[ \t]*\d+)[ \t]*\}/.test(body.slice(i)) && !/^\{\d+(,\d*)?\}/.test(body.slice(i))) return true;
  }
  return false;
}

addRule('regex', {
  check: function (value, arg) {
    var read = pattern(arg);
    return read && (read.unicode || !/[^\x00-\x7f]/.test(value)) ? read.test(value) : true;
  }
});
