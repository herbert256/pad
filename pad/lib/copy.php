<?php

  // A text with a copy-to-clipboard button - the {copy} tag.
  //
  //   {copy 'composer require pad/pad'}
  //   {copy label='Copy the command'}npm install {$package}{/copy}
  //
  // The text stands in a <pre><code> that selects as a whole with one click, so it can be
  // copied by hand in any browser. The button is there when scripting is: a small script,
  // written once per page with the CSP nonce, puts the text on the clipboard
  // (navigator.clipboard, or the selection and execCommand where that is missing - a page
  // over plain http) and says 'Copied' on the button and to a screen reader, through a
  // status region beside it. Scripting off, there is no button that would do nothing.
  //
  // padCopy        the box: the text, the button and its status
  // padCopyStyle   the CSS, once per page
  // padCopyScript  the script, once per page

  function padCopy ( $html, $seed, $label ) {

    $id    = padWidgetId ( 'copy', $seed );
    $label = padWidgetAttr ( $label );

    return padCopyStyle ()
         . padProtect ( '<div class="pad-copy">' . "<pre class=\"pad-copy-text\" id=\"$id\"><code>" )
         . $html
         . padProtect ( '</code></pre>'
                      . "<button type=\"button\" class=\"pad-copy-button\" data-pad-copy=\"$id\" data-pad-label=\"$label\">$label</button>"
                      . '<span class="pad-copy-status" role="status"></span></div>' )
         . padCopyScript ();

  }

  function padCopyStyle () {

    return padWidgetStyle ( 'copy',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'line'    => [ '#e4e3df', '#3a3a37' ],
        'surface' => [ '#f6f5f2', '#232321' ],
        'done'    => [ '#127a52', '#4fc794' ] ],
      '.pad-copy{display:flex;align-items:stretch;gap:.5em;margin:0 0 1em;padding:.35em .35em .35em .9em;'
      .   'border:1px solid var(--pad-copy-line);border-radius:10px;background:var(--pad-copy-surface)}'
      . '.pad-copy-text{flex:1;align-self:center;margin:0;overflow-x:auto;color:var(--pad-copy-text);'
      .   'font:.92em/1.5 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;user-select:all}'
      . '.pad-copy-text code{font:inherit;background:none;padding:0}'
      . '.pad-copy-button{display:none;flex:none;align-items:center;padding:.4em .9em;border:1px solid var(--pad-copy-line);'
      .   'border-radius:7px;background:transparent;color:var(--pad-copy-muted);font:inherit;font-size:.88em;font-weight:600;cursor:pointer}'
      . '@media (scripting:enabled){.pad-copy-button{display:inline-flex}}'
      . '.pad-copy-button:hover{color:var(--pad-copy-text);border-color:var(--pad-copy-muted)}'
      . '.pad-copy-button:focus-visible{outline:2px solid var(--pad-copy-accent);outline-offset:2px}'
      . '.pad-copy-button.is-copied{color:var(--pad-copy-done);border-color:var(--pad-copy-done)}'
      . '.pad-copy-status{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);white-space:nowrap}' );

  }

  // The click is caught on the document, so a box a {live} region swaps in works the same.

  function padCopyScript () {

    return padWidgetScript ( 'copy', <<<'SCRIPT'
(function () {
  if (window.padCopyReady) return;
  window.padCopyReady = true;
  function fallback(element) {
    var range = document.createRange();
    range.selectNodeContents(element);
    var selection = getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
    var done = document.execCommand && document.execCommand('copy');
    return done ? Promise.resolve() : Promise.reject(new Error('copy'));
  }
  function say(button, text, copied) {
    var status = button.parentNode.querySelector('.pad-copy-status');
    button.textContent = text;
    button.classList.toggle('is-copied', copied);
    if (status) status.textContent = copied ? 'Copied to the clipboard' : text;
    clearTimeout(button.padCopyTimer);
    button.padCopyTimer = setTimeout(function () {
      button.textContent = button.getAttribute('data-pad-label');
      button.classList.remove('is-copied');
      if (status) status.textContent = '';
    }, 2000);
  }
  document.addEventListener('click', function (e) {
    var button = e.target.closest && e.target.closest('button[data-pad-copy]');
    if (!button) return;
    var element = document.getElementById(button.getAttribute('data-pad-copy'));
    if (!element) return;
    var text = element.textContent;
    var copy = navigator.clipboard && window.isSecureContext
      ? navigator.clipboard.writeText(text) : fallback(element);
    copy.then(function () { say(button, 'Copied', true); },
              function () { say(button, 'Select and copy', false); });
  });
})();
SCRIPT );

  }

?>
