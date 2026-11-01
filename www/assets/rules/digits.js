// digits, digits:n - the digits 0 to 9 only, and with n exactly n of them.
addRule('digits', {
  check: function (value, arg) {
    return /^[0-9]+$/.test(value) && (trim(arg) === '' || value.length === parseInt(trim(arg), 10));
  }
});
