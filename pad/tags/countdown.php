<?php

  // {countdown '2026-12-31 00:00'} - the time left until a moment, in words, inside a
  // <time datetime>: lib/countdown.php. units= is how many units are shown, 2 when not
  // given - 84 days, 3 hours - and at most 4: days, hours, minutes and seconds. past= is
  // the text once the moment has gone, 'ended' when not given. live adds a small script,
  // once per page, that counts down every second in the browser. now= counts from that
  // moment instead of now; format= is the PHP date format of the full moment in the title.

  $padCountdownMoment = padDateParse ( $padParm );
  $padCountdownNow    = padTagParm ( 'now', '' );
  $padCountdownFrom   = ( $padCountdownNow === '' ) ? padNow () : padDateParse ( $padCountdownNow );
  $padCountdownUnits  = padTagParm ( 'units', 2 );

  if ( $padCountdownMoment === NULL or $padCountdownFrom === NULL ) {
    if ( $padCheckSyntax )
      padError ( "the countdown cannot read '" . padMakeSafe ( is_scalar ( $padCountdownMoment === NULL ? $padParm : $padCountdownNow )
               ? (string) ( $padCountdownMoment === NULL ? $padParm : $padCountdownNow ) : 'a list', 40 ) . "' as a date" );
    return '';
  }

  if ( ! ctype_digit ( (string) $padCountdownUnits ) or $padCountdownUnits < 1 or $padCountdownUnits > 4 ) {
    if ( $padCheckSyntax )
      padError ( "the units of the countdown are 1 to 4, not '" . padMakeSafe ( $padCountdownUnits, 10 ) . "'" );
    $padCountdownUnits = 2;
  }

  return padCountdown ( $padCountdownMoment, $padCountdownFrom, (int) $padCountdownUnits,
                        (string) padTagParm ( 'past', 'ended' ), (bool) padTagParm ( 'live', FALSE ),
                        (string) padTagParm ( 'format', 'l j F Y, H:i' ) );

?>
