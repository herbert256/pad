// in:a,b,c - one of the values as written; a list when every item is one of them.
addRule('in', {
  check: function (value, arg) { return items(arg).indexOf(value) >= 0; },
  list: function (list, arg) {
    return list.every(function (one) { return typeof one === 'string' && items(arg).indexOf(trim(one)) >= 0; });
  }
});
