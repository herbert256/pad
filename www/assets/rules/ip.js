// ip, ip:v4, ip:v6 - _php_filter_validate_ipv4 and _ipv6 of FILTER_VALIDATE_IP, the ones
// the url rule reads a host with.
addRule('ip', {
  check: function (value, arg) {
    var only = trim(arg).toLowerCase();
    return (only !== 'v6' && ipv4(value)) || (only !== 'v4' && ipv6(value));
  }
});
