// alphanum - letters, marks and digits of any script, as \pL, \pM and \pN read them.
addRule('alphanum', {
  check: function (value) { return /^[\p{L}\p{M}\p{N}]+$/u.test(value); }
});
