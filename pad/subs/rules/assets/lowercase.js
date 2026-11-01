// lowercase - the value is what lowering its case makes of it, as mb_strtolower.
addRule('lowercase', {
  check: function (value) { return value === value.toLowerCase(); }
});
