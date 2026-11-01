// after:other - an ISO day or day with a time after the other field's, or a date of the rule.
addRule('after', {
  check: function (value, arg, values) { return when(value, arg, values, 1); }
});
