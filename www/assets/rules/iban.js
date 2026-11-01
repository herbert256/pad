// iban - the ISO 13616 checksum: the first four moved to the end, letters read as 10 to 35,
// the remainder of 97 is 1.
addRule('iban', {
  check: function (value) {
    var iban = value.replace(/ /g, '').toUpperCase();
    if (!/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/.test(iban)) return false;
    var digits = (iban.slice(4) + iban.slice(0, 4)).replace(/[A-Z]/g, function (c) { return String(c.charCodeAt(0) - 55); });
    var rest = 0;
    for (var i = 0; i < digits.length; i++) rest = (rest * 10 + +digits.charAt(i)) % 97;
    return rest === 1;
  }
});
