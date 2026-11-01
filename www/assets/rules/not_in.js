// not_in:a,b,c - none of the values as written; a list when no item is one of them.
addRule('not_in', {
  check: function (value, arg) { return items(arg).indexOf(value) < 0; },
  list: function (list, arg) {
    return list.every(function (one) { return typeof one === 'string' && items(arg).indexOf(trim(one)) < 0; });
  }
});
