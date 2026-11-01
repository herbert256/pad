<?php

  // longitude - A longitude in decimal degrees, east of Greenwich positive, from west= to
  // east= - -180 to 180 unless said otherwise - written with decimals= places, 6, the zeros at
  // its end kept: 4.895168. A west= east of east= is a box across the 180th meridian, the
  // way a map's bounding box says it: west=177, east=-178 gives 177 to 180 and -180 to -178,
  // the islands of Fiji. With a latitude it is a point of a box - {fake 'lat:latitude,
  // lon:longitude', south=50.75, north=53.55, west=3.36, east=7.23, rows=5/}.

  $west     = padFakeOpt ( 'west',     -180 );
  $east     = padFakeOpt ( 'east',      180 );
  $decimals = padFakeOpt ( 'decimals',    6 );

  if ( ! is_numeric ( $west ) or ! is_numeric ( $east ) or abs ( $west ) > 180 or abs ( $east ) > 180 ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? TRUE )
      padError ( "the fake longitude is from west= to east=, two numbers from -180 to 180 - not "
                 . padMakeSafe ( "west='" . ( is_scalar ( $west ) ? $west : get_debug_type ( $west ) ) . "', east='"
                                          . ( is_scalar ( $east ) ? $east : get_debug_type ( $east ) ) . "'", 60 ) );
    return '';
  }

  if ( ! is_scalar ( $decimals ) or ! ctype_digit ( (string) $decimals ) or (int) $decimals > 10 ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? TRUE )
      padError ( "the fake longitude has decimals='" . padMakeSafe ( is_scalar ( $decimals ) ? (string) $decimals : get_debug_type ( $decimals ), 20 ) . "' - a whole number from 0 to 10" );
    return '';
  }

  // Across the 180th meridian the span goes on past 180 and what lies beyond is counted from
  // -180 again. A value rounded to its decimals stays in the box, as a latitude's does
  // (latitude.php): the bounds are the first and the last value of that many places from
  // west= to east=, the draw the part of the box that rounds to them - decimals=0 from 3.36
  // to 7.23 gives 4 to 7, where the value rounded after the draw was 3 for one before 3.5.

  $west     = (float) $west;
  $span     = (float) $east - $west;
  $span    += ( $span < 0 ) ? 360 : 0;
  $decimals = (int) $decimals;
  $half     = 0.5 / 10 ** $decimals;
  $low      = round ( $west, $decimals );
  $high     = round ( $west + $span, $decimals );

  if ( $low < $west )
    $low = round ( $low + 2 * $half, $decimals );

  if ( $high > $west + $span )
    $high = round ( $high - 2 * $half, $decimals );

  if ( $low > $high ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? TRUE )
      padError ( "the fake longitude has no value of decimals=$decimals from west=$west to east=" . (float) $east );
    return '';
  }

  $from  = max ( 0.0, $low - $half - $west );
  $to    = min ( $span, $high + $half - $west );
  $value = min ( $high, max ( $low, round ( $west + $from + padFakeUnit () * ( $to - $from ), $decimals ) ) );
  $value = ( $value > 180 ) ? round ( $value - 360, $decimals ) : $value;

  return number_format ( $value, $decimals, '.', '' );

?>
