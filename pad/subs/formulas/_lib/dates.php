<?php

  // Dates as a spreadsheet keeps them: a number of days, the serial of the 1900 system -
  // 1 is 1900-01-01, 45306 is 2024-01-15 - counted from 1899-12-30, the fraction the time of
  // day, 0.5 noon. Excel took over Lotus 1-2-3's mistake of a 29 February 1900: the days
  // before 1 March 1900 are one less than a count from 1899-12-30 gives, 60 is that day that
  // never was, and 0 is 1900-01-00. These functions keep all three, so DATE(1900,3,1) is 61
  // as in Excel and WEEKDAY(1) a Sunday.
  //
  //   padFormulaDays      the days from 1970-01-01 of a date of the calendar we use
  //   padFormulaCivil     a number of such days as [ year, month, day ]
  //   padFormulaSerial    year, month and day as a serial - a month past 12 or a day past the
  //                       month's end rolls over, as DATE does: DATE(2024,13,1) is 2025-01-01
  //   padFormulaYmd       a serial as [ year, month, day ]
  //   padFormulaHms       the time of a serial as [ hours, minutes, seconds ]
  //   padFormulaWeekday   0 for Sunday to 6 for Saturday
  //   padFormulaToday     today and now as serials, from padNow - padNowFreeze holds them

  const padFormulaLastSerial = 2958465;      // 9999-12-31

  function padFormulaDays ( $year, $month, $day ) {

    $year -= ( $month <= 2 ) ? 1 : 0;
    $era   = intdiv ( $year >= 0 ? $year : $year - 399, 400 );
    $yoe   = $year - $era * 400;
    $doy   = intdiv ( 153 * ( $month + ( $month > 2 ? -3 : 9 ) ) + 2, 5 ) + $day - 1;
    $doe   = $yoe * 365 + intdiv ( $yoe, 4 ) - intdiv ( $yoe, 100 ) + $doy;

    return $era * 146097 + $doe - 719468;

  }

  function padFormulaCivil ( $days ) {

    $days += 719468;
    $era   = intdiv ( $days >= 0 ? $days : $days - 146096, 146097 );
    $doe   = $days - $era * 146097;
    $yoe   = intdiv ( $doe - intdiv ( $doe, 1460 ) + intdiv ( $doe, 36524 ) - intdiv ( $doe, 146096 ), 365 );
    $year  = $yoe + $era * 400;
    $doy   = $doe - ( 365 * $yoe + intdiv ( $yoe, 4 ) - intdiv ( $yoe, 100 ) );
    $mp    = intdiv ( 5 * $doy + 2, 153 );
    $day   = $doy - intdiv ( 153 * $mp + 2, 5 ) + 1;
    $month = $mp + ( $mp < 10 ? 3 : -9 );

    return [ $year + ( $month <= 2 ? 1 : 0 ), $month, $day ];

  }

  function padFormulaSerial ( $year, $month, $day ) {

    $months = $year * 12 + ( $month - 1 );
    $year   = intdiv ( $months - ( $months < 0 ? 11 : 0 ), 12 );
    $month  = $months - $year * 12 + 1;
    $first  = padFormulaDays ( $year, $month, 1 ) - padFormulaDays ( 1899, 12, 30 );

    if ( $first < 61 )
      $first--;

    return $first + $day - 1;

  }

  function padFormulaYmd ( $serial ) {

    $serial = (int) floor ( $serial );

    if ( $serial == 60 ) return [ 1900, 2, 29 ];
    if ( $serial == 0 )  return [ 1900, 1, 0 ];

    return padFormulaCivil ( padFormulaDays ( 1899, 12, 30 ) + ( $serial < 60 ? $serial + 1 : $serial ) );

  }

  // The time of a serial, to the second - rounded, so 0.99999999 is no 23:59:59.

  function padFormulaHms ( $serial ) {

    $seconds = (int) round ( ( $serial - floor ( $serial ) ) * 86400 );

    if ( $seconds >= 86400 )
      $seconds = 86399;

    return [ intdiv ( $seconds, 3600 ), intdiv ( $seconds % 3600, 60 ), $seconds % 60 ];

  }

  function padFormulaWeekday ( $serial ) {

    return ( ( (int) floor ( $serial ) - 1 ) % 7 + 7 ) % 7;

  }

  function padFormulaToday ( $time = FALSE ) {

    $now    = padNow ();
    $serial = padFormulaSerial ( (int) $now->format ( 'Y' ), (int) $now->format ( 'n' ), (int) $now->format ( 'j' ) );

    if ( $time )
      $serial += ( (int) $now->format ( 'G' ) * 3600 + (int) $now->format ( 'i' ) * 60 + (int) $now->format ( 's' ) ) / 86400;

    return (float) $serial;

  }

  // A serial a date function takes: a number, or a text that reads as a date; one below 0 or
  // past 9999-12-31 is #NUM!.

  function padFormulaDateArg ( $value, $env ) {

    $value = padFormulaScalar ( $value, $env );

    if ( is_string ( $value ) ) {
      $number = padFormulaParseValue ( $value );
      if ( $number === NULL )
        return padFormulaError ( '#VALUE!' );
      $value = $number;
    }

    $number = padFormulaNumber ( $value );

    if ( padFormulaIsError ( $number ) )
      return $number;

    if ( $number < 0 or $number >= padFormulaLastSerial + 1 )
      return padFormulaError ( '#NUM!' );

    return $number;

  }

  const padFormulaMonths = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July',
                             'August', 'September', 'October', 'November', 'December' ];

  const padFormulaDayNames = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];

  // A date or a time written as text, as a serial, or NULL: 2024-01-15, 2024/1/15, 1/15/2024
  // (month first, as Excel in English reads it), 15-Jan-2024, 15 January 2024, Jan 15, 2024,
  // each with a time after it or not - 13:45, 1:45 PM, 13:45:30 - or a time alone. A two-digit
  // year is 2000 to 2029 up to 29, else 1930 to 1999, Excel's rule.

  function padFormulaParseDate ( $text ) {

    $text = trim ( preg_replace ( '/\s+/', ' ', $text ) );
    $time = 0.0;

    if ( preg_match ( '/^(.*?)[ T]?(\d{1,2}):(\d{2})(?::(\d{2}(?:\.\d+)?))? ?([AaPp][Mm])?$/', $text, $match ) ) {

      $hours   = (int) $match [2];
      $minutes = (int) $match [3];
      $seconds = (float) ( $match [4] ?? 0 );
      $half    = strtoupper ( $match [5] ?? '' );

      if ( $half !== '' and ( $hours < 1 or $hours > 12 ) )
        return NULL;

      if ( $half == 'PM' and $hours < 12 ) $hours += 12;
      if ( $half == 'AM' and $hours == 12 ) $hours = 0;

      if ( $hours > 23 or $minutes > 59 or $seconds >= 60 )
        return NULL;

      $time = ( $hours * 3600 + $minutes * 60 + $seconds ) / 86400;
      $text = trim ( $match [1] );

      if ( $text === '' )
        return $time;

    }

    $months = [];

    foreach ( padFormulaMonths as $index => $name ) {
      $months [ strtolower ( $name ) ]             = $index + 1;
      $months [ strtolower ( substr ( $name, 0, 3 ) ) ] = $index + 1;
    }

    $month = fn ( $word ) => $months [ strtolower ( rtrim ( $word, '.' ) ) ] ?? NULL;
    $year  = fn ( $digits ) => strlen ( $digits ) <= 2 ? ( (int) $digits < 30 ? 2000 : 1900 ) + (int) $digits : (int) $digits;

    if ( preg_match ( '/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/', $text, $m ) )
      [ $y, $mo, $d ] = [ (int) $m [1], (int) $m [2], (int) $m [3] ];
    elseif ( preg_match ( '/^(\d{1,2})\/(\d{1,2})\/(\d{2}|\d{4})$/', $text, $m ) )
      [ $y, $mo, $d ] = [ $year ( $m [3] ), (int) $m [1], (int) $m [2] ];
    elseif ( preg_match ( '/^(\d{1,2})[- ]([A-Za-z]{3,9}\.?)[- ](\d{2}|\d{4})$/', $text, $m ) and $month ( $m [2] ) )
      [ $y, $mo, $d ] = [ $year ( $m [3] ), $month ( $m [2] ), (int) $m [1] ];
    elseif ( preg_match ( '/^([A-Za-z]{3,9}\.?) (\d{1,2}),? (\d{2}|\d{4})$/', $text, $m ) and $month ( $m [1] ) )
      [ $y, $mo, $d ] = [ $year ( $m [3] ), $month ( $m [1] ), (int) $m [2] ];
    else
      return NULL;

    if ( $mo < 1 or $mo > 12 or $d < 1 or $y < 1900 or $y > 9999 )
      return NULL;

    $leap = ( $y % 4 == 0 and ( $y % 100 != 0 or $y % 400 == 0 ) ) || $y == 1900;

    if ( $d > [ 31, $leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ] [ $mo - 1 ] )
      return NULL;

    return padFormulaSerial ( $y, $mo, $d ) + $time;

  }

?>
