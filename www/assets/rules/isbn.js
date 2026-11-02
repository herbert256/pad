addRule('isbn', { check: function(value) {
  value = value.replace(/[ -]/g, '');
  var sum = 0, i;
  if (/^[0-9]{9}[0-9X]$(?![\s\S])/.test(value)) {
    for (i = 0; i < 10; i++) sum += (10 - i) * (value[i] === 'X' ? 10 : +value[i]);
    return sum % 11 === 0;
  }
  if (!/^97[89][0-9]{10}$(?![\s\S])/.test(value)) return false;
  for (i = 0; i < 13; i++) sum += +value[i] * (i % 2 ? 3 : 1);
  return sum % 10 === 0;
}});
