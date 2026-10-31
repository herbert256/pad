// required - a text that is not empty once trimmed, a list with an item.
addRule('required', {
  check: function (value) { return value !== ''; },
  list: function (list) { return list.length > 0; }
});
