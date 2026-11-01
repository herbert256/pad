// after_or_equal:other - an ISO day or day with a time after the other's, or the same moment.
addRule('after_or_equal', {
  check: function (value, arg, values) { return when(value, arg, values, 1) || whenSame(value, arg, values); }
});
