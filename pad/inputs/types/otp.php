<?php

  // otp - a one-time code, the digits a text message or an app gives: a native text field
  // that posts the code as it is typed - inputmode="numeric", autocomplete="one-time-code" so
  // a phone offers the code it received - and where the script runs a box per character, the
  // next box taking the focus as one is typed, Backspace going back, a pasted code filling
  // them all. The code the form posts is then the hidden field the boxes fill. length= the
  // number of characters, 6 unless given (4 to 10); letters takes letters and digits, in
  // upper case, where the code is digits alone unless given.

  $padInputLength  = padInputNumber ( 'length', 6, 4, 10, TRUE );
  $padInputLetters = padInputFlag ( 'letters' );

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputLength, $padInputLetters ) {

    global $padInputNow;

    $kind = $padInputLetters ? ' autocapitalize="characters"' : ' inputmode="numeric"';

    return padInputFrame ( 'otp', $worded,
             $label
           . "<input type=\"text\" class=\"pi-otp-whole\" $head value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\" maxlength=\"$padInputLength\""
           . "$kind autocomplete=\"one-time-code\" spellcheck=\"false\"$rest>",
             $error, [ 'length' => $padInputLength, 'letters' => $padInputLetters ] );

  } );

?>
