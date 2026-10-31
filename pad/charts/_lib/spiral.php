<?php

  // A long series wound up: a turn per period, so a season lies along a spoke and the
  // years lie outward ring after ring.
  //
  //   {chart 'spiral', data='sales', label='month', value='amount', turn='year'}
  //   {chart 'spiral', data='visits', value='count', period=7}
  //
  // The rows go clockwise from twelve o'clock along an Archimedean spiral, period= of them
  // to a turn (12; 7 for the days of a week, 52 for weeks). Each row is a segment of the
  // band, coloured by its value= - else the first numeric field - in the seven steps of
  // the heat ramp, the scale under the spiral. label= names the rows; the labels of the
  // first turn are written around the outside, one per position. turn= names a field whose
  // value is written where each turn begins - a year - and goes in the tooltips.

  function padChartSpiral ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $value  = padChartPick ( 'value', $numbers, $used );
    $label  = padChartPick ( 'label', $names,   $used );
    $turn   = (string) padTagParm ( 'turn' );
    $period = padTagParm ( 'period', 12 );

    if ( ! is_numeric ( $period ) or $period < 2 or $period != (int) $period ) {
      if ( $padCheckSyntax )
        padError ( "the period of a spiral is a whole number of rows to a turn, 2 or more - not '" . padMakeSafe ( $period, 20 ) . "'" );
      $period = 12;
    }

    $period = (int) $period;
    $points = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $v   = $row [$value] ?? NULL;

      if ( ! padChartFinite ( $v ) )
        continue;

      $text      = padChartText ( $row, $label );
      $points [] = [ $text !== '' ? $text : (string) $index, $v + 0, padChartText ( $row, $turn ) ];

    }

    $n = count ( $points );

    if ( ! $n )
      return '';

    $vals = array_column ( $points, 1 );
    $min  = min ( $vals );
    $max  = max ( $vals );
    $span = $max - $min;
    $step = fn ( $v ) => ( is_finite ( $span ) and $span > 0 ) ? (int) round ( ( $v - $min ) / $span * 6 ) : 6;
    $name = fn ( $p ) => $p [0] . ( $p [2] !== '' ? " {$p [2]}" : '' );
    $desc = [];

    foreach ( $points as $p )
      $desc [] = $name ( $p ) . ': ' . padChartNumber ( $p [1] );

    $title = (string) padTagParm ( 'title', ucfirst ( $value ) . ( $label !== '' ? " by $label" : '' ) . ", $period to a turn" );

    list ( $id, $svg ) = padChartOpen ( 'spiral', [ $points, $period ], $desc, $title, $width, $height );

    // The room: the labels of the first turn around the outside, the scale at the foot.

    $labelW = 0;
    if ( $label !== '' )
      for ( $q = 0; $q < min ( $period, $n ); $q++ )
        $labelW = max ( $labelW, padChartWidth ( $points [$q] [0] ) );

    $roomX = ( $label !== '' ) ? $labelW + 14 : 6;
    $roomY = ( $label !== '' ) ? 20 : 6;
    $scale = 26;
    $R     = max ( 10, min ( $width / 2 - $roomX, ( $height - $scale ) / 2 - $roomY ) );
    $cx    = $width / 2;
    $cy    = $roomY + $R + 4;

    // The band: w apart from turn to turn, 90% of that wide, from r0 at the start - so wide
    // a hole in the middle that the first turn still has room for a label.

    $turns = $n / $period;
    $w     = 0.78 * $R / ( $turns + 0.5 );
    $r0    = 0.22 * $R;

    if ( $r0 < 0.6 * $w ) {
      $w  = $R / ( $turns + 1.1 );
      $r0 = 0.6 * $w;
    }

    $half  = 0.45 * $w;
    $angle = fn ( $t ) => 2 * M_PI * $t / $period;
    $rad   = fn ( $t ) => $r0 + $w * $t / $period;
    $at    = fn ( $t, $r ) => padChartXY ( $cx + $r * sin ( $angle ( $t ) ) ) . ',' . padChartXY ( $cy - $r * cos ( $angle ( $t ) ) );
    $parts = max ( 2, (int) ceil ( 72 / $period ) );

    // A segment per row: its outer edge along the spiral, back along the inner one.

    foreach ( $points as $i => $p ) {

      $outer = $inner = [];

      for ( $s = 0; $s <= $parts; $s++ ) {
        $t        = $i + $s / $parts;
        $outer [] = $at ( $t, $rad ( $t ) + $half );
        $inner [] = $at ( $t, $rad ( $t ) - $half );
      }

      $d = 'M' . implode ( 'L', $outer ) . 'L' . implode ( 'L', array_reverse ( $inner ) ) . 'Z';

      $svg .= '<path class="pc-mark pc-seg pc-h' . $step ( $p [1] ) . "\" d=\"$d\"><title>" . padChartAttr ( $desc [$i] ) . '</title></path>';

    }

    // The turn names, where each turn begins - when the band is as high as a line of text
    // and the last segment of a turn and the first of the next have room.

    if ( $turn !== '' and 2 * $half >= 13 )
      for ( $i = 0; $i < $n; $i += $period ) {
        $text = $points [$i] [2];
        if ( $text !== '' and padChartWidth ( $text ) <= 2 * 2 * M_PI * $rad ( $i ) / $period - 4 )
          $svg .= '<text class="pc-in" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy - $rad ( $i ) + 4 ) . '" text-anchor="middle" pointer-events="none">'
                . padChartAttr ( $text ) . '</text>';
      }

    // The labels of the first turn, one past the middle of each position, thinned out
    // when their spacing along the outside is less than they need.

    if ( $label !== '' ) {

      $ring  = $rad ( $n ) + $half + 8;
      $gap   = 2 * M_PI * $ring / $period;
      $every = max ( 1, (int) ceil ( ( $labelW + 6 ) / max ( 1, $gap ) ) );

      for ( $q = 0; $q < min ( $period, $n ); $q += $every ) {
        $a      = $angle ( $q + 0.5 );
        $s      = sin ( $a );
        $c      = cos ( $a );
        $anchor = ( $s > 0.3 ) ? 'start' : ( $s < -0.3 ? 'end' : 'middle' );
        $svg   .= '<text x="' . padChartXY ( $cx + $ring * $s ) . '" y="' . padChartXY ( $cy - $ring * $c + 4 + ( $c < -0.5 ? 4 : 0 ) - ( $c > 0.5 ? 2 : 0 ) ) . "\" text-anchor=\"$anchor\">"
                . padChartAttr ( $points [$q] [0] ) . '</text>';
      }

    }

    // The scale: the lowest value, the seven steps, the highest.

    $low  = padChartNumber ( $min );
    $high = padChartNumber ( $max );
    $x    = ( $width - padChartWidth ( $low ) - padChartWidth ( $high ) - 7 * 16 - 12 ) / 2;
    $y    = $height - 16;

    $svg .= '<text x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y + 9 ) . '">' . padChartAttr ( $low ) . '</text>';
    $x   += padChartWidth ( $low ) + 6;

    for ( $k = 0; $k <= 6; $k++ )
      $svg .= '<rect class="pc-h' . $k . '" x="' . padChartXY ( $x + $k * 16 ) . '" y="' . padChartXY ( $y ) . '" width="16" height="10"/>';

    $svg .= '<text x="' . padChartXY ( $x + 7 * 16 + 6 ) . '" y="' . padChartXY ( $y + 9 ) . '">' . padChartAttr ( $high ) . '</text>';

    return "$svg</svg>";

  }

?>
