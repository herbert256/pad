<?php

  // slider - a range of numbers to drag along: a native <input type="range"> with its value
  // beside it in an <output>, the ends of the range under it. min= and max=, 0 and 100 unless
  // given - a max of min= + 100 when min= is 100 or more - step= 1; unit= follows the value as
  // it is shown - '%', ' kg'. marks= a list of values the track shows a tick at. Without a
  // value the slider stands in the middle, as a range does, and that is what it posts. The
  // script keeps the output - and the value a screen reader says, aria-valuetext - in step as
  // it moves.

  // The default max follows a min= past it: held to the min=, the default 100 refused
  // {slider 'v', min=200/} for a max the template never wrote.

  $padInputMin  = padInputNumber ( 'min',  0 );
  $padInputMax  = padInputNumber ( 'max',  $padInputMin < 100 ? 100 : $padInputMin + 100, $padInputMin );
  $padInputStep = padInputNumber ( 'step', 1, 0 );
  $padInputUnit = (string) padInputTake ( 'unit', '' );
  $padInputMark = padInputList ( 'marks' );

  $padInputMid  = $padInputMin + ( $padInputStep > 0 ? round ( ( $padInputMax - $padInputMin ) / 2 / $padInputStep ) * $padInputStep : ( $padInputMax - $padInputMin ) / 2 );

  $padInputNow ['value'] = padInputValue ( (string) $padInputMid );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputMin, $padInputMax, $padInputStep, $padInputUnit, $padInputMark ) {

    global $padInputNow;

    $value = $padInputNow ['value'];
    $shown = padInputEscape ( $value . $padInputUnit );
    $list  = '';

    if ( $padInputMark ) {
      $list = '<datalist id="' . padInputEscape ( "$id-marks" ) . '">';
      foreach ( $padInputMark as $mark )
        $list .= '<option value="' . padInputEscape ( $mark ) . '"></option>';
      $list .= '</datalist>';
      $rest .= ' list="' . padInputEscape ( "$id-marks" ) . '"';
    }

    $range = "<input type=\"range\" $head min=\"$padInputMin\" max=\"$padInputMax\"" . ( $padInputStep > 0 ? " step=\"$padInputStep\"" : ' step="any"' )
           . ' value="' . padInputEscape ( $value ) . '" aria-valuetext="' . $shown . "\"$rest>";

    return padInputFrame ( 'slider', $worded,
             $label
           . '<div class="pi-row pi-slider-row"><div class="pi-slider-track">' . $range
           . '<div class="pi-slider-ends pi-muted" aria-hidden="true"><span>' . padInputEscape ( $padInputMin . $padInputUnit ) . '</span><span>'
           . padInputEscape ( $padInputMax . $padInputUnit ) . '</span></div></div>'
           . '<output class="pi-slider-out" for="' . padInputEscape ( $id ) . '">' . $shown . '</output></div>' . $list,
             $error, [ 'unit' => $padInputUnit ] );

  } );

?>
