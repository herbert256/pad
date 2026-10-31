// between:a,b - from a to b characters, or on a number field a value from a to b.
addRule('between', {
  check: function (value, arg, values, number) {
    var two = String(arg).split(',');
    var at = size(value, number);
    return at >= toNumber(two[0]) && at <= toNumber(two.length > 1 ? two.slice(1).join(',') : '');
  }
});
