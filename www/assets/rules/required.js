// required - a text that is not empty once trimmed, a list with an item filled in.
addRule('required', {
  check: function (value) { return value !== ''; },
  list: function (list) { return counted(list) > 0; }
});
