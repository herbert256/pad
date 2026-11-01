// required_with:field,... - required when one of the other fields is filled.
addRule('required_with', {
  check: function (value, arg, values) { return filled(value) || !anyFilled(values, arg, true); },
  list: function (list, arg, values) { return filled(list) || !anyFilled(values, arg, true); }
});
