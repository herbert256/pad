// different:field - not the same text as the other field of the post, the mirror of same:.
addRule('different', {
  check: function (value, arg, values) {
    var other = values[arg];
    return !(typeof other !== 'object' && value === trim(String(other === undefined ? '' : other)));
  }
});
