<?php

  // A text with a copy-to-clipboard button - the copy widget, {copy}.
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
  // padCopyStyle   the CSS, assets/copy.css, once per page
  // padCopyScript  the script, assets/copy.js, once per page

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
      padWidgetAsset ( 'copy.css' ) );

  }

  // The click is caught on the document, so a box a {live} region swaps in works the same.

  function padCopyScript () {

    return padWidgetScript ( 'copy', padWidgetAsset ( 'copy.js' ) );

  }

?>
