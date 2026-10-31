<?php

  // newpassword - a password chosen, with a meter of its strength and a button that shows it:
  // a native password field, autocomplete="new-password" so a password manager offers one, and
  // where the script runs the button and the meter of how hard the password is to guess, its
  // word said to a screen reader - weak, fair, good, strong. The meter judges length, the kinds
  // of character and the patterns that make a password easy - a run like 1234 or abcd, a
  // character repeated, a password everyone tries; rules= say what the server takes -
  // 'required|min:12'. known makes it the field of a password known already - a login - with
  // autocomplete="current-password" and no meter. A password is never refilled: it would stand
  // in the page.

  $padInputCurrent = padInputFlag ( 'known' );

  padInputTake ( 'value' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputCurrent ) {

    $own  = padInputEscape ( $id );
    $fill = str_contains ( $rest, ' autocomplete=' ) ? '' : ' autocomplete="' . ( $padInputCurrent ? 'current-password' : 'new-password' ) . '"';

    return padInputFrame ( 'newpassword', $worded,
             $label
           . '<div class="pi-row pi-pass-row">'
           . "<input type=\"password\" $head$fill spellcheck=\"false\"$rest>"
           . "<button type=\"button\" class=\"pi-button pi-reveal\" aria-controls=\"$own\" hidden>Show</button>"
           . '</div>'
           . ( $padInputCurrent ? '' :
               "<div class=\"pi-meter\" hidden><meter id=\"$own-meter\" min=\"0\" max=\"4\" low=\"1.5\" high=\"2.5\" optimum=\"4\" value=\"0\" aria-label=\""
             . padInputEscape ( "Strength of $worded" ) . '"></meter><span class="pi-strength" aria-live="polite"></span></div>' ),
             $error, [ 'meter' => ! $padInputCurrent ] );

  } );

?>
