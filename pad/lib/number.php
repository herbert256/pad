<?php

  // Number helpers: numbers written for people from a page's PHP - grouped with the locale's
  // marks, shortened to 1.5K or 1.5 million, as an ordinal, a percentage or a file size - and
  // kept between bounds; what Laravel's Number class does, as plain functions. The pipes
  // abbreviate and ordinal (pad/functions/) are the template side, and the bytes pipe answers
  // what padNumberFileSize answers.
  //
  // padNumberFormat      the number grouped, with so many decimals, the locale's way
  // padNumberAbbreviate  1500 as 2K, or 1.5K with a precision of 1 - K, M, B, T and Q
  // padNumberForHumans   1500000 as 2 million, or 1.5 million - thousand up to quadrillion
  // padNumberOrdinal     21 as 21st, 112 as 112th - English
  // padNumberPercentage  25.55 as 26%, or 25.6% with a precision of 1 - the locale's way
  // padNumberClamp       the number kept between a minimum and a maximum
  // padNumberFileSize    1536 as 2 KB, or 1.5 KB - units of 1024, the bytes pipe's answer
  // padNumberRead        private: a value as a number - NULL for nothing, FALSE when wrong
  // padNumberPrecision   private: a precision as a whole number of 0 or more
  // padNumberLocal       private: a number written the locale's way, intl or number_format
  // padNumberScale       private: a number in the largest unit that keeps it under the base
  // padNumberTrim        private: a number with at most so many decimals, no trailing zeros
  //
  // The locale is $padLocale, the request's. intl's NumberFormatter writes the number when
  // the extension is there, with half rounded up as number_format rounds it; without intl
  // the number is written the plain number_format way - a comma between thousands, a point
  // before the decimals. Abbreviations, words, ordinals and file sizes are English and written
  // with a point whatever the locale, as the bytes pipe has always written them.
  //
  // Nothing to write - NULL, FALSE or empty text - answers '' (padNumberClamp NULL): a missing
  // value in a database row shows as nothing. A value that is no number, a negative precision
  // or a locale intl does not know is reported with padError, naming the function, and
  // answers the same.

  function padNumberFormat ( $number, $decimals = 0, $locale = NULL ) {

    $number   = padNumberRead      ( 'padNumberFormat', $number );
    $decimals = padNumberPrecision ( 'padNumberFormat', $decimals, 'number of decimals' );

    if ( $locale !== NULL and ! is_string ( $locale ) ) {
      padError ( "padNumberFormat: the locale must be text, not " . padValueShow ( $locale ) );
      return '';
    }

    if ( $number === NULL or $number === FALSE or $decimals === FALSE )
      return '';

    return padNumberLocal ( 'padNumberFormat', $number, $decimals, padLocaleOf ( $locale ), FALSE );

  }

  // Large numbers short, the way a counter of visits or followers shows them: 1000 is 1K,
  // 1500 is 2K, or 1.5K with a precision of 1. The unit is chosen on the number as it will be
  // written, so 999999 is 1M and not 1000K. Below 1000 the number has no letter.

  function padNumberAbbreviate ( $number, $precision = 0 ) {

    return padNumberScale ( 'padNumberAbbreviate', $number, $precision, 1000,
                            [ '', 'K', 'M', 'B', 'T', 'Q' ], '' );

  }

  // The same in words, for a sentence: 1 thousand, 1.5 million, 3 billion.

  function padNumberForHumans ( $number, $precision = 0 ) {

    return padNumberScale ( 'padNumberForHumans', $number, $precision, 1000,
                            [ '', 'thousand', 'million', 'billion', 'trillion', 'quadrillion' ], ' ' );

  }

  // A place in English: 1st 2nd 3rd 4th, and 11th 12th 13th - the teens take th - then 21st,
  // 101st, 111th. A number with a fraction has no place, and is reported.

  function padNumberOrdinal ( $number ) {

    $number = padNumberRead ( 'padNumberOrdinal', $number );

    if ( $number === NULL or $number === FALSE )
      return '';

    if ( is_float ( $number ) and floor ( $number ) != $number ) {
      padError ( "padNumberOrdinal: " . padValueShow ( $number ) . " is not a whole number" );
      return '';
    }

    $digits = ( $number == 0 ) ? '0' : ( is_int ( $number ) ? (string) $number : sprintf ( '%.0f', $number ) );
    $tens   = (int) substr ( ltrim ( $digits, '-' ), -2 );

    if ( $tens >= 11 and $tens <= 13 )
      return $digits . 'th';

    return $digits . ( [ 1 => 'st', 2 => 'nd', 3 => 'rd' ] [$tens % 10] ?? 'th' );

  }

  // A share already counted in hundreds - 25 is 25% - written the locale's way: 25.6% in
  // en, 25,6% in nl, 25,6 % in de.

  function padNumberPercentage ( $number, $precision = 0 ) {

    $number    = padNumberRead      ( 'padNumberPercentage', $number );
    $precision = padNumberPrecision ( 'padNumberPercentage', $precision );

    if ( $number === NULL or $number === FALSE or $precision === FALSE )
      return '';

    return padNumberLocal ( 'padNumberPercentage', $number, $precision, padLocaleOf ( '' ), TRUE );

  }

  // The number, or the bound it went past: a page number, a quantity, a slider's value kept
  // within what the page can handle. Both bounds must be numbers, the minimum not above the
  // maximum.

  function padNumberClamp ( $number, $min, $max ) {

    $number = padNumberRead ( 'padNumberClamp', $number );
    $low    = padNumberRead ( 'padNumberClamp', $min, 'minimum' );
    $high   = padNumberRead ( 'padNumberClamp', $max, 'maximum' );

    if ( $low === FALSE or $high === FALSE )
      return NULL;

    if ( $low > $high ) {
      padError ( "padNumberClamp: the minimum $low is above the maximum $high" );
      return NULL;
    }

    if ( $number === NULL or $number === FALSE )
      return NULL;

    return min ( max ( $number, $low ), $high );

  }

  // A byte count for people, in units of 1024: 1536 is 2 KB, or 1.5 KB with a precision of
  // 1. The bytes pipe is this function with a precision of 2 unless it is given one.

  function padNumberFileSize ( $bytes, $precision = 0 ) {

    return padNumberScale ( 'padNumberFileSize', $bytes, $precision, 1024,
                            [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB' ], ' ' );

  }

  // A value as a number: an int or a float as it is, numeric text as the number it holds.
  // NULL, FALSE and empty text are nothing to write - NULL answered, and nothing reported
  // unless the value is a bound ($what names it), which must be there. Anything else - text,
  // an array, TRUE, INF - is reported, and FALSE answered.

  function padNumberRead ( $function, $number, $what = '' ) {

    $the = ( $what === '' ) ? '' : "the $what ";

    if ( $number === NULL or $number === FALSE or ( is_string ( $number ) and trim ( $number ) === '' ) ) {

      if ( $what === '' )
        return NULL;

      padError ( "$function: {$the}is missing" );
      return FALSE;

    }

    $given = $number;

    if ( is_string ( $number ) and is_numeric ( $number ) )
      $number = $number + 0;

    if ( is_int ( $number ) or ( is_float ( $number ) and is_finite ( $number ) ) )
      return $number;

    padError ( "$function: $the" . padValueShow ( $given ) . " is not a number" );

    return FALSE;

  }

  // A precision or a number of decimals: a whole number of 0 or more, numeric text too.
  // NULL is the default, 0. One past any whole number PHP has - INF, 1e20 - is reported
  // too: INF was a PHP warning, and the text '1e20' became PHP_INT_MAX decimals, which
  // number_format answered by running out of memory.

  function padNumberPrecision ( $function, $precision, $what = 'precision' ) {

    if ( $precision === NULL )
      return 0;

    if ( is_numeric ( $precision ) and $precision >= 0 and floor ( (float) $precision ) == $precision and $precision < PHP_INT_MAX ) {

      // At most 100 decimals - far past the 17 digits a float holds: number_format makes a
      // string of that many, and 1e18 of them ended the request out of memory.

      if ( $precision <= 100 )
        return (int) $precision;

      padError ( "$function: the $what " . padValueShow ( $precision ) . " is more than 100" );

      return FALSE;

    }

    padError ( "$function: the $what " . padValueShow ( $precision ) . " is not a whole number of 0 or more" );

    return FALSE;

  }

  // The number with exactly $decimals decimals, grouped, the locale's way - as a percentage
  // when $percent. Rounded first, half up, so intl and number_format agree and a value that
  // rounds to zero is not written -0. The formatters are kept for the request.

  function padNumberLocal ( $function, $number, $decimals, $locale, $percent ) {

    static $formatters = [];

    if ( is_float ( $number ) ) {
      $number = round ( $number, $decimals );
      if ( $number == 0 )
        $number = 0.0;
    }

    if ( ! class_exists ( 'NumberFormatter', FALSE ) )
      return number_format ( $number, $decimals ) . ( $percent ? '%' : '' );

    $key = $locale . '|' . ( $percent ? 'percent' : 'decimal' ) . '|' . $decimals;

    if ( ! isset ( $formatters [$key] ) ) {

      try {
        $format = new NumberFormatter ( $locale, $percent ? NumberFormatter::PERCENT : NumberFormatter::DECIMAL );
      } catch ( Throwable $error ) {
        padError ( "$function: '" . padMakeSafe ( $locale, 40 ) . "' is not a locale" );
        return '';
      }

      // A percentage is handed over in hundreds already: 25 is 25%, not 2500%.

      if ( $percent )
        $format->setAttribute ( NumberFormatter::MULTIPLIER, 1 );

      $format->setAttribute ( NumberFormatter::FRACTION_DIGITS, $decimals );
      $format->setAttribute ( NumberFormatter::ROUNDING_MODE, NumberFormatter::ROUND_HALFUP );

      $formatters [$key] = $format;

    }

    $text = $formatters [$key]->format ( $number );

    if ( $text === FALSE )
      return number_format ( $number, $decimals ) . ( $percent ? '%' : '' );

    return $text;

  }

  // The number in the largest of the units that keeps it under the base, the unit chosen on
  // the number as it will be written: 1048575 bytes rounded to no decimals is 1024 KB, which
  // is 1 MB. After the last unit the number just grows - 5000Q.

  function padNumberScale ( $function, $number, $precision, $base, $units, $glue ) {

    $number    = padNumberRead      ( $function, $number );
    $precision = padNumberPrecision ( $function, $precision );

    if ( $number === NULL or $number === FALSE or $precision === FALSE )
      return '';

    $unit = 0;
    $last = count ( $units ) - 1;

    while ( $unit < $last and abs ( round ( $number / $base ** $unit, $precision ) ) >= $base )
      $unit++;

    $text = padNumberTrim ( $number / $base ** $unit, $precision );

    return ( $units [$unit] === '' ) ? $text : $text . $glue . $units [$unit];

  }

  // At most $precision decimals, a point before them, no thousands separator, and the
  // trailing zeros dropped: 1.50 is 1.5, 2.00 is 2.

  function padNumberTrim ( $number, $precision ) {

    $number = round ( (float) $number, $precision );

    if ( $number == 0 )
      $number = 0.0;

    $text = number_format ( $number, $precision, '.', '' );

    if ( str_contains ( $text, '.' ) )
      $text = rtrim ( rtrim ( $text, '0' ), '.' );

    return $text;

  }

?>
