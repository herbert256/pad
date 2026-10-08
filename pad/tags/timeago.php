<?php

  // {timeago $created} - how long ago a moment was, in words, inside a <time> with the
  // moment as its datetime and, in full, as its title: lib/timeago.php. The words are the
  // ago pipe's - just now, 5 minutes ago, yesterday, in 3 weeks. The moment is a Unix
  // timestamp, a date in text or a date object; now= counts from that moment instead of
  // now, format= is the PHP date format of the title. An empty moment answers nothing.

  if ( $padParm === NULL or ( is_string ( $padParm ) and trim ( $padParm ) === '' ) )
    return '';

  $padTimeagoMoment = padDateParse ( $padParm );
  $padTimeagoNow    = padTagParm ( 'now', '' );

  if ( $padTimeagoMoment === NULL or ( $padTimeagoNow !== '' and padDateParse ( $padTimeagoNow ) === NULL ) ) {
    if ( $padCheckSyntax )
      padError ( "the timeago cannot read '" . padMakeSafe ( is_scalar ( $padTimeagoMoment === NULL ? $padParm : $padTimeagoNow )
               ? (string) ( $padTimeagoMoment === NULL ? $padParm : $padTimeagoNow ) : 'a list', 40 ) . "' as a date" );
    return '';
  }

  return padTimeago ( $padTimeagoMoment, $padTimeagoNow === '' ? NULL : $padTimeagoNow,
                      (string) padTagParm ( 'format', 'l j F Y, H:i' ) );

?>
