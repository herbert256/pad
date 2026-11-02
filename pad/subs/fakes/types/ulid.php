<?php
  // A fixture ULID: explicit Unix milliseconds, plus this fake stream's 80 random bits.
  $time = padFakeOpt ( 'time', NULL );
  if ( ! is_numeric ( $time ) or ! is_finite ( (float) $time ) or $time < 0 or $time > 281474976710655 or floor ( (float) $time ) != $time ) {
    padError ( 'fake:ulid requires time= as integer Unix milliseconds from 0 to 281474976710655' );
    return NULL;
  }
  $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
  $time = (int) $time;
  $text = '';
  for ( $i = 0; $i < 10; $i++ ) {
    $text = $alphabet [ $time & 31 ] . $text;
    $time = intdiv ( $time, 32 );
  }
  // Two random bytes per 16-bit chunk; no clock, global PRNG or monotonic counter.
  $bits = '';
  for ( $i = 0; $i < 5; $i++ ) $bits .= str_pad ( decbin ( padFakeNext () & 65535 ), 16, '0', STR_PAD_LEFT );
  for ( $i = 0; $i < 80; $i += 5 ) $text .= $alphabet [ bindec ( substr ( $bits, $i, 5 ) ) ];
  return $text;
?>
