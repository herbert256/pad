// ends_with:a,b - ends with one of the texts of the list.
addRule('ends_with', {
  check: function (value, arg) {
    return items(arg).some(function (one) { return one !== '' && value.slice(-one.length) === one; });
  }
});
