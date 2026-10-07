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
  // given. A day without a value is drawn in the grid colour, the others in seven steps
  // from light to dark; a day has its tooltip, the months are named above their first
  // week, Monday, Wednesday and Friday on the left.

  // A date as seconds since 1970, UTC: digits alone are such a number already.

  function padChartStamp ( $when ) {

    $when = trim ( (string) $when );

    if ( $when === '' )
      return FALSE;

    return ctype_digit ( $when ) ? (int) $when : strtotime ( "$when UTC" );

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

    if ( $last->diff ( $first )->days > 3660 )
      $first = $last->modify ( '-3660 days' );

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
