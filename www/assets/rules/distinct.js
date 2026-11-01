// distinct - no value of the list twice, each trimmed, the empty ones left out; one value holds.
addRule('distinct', {
  check: function () { return true; },
  list: function (list) {
    var seen = [];
    return list.every(function (one) {
      if (!text(one)) return false;
      one = trim(String(one));
      if (one === '') return true;
      if (seen.indexOf(one) >= 0) return false;
      seen.push(one);
      return true;
    });
  }
});
