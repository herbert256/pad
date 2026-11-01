// slug - lower case letters and digits, joined by single dashes.
addRule('slug', {
  check: function (value) { return /^[a-z0-9]+(-[a-z0-9]+)*$/.test(value); }
});
