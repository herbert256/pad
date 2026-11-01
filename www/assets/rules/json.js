// json - what JSON.parse reads, held to what PHP takes besides: PHP refuses a structure
// nested 512 deep or more, and a \u escape of half a surrogate pair - in a value or a key -
// both of which JavaScript reads. A text of the browser holds no lone half itself, so one in
// what was read came from an escape.
function whole(text) {
  return !/[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(^|[^\uD800-\uDBFF])[\uDC00-\uDFFF]/.test(text);
}

// Whether a value read holds, nested no deeper than PHP reads: the walk stops at the depth
// PHP refuses, before a structure deep enough to end the browser's stack.
function holds(value, level) {
  if (typeof value === 'string') return whole(value);
  if (value === null || typeof value !== 'object') return true;
  if (level >= 511) return false;
  return Object.keys(value).every(function (key) { return whole(key) && holds(value[key], level + 1); });
}

addRule('json', {
  check: function (value) {
    var read;
    try { read = JSON.parse(value); } catch (e) { return false; }
    return holds(read, 0);
  }
});
