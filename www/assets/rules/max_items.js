// max_items:n - at most n values filled in, as padRuleItems counts them; one value is one.
addRule('max_items', {
  check: function (value, arg) { return 1 <= parseInt(trim(String(arg)), 10); },
  list: function (list, arg) { return counted(list) <= parseInt(trim(String(arg)), 10); }
});
