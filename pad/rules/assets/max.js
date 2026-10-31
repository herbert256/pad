// max:n - at most n characters, or on a number field a value of at most n.
addRule('max', {
  check: function (value, arg, values, number) { return size(value, number) <= toNumber(arg); }
});
