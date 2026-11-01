<?php

  // durationpicker - a length of time, an hour and a half, a lap of 3 minutes 12: a native
  // number field that posts the duration as a whole number of seconds - 5400 for 1 h 30 min,
  // whatever is shown - and where the script runs a box per part, hours, minutes and seconds,
  // each a spin button: typed into, ArrowUp and ArrowDown a unit more or less of the whole
  // duration - 59 minutes and one more is an hour - Page Up and Page Down ten, Home the part
  // to 0, End to its most. parts= the parts shown, a run of d h m s - 'hms' unless given, 'hm',
  // 'dhm', 'ms' ... - the largest without a bound, so a duration runs past 24 hours, the
  // smallest the step: parts='hm' posts whole minutes, in seconds. min= and max= are seconds,
  // and so are value= and the rules: rules='integer|between:60,7200'. Without the script the
  // field takes the seconds typed, its step the smallest part's.

  $padInputUnits = [ 'd' => 86400, 'h' => 3600, 'm' => 60, 's' => 1 ];

  $padInputParts = strtolower ( trim ( (string) padInputTake ( 'parts', 'hms' ) ) );

  if ( $padInputParts === '' or ! str_contains ( 'dhms', $padInputParts ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padInputWhat () . " has no usable parts '" . padMakeSafe ( $padInputParts, 20 ) . "' - a run of d h m s: 'hms', 'hm', 'dhm', 'ms'" );
    $padInputParts = 'hms';
  }

  $padInputStep = $padInputUnits [ substr ( $padInputParts, -1 ) ];
  $padInputMin  = padInputNumber ( 'min', 0, 0, INF, TRUE );
  $padInputMax  = padInputPeek ( 'max', NULL ) === NULL ? NULL : padInputNumber ( 'max', 0, $padInputMin, INF, TRUE );

  // A value that is no whole number of seconds - a post that broke the integer rule - is
  // shown as it is in the number field, which the browser marks, and the boxes start empty.

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputParts, $padInputStep, $padInputMin, $padInputMax ) {

    global $padInputNow;

    $unit  = padInputEscape ( "$id-unit" );
    $mine  = padInputOwn ( $rest, [ 'class' => 'pi-dur-whole', 'inputmode' => 'numeric' ] );
    $bound = " min=\"$padInputMin\"" . ( $padInputMax !== NULL ? " max=\"$padInputMax\"" : '' ) . " step=\"$padInputStep\"";

    // The unit the number is typed in is said with it: after the message the field may point
    // at, as a description of its own.

    if ( preg_match ( '/ aria-describedby="([^"]*)"/', $rest, $said ) )
      $rest = str_replace ( $said [0], " aria-describedby=\"$said[1] $unit\"", $rest );
    else
      $rest .= " aria-describedby=\"$unit\"";

    return padInputFrame ( 'durationpicker', $worded,
             $label
           . '<div class="pi-row pi-dur-row">'
           . "<input type=\"number\" $head$mine$bound value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\"$rest>"
           . "<span class=\"pi-muted pi-dur-unit\" id=\"$unit\">seconds</span>"
           . '</div>',
             $error, [ 'parts' => $padInputParts ] );

  } );

?>
