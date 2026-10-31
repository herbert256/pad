// time - HH:MM or HH:MM:SS on the 24-hour clock.
addRule('time', {
  check: function (value) { return /^([01]?\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/.test(value); }
});
