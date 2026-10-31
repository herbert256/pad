/* newpassword - the button that shows the password, and the meter of its strength. The score
   is 0 to 4: a point each for 8, 12 and 16 characters, a point for each kind of character past
   the first - lower case, upper case, digits, the rest - two at most, and points off for what
   makes a password easy to guess: one of the passwords everyone tries, a run of three or more
   (abc, 321), a character three times in a row, the field's own name in it; a password of
   fewer than 8 characters, or of fewer than 4 different ones, is weak at best. The word of the
   score is said a moment after the typing stops, not at every key. */
PadInputs.register('newpassword', function (frame, cfg, kit) {
  var field = frame.querySelector('input[type=password]');
  var reveal = frame.querySelector('.pi-reveal');
  var box = frame.querySelector('.pi-meter');
  var meter = frame.querySelector('meter');
  var word = frame.querySelector('.pi-strength');
  if (!field) return;

  // The button says what it does - Show, Hide - and its name, Show password, begins with the
  // word it shows, as the name of a control should.
  function state(shown) {
    field.type = shown ? 'text' : 'password';
    reveal.textContent = shown ? 'Hide' : 'Show';
    reveal.setAttribute('aria-label', (shown ? 'Hide' : 'Show') + ' password');
  }

  if (reveal) {
    reveal.hidden = false;
    state(false);
    reveal.addEventListener('click', function () {
      state(field.type === 'password');
      field.focus();
    });
    // A password is not left visible behind a send: the form leaves with the field hidden.
    if (field.form) field.form.addEventListener('submit', function () { state(false); });
  }

  if (!cfg.meter || !box || !meter) return;

  var common = ['password', 'passw0rd', 'qwerty', 'azerty', 'letmein', 'welcome', 'admin', 'iloveyou', 'monkey', 'dragon',
                'football', 'baseball', 'sunshine', 'princess', 'master', 'shadow', 'secret', 'trustno1', 'abc123', '123456',
                '1234567', '12345678', '123456789', '1234567890', '111111', '000000', 'qwertyuiop', 'login', 'changeme'];

  function runs(text) {
    var count = 0;
    for (var i = 2; i < text.length; i++) {
      var a = text.charCodeAt(i - 2), b = text.charCodeAt(i - 1), c = text.charCodeAt(i);
      if ((b - a === 1 && c - b === 1) || (a - b === 1 && b - c === 1)) count++;
    }
    return count;
  }

  function score(text) {
    if (!text) return 0;
    var lower = text.toLowerCase();
    if (common.indexOf(lower.replace(/[^a-z0-9]/g, '')) >= 0) return 0;
    var points = 0;
    var length = Array.from(text).length;
    if (length >= 8) points++;
    if (length >= 12) points++;
    if (length >= 16) points++;
    var kinds = [/[a-z]/, /[A-Z]/, /\d/, /[^A-Za-z0-9]/].filter(function (one) { return one.test(text); }).length;
    points += Math.min(2, kinds - 1);
    if (runs(lower) >= 2) points--;
    if (/(.)\1\1/.test(text)) points--;
    var own = (field.name || '').toLowerCase().replace(/[^a-z]/g, '');
    if (own.length > 3 && lower.indexOf(own) >= 0) points--;
    if (length < 8 || new Set(Array.from(lower)).size < 4) points = Math.min(points, 1);
    return Math.max(0, Math.min(4, points));
  }

  var words = ['Too easy to guess', 'Weak', 'Fair', 'Good', 'Strong'];
  var timer = null;

  box.hidden = false;

  field.addEventListener('input', function () {
    var value = score(field.value);
    meter.value = value;
    meter.setAttribute('aria-valuetext', field.value ? words[value] : 'empty');
    box.setAttribute('data-score', String(value));
    clearTimeout(timer);
    timer = setTimeout(function () { word.textContent = field.value ? words[value] : ''; }, 500);
  });
});
