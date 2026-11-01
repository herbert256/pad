<?php

  // Two values per category - before and after, this year and last, men and women - as a
  // dumbbell: a row per category, a dot at each value and a line joining the two.
  //
  //   {chart 'dumbbell', data='wages', label='country', from='y2015', to='y2025', names='2015, 2025'}
  //   {chart 'dumbbell', data='scores', label='school', from='before', to='after', ranked}
  //
  // label= names the row - else the first field that is no number; from= and to= name the
  // fields of the two ends - else the first two numeric fields - from and to as gantt's bar
  // runs from one to the other. The from dot takes the first colour, the to dot the second,
  // and a legend above names them: names= gives the two names, else the fields' own. All the
  // rows share one horizontal scale of round numbers around their lowest and highest value -
  // no zero needed, a dot is read by its place; negative values are values like any other,
  // and the line at zero is drawn stronger when zero lies inside. ranked orders the rows by
  // their change, from the largest rise to the largest fall. A row with one end alone draws
  // that dot without a line; a row with neither is left out. The <desc> and the line's
  // tooltip say both values and the signed change, +3 or -2.

  function padChartDumbbell ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows, TRUE );

    // The fields from= and to= name are no other field's guess, as errorbar keeps its bounds
    // out: to='a' alone took a for from= as well, and the chart failed for want of two fields.

    $used  = array_values ( array_filter ( [ (string) padTagParm ( 'from' ), (string) padTagParm ( 'to' ) ], 'strlen' ) );
    $label = padChartPick ( 'label', $names,   $used );
    $from  = padChartPick ( 'from',  $numbers, $used );
    $to    = padChartPick ( 'to',    $numbers, $used );

    if ( $from === '' or $to === '' or $from === $to ) {
      if ( $padCheckSyntax and $rows )
        padError ( "a dumbbell chart needs two numeric fields - from='field' and to='field'" );
      return '';
    }

    // A field named that no row has is a misspelt option, where a row without a value in it
    // is a value left out - the one draws nothing anywhere, the other a dot alone.

    if ( $padCheckSyntax )
      foreach ( [ 'from' => $from, 'to' => $to ] as $option => $field )
        if ( $rows and ! padChartFieldSomewhere ( $rows, $field ) )
          padError ( "the dumbbell chart's $option='" . padMakeSafe ( $field, 30 ) . "' is no field of its rows" );

    // names= as text, 'before, after', or as PAD's own list, ['2025', '2026'] - which ended
    // the page with "Array to string conversion", in the lenient walk too.

    $ends = padTagParm ( 'names' );
    $ends = is_array ( $ends ) ? array_values ( array_filter ( array_map ( fn ( $end ) => trim ( (string) $end ), array_filter ( $ends, 'is_scalar' ) ), 'strlen' ) )
                               : padChartFields ( $ends );

    if ( $ends and count ( $ends ) != 2 ) {
      if ( $padCheckSyntax )
        padError ( "a dumbbell chart names its two ends - names='before, after', it has " . count ( $ends ) );
      $ends = [];
    }

    list ( $fromName, $toName ) = $ends ?: [ ucfirst ( $from ), ucfirst ( $to ) ];

    // Per row: its name and the two values, NULL for an end that has no number.

    $bells = [];
    $index = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $a   = $row [$from] ?? NULL;
      $b   = $row [$to]   ?? NULL;
      $a   = padChartFinite ( $a ) ? $a + 0 : NULL;
      $b   = padChartFinite ( $b ) ? $b + 0 : NULL;

      if ( $a === NULL and $b === NULL )
        continue;

      $name     = padChartText ( $row, $label );
      $bells [] = [ ( $name !== '' ) ? $name : "Row $index", $a, $b ];

    }

    if ( ! $bells ) {
      if ( $padCheckSyntax and $rows )
        padError ( "the dumbbell chart has no row with a number in from='" . padMakeSafe ( $from, 30 ) . "' or to='" . padMakeSafe ( $to, 30 ) . "'" );
      return '';
    }

    // ranked: the largest rise on top, the largest fall at the bottom, the rows with one end
    // alone after them - in their own order, as the sort keeps rows that compare equal.

    $ranked = padTagParm ( 'ranked', FALSE );

    if ( $ranked !== FALSE and $ranked !== '' and $ranked !== 0 and $ranked !== '0' )
      usort ( $bells, function ( $p, $q ) {
        $one = ( $p [1] === NULL or $p [2] === NULL );
        $two = ( $q [1] === NULL or $q [2] === NULL );
        if ( $one or $two )
          return $one <=> $two;
        return ( $q [2] - $q [1] ) <=> ( $p [2] - $p [1] );
      } );

    $show = fn ( $v ) => ( $v === NULL ) ? 'not given' : padChartNumber ( $v );
    $desc = [];

    foreach ( $bells as list ( $name, $a, $b ) )
      $desc [] = "$name: $fromName " . $show ( $a ) . ", $toName " . $show ( $b )
               . ( ( $a !== NULL and $b !== NULL ) ? ', change ' . padChartChange ( $b - $a ) : '' );

    $title = (string) padTagParm ( 'title', "$fromName to $toName" . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'dumbbell', [ $bells, $fromName, $toName ], $desc, $title, $width, $height );

    // The names on the left, at most a third of the width; the scale's numbers under the
    // rows, the widest half past the last grid line kept inside the picture.

    $values = [];
    foreach ( $bells as list ( , $a, $b ) )
      foreach ( [ $a, $b ] as $v )
        if ( $v !== NULL )
          $values [] = $v;

    $labelW = 0;
    foreach ( $bells as $bell )
      $labelW = max ( $labelW, padChartWidth ( $bell [0] ) );

    $n     = count ( $bells );
    $left  = 12 + min ( $labelW, $width / 3 ) + 12;
    $ticks = padChartTicks ( min ( $values ), max ( $values ), max ( 2, min ( 6, (int) ( ( $width - $left - 16 ) / 70 ) ) ) );
    $low   = $ticks [0];
    $high  = end ( $ticks );

    if ( $high == $low )
      $high = $low + 1;

    $tickW = 0;
    foreach ( $ticks as $tick )
      $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) ) );

    $right = max ( 16, (int) ceil ( $tickW / 2 ) + 4 );
    $plotW = max ( 10, $width - $left - $right );

    list ( $legend, $legendH ) = padChartLegend ( [ $fromName, $toName ], $left, 6, $plotW );

    $svg   .= $legend;
    $top    = 10 + $legendH;
    $bottom = 24;
    $plotH  = max ( 1, $height - $top - $bottom );
    $band   = $plotH / $n;
    $r      = max ( 2.5, min ( 6, $band * 0.3 ) );
    $x      = fn ( $v ) => $left + ( $v - $low ) / ( $high - $low ) * $plotW;
    $mid    = fn ( $i ) => $top + ( $i + 0.5 ) * $band;
    $floor  = padChartXY ( $top + $plotH );

    foreach ( $ticks as $tick ) {
      $p    = padChartXY ( $x ( $tick ) );
      $svg .= "<line class=\"pc-grid\" x1=\"$p\" x2=\"$p\" y1=\"" . padChartXY ( $top ) . "\" y2=\"$floor\"/>"
            . "<text x=\"$p\" y=\"" . ( $height - 8 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';
    }

    if ( $low < 0 and $high > 0 )
      $svg .= '<line class="pc-axis" x1="' . padChartXY ( $x ( 0 ) ) . '" x2="' . padChartXY ( $x ( 0 ) ) . '" y1="' . padChartXY ( $top ) . "\" y2=\"$floor\"/>";

    // A row: its name, the line between the two ends - carrying the whole row as its
    // tooltip - and a ringed dot at each end, the to dot over the from dot where they meet.
    // The names are thinned out when the rows stand closer than a line of text, as hbar's
    // are - every row keeps its dots and their tooltips: forty rows wrote forty names 6px
    // apart, one over the other.

    $every = max ( 1, (int) ceil ( 13 / max ( 1, $band ) ) );

    foreach ( $bells as $i => list ( $name, $a, $b ) ) {

      $y = padChartXY ( $mid ( $i ) );

      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $left - 12 ) . '" y="' . padChartXY ( $mid ( $i ) + 4 ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $name, $left - 24 ) ) . '</text>';

      if ( $a !== NULL and $b !== NULL )
        $svg .= '<line class="pc-edge" stroke-width="3" x1="' . padChartXY ( $x ( $a ) ) . '" x2="' . padChartXY ( $x ( $b ) ) . "\" y1=\"$y\" y2=\"$y\">"
              . '<title>' . padChartAttr ( $desc [$i] ) . '</title></line>';

      foreach ( [ [ $a, $fromName, 1 ], [ $b, $toName, 2 ] ] as list ( $v, $end, $slot ) )
        if ( $v !== NULL )
          $svg .= "<circle class=\"pc-mark pc-c$slot pc-ring\" cx=\"" . padChartXY ( $x ( $v ) ) . "\" cy=\"$y\" r=\"" . padChartXY ( $r ) . '">'
                . '<title>' . padChartAttr ( "$name · $end: " . padChartNumber ( $v ) ) . '</title></circle>';

    }

    return "$svg</svg>";

  }

  // A change as a signed number: +3, -2, and 0 for none.

  function padChartChange ( $change ) {

    return ( ( $change > 0 ) ? '+' : '' ) . padChartNumber ( $change );

  }

?>
