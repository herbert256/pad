<?php

  // stepper - a number with a minus and a plus beside it: a native <input type="number">,
  // typed into or stepped with the arrow keys as it is, and two buttons the script shows,
  // which step it by step= - 1 unless given - within min= and max=. An empty stepper steps
  // from min=, or from 0.

  $padInputMin  = padInputPeek ( 'min', NULL ) === NULL ? NULL : padInputNumber ( 'min', 0 );
  $padInputMax  = padInputPeek ( 'max', NULL ) === NULL ? NULL : padInputNumber ( 'max', 0, $padInputMin ?? -INF );
  $padInputStep = padInputNumber ( 'step', 1, 0 );

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputMin, $padInputMax, $padInputStep ) {

    global $padInputNow;

    $bounds = ( $padInputMin !== NULL ? " min=\"$padInputMin\"" : '' ) . ( $padInputMax !== NULL ? " max=\"$padInputMax\"" : '' )
            . ( $padInputStep > 0 ? " step=\"$padInputStep\"" : ' step="any"' );

    $control = padInputEscape ( $id );
    $name    = padInputEscape ( $worded );

    return padInputFrame ( 'stepper', $worded,
             $label
           . '<div class="pi-stepper-row">'
           . "<button type=\"button\" class=\"pi-button pi-step\" data-step=\"-1\" aria-controls=\"$control\" aria-label=\"Decrease $name\" tabindex=\"-1\" hidden>&minus;</button>"
           . "<input type=\"number\" $head inputmode=\"decimal\"$bounds value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\"$rest>"
           . "<button type=\"button\" class=\"pi-button pi-step\" data-step=\"1\" aria-controls=\"$control\" aria-label=\"Increase $name\" tabindex=\"-1\" hidden>+</button>"
           . '</div>',
             $error );

  } );

?>
