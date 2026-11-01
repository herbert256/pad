<?php

  // multiple_of:n - a number that is a whole multiple of n: packs of six, multiple_of:6, a
  // quarter of an hour, multiple_of:0.25, whole cents, multiple_of:0.01. The value is a
  // number as numeric reads one - 1e3 and a sign too, the sign not counting - and it is
  // reckoned in the decimal digits as written, never in floating point, where 0.3 is no
  // multiple of 0.1 and a large number has lost its last digits: value and n are made whole
  // numbers of one scale and the remainder taken digit by digit. 0 is a multiple of every n.
  // Like numeric it makes the field a number, so min, max and between compare its value.
  // Its argument is a positive number written plainly, of at most 14 digits once its zeros
  // at either end are left out - multiple_of:0, multiple_of:-5, multiple_of:1e3 or
  // multiple_of:six is the author's error.

  // A number as [ digits, scale ] - its value the digits divided by 10 to the scale, the
  // zeros at either end of the digits left out ( '' for 0 ) - or NULL for a text numeric
  // does not read: the browser's half (www/assets/rules/multiple_of.js) parts it alike. An
  // exponent beyond nine digits is held at 10^9: the remainder is the same from 50 zeros on,
  // and a scale that large leaves no whole multiple.

  $decimal = function ( $text ) {

    if ( ! preg_match ( '/^[ \t\n\r\x0B\f]*[+-]?(?:([0-9]+)(?:\.([0-9]*))?|\.([0-9]+))(?:[eE]([+-]?)([0-9]+))?[ \t\n\r\x0B\f]*$/D', (string) $text, $at ) )
      return NULL;

    $fraction = ( $at [2] ?? '' ) . ( $at [3] ?? '' );
    $power    = ltrim ( $at [5] ?? '', '0' );
    $power    = ( strlen ( $power ) > 9 ) ? 1000000000 : (int) $power;
    $digits   = ltrim ( ( $at [1] ?? '' ) . $fraction, '0' );
    $whole    = rtrim ( $digits, '0' );
    $scale    = strlen ( $fraction ) - ( ( ( $at [4] ?? '' ) === '-' ) ? -$power : $power ) - ( strlen ( $digits ) - strlen ( $whole ) );

    return [ $whole, $scale ];

  };

  return [
    'numeric'  => TRUE,
    'check'    => function ( $value, $arg ) use ( $decimal ) {
      $number = $decimal ( $value );
      $by     = $decimal ( trim ( $arg ) );
      if ( $number === NULL or $by === NULL or $by [0] === '' or strlen ( $by [0] ) > 14 )
        return FALSE;
      if ( $number [0] === '' )
        return TRUE;
      if ( $number [1] > $by [1] )
        return FALSE;
      $divisor = (int) $by [0];
      $rest    = 0;
      foreach ( str_split ( $number [0] ) as $digit )
        $rest = ( $rest * 10 + (int) $digit ) % $divisor;
      for ( $zeros = min ( $by [1] - $number [1], 50 ); $zeros > 0 and $rest; $zeros-- )
        $rest = $rest * 10 % $divisor;
      return $rest == 0;
    },
    'argument' => function ( $arg ) use ( $decimal ) {
      $by = preg_match ( '/^([0-9]+(\.[0-9]*)?|\.[0-9]+)$/D', trim ( $arg ) ) ? $decimal ( trim ( $arg ) ) : NULL;
      if ( $by === NULL or $by [0] === '' )
        return 'no positive number - multiple_of:5 or multiple_of:0.25';
      return ( strlen ( $by [0] ) > 14 ) ? 'a number of more than 14 digits - multiple_of:0.25' : '';
    },
    'shown'    => fn ( $arg ) => trim ( $arg ),
    'message'  => ':label must be a multiple of :n'
  ];

?>
