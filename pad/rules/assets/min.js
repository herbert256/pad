// min:n - at least n characters, or on a number field a value of at least n.
addRule('min', {
  check: function (value, arg, values, number) { return size(value, number) >= toNumber(arg); }
});
