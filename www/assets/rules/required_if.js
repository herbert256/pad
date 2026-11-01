// required_if:field,value,... - required when the other field holds one of the values.
addRule('required_if', {
  check: function (value, arg, values) { return !siblingIs(values, arg) || filled(value); },
  list: function (list, arg, values) { return !siblingIs(values, arg) || filled(list); }
});
