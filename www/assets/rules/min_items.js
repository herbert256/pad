// min_items:n - at least n values filled in, as padRuleItems counts them; one value is one,
// a list with none filled in is empty and judged by required alone.
addRule('min_items', {
  check: function (value, arg) { return 1 >= parseInt(trim(String(arg)), 10); },
  list: function (list, arg) { return counted(list) >= parseInt(trim(String(arg)), 10); }
});
