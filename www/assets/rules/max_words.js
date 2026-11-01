// max_words:n - at most n runs of characters between white space, as padRuleWords counts.
addRule('max_words', {
  check: function (value, arg) { return words(value) <= parseInt(trim(String(arg)), 10); }
});
