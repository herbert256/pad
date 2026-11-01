// gt:other - more than a number of the rule or the other field - its value or length.
addRule('gt', {
  check: function (value, arg, values, number) { var two = compare(value, arg, values, number); return !two || two[0] > two[1]; }
});
