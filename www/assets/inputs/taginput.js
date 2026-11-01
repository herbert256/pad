/* taginput - chips over the comma list. The text field the server wrote gives its name to a
   hidden field, which holds the words joined with ', ' - what the form posts, the same text
   the field posted without the script - and becomes the field a word is typed in. Enter or a
   comma adds the word, a pasted list adds each of its words, leaving the field adds what was
   typed; Backspace in the empty field takes the last word back, and every chip has a button
   that takes it out. A word is in the list once, in the case it was first written in. What
   happens is said to a screen reader. A required list stays one: the text field the words
   leave holds the browser's required check while there is no word, and lets it go once a
   chip holds one - kept on the emptied field, it refused every list. */
PadInputs.register('taginput', function (frame, cfg, kit) {
  var box = frame.querySelector('.pi-tags input[type=text]');
  var wrap = frame.querySelector('.pi-tags');
  var said = frame.querySelector('.pi-tags-said');
  if (!box || !wrap) return;

  var needed = box.required;
  if (needed) box.setAttribute('aria-required', 'true');

  var words = [];
  var hidden = document.createElement('input');
  hidden.type = 'hidden';
  hidden.name = box.name;
  if (box.id) hidden.setAttribute('data-pad-shows', box.id);
  box.removeAttribute('name');
  box.value.split(',').forEach(function (one) { add(one, true); });
  box.value = '';

  var chips = kit.el('ul', 'pi-chips');
  chips.setAttribute('aria-label', kit.label(frame) || 'Words');
  wrap.insertBefore(chips, box);
  wrap.appendChild(hidden);

  function say(text) { if (said) said.textContent = text; }

  function sync(quiet) {
    hidden.value = words.join(', ');
    if (needed) box.required = !words.length;
    chips.textContent = '';
    words.forEach(function (word, i) {
      var li = kit.el('li', 'pi-chip');
      li.appendChild(kit.el('span', 'pi-chip-text', word));
      var x = kit.el('button', 'pi-chip-x', '×');
      x.type = 'button';
      x.setAttribute('aria-label', 'Remove ' + word);
      x.addEventListener('click', function () {
        words.splice(i, 1);
        sync();
        say(word + ' removed');
        box.focus();
      });
      li.appendChild(x);
      chips.appendChild(li);
    });
    chips.hidden = !words.length;
    if (!quiet) kit.changed(hidden);
  }

  function add(text, quiet) {
    var word = String(text).replace(/\s+/g, ' ').trim();
    if (!word) return false;
    var lower = word.toLowerCase();
    if (words.some(function (one) { return one.toLowerCase() === lower; })) { if (!quiet) say(word + ' is there already'); return false; }
    if (cfg.most && words.length >= cfg.most) { if (!quiet) say('No more than ' + cfg.most); return false; }
    words.push(word);
    if (!quiet) say(word + ' added');
    return true;
  }

  function take() {
    var parts = box.value.split(',');
    var added = false;
    parts.forEach(function (one) { if (add(one)) added = true; });
    box.value = '';
    if (added) sync();
  }

  box.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' || event.key === ',') {
      if (box.value.trim() !== '' || event.key === ',') event.preventDefault();
      take();
    } else if (event.key === 'Backspace' && box.value === '' && words.length) {
      var word = words.pop();
      sync();
      say(word + ' removed');
    }
  });

  box.addEventListener('input', function () {
    if (box.value.indexOf(',') >= 0) take();
  });

  box.addEventListener('change', function () {
    if (box.value.trim() !== '') take();
  });

  box.addEventListener('blur', function () { if (box.value.trim() !== '') take(); });

  // A click in the frame of chips puts the focus in the field, as in a text field.
  wrap.addEventListener('click', function (event) { if (event.target === wrap) box.focus(); });

  sync(true);
});
