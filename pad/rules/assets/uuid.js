// uuid - 8-4-4-4-12 hexadecimal digits.
addRule('uuid', {
  check: function (value) { return /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(value); }
});
