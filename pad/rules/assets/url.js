// url - FILTER_VALIDATE_URL of an http or https address, step by step as PHP takes it: every
// character one a URL may hold (php_filter_url drops the others, and a dropped one fails
// it); the authority parted as parse_url parts it - the user info up to its last @, a port
// of up to five characters after the last :, read as strtol reads it; the host a name of
// letters, digits and hyphens, a dot only between two of them, or an IPv6 address in
// brackets; the user info of the characters RFC 3986 allows. The URL parser of the
// browser converted what PHP refuses - http://exämple.com, ex%41mple.com - and refused
// what it takes: https://999.2.3.4.
function url(value) {
  if (!/^https?:\/\//i.test(value) || /[^A-Za-z0-9$\-_.+!*'(),{}|\\^~\[\]`<>#%";\/?:@&=]/.test(value)) return false;
  var rest = value.replace(/^https?:\/\//i, '');
  var authority = rest.slice(0, rest.search(/[\/?#]|$/));
  var at = authority.lastIndexOf('@');
  var host = authority.slice(at + 1);
  if (!(host.charAt(0) === '[' && host.slice(-1) === ']')) {
    var colon = host.lastIndexOf(':');
    if (colon >= 0) {
      var port = host.slice(colon + 1);
      if (port.length > 5) return false;
      if (port !== '') {
        var number = port.match(/^[+-]?\d+/);
        if (!number || +number[0] < 0 || +number[0] > 65535) return false;
      }
      host = host.slice(0, colon);
    }
  }
  if (host === '') return false;
  if (!(host.charAt(0) === '[' && host.slice(-1) === ']' && ipv6(host.slice(1, -1))) && !hostname(host)) return false;
  if (at < 0) return true;
  var info = authority.slice(0, at), split = info.indexOf(':');
  return userinfo(split < 0 ? info : info.slice(0, split)) && (split < 0 || userinfo(info.slice(split + 1)));
}

// _php_filter_validate_domain for a host name: a trailing dot left out, at most 253
// characters, a letter or digit first, no hyphen last, a label at most 63 long.
function hostname(host) {
  var end = host.length;
  if (end > 0 && host.charAt(end - 1) === '.') end--;
  if (end > 253 || !alnum(host.charAt(0)) || host.slice(-1) === '-') return false;
  for (var i = 0, size = 1; i < end; i++) {
    var c = host.charAt(i);
    if (c === '.') {
      if (host.charAt(i + 1) === '.' || !alnum(host.charAt(i - 1)) || !alnum(host.charAt(i + 1))) return false;
      size = 1;
    } else {
      if (size > 63 || (c !== '-' && !alnum(c))) return false;
      size++;
    }
  }
  return true;
}

// is_userinfo_valid: a %-escape there starts with a digit.
function userinfo(text) {
  return /^(?:[A-Za-z0-9\-._~!$&'()*+,;=:]|%[0-9][0-9A-Fa-f])*$/.test(text);
}

addRule('url', { check: url });
