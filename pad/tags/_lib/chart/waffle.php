<?php

  // Parts of a whole as squares: a grid of cells= squares (100), each part given its share
  // of them in its own colour.
  //
  //   {chart 'waffle', data='energy', label='use', value='kwh'}
  //   {chart 'waffle', data='votes', label='party', value='votes', cells=200}
  //
  // label= names the parts - else the first field that is no number - and value= their
  // size, else the first numeric field; only a value above zero is a part. The squares are
  // shared out by the largest remainder, so they add up to cells= exactly; they fill the
  // grid row by row from the top left, in the order of the rows. The grid is as near square
  // as cells= allows - ten by ten for 100 - and a legend beside it gives each part its value
  // and its share. Past eight parts the smallest are folded into a grey 'Other', as in a pie.

  function padChartWaffle ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used  = [];
    $value = padChartPick ( 'value', $numbers, $used );
    $label = padChartPick ( 'label', $names,   $used );
    $cells = padTagParm ( 'cells', 100 );

    if ( ! is_numeric ( $cells ) or $cells < 4 or $cells > 2500 or $cells != (int) $cells ) {
      if ( $padCheckSyntax )
        padError ( 'the waffle needs cells= a whole number from 4 to 2500' );
      $cells = 100;
    }

    $cells  = (int) $cells;
    $points = array_values ( array_filter ( padChartPoints ( $rows, $label, $value ), fn ( $point ) => $point [1] > 0 ) );
    $folded = ( count ( $points ) > 8 );

    if ( $folded ) {

      $values = array_column ( $points, 1 );
      arsort ( $values );
      $keep   = array_slice ( array_keys ( $values ), 0, 7 );
      $kept   = [];
      $other  = 0;

      foreach ( $points as $i => $point )
        if ( in_array ( $i, $keep ) )
          $kept [] = $point;
        else
          $other += $point [1];

      $points   = $kept;
      $points[] = [ 'Other', $other ];

    }

    $total = array_sum ( array_column ( $points, 1 ) );

    if ( ! $points or ! is_finite ( $total ) or $total <= 0 )
      return '';

    // The squares by the largest remainder: each part its whole squares first, then the
    // ones left over to the largest fractions - the earlier part first on a tie.

    $count = $rest = [];

    foreach ( $points as $i => $point ) {
      $exact      = $point [1] / $total * $cells;
      $count [$i] = (int) floor ( $exact );
      $rest  [$i] = $exact - $count [$i];
    }

    $left  = $cells - array_sum ( $count );
    $order = array_keys ( $rest );
    usort ( $order, fn ( $a, $b ) => ( $rest [$b] <=> $rest [$a] ) ?: ( $a <=> $b ) );

    foreach ( array_slice ( $order, 0, $left ) as $i )
      $count [$i]++;

    $share = fn ( $v ) => padChartNumber ( round ( $v / $total * 100, 1 ) ) . '%';
    $slot  = fn ( $i ) => ( $folded and $i == count ( $points ) - 1 ) ? 'pc-co' : padChartSlot ( $i );
    $desc  = [];

    foreach ( $points as $i => $point )
      $desc [] = $point [0] . ': ' . padChartNumber ( $point [1] ) . ' (' . $share ( $point [1] ) . ', ' . $count [$i] . ' of ' . $cells . ' squares)';

    $title = (string) padTagParm ( 'title', ucfirst ( $value !== '' ? $value : 'value' ) . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'waffle', [ $points, $cells ], $desc, $title, $width, $height );

    // The grid: as many columns as the root of cells= rounded, the rows it takes; the
    // squares as large as the room left of the legend allows.

    $cols = max ( 1, (int) round ( sqrt ( $cells ) ) );
    $rowN = (int) ceil ( $cells / $cols );
    $size = max ( 2, min ( ( $height - 20 ) / $rowN, ( $width * 0.55 - 20 ) / $cols ) );
    $gap  = max ( 1, $size * 0.12 );
    $x0   = 10;
    $y0   = ( $height - $size * $rowN ) / 2;
    $cell = 0;

    foreach ( $points as $i => $point ) {

      $tip = padChartAttr ( $desc [$i] );

      for ( $k = 0; $k < $count [$i]; $k++, $cell++ ) {
        $x    = $x0 + ( $cell % $cols ) * $size;
        $y    = $y0 + intdiv ( $cell, $cols ) * $size;
        $svg .= '<rect class="pc-mark ' . $slot ( $i ) . '" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $size - $gap ) . '" height="' . padChartXY ( $size - $gap ) . '"'
              . ' rx="' . padChartXY ( min ( 3, $size * 0.15 ) ) . "\"><title>$tip</title></rect>";
      }

    }

    // The legend, a row per part, centred beside the grid.

    $x = $x0 + $cols * $size + 22;
    $y = max ( 4, $height / 2 - count ( $points ) * 9 );

    foreach ( $points as $i => $point ) {
      $text  = $point [0] . '  ' . padChartNumber ( $point [1] ) . ' (' . $share ( $point [1] ) . ')';
      $svg  .= '<rect class="' . $slot ( $i ) . '" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y + $i * 18 ) . '" width="10" height="10" rx="2"/>'
             . '<text x="' . padChartXY ( $x + 16 ) . '" y="' . padChartXY ( $y + $i * 18 + 9 ) . '">' . padChartAttr ( padChartFit ( $text, $width - $x - 20 ) ) . '</text>';
    }

    return "$svg</svg>";

  }

?>
