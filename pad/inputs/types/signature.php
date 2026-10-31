<?php

  // signature - a signature drawn with the mouse, a pen or a finger: a canvas, and the picture
  // drawn posted as a PNG data URL - data:image/png;base64,... - in a hidden field. The page's
  // PHP decodes it: base64_decode of what follows the comma is the PNG. Who cannot draw types
  // the name in the field beside it, and the name is drawn on the canvas in a hand of writing;
  // Clear wipes it. width= and height= the canvas, 400 by 150 unless given. A signature
  // needs the script: without it the page says so. After a post that came back the picture
  // is drawn again from the posted field - one that is no PNG data URL is not refilled.

  $padInputWidth  = padInputNumber ( 'width',  400, 120, 1200, TRUE );
  $padInputHeight = padInputNumber ( 'height', 150, 60,  600,  TRUE );

  $padInputNow ['value'] = padInputValue ( '' );

  if ( ! preg_match ( '#^data:image/png;base64,[A-Za-z0-9+/=]+$#D', $padInputNow ['value'] ) )
    $padInputNow ['value'] = '';

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputWidth, $padInputHeight ) {

    global $padInputNow;

    $own  = padInputEscape ( $id );
    $name = padInputEscape ( padInputName () );

    // The field a visitor reaches - the typed name - has the field's id, so the label names it
    // and the aria attributes of a message are on it; the hidden field the form posts says it
    // shows there (data-pad-shows), as the browser's checker reads it.

    return padInputFrame ( 'signature', $worded,
             $label
           . '<div class="pi-signature-pad" hidden>'
           . "<canvas class=\"pi-signature-canvas\" width=\"$padInputWidth\" height=\"$padInputHeight\" role=\"img\" aria-label=\""
           . padInputEscape ( "$worded - drawn with the mouse, a pen or a finger" ) . "\"></canvas></div>"
           . '<div class="pi-row pi-signature-bar" hidden>'
           . "<input type=\"text\" class=\"pi-signature-typed\" id=\"$own\" placeholder=\"Or type your name\" autocomplete=\"name\"$rest>"
           . '<button type="button" class="pi-button pi-signature-clear">Clear</button></div>'
           . "<input type=\"hidden\" name=\"$name\" value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\" data-pad-shows=\"$own\">"
           . '<noscript><p class="pi-noscript">Signing needs JavaScript.</p></noscript>',
             $error );

  } );

?>
