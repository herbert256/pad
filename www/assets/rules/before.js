// before:other - an ISO day or day with a time before the other field's, or a date of the rule.
addRule('before', {
  check: function (value, arg, values) { return when(value, arg, values, -1); }
});
