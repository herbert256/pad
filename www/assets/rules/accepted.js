// accepted - 1, on, yes or true, in any case.
addRule('accepted', {
  check: function (value) { return ['1', 'on', 'yes', 'true'].indexOf(value.toLowerCase()) >= 0; }
});
