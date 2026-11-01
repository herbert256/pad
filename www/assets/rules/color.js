// color - #rgb or #rrggbb.
addRule('color', {
  check: function (value) { return /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(value); }
});
