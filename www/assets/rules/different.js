// different:field - not the same text as the other field of the post, found where PHP files
// it - the mirror of same:.
addRule('different', {
  check: function (value, arg, values) {
    var other = find(values, arg)[1];
    return !(typeof other !== 'object' && value === trim(String(other === undefined ? '' : other)));
  }
});
