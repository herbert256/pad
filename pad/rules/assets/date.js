// date - padValidate takes what date_parse reads as a day of a month of a year - more forms
// than can be told alike here. The ISO day, with a time to 24:59:60, and the day written
// with / . or - are judged; a text without a digit is no date; any other passes here and is
// left to the server - Date.parse refused what date_parse takes (2026-W42, 20260105), took
// what it refuses, and reads it another way in every browser.

// checkdate: Date.UTC took a year of 0 to 99 for 1900 to 1999, and 0099-12-31 was no date.
function calendarDay(year, month, day) {
  var moment = new Date(0);
  moment.setUTCFullYear(year, month - 1, day);
  return year > 0 && moment.getUTCFullYear() === year && moment.getUTCMonth() === month - 1 && moment.getUTCDate() === day;
}

addRule('date', {
  check: function (value) {
    var iso = value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})(?:[Tt ](\d{1,2}):(\d{2})(?::(\d{2}))?)?$/);
    if (iso) {
      if (iso[4] !== undefined && (+iso[4] > 24 || +iso[5] > 59 || +(iso[6] || 0) > 60)) return false;
      return calendarDay(+iso[1], +iso[2], +iso[3]);
    }
    var dmy = value.match(/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})$/);
    if (dmy) return value.charAt(dmy[1].length) === '/' ? calendarDay(+dmy[3], +dmy[1], +dmy[2]) : calendarDay(+dmy[3], +dmy[2], +dmy[1]);
    return /\d/.test(value);
  }
});
