<?php

  // The seats of a parliament: a dot per seat in rows of half circles, the parties in their
  // order from left to right.
  //
  //   {chart 'parliament', data='election', label='party', value='seats'}
  //
  // label= names the parties - else the first field that is no number - and value= their
  // seats, else the first numeric field: a whole number above zero, and 5000 seats at most
  // all together. The rows are as few as hold every seat with the dots evenly spaced,
  // the seats shared over them by their length; the seats of all rows are then taken by
  // their angle from the left, so each party fills a wedge of the hemicycle. A party's
  // colour is its slot in the order of the rows, past eight grey. The total and the seats
  // a majority needs stand in the middle, a legend with the seats of every party below.

  function padChartParliament ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used    = [];
    $value   = padChartPick ( 'value', $numbers, $used );
    $label   = padChartPick ( 'label', $names,   $used );
    $parties = [];

    foreach ( padChartPoints ( $rows, $label, $value ) as list ( $name, $seats ) ) {

      if ( $seats != floor ( $seats ) ) {
        if ( $padCheckSyntax )
          padError ( "the parliament has a party of " . padChartNumber ( $seats ) . " seats - seats are whole numbers" );
        $seats = round ( $seats );
      }

      if ( $seats > 0 )
        $parties [] = [ $name, (int) $seats ];

    }

    $total = array_sum ( array_column ( $parties, 1 ) );

    if ( ! $parties )
      return '';

    if ( $total > 5000 ) {
      if ( $padCheckSyntax )
        padError ( "the parliament has $total seats - 5000 at most" );
      return '';
    }

    $majority = intdiv ( $total, 2 ) + 1;
    $desc     = [];

    foreach ( $parties as list ( $name, $seats ) )
      $desc [] = "$name: $seats " . ( $seats == 1 ? 'seat' : 'seats' );

    $desc [] = "$total " . ( $total == 1 ? 'seat' : 'seats' ) . ", $majority for a majority";

    $title = (string) padTagParm ( 'title', 'Seats' . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'parliament', $parties, $desc, $title, $width, $height );

    // The rows: from an inner radius of 0.4 out to 1, as few as hold every seat when the
    // dots are as far apart along a row as the rows are apart.

    $inner = 0.4;

    for ( $k = 1; ; $k++ ) {

      $space = ( 1 - $inner ) / $k;
      $radii = $room = [];

      for ( $j = 0; $j < $k; $j++ ) {
        $radii [$j] = $inner + ( $j + 0.5 ) * $space;
        $room  [$j] = (int) floor ( M_PI * $radii [$j] / $space ) + 1;
      }

      if ( array_sum ( $room ) >= $total )
        break;

    }

    // The seats over the rows by their length, the largest remainder getting the rest -
    // never more than a row holds.

    $length = array_sum ( $radii );
    $count  = $rest = [];

    foreach ( $radii as $j => $radius ) {
      $exact      = $total * $radius / $length;
      $count [$j] = min ( $room [$j], (int) floor ( $exact ) );
      $rest  [$j] = $exact - $count [$j];
    }

    while ( array_sum ( $count ) < $total ) {
      $best = NULL;
      foreach ( $radii as $j => $radius )
        if ( $count [$j] < $room [$j] and ( $best === NULL or $rest [$j] > $rest [$best] ) )
          $best = $j;
      $count [$best]++;
      $rest  [$best]--;
    }

    // Every seat by its angle, from the left: on one angle the outer row first.

    $places = [];

    foreach ( $radii as $j => $radius )
      for ( $t = 0; $t < $count [$j]; $t++ )
        $places [] = [ $count [$j] == 1 ? M_PI / 2 : M_PI * ( 1 - $t / ( $count [$j] - 1 ) ), $radius ];

    usort ( $places, fn ( $a, $b ) => ( round ( $b [0], 9 ) <=> round ( $a [0], 9 ) ) ?: ( $b [1] <=> $a [1] ) );

    // The legend first, for the room it takes below the hemicycle.

    $keys = array_map ( fn ( $party ) => $party [0] . ' ' . $party [1], $parties );

    list ( , $legendH ) = padChartLegend ( $keys, 10, 0, $width - 20 );

    // A dot is 0.38 of the space between the rows across, its centre half a space inside
    // the radius: the hemicycle is as large as the width and the height under the legend
    // allow with the dots of the outer row and of the ends of each row inside them.

    $r   = max ( 20, min ( ( $width - 20 ) / 2 / ( 1 - 0.12 * $space ), ( $height - $legendH - 22 ) / ( 1 + 0.26 * $space ) ) );
    $dot = max ( 0.5, $space * $r * 0.38 );
    $cx  = $width / 2;
    $cy  = 8 + $r * ( 1 - $space / 2 ) + $dot;

    // The parties' seats in their order, one after the other along the angles.

    $owner = [];

    foreach ( $parties as $i => list ( $name, $seats ) )
      for ( $s = 0; $s < $seats; $s++ )
        $owner [] = $i;

    foreach ( $places as $p => list ( $angle, $radius ) ) {
      $i    = $owner [$p];
      $svg .= '<circle class="pc-mark ' . padChartSlot ( $i ) . '" cx="' . padChartXY ( $cx + $radius * $r * cos ( $angle ) ) . '" cy="' . padChartXY ( $cy - $radius * $r * sin ( $angle ) ) . '"'
            . ' r="' . padChartXY ( $dot ) . '"><title>' . padChartAttr ( $desc [$i] ) . '</title></circle>';
    }

    // The total in the middle, the majority under it.

    $svg .= '<text class="pc-big" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy - 18 ) . '" text-anchor="middle">' . $total . '</text>'
          . '<text x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy - 2 ) . '" text-anchor="middle">' . padChartAttr ( "$majority for a majority" ) . '</text>';

    // The legend centred under it when it takes one line.

    $line = array_sum ( array_map ( fn ( $key ) => padChartWidth ( $key ) + 26, $keys ) ) - 12;
    $from = ( $line <= $width - 20 ) ? ( $width - $line ) / 2 : 10;

    list ( $legend ) = padChartLegend ( $keys, $from, $cy + $dot + 12, $width - 20 );

    return "$svg$legend</svg>";

  }

?>
