// multiple_of:n - a whole multiple of n, reckoned in decimal digits as the server reckons:
// value and n parted into their digits and a scale, made whole numbers of one scale, the
// remainder taken digit by digit - never in floating point, where 0.3 % 0.1 is no 0.
function decimal(text) {
  var at = /^[ \t\n\r\v\f]*[+-]?(?:([0-9]+)(?:\.([0-9]*))?|\.([0-9]+))(?:[eE]([+-]?)([0-9]+))?[ \t\n\r\v\f]*$/.exec(String(text));
  if (!at) return null;
  var fraction = (at[2] || '') + (at[3] || '');
  var power = (at[5] || '').replace(/^0+/, '');
  power = power.length > 9 ? 1000000000 : +power;
  var digits = ((at[1] || '') + fraction).replace(/^0+/, '');
  var whole = digits.replace(/0+$/, '');
  return [whole, fraction.length - (at[4] === '-' ? -power : power) - (digits.length - whole.length)];
}

addRule('multiple_of', {
  check: function (value, arg) {
    var number = decimal(value), by = decimal(trim(String(arg)));
    if (!number || !by || by[0] === '' || by[0].length > 14) return false;
    if (number[0] === '') return true;
    if (number[1] > by[1]) return false;
    var divisor = parseInt(by[0], 10), rest = 0;
    for (var i = 0; i < number[0].length; i++) rest = (rest * 10 + +number[0].charAt(i)) % divisor;
    for (var zeros = Math.min(by[1] - number[1], 50); zeros > 0 && rest; zeros--) rest = rest * 10 % divisor;
    return rest === 0;
  }
});
