// same:field - the same text as the other field of the post.
addRule('same', {
  check: function (value, arg, values) {
    var other = values[arg];
    return typeof other !== 'object' && value === trim(String(other === undefined ? '' : other));
  }
});
