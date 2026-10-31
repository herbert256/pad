// numeric - is_numeric: a sign, digits with a point, an exponent; no hex, no blanks inside.
addRule('numeric', {
  check: function (value) { return /^[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?$/.test(value); }
});
