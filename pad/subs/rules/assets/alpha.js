// alpha - letters of any script and their marks, as \pL and \pM of PCRE read them.
addRule('alpha', {
  check: function (value) { return /^[\p{L}\p{M}]+$/u.test(value); }
});
