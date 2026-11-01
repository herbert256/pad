<?php

  // rangeslider - a lower and an upper number chosen on one track, a price from 100 to 400:
  // two native <input type="range">, one for each end, under a legend - two fields of the
  // form, {rangeslider 'price'} posting price_from and price_to, names= two others. Where the
  // script runs the two lie on one track, the part between them filled: two WAI-ARIA sliders,
  // each reached with Tab and moved with the arrow keys, Page Up and Page Down, Home and End,
  // as a range is, and neither passing the other - the lower stops at the upper. min= and
  // max=, 0 and 100 unless given, step= 1; unit= follows the values as they are shown -
  // ' EUR', '%'; labels= what each end says, 'From, To' unless given; value= the two before
  // a post, the whole track unless given. A range is two fields and has no rules= - the
  // page's PHP checks the two with padValidate, and each message stands after the frame.
  // Without the script the two ranges stand one above the other, showing no number - the
  // value written beside each would stay while it is moved - and either can be put past the
  // other: the page's PHP gets the two as they were posted.

  include_once PAD . 'subs/inputs/_lib/pair.php';

  $padInputMin  = padInputNumber ( 'min',  0 );
  $padInputMax  = padInputNumber ( 'max',  $padInputMin < 100 ? 100 : $padInputMin + 100, $padInputMin );
  $padInputStep = padInputNumber ( 'step', 1, 0 );
  $padInputUnit = (string) padInputTake ( 'unit', '' );

  return padInputPair ( function ( $ends, $legend, $worded ) use ( $padInputMin, $padInputMax, $padInputStep, $padInputUnit ) {

    $step   = $padInputStep > 0 ? " step=\"$padInputStep\"" : ' step="any"';
    $fields = '';
    $errors = '';

    foreach ( $ends as $at => $end ) {

      $value = $end ['value'];
      $shown = padInputEscape ( $value . $padInputUnit );

      // The value beside the range is the script's to show, which follows the range: without
      // it the number written here stayed while the range was moved.

      $fields .= '<div class="pi-range-end pi-range-' . ( $at ? 'to' : 'from' ) . '">' . $end ['label']
               . "<input type=\"range\" $end[head] min=\"$padInputMin\" max=\"$padInputMax\"$step value=\"" . padInputEscape ( $value ) . "\""
               . " aria-valuetext=\"$shown\"$end[rest]>"
               . '<output class="pi-range-out" for="' . padInputEscape ( $end ['id'] ) . "\" hidden>$shown</output></div>";

      $errors .= $end ['error'];

    }

    return padInputFrame ( 'rangeslider', $worded,
             "<legend>$legend</legend>"
           . '<div class="pi-range-said" hidden></div>'
           . "<div class=\"pi-range-track\">$fields</div>"
           . '<div class="pi-range-ends pi-muted" aria-hidden="true"><span>' . padInputEscape ( $padInputMin . $padInputUnit ) . '</span><span>'
           . padInputEscape ( $padInputMax . $padInputUnit ) . '</span></div>',
             $errors, [ 'unit' => $padInputUnit ], 'fieldset' );

  } , [ (string) $padInputMin, (string) $padInputMax ] );

?>
