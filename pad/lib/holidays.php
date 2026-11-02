<?php

  // Public holidays for a page's PHP - the entries of the holidays subsystem, subs/calendars/holidays/,
  // whose library is included the first time one is called. A country is its ISO 3166-1
  // alpha-2 code, nl, de, gb ..., the file of subs/calendars/holidays/types/ of that name; a region is
  // written after it as ISO 3166-2 does, de-by for Bavaria, gb-sct for Scotland.
  //
  //   padHolidays ( 'nl', 2026 )                  the rows of a year - date, name, local,
  //                                               type (public, observance), region, substitute
  //   padIsHoliday ( '2026-04-27', 'nl' )         whether the date is a public holiday there
  //   padWorkdays ( '2026-04-01', '2026-04-30', 'nl' )   the weekdays from one date to the
  //                                               other, both counted, that are no public holiday
  //   padAddWorkdays ( '2026-04-24', 1, 'nl' )    the date a number of workdays later (earlier
  //                                               for a negative number) - 2026-04-28 here
  //
  // A date is text (2026-04-27, tomorrow), a timestamp or a date object, read in the
  // application's zone. An observance - a day that is no day off - counts as a workday. A
  // wrong argument is reported with padError naming the function, which then answers an
  // empty value of its kind - so is a date in a year the country's file does not know, as
  // New Zealand's after its last Matariki date, 2052.

  function padHolidaysLoad () {

    include_once PAD . 'subs/calendars/holidays/_lib/holidays.php';

  }

  // The country - and region - of a call, or FALSE after reporting the error.

  function padHolidaysWhere ( $function, $country, $region = '' ) {

    padHolidaysLoad ();

    $place = padHolidaysPlace ( $country, $region );

    if ( is_string ( $place ) ) {
      padError ( "$function: $place" );
      return FALSE;
    }

    return $place;

  }

  // Whether the days from $a to $b lie in the years the country's file knows - FALSE after
  // reporting the error when they do not.

  function padHolidaysKnownOf ( $function, $country, $a, $b ) {

    $known = padHolidaysKnown ( $country, $a, $b );

    if ( $known === '' )
      return TRUE;

    padError ( "$function: $known" );

    return FALSE;

  }

  function padHolidaysDayOf ( $function, $value ) {

    padHolidaysLoad ();

    $z = padHolidaysValue ( $value );

    if ( $z !== NULL and $z >= padHolidaysDay ( padHolidaysFirst, 1, 1 ) and $z <= padHolidaysDay ( padHolidaysLast, 12, 31 ) )
      return $z;

    padError ( "$function: '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : get_debug_type ( $value ), 40 ) . "' is no date from 1583 to 9999" );

    return NULL;

  }

  function padHolidays ( $country, $year = NULL, $region = '' ) {

    if ( ! $place = padHolidaysWhere ( 'padHolidays', $country, $region ) )
      return [];

    $year = $year ?? padNow ( 'Y' );

    if ( ! is_scalar ( $year ) or ! ctype_digit ( (string) $year ) or $year < padHolidaysFirst or $year > padHolidaysLast ) {
      padError ( "padHolidays: '" . padMakeSafe ( is_scalar ( $year ) ? (string) $year : get_debug_type ( $year ), 20 ) . "' is no year from 1583 to 9999" );
      return [];
    }

    $a = padHolidaysDay ( (int) $year, 1, 1 );
    $b = padHolidaysDay ( (int) $year, 12, 31 );

    if ( ! padHolidaysKnownOf ( 'padHolidays', $place [0], $a, $b ) )
      return [];

    return padHolidaysRange ( $place [0], $place [1], $a, $b );

  }

  function padIsHoliday ( $date, $country ) {

    if ( ! $place = padHolidaysWhere ( 'padIsHoliday', $country ) or ( $z = padHolidaysDayOf ( 'padIsHoliday', $date ) ) === NULL
         or ! padHolidaysKnownOf ( 'padIsHoliday', $place [0], $z, $z ) )
      return FALSE;

    return isset ( padHolidaysOff ( $place [0], $place [1], $z, $z ) [$z] );

  }

  // Both ends count, as a spreadsheet's NETWORKDAYS counts them: a week of five workdays from
  // its Monday to its Friday is 5, and a negative number when the end comes first.

  function padWorkdays ( $from, $to, $country ) {

    if ( ! $place = padHolidaysWhere ( 'padWorkdays', $country )
         or ( $a = padHolidaysDayOf ( 'padWorkdays', $from ) ) === NULL
         or ( $b = padHolidaysDayOf ( 'padWorkdays', $to   ) ) === NULL )
      return 0;

    $sign = ( $b < $a ) ? -1 : 1;

    [ $a, $b ] = [ min ( $a, $b ), max ( $a, $b ) ];

    if ( ! padHolidaysKnownOf ( 'padWorkdays', $place [0], $a, $b ) )
      return 0;

    $off   = padHolidaysOff ( $place [0], $place [1], $a, $b );
    $count = 0;

    for ( $z = $a; $z <= $b; $z++ )
      if ( padHolidaysWorkday ( $z, $off ) )
        $count++;

    return $sign * $count;

  }

  // The date as WORKDAY counts it: the start does not count, the steps do - one workday after
  // a Friday is the Monday, or the Tuesday when the Monday is a holiday; 0 is the date itself.
  // The holidays are read a year at a time, as far as the count reaches.

  function padAddWorkdays ( $date, $n, $country ) {

    if ( ! $place = padHolidaysWhere ( 'padAddWorkdays', $country ) or ( $z = padHolidaysDayOf ( 'padAddWorkdays', $date ) ) === NULL )
      return '';

    if ( ! is_numeric ( $n ) or (int) $n != $n or abs ( $n ) > 100000 ) {
      padError ( "padAddWorkdays: '" . padMakeSafe ( is_scalar ( $n ) ? (string) $n : gettype ( $n ), 20 ) . "' is no whole number of workdays" );
      return '';
    }

    $n    = (int) $n;
    $step = ( $n < 0 ) ? -1 : 1;
    $off  = [];
    $read = [];

    for ( $left = abs ( $n ); $left > 0; ) {

      $z   += $step;
      $year = padHolidaysCivil ( $z ) [0];

      if ( $year < padHolidaysFirst or $year > padHolidaysLast ) {
        padError ( "padAddWorkdays: $n workdays from the date run past the years 1583 to 9999" );
        return '';
      }

      if ( ! isset ( $read [$year] ) ) {
        if ( ! padHolidaysKnownOf ( 'padAddWorkdays', $place [0], $z, $z ) )
          return '';
        $off         += padHolidaysOff ( $place [0], $place [1], padHolidaysDay ( $year, 1, 1 ), padHolidaysDay ( $year, 12, 31 ) );
        $read [$year] = TRUE;
      }

      if ( padHolidaysWorkday ( $z, $off ) )
        $left--;

    }

    return padHolidaysDate ( $z );

  }

?>
