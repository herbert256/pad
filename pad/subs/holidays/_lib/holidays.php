<?php

  include_once PAD . 'subs/holidays/_lib/merge.php';

  // Public holidays - the holidays subsystem behind {pad:holidays}, the holidays= option of
  // {calendar:month} and the helpers padHolidays, padIsHoliday, padWorkdays and padAddWorkdays
  // (lib/holidays.php). No service, no table to keep up to date: every holiday is a rule, and
  // the rules of a country are the file of subs/holidays/types/ named by its ISO 3166-1
  // alpha-2 code in lower case - nl, de, gb ... - so a country is added by adding its file.
  //
  // A country file answers an array:
  //
  //   name       the country's English name
  //   langs      the languages of its local names, the first the default - a name of more
  //              than one language is [ 'nl' => ..., 'fr' => ... ]
  //   regions    [ code => name ] - the regions with holidays of their own, by the code ISO
  //              3166-2 gives them after the country's: de-by is Bavaria, gb-sct Scotland
  //   substitute how a public holiday on a weekend is made up for, for the days marked
  //              substitute: 'next' (the next weekday that is no holiday yet - gb), 'nearest'
  //              (Saturday to the Friday before, Sunday to the Monday after - us), 'sunday'
  //              (a Sunday only, to the Monday after, a holiday already or not - za),
  //              'sundayNext' (a Sunday only, to the next day that is no holiday yet - jp);
  //              with suffix, what the name of the substitute day gets: ' (substitute day)',
  //              and suffixLocal, what its local name gets where that is another language
  //   substituteFrom  'YYYY-MM-DD' - the first day whose holiday gets a substitute (jp)
  //   between    [ 'from' => year, 'name' => ..., 'local' => ... ] - a day that is no holiday,
  //              between two holidays that are no substitutes, is a holiday of that name
  //              since the year from, a Sunday left out: Japan's Citizens' Holiday
  //   years      [ first, last ] - the years the rules are known to be right for, where the
  //              file says so: a country whose holidays begin with a law or end with a table
  //              (New Zealand's Matariki dates run to 2052). A year outside them is an error
  //              of {pad:holidays} and of the calendar's holidays= under the strict check, and of
  //              the helpers; a file without it is computed for every year, 1583 to 9999
  //   days       the holidays, each a rule:
  //
  //     date        'MM-DD', the same date every year
  //     easter      a number of days from Easter Sunday (Gregorian computus): -2 Good Friday,
  //                 39 Ascension Day, 50 Whit Monday
  //     orthodox    a number of days from the Easter Sunday of the Orthodox churches (the
  //                 Julian computus, as a Gregorian date): -48 Clean Monday, 50 Whit Monday
  //     nth         [ n, weekday, month ] - the nth weekday of the month, n -1 the last:
  //                 [ 4, 'thu', 11 ] Thanksgiving, [ -1, 'mon', 5 ] Memorial Day
  //     onOrAfter   [ weekday, 'MM-DD' ] - the first such weekday on or after the date;
  //     onOrBefore  the last on or before it: [ 'mon', '05-24' ] Victoria Day
  //     calc        fn ( $year ) => 'YYYY-MM-DD' or NULL, for a rule that is no data
  //     from, to    the first and the last year the holiday is held
  //     moved       [ year => 'MM-DD' ] - the years it was held on another date
  //     sunday      -1 or 1: a day falling on a Sunday is held the day before or after
  //     substitute  TRUE: falling on a weekend, the country's substitute rule gives it a
  //                 day of its own; or a rule of its own - 'monday', the Monday after it,
  //                 whether that is a holiday already or not (New Zealand's Anzac Day)
  //     type        'observance' for a day that is no day off; publicEvery = n makes it a
  //                 public holiday in the years that divide by n only (nl's 5 May)
  //     in, not     the regions it is held in only, or the regions it is not held in
  //
  // A holiday is a row: date (YYYY-MM-DD), name (English), local (the name in the country's
  // language), type (public or observance), region (the region code of a regional holiday,
  // else '') and substitute (TRUE for the day that makes up for one on a weekend).
  //
  // padHolidaysCountry  the rules of a country, read once per request, or an error message
  // padHolidaysPlace    'de-by', or 'de' with a region, as [ country, region ] - or a message
  // padHolidaysKnown    '' for a span of days in the years a country's rules are known for,
  //                     else the message that says which they are
  // padHolidaysRange    the rows between two day numbers
  // padHolidaysOff      the days off between two day numbers: weekends and public holidays
  // padHolidaysDay      a date as a day number - days from 1970-01-01 - and back with
  //                     padHolidaysCivil; padHolidaysWeekday the ISO weekday of one
  // padHolidaysEaster   Easter Sunday of a year; padHolidaysOrthodox the Orthodox one
  // padHolidaysTable    the rows as the table the single {pad:holidays/} writes
  //
  // The arithmetic is integer days, no clock and no zone: the same rules, the same rows, on
  // every server and in pad pack's WebAssembly PHP.

  const padHolidaysWeekdays = [ 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7 ];

  // The years a rule is computed for: the Gregorian calendar's, from its first full year.

  const padHolidaysFirst = 1583;
  const padHolidaysLast  = 9999;

  function padHolidaysCountry ( $country ) {

    static $countries = [];

    $country = strtolower ( trim ( (string) $country ) );

    if ( ! isset ( $countries [$country] ) ) {

      if ( ! padKind ( 'holidays', $country ) )
        return "there are no holidays of '" . padMakeSafe ( $country, 20 ) . "' - " . implode ( ', ', padKinds ( 'holidays' ) );

      $countries [$country] = ( include PAD . "subs/holidays/types/$country.php" )
                            + [ 'langs' => [ 'en' ], 'regions' => [], 'substitute' => '', 'suffix' => '', 'years' => [ padHolidaysFirst, padHolidaysLast ],
                                'substituteFrom' => '', 'between' => NULL ];

    }

    return $countries [$country];

  }

  // A place: the country with its region, written 'de-by' or 'de' and 'by' apart, in any case.
  // An error message for a country without a file or a region the country does not list -
  // and for a country or region that is no value: a list met the (string) cast and ended
  // the request in PHP's "Array to string conversion".

  function padHolidaysPlace ( $country, $region = '' ) {

    $region ??= '';

    if ( ! is_scalar ( $country ) or ! is_scalar ( $region ) )
      return 'a country is its code, with its region after it - nl, de-by - not ' . get_debug_type ( is_scalar ( $country ) ? $region : $country );

    $country = strtolower ( trim ( (string) $country ) );
    $region  = strtolower ( trim ( (string) $region ) );

    if ( $region === '' and preg_match ( '/^([a-z]{2})[-_ ]([a-z0-9]{1,3})$/', $country, $m ) )
      [ , $country, $region ] = $m;

    $rules = padHolidaysCountry ( $country );

    if ( is_string ( $rules ) )
      return $rules;

    if ( $region !== '' and ! isset ( $rules ['regions'] [$region] ) )
      return $rules ['regions']
           ? "$country has no region '" . padMakeSafe ( $region, 20 ) . "' - " . implode ( ', ', array_keys ( $rules ['regions'] ) )
           : "$country has no regions with holidays of their own";

    return [ $country, $region ];

  }

  // The 'years' of a country's file for a span from day $from to day $to: '' when the span
  // lies in them, else a message naming them and the year outside - the rows of such a year
  // would leave out what no rule knows, New Zealand's Matariki after 2052.

  function padHolidaysKnown ( $country, $from, $to ) {

    $rules = padHolidaysCountry ( $country );

    if ( is_string ( $rules ) )
      return $rules;

    [ $first, $last ] = $rules ['years'];

    $a = padHolidaysCivil ( $from ) [0];
    $b = padHolidaysCivil ( $to   ) [0];

    if ( $a >= $first and $b <= $last )
      return '';

    return "the holidays of {$rules ['name']} are known for $first to $last - not " . ( $a < $first ? $a : $b );

  }

  // Day numbers, the days since 1970-01-01 - Howard Hinnant's days_from_civil and back.

  function padHolidaysDay ( $y, $m, $d ) {

    $y  -= ( $m <= 2 ) ? 1 : 0;
    $era = intdiv ( $y >= 0 ? $y : $y - 399, 400 );
    $yoe = $y - $era * 400;
    $doy = intdiv ( 153 * ( $m + ( $m > 2 ? -3 : 9 ) ) + 2, 5 ) + $d - 1;
    $doe = $yoe * 365 + intdiv ( $yoe, 4 ) - intdiv ( $yoe, 100 ) + $doy;

    return $era * 146097 + $doe - 719468;

  }

  function padHolidaysCivil ( $z ) {

    $z  += 719468;
    $era = intdiv ( $z >= 0 ? $z : $z - 146096, 146097 );
    $doe = $z - $era * 146097;
    $yoe = intdiv ( $doe - intdiv ( $doe, 1460 ) + intdiv ( $doe, 36524 ) - intdiv ( $doe, 146096 ), 365 );
    $doy = $doe - ( 365 * $yoe + intdiv ( $yoe, 4 ) - intdiv ( $yoe, 100 ) );
    $mp  = intdiv ( 5 * $doy + 2, 153 );
    $d   = $doy - intdiv ( 153 * $mp + 2, 5 ) + 1;
    $m   = ( $mp < 10 ) ? $mp + 3 : $mp - 9;

    return [ $yoe + $era * 400 + ( $m <= 2 ? 1 : 0 ), $m, $d ];

  }

  function padHolidaysDate ( $z ) {

    [ $y, $m, $d ] = padHolidaysCivil ( $z );

    return sprintf ( '%04d-%02d-%02d', $y, $m, $d );

  }

  // 1 for Monday to 7 for Sunday: 1970-01-01, day 0, was a Thursday.

  function padHolidaysWeekday ( $z ) {

    return ( ( $z % 7 ) + 7 + 3 ) % 7 + 1;

  }

  // A value as a day number - a date as text (2026-04-27, 27 April 2026, tomorrow), a
  // timestamp or a date object, read in the application's zone (padDateParse) - else NULL.

  function padHolidaysValue ( $value ) {

    if ( is_string ( $value ) and preg_match ( '/^\s*(\d{4})-(\d{2})-(\d{2})\s*$/', $value, $m ) )
      return checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) ? padHolidaysDay ( (int) $m [1], (int) $m [2], (int) $m [3] ) : NULL;

    $date = padDateParse ( $value );

    if ( ! $date )
      return NULL;

    return padHolidaysDay ( (int) $date->format ( 'Y' ), (int) $date->format ( 'n' ), (int) $date->format ( 'j' ) );

  }

  // Easter Sunday by the Gregorian computus - the anonymous algorithm of 1876 (Meeus, Jones,
  // Butcher): 2024-03-31, 2025-04-20, 2026-04-05, 2027-03-28, 2038-04-25.

  function padHolidaysEaster ( $y ) {

    $a = $y % 19;
    $b = intdiv ( $y, 100 );
    $c = $y % 100;
    $d = intdiv ( $b, 4 );
    $e = $b % 4;
    $f = intdiv ( $b + 8, 25 );
    $g = intdiv ( $b - $f + 1, 3 );
    $h = ( 19 * $a + $b - $d - $g + 15 ) % 30;
    $i = intdiv ( $c, 4 );
    $k = $c % 4;
    $l = ( 32 + 2 * $e + 2 * $i - $h - $k ) % 7;
    $m = intdiv ( $a + 11 * $h + 22 * $l, 451 );
    $n = $h + $l - 7 * $m + 114;

    return padHolidaysDay ( $y, intdiv ( $n, 31 ), $n % 31 + 1 );

  }

  // Easter Sunday of the Orthodox churches: the Julian computus (Meeus) gives a date of the
  // Julian calendar, from 22 March to 25 April, and the days the Julian calendar lags behind
  // the Gregorian one make it a Gregorian date - 13 from 1900 to 2099, 14 from March 2100,
  // past the Julian 29 February that year has and the Gregorian one has not: 2024-05-05,
  // 2025-04-20, 2026-04-12, 2027-05-02.

  function padHolidaysOrthodox ( $y ) {

    $d = ( 19 * ( $y % 19 ) + 15 ) % 30;
    $e = ( 2 * ( $y % 4 ) + 4 * ( $y % 7 ) - $d + 34 ) % 7;
    $n = $d + $e + 114;

    return padHolidaysDay ( $y, intdiv ( $n, 31 ), $n % 31 + 1 ) + intdiv ( $y, 100 ) - intdiv ( $y, 400 ) - 2;

  }

  // 'MM-DD' of a year as a day number, NULL for a date the year has not (02-29).

  function padHolidaysMonthDay ( $year, $md ) {

    [ $m, $d ] = array_map ( 'intval', explode ( '-', $md ) );

    return checkdate ( $m, $d, $year ) ? padHolidaysDay ( $year, $m, $d ) : NULL;

  }

  // The day of a rule in a year, or NULL: not held that year, or no such day.

  function padHolidaysRule ( $day, $year ) {

    if ( $year < ( $day ['from'] ?? padHolidaysFirst ) or $year > ( $day ['to'] ?? padHolidaysLast ) )
      return NULL;

    if ( isset ( $day ['moved'] [$year] ) )
      return padHolidaysMonthDay ( $year, $day ['moved'] [$year] );

    if ( isset ( $day ['date'] ) )
      $z = padHolidaysMonthDay ( $year, $day ['date'] );

    elseif ( isset ( $day ['easter'] ) )
      $z = padHolidaysEaster ( $year ) + $day ['easter'];

    elseif ( isset ( $day ['orthodox'] ) )
      $z = padHolidaysOrthodox ( $year ) + $day ['orthodox'];

    elseif ( isset ( $day ['nth'] ) ) {

      [ $n, $weekday, $month ] = $day ['nth'];

      $want = padHolidaysWeekdays [$weekday];

      if ( $n > 0 ) {
        $first = padHolidaysDay ( $year, $month, 1 );
        $z     = $first + ( $want - padHolidaysWeekday ( $first ) + 7 ) % 7 + 7 * ( $n - 1 );
      } else {
        $last = padHolidaysDay ( $year, $month + 1, 1 ) - 1;
        $z    = $last - ( padHolidaysWeekday ( $last ) - $want + 7 ) % 7 - 7 * ( - $n - 1 );
      }

      if ( padHolidaysCivil ( $z ) [1] != $month )
        $z = NULL;

    } elseif ( isset ( $day ['onOrAfter'] ) ) {

      $z = padHolidaysMonthDay ( $year, $day ['onOrAfter'] [1] );
      $z = $z + ( padHolidaysWeekdays [ $day ['onOrAfter'] [0] ] - padHolidaysWeekday ( $z ) + 7 ) % 7;

    } elseif ( isset ( $day ['onOrBefore'] ) ) {

      $z = padHolidaysMonthDay ( $year, $day ['onOrBefore'] [1] );
      $z = $z - ( padHolidaysWeekday ( $z ) - padHolidaysWeekdays [ $day ['onOrBefore'] [0] ] + 7 ) % 7;

    } elseif ( isset ( $day ['calc'] ) ) {

      $date = ( $day ['calc'] ) ( $year );
      $z    = $date ? padHolidaysValue ( $date ) : NULL;

    } else
      $z = NULL;

    if ( $z !== NULL and isset ( $day ['sunday'] ) and padHolidaysWeekday ( $z ) == 7 )
      $z += $day ['sunday'];

    return $z;

  }

  // Whether a rule holds in a region: '' is the country without a region - the days limited
  // to some regions (in) are not there, the days some regions lack (not) are.

  function padHolidaysIn ( $day, $region ) {

    if ( isset ( $day ['in'] ) )
      return $region !== '' and in_array ( $region, $day ['in'], TRUE );

    if ( isset ( $day ['not'] ) )
      return $region === '' or ! in_array ( $region, $day ['not'], TRUE );

    return TRUE;

  }

  // The name of a holiday in a language of the country: the first of its languages unless
  // lang= asks for another it has.

  function padHolidaysLocal ( $day, $lang ) {

    $local = $day ['local'] ?? $day ['name'];

    if ( ! is_array ( $local ) )
      return $local;

    return $local [$lang] ?? reset ( $local );

  }

  // The rows of a place from day $from to day $to, sorted by date, a day of two holidays in
  // the order of the file. The years around are computed as well, for a substitute day or
  // a move that crosses the turn of the year - 2022-01-01, a Saturday, was observed in the
  // United States on Friday 2021-12-31, a day of 2021.

  function padHolidaysRange ( $country, $region, $from, $to, $lang = '' ) {

    $rules = padHolidaysCountry ( $country );

    if ( is_string ( $rules ) )
      return [];

    $lang  = in_array ( $lang, $rules ['langs'], TRUE ) ? $lang : $rules ['langs'] [0];
    $first = max ( padHolidaysFirst, padHolidaysCivil ( $from ) [0] - 1 );
    $last  = min ( padHolidaysLast,  padHolidaysCivil ( $to   ) [0] + 1 );
    $found = [];

    for ( $year = $first; $year <= $last; $year++ )
      foreach ( $rules ['days'] as $n => $day ) {

        if ( ! padHolidaysIn ( $day, $region ) )
          continue;

        $z = padHolidaysRule ( $day, $year );

        if ( $z === NULL )
          continue;

        $type = $day ['type'] ?? 'public';

        if ( isset ( $day ['publicEvery'] ) )
          $type = ( $year % $day ['publicEvery'] == 0 ) ? 'public' : 'observance';

        $found [] = [ $z, $n, [
          'date'       => padHolidaysDate ( $z ),
          'name'       => $day ['name'],
          'local'      => padHolidaysLocal ( $day, $lang ),
          'type'       => $type,
          'region'     => isset ( $day ['in'] ) ? $region : '',
          'substitute' => FALSE
        ], ( $type == 'public' ) ? ( $day ['substitute'] ?? FALSE ) : FALSE ];

      }

    usort ( $found, fn ( $a, $b ) => [ $a [0], $a [1] ] <=> [ $b [0], $b [1] ] );

    if ( $rules ['substitute'] !== '' )
      $found = padHolidaysSubstitutes ( $found, $rules ['substitute'], $rules ['suffix'], $rules ['suffixLocal'] ?? $rules ['suffix'],
                                        $rules ['substituteFrom'] !== '' ? padHolidaysValue ( $rules ['substituteFrom'] ) : NULL );

    if ( $rules ['between'] )
      $found = padHolidaysBetween ( $found, $rules ['between'], $lang, count ( $rules ['days'] ) );

    $rows = [];

    foreach ( $found as [ $z, , $row ] )
      if ( $z >= $from and $z <= $to )
        $rows [] = $row;

    return $rows;

  }

  // The substitute days, in date order: 'next' gives a holiday on a Saturday or Sunday the
  // first weekday after it that is no holiday yet, counting the substitutes given before it
  // - in Britain Christmas on a Saturday is made up for on Monday the 27th and Boxing Day on
  // the Sunday on Tuesday the 28th; 'nearest' moves Saturday to the Friday before and Sunday
  // to the Monday after - the United States' observed days; 'sunday' leaves a Saturday be and
  // makes up for a Sunday on the Monday after, a holiday already or not, as South Africa's
  // act says - Christmas on a Sunday gives no day more than the Day of Goodwill. A holiday
  // with a rule of its own follows that: 'monday' is the Monday after it, a holiday already
  // or not - New Zealand's Anzac Day on Sunday 25 April 2038 is observed on Easter Monday.
  // 'sundayNext' makes up for a Sunday on the next day that is no holiday yet, whatever its
  // weekday: Japan's Constitution Memorial Day on Sunday 3 May 2026 gives Wednesday the 6th,
  // after Greenery Day and Children's Day. A holiday before the day $since gets none.

  function padHolidaysSubstitutes ( $found, $how, $suffix, $suffixLocal = NULL, $since = NULL ) {

    $taken = [];

    foreach ( $found as [ $z, , $row ] )
      if ( $row ['type'] == 'public' )
        $taken [$z] = TRUE;

    $extra = [];

    foreach ( $found as [ $z, $n, $row, $substitute ] ) {

      $weekday = padHolidaysWeekday ( $z );

      if ( ! $substitute or $weekday < 6 or ( $since !== NULL and $z < $since ) )
        continue;

      $rule = ( $substitute === TRUE ) ? $how : $substitute;

      if ( ( $rule == 'sunday' or $rule == 'sundayNext' ) and $weekday == 6 )
        continue;

      if ( $rule == 'nearest' )
        $day = ( $weekday == 6 ) ? $z - 1 : $z + 1;
      elseif ( $rule == 'monday' or $rule == 'sunday' )
        $day = $z + 8 - $weekday;
      elseif ( $rule == 'sundayNext' )
        for ( $day = $z + 1; isset ( $taken [$day] ); $day++ ) ;
      else
        for ( $day = $z + 1; padHolidaysWeekday ( $day ) >= 6 or isset ( $taken [$day] ); $day++ ) ;

      $taken [$day] = TRUE;

      $extra [] = [ $day, $n, array_merge ( $row, [ 'date'       => padHolidaysDate ( $day ),
                                                    'name'       => $row ['name']  . $suffix,
                                                    'local'      => $row ['local'] . ( $suffixLocal ?? $suffix ),
                                                    'substitute' => TRUE ] ), FALSE ];

    }

    $found = array_merge ( $found, $extra );

    usort ( $found, fn ( $a, $b ) => [ $a [0], $a [2] ['substitute'], $a [1] ] <=> [ $b [0], $b [2] ['substitute'], $b [1] ] );

    return $found;

  }

  // The days between two holidays, from the year of the rule's from: a day that is no
  // holiday - nor a substitute day - with a holiday that is no substitute day before and
  // after it is a holiday of the rule's name. A Sunday is left out: Japan's law excluded it
  // until 2006, and it is no workday either way. Japan's Citizens' Holiday - 4 May from 1988
  // to 2006, 22 September 2026 between Respect for the Aged Day and the equinox, 30 April
  // and 2 May 2019 around the enthronement. $n orders it after the file's own days.

  function padHolidaysBetween ( $found, $rule, $lang, $n ) {

    $holiday = [];
    $taken   = [];

    foreach ( $found as [ $z, , $row ] )
      if ( $row ['type'] == 'public' ) {
        $taken [$z] = TRUE;
        if ( ! $row ['substitute'] )
          $holiday [$z] = TRUE;
      }

    $extra = [];

    foreach ( array_keys ( $holiday ) as $z )
      if ( isset ( $holiday [$z + 2] ) and ! isset ( $taken [$z + 1] ) and padHolidaysWeekday ( $z + 1 ) != 7
           and padHolidaysCivil ( $z + 1 ) [0] >= $rule ['from'] ) {
        $taken [$z + 1] = TRUE;
        $extra []       = [ $z + 1, $n, [ 'date'       => padHolidaysDate ( $z + 1 ),
                                          'name'       => $rule ['name'],
                                          'local'      => padHolidaysLocal ( $rule, $lang ),
                                          'type'       => 'public',
                                          'region'     => '',
                                          'substitute' => FALSE ], FALSE ];
      }

    $found = array_merge ( $found, $extra );

    usort ( $found, fn ( $a, $b ) => [ $a [0], $a [2] ['substitute'], $a [1] ] <=> [ $b [0], $b [2] ['substitute'], $b [1] ] );

    return $found;

  }

  // The days off between two day numbers - Saturdays, Sundays and the public holidays of the
  // place - as [ day number => TRUE ].

  function padHolidaysOff ( $country, $region, $from, $to ) {

    $off = [];

    foreach ( padHolidaysRange ( $country, $region, $from, $to ) as $row )
      if ( $row ['type'] == 'public' )
        $off [ padHolidaysValue ( $row ['date'] ) ] = TRUE;

    return $off;

  }

  function padHolidaysWorkday ( $z, $off ) {

    return padHolidaysWeekday ( $z ) < 6 and ! isset ( $off [$z] );

  }

  // The rows as a table: the date in the request's locale, the name in English with the
  // local name beside it in its own language, an observance marked as no day off.

  function padHolidaysTable ( $rows, $caption, $lang ) {

    $h    = fn ( $t ) => htmlspecialchars ( padUnprotectQuotes ( (string) $t ), ENT_QUOTES, 'UTF-8' );
    $html = padHolidaysStyle () . '<table class="pad-holidays"><caption>' . $h ( $caption ) . '</caption>'
          . '<thead><tr><th scope="col">Date</th><th scope="col">Holiday</th></tr></thead><tbody>';

    include_once PAD . 'subs/calendars/_lib/calendar.php';

    foreach ( $rows as $row ) {

      $day   = new DateTimeImmutable ( $row ['date'], new DateTimeZone ( 'UTC' ) );
      $html .= '<tr' . ( $row ['type'] != 'public' ? ' class="observance"' : '' ) . '>'
             . '<td><time datetime="' . $row ['date'] . '">' . $h ( padCalendarWord ( $day, 'EEE d MMM', 'D j M' ) ) . '</time></td>'
             . '<td>' . $h ( $row ['name'] )
             . ( $row ['local'] !== $row ['name'] ? ' <span lang="' . $h ( $lang ) . '">' . $h ( $row ['local'] ) . '</span>' : '' )
             . ( $row ['type'] != 'public' ? ' <small>(no day off)</small>' : '' )
             . '</td></tr>';

    }

    return "$html</tbody></table>";

  }

  // A light default look, once per page, every rule under :where() so a page's own CSS wins.

  function padHolidaysStyle () {

    if ( ! padWidgetOnce ( 'style:holidays' ) )
      return '';

    return '<style' . padStyleNonce () . '>'
         . ':where(.pad-holidays){border-collapse:collapse;font-size:14px}'
         . ':where(.pad-holidays caption){padding:6px 0;font-weight:600;text-align:left}'
         . ':where(.pad-holidays th,.pad-holidays td){padding:3px 12px 3px 0;text-align:left;vertical-align:top}'
         . ':where(.pad-holidays th){font-size:12px;opacity:.7}'
         . ':where(.pad-holidays td:first-child){white-space:nowrap;font-variant-numeric:tabular-nums}'
         . ':where(.pad-holidays span){opacity:.7}'
         . ':where(.pad-holidays tr.observance){opacity:.6}'
         . '</style>';

  }

?>
