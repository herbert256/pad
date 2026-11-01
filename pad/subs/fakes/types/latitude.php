<?php

  // latitude - A latitude in decimal degrees, north of the equator positive, from south= to
  // north= - -90 to 90 unless said otherwise - written with decimals= places, 6 (a tenth of a
  // metre or so), the zeros at its end kept: 52.370216. Spread evenly over the degrees, not
  // over the earth's surface: a band near a pole gets as many points as one as wide at the
  // equator. With a longitude it is a point of a box - {fake 'lat:latitude, lon:longitude',
  // south=50.75, north=53.55, west=3.36, east=7.23, rows=5/} - in the Netherlands' box, not
  // always on land.

  $south    = padFakeOpt ( 'south',    -90 );
  $north    = padFakeOpt ( 'north',     90 );
  $decimals = padFakeOpt ( 'decimals',   6 );

  // The bounds two numbers within the poles, the first not above the second; decimals a whole
  // number up to 10. The strict check names a wrong one, the lenient walk leaves the value out.

  if ( ! is_numeric ( $south ) or ! is_numeric ( $north ) or $south < -90 or $north > 90 or $south > $north ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? TRUE )
      padError ( "the fake latitude is from south= to north=, two numbers from -90 to 90, the first not above the second - not "
                 . padMakeSafe ( "south='" . ( is_scalar ( $south ) ? $south : get_debug_type ( $south ) ) . "', north='"
                                           . ( is_scalar ( $north ) ? $north : get_debug_type ( $north ) ) . "'", 60 ) );
    return '';
  }

  if ( ! is_scalar ( $decimals ) or ! ctype_digit ( (string) $decimals ) or (int) $decimals > 10 ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? TRUE )
      padError ( "the fake latitude has decimals='" . padMakeSafe ( is_scalar ( $decimals ) ? (string) $decimals : get_debug_type ( $decimals ), 20 ) . "' - a whole number from 0 to 10" );
    return '';
  }

  // number_format keeps the places a coordinate is written with, where a float would print
  // 1.0E-6 for a point just north of the equator and -0 for one just south of it.

  return number_format ( padFakeFloat ( (float) $south, (float) $north, (int) $decimals ), (int) $decimals, '.', '' );

?>
