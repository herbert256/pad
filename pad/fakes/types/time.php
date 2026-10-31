<?php

  // time - A time of day from from= to to=, both as hours and minutes - 08:00 to 18:00
  // unless said otherwise, the hours of an office - to the minute, 14:35.

  $from  = padFakeOpt ( 'from', '08:00' );
  $to    = padFakeOpt ( 'to',   '18:00' );
  $clock = fn ( $time ) => preg_match ( '/^([01]?\d|2[0-3]):([0-5]\d)$/D', trim ( (string) $time ), $m ) ? 60 * (int) $m [1] + (int) $m [2] : NULL;

  if ( $clock ( $from ) === NULL or $clock ( $to ) === NULL or $clock ( $from ) > $clock ( $to ) ) {
    padError ( "the fake time is from= and to= as hours and minutes, the first not after the second - not '"
               . padMakeSafe ( "$from', '$to", 40 ) . "'" );
    return '';
  }

  $minute = padFakeNumber ( $clock ( $from ), $clock ( $to ) );

  return sprintf ( '%02d:%02d', intdiv ( $minute, 60 ), $minute % 60 );

?>
