// uppercase - the value is what raising its case makes of it, as mb_strtoupper.
addRule('uppercase', {
  check: function (value) { return value === value.toUpperCase(); }
});
