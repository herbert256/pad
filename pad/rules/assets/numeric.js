// numeric - is_numeric: a sign, digits with a point, an exponent; no hex, no blanks inside.
// Whitespace before and after is allowed, as is_numeric allows it: PHP's trim leaves a form
// feed, and 1 followed by one passed the server and was refused here.
addRule('numeric', {
  check: function (value) { return /^[ \t\n\r\v\f]*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?[ \t\n\r\v\f]*$/.test(value); }
});
