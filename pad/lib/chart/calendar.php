<?php

  // A year at a glance: a square per day, a column per week, coloured by the day's value.
  //
  //   {chart 'calendar', data='commits', date='day', value='count'}
  //   {chart 'calendar', data='visits', year=2026}
  //
  // date= names the field with the date - anything strtotime reads, 2026-03-14 best - else
  // the first field that is no number. value= is the number - else the first numeric
  // field, else every row counts as one. Rows of one day add up. The days run from the
  // Monday before the first date to the last date, or over the whole year= when it is
  // given - the latest weeks that fit the width, ten years at most. A day without a value is drawn in the grid colour, the others in seven steps
  // from light to dark; a day has its tooltip, the months are named above their first
  // week, Monday, Wednesday and Friday on the left.

  // A date as seconds since 1970 of its wall clock, written as if in UTC - what gmdate reads
  // back as the day and time a reader sees. A date written without a zone is taken as it
  // stands; a moment - a Unix timestamp, a date with its offset or zone - as it is in the
  // application's timezone, the one today is: a timestamp and an offset date fell on their
  // UTC day, the day before for an event at local midnight east of Greenwich.

  function padChartStamp ( $when ) {

    // A date object of the page's PHP is its moment with its offset: cast to text it ended
    // the gantt on "could not be converted to string".

    if ( $when instanceof DateTimeInterface )
      $when = $when->format ( 'Y-m-d H:i:sP' );

    if ( ! is_scalar ( $when ) )
      return FALSE;

    // Read as the characters it holds: a field inside a data= literal came with the
    // stand-ins of a value, and Oct 8, 2026 was no date.

    $when = trim ( padUnprotect ( (string) $when ) );

    if ( $when === '' )
      return FALSE;

    // Eight digits that name a day - 20261012, ISO 8601's basic form, as strtotime reads it -
    // are that day: as seconds they were a moment of 1970, and only 1970 to 1973 is one.

    if ( padChartBasicDate ( $when ) )
      $when = substr ( $when, 0, 4 ) . '-' . substr ( $when, 4, 2 ) . '-' . substr ( $when, 6, 2 );

    // Text is a date when it names a day of a month of a year - a time and a zone may come
    // with it - and nothing it cannot read: -44 was tomorrow, 1990s the clock time of today
    // in 1990, tomorrow a moment that moved on every request and 2026-02-30 the 2nd of March.

    // A weekday the date names - Mon, 12 Oct 2026, as RSS, mail and HTTP write one - moves
    // nothing and is no reason to refuse it; any other relative part is.

    if ( ! ctype_digit ( $when ) ) {
      $parts    = date_parse ( $when );
      $relative = $parts ['relative'] ?? [];
      unset ( $relative ['weekday'], $relative ['weekdays'] );
      if ( $parts ['error_count'] or $parts ['warning_count'] or array_filter ( $relative )
           or $parts ['year'] === FALSE or $parts ['month'] === FALSE or $parts ['day'] === FALSE )
        return FALSE;
    }

    try {
      $moment = ctype_digit ( $when ) ? new DateTimeImmutable ( "@$when" ) : new DateTimeImmutable ( $when, padDateZone () );
    } catch ( Throwable $e ) {
      return FALSE;
    }

    // The local wall clock read again as UTC - not through gmmktime, whose rule for a year
    // of two digits put 0079-08-24 in 1979.

    $local = $moment->setTimezone ( padDateZone () );

    // A moment past the year 9999 - a millisecond timestamp, 1760000000000 - is no day a
    // calendar shows: written back as 57742-03-07 it was read as 2002.

    if ( (int) $local->format ( 'Y' ) > 9999 )
      return FALSE;

    return ( new DateTimeImmutable ( $local->format ( 'Y-m-d H:i:s' ), new DateTimeZone ( 'UTC' ) ) )->getTimestamp ();

  }

  function padChartBasicDate ( $when ) {

    return (bool) preg_match ( '/^(\d{4})(\d{2})(\d{2})$/', (string) $when, $m ) and checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] );

  }

  function padChartCalendar ( $rows, $width, $height ) {

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $dField = padChartPick ( 'date',  $names,   $used );
    $vField = padChartPick ( 'value', $numbers, $used );
    $utc    = new DateTimeZone ( 'UTC' );
    $days   = [];

    foreach ( (array) $rows as $row ) {

      $row  = padChartRow ( $row );
      $when = padChartText ( $row, $dField );
      $v    = ( $vField !== '' ) ? ( $row [$vField] ?? NULL ) : 1;

      if ( $when === '' or ! padChartFinite ( $v ) or ( $stamp = padChartStamp ( $when ) ) === FALSE )
        continue;

      $day = gmdate ( 'Y-m-d', $stamp );
      $days [$day] = ( $days [$day] ?? 0 ) + $v;

    }

    $year = (int) padTagParm ( 'year', 0 );

    if ( $year ) {
      $first = new DateTimeImmutable ( "$year-01-01", $utc );
      $last  = new DateTimeImmutable ( "$year-12-31", $utc );
    } elseif ( $days ) {
      ksort ( $days );
      $first = new DateTimeImmutable ( array_key_first ( $days ), $utc );
      $last  = new DateTimeImmutable ( array_key_last  ( $days ), $utc );
    } else
      return '';

    // The latest weeks a column of at least 3px each fits in the width - more than about
    // four years in 720px drew the latest days outside the picture - and ten years at most.

    $keep = min ( 3660, 7 * ( max ( 1, (int) floor ( ( $width - 34 ) / 3 ) ) - 1 ) );

    if ( $last->diff ( $first )->days > $keep )
      $first = $last->modify ( "-$keep days" );

    $start = $first->modify ( '-' . ( (int) $first->format ( 'N' ) - 1 ) . ' days' );
    $weeks = intdiv ( $last->diff ( $start )->days, 7 ) + 1;
    $shown = array_filter ( $days, fn ( $d ) => $d >= $first->format ( 'Y-m-d' ) and $d <= $last->format ( 'Y-m-d' ), ARRAY_FILTER_USE_KEY );
    $max   = $shown ? max ( max ( $shown ), 0 ) : 0;
    $total = array_sum ( $shown );
    $label = $vField !== '' ? $vField : 'rows';
    $desc  = [ padChartNumber ( $total ) . " $label from " . $first->format ( 'M j, Y' ) . ' to ' . $last->format ( 'M j, Y' ) . ', on ' . count ( array_filter ( $shown ) ) . ' days' ];

    if ( $shown ) {
      arsort ( $shown );
      $desc [] = 'highest ' . padChartNumber ( reset ( $shown ) ) . ' on ' . gmdate ( 'M j, Y', strtotime ( key ( $shown ) . ' UTC' ) );
      ksort ( $shown );
    }

    $title = (string) padTagParm ( 'title', ucfirst ( $label ) . ' per day' );

    list ( $id, $svg ) = padChartOpen ( 'calendar', [ $shown, $first->format ( 'Y-m-d' ), $last->format ( 'Y-m-d' ) ], $desc, $title, $width, $height );

    $left = 30;
    $top  = 16;
    $cell = max ( 3, min ( ( $width - $left - 4 ) / $weeks, ( $height - $top - 26 ) / 7 ) );
    $gap  = $cell >= 8 ? 2 : 1;

    foreach ( [ 0 => 'Mon', 2 => 'Wed', 4 => 'Fri' ] as $row => $name )
      $svg .= '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $top + $row * $cell + $cell / 2 + 3 ) . '" text-anchor="end">' . $name . '</text>';

    $lastMonth = '';
    $lastLabel = -9;

    for ( $week = 0; $week < $weeks; $week++ )
      for ( $dow = 0; $dow < 7; $dow++ ) {

        $date = $start->modify ( '+' . ( $week * 7 + $dow ) . ' days' );
        $day  = $date->format ( 'Y-m-d' );

        if ( $date < $first or $date > $last )
          continue;

        $x = $left + $week * $cell;
        $y = $top + $dow * $cell;

        if ( $date->format ( 'Y-m' ) != $lastMonth ) {
          $lastMonth = $date->format ( 'Y-m' );
          if ( $week - $lastLabel >= 3 and ( $date->format ( 'j' ) <= 7 or $week == 0 ) ) {
            $svg .= '<text x="' . padChartXY ( $x ) . '" y="11">' . $date->format ( 'M' ) . '</text>';
            $lastLabel = $week;
          }
        }

        $v     = $days [$day] ?? 0;
        $class = ( $v > 0 and $max > 0 ) ? 'pc-h' . max ( 0, min ( 6, (int) ceil ( $v / $max * 7 ) - 1 ) ) : 'pc-none';

        $svg .= '<rect class="pc-mark ' . $class . '" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $cell - $gap ) . '" height="' . padChartXY ( $cell - $gap ) . '" rx="' . ( $cell >= 8 ? 2 : 0 ) . '">'
              . '<title>' . padChartAttr ( $date->format ( 'D M j, Y' ) . ': ' . padChartNumber ( $v ) ) . '</title></rect>';

      }

    // The scale under the grid, on the right: less, the steps, more.

    $y     = $top + 7 * $cell + 8;
    $size  = min ( 11, max ( 6, $cell - $gap ) );
    $x     = max ( $left + 32, $left + $weeks * $cell - 8 * ( $size + 2 ) - 30 );
    $svg  .= '<text x="' . padChartXY ( $x - 6 ) . '" y="' . padChartXY ( $y + $size - 1 ) . '" text-anchor="end">Less</text>'
           . '<rect class="pc-none" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . '" width="' . $size . '" height="' . $size . '" rx="2"/>';

    for ( $k = 0; $k <= 6; $k++ )
      $svg .= '<rect class="pc-h' . $k . '" x="' . padChartXY ( $x + ( $k + 1 ) * ( $size + 2 ) ) . '" y="' . padChartXY ( $y ) . '" width="' . $size . '" height="' . $size . '" rx="2"/>';

    $svg .= '<text x="' . padChartXY ( $x + 8 * ( $size + 2 ) + 4 ) . '" y="' . padChartXY ( $y + $size - 1 ) . '">More</text>';

    return "$svg</svg>";

  }

?>
