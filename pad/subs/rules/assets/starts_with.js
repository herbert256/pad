// starts_with:a,b - begins with one of the texts of the list.
addRule('starts_with', {
  check: function (value, arg) {
    return items(arg).some(function (one) { return one !== '' && value.indexOf(one) === 0; });
  }
});
