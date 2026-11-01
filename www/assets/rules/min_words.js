// min_words:n - at least n runs of characters between white space, as padRuleWords counts.
addRule('min_words', {
  check: function (value, arg) { return words(value) >= parseInt(trim(String(arg)), 10); }
});
