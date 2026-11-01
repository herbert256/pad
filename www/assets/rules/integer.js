// integer - FILTER_VALIDATE_INT: a whole number PHP's integers hold - past them it is no
// number to the server, and integer|max:10 said "at most 10" here, "a whole number" there.
addRule('integer', {
  check: function (value) {
    var found = value.match(/^([+-]?)(0|[1-9]\d*)$/);
    if (!found) return false;
    var limit = found[1] === '-' ? '9223372036854775808' : '9223372036854775807';
    return found[2].length < limit.length || (found[2].length === limit.length && found[2] <= limit);
  }
});
