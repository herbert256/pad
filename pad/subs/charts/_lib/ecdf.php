<?php

  // How values spread, without bins: the empirical cumulative distribution - for every value
  // along the bottom, the share of the observations at or below it.
  //
  //   {chart:ecdf data='trials', label='group', value='ms'}
  //   {chart:ecdf data='deliveries', value='days'}
  //
  // value= names the numbers - else the first numeric field; label= groups them, a step line
  // per group in its colour with a legend - all values one line without it. The values of a
  // group are sorted and the line climbs from 0 to 100%: flat between two values, straight
  // up at each by its share of the group - values that are equal are one step, as high as
  // their count - so the line is right-continuous, the share at a value includes the value
  // itself, and it ends at 100% at the highest. Nothing is binned or smoothed: what a
  // histogram and a density curve choose - the bins, the bandwidth - is not needed here.
  // The value axis takes in the lowest and the highest value of every group, no zero needed;
  // the share axis runs from 0 to 100%. A row whose value is no number is left out and
  // counted: the <desc> says per group how many values it draws, their median and range and
  // how many rows had no number. Each step of a group of at most 100 different values carries
  // its tooltip - at or below the value, so many of the group, the share - on a larger
  // invisible target; a larger group has the line's tooltip alone, and the line folds a step
  // drawn where the one before it is drawn into that one, so the SVG grows with the groups
  // and the size of the plot, not with the rows.

  function padChartEcdf ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers ) = padChartGuess ( $rows );

    $used  = [];
    $field = padChartPick ( 'value', $numbers, $used );
    $label = (string) padTagParm ( 'label' );

    if ( $field === '' ) {
      if ( $padCheckSyntax and $rows )
        padError ( "an ecdf chart needs a numeric field - value='field'" );
      return '';
    }

    if ( $padCheckSyntax and $rows and ! padChartFieldSomewhere ( $rows, $field ) )
      padError ( "the ecdf chart's value='" . padMakeSafe ( $field, 30 ) . "' is no field of its rows" );

    // Per group, in the order the groups first appear: its numbers, and the rows of it whose
    // value is no number.

    $groups  = [];
    $missing = [];

    foreach ( (array) $rows as $row ) {

      $row  = padChartRow ( $row );
      $v    = $row [$field] ?? NULL;
      $name = ( $label !== '' ) ? padChartText ( $row, $label ) : ucfirst ( $field );

      $groups  [$name] ??= [];
      $missing [$name] ??= 0;

      if ( padChartFinite ( $v ) )
        $groups [$name] [] = $v + 0;
      else
        $missing [$name]++;

    }

    // The steps of a group: each value it has once, with the count of the values at or
    // below it.

    $sets = [];
    $desc = [];

    foreach ( $groups as $name => $values ) {

      $n    = count ( $values );
      $none = $missing [$name] ? ', ' . $missing [$name] . ( $missing [$name] == 1 ? ' row' : ' rows' ) . ' without a number' : '';

      if ( ! $n ) {
        $desc [] = "$name: no values$none";
        continue;
      }

      sort ( $values );

      $steps = [];

      foreach ( $values as $i => $v )
        if ( $steps and $steps [ count ( $steps ) - 1 ] [0] == $v )
          $steps [ count ( $steps ) - 1 ] [1] = $i + 1;
        else
          $steps [] = [ $v, $i + 1 ];

      $sets [$name] = [ $values, $steps, count ( $desc ) ];

      $desc [] = "$name: $n " . ( $n == 1 ? 'value' : 'values' ) . ', median ' . padChartNumber ( padChartQuantile ( $values, 0.5 ) )
               . ', range ' . padChartNumber ( $values [0] ) . ' – ' . padChartNumber ( end ( $values ) ) . $none;

    }

    if ( ! $sets )
      return '';

    $title = (string) padTagParm ( 'title', ucfirst ( $field ) . ( $label !== '' ? " by $label" : '' ) . ', cumulative share' );

    list ( $id, $svg ) = padChartOpen ( 'ecdf', $sets, $desc, $title, $width, $height );

    $lo = $hi = NULL;

    foreach ( $sets as list ( $values ) ) {
      $lo = min ( $lo ?? INF,  $values [0] );
      $hi = max ( $hi ?? -INF, end ( $values ) );
    }

    $legendH = 0;

    if ( count ( $sets ) > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_keys ( $sets ), 12, 6, $width - 24 );
      $svg .= $legend;
    }

    // The share axis on the left - 0 to 100% in quarters, in halves on a low chart - and the
    // value axis along the bottom, its widest half number kept inside the picture.

    $top    = $legendH + 12;
    $bottom = 24;
    $plotH  = max ( 10, $height - $top - $bottom );
    $shares = ( $plotH >= 120 ) ? [ 0, 0.25, 0.5, 0.75, 1 ] : [ 0, 0.5, 1 ];
    $left   = (int) ceil ( padChartWidth ( '100%' ) ) + 10;
    $ticks  = padChartTicks ( $lo, $hi, max ( 3, min ( 8, (int) ( ( $width - $left ) / 90 ) ) ) );
    $low    = $ticks [0];
    $high   = end ( $ticks );
    $right  = max ( 12, (int) ceil ( padChartWidth ( padChartNumber ( $high ) ) / 2 ) + 4 );
    $plotW  = max ( 10, $width - $left - $right );
    $base   = $top + $plotH;
    $x      = fn ( $v ) => $left + ( $v - $low ) / ( $high - $low ) * $plotW;
    $y      = fn ( $p ) => $top + ( 1 - $p ) * $plotH;

    foreach ( $shares as $share )
      $svg .= "<line class=\"pc-grid\" x1=\"$left\" x2=\"" . ( $left + $plotW ) . '" y1="' . padChartXY ( $y ( $share ) ) . '" y2="' . padChartXY ( $y ( $share ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $y ( $share ) + 4 ) . '" text-anchor="end">' . ( $share * 100 ) . '%</text>';

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $x ( $tick ) ) . '" x2="' . padChartXY ( $x ( $tick ) ) . "\" y1=\"$top\" y2=\"$base\"/>"
            . '<text x="' . padChartXY ( $x ( $tick ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';

    $svg .= "<line class=\"pc-axis\" x1=\"$left\" x2=\"" . ( $left + $plotW ) . "\" y1=\"$base\" y2=\"$base\"/>";

    // A step line per group: from 0 at the left end of the axis, along to each value and up
    // by its share, from 100% on along to the right end. Past eight groups the grey of
    // 'Other', as the legend has it.

    $i = 0;

    foreach ( $sets as $name => list ( $values, $steps, $about ) ) {

      $n    = count ( $values );
      $line = ( $i < 8 ) ? 'pc-l' . ( $i + 1 ) : 'pc-edge';
      $pen  = padChartXY ( $y ( 0 ) );
      $d    = 'M' . padChartXY ( $x ( $low ) ) . ",$pen";

      // A step drawn where the one before it is drawn is folded into it: one at the same
      // place along goes up from there, and one at the same height is a line along that the
      // next step's covers - the line drawn the same, its path a step per tenth of a pixel of
      // the plot at the most, where it had one per different value, 100,000 rows a 1 MB SVG.

      $points = [];

      foreach ( $steps as list ( $v, $at ) ) {

        $px = padChartXY ( $x ( $v ) );
        $py = padChartXY ( $y ( $at / $n ) );

        if ( $py === $pen )
          continue;

        if ( $points and $points [ count ( $points ) - 1 ] [0] === $px )
          $points [ count ( $points ) - 1 ] [1] = $py;
        else
          $points [] = [ $px, $py ];

        $pen = $py;

      }

      foreach ( $points as list ( $px, $py ) )
        $d .= "H{$px}V{$py}";

      $svg .= "<path class=\"$line\" d=\"{$d}H" . padChartXY ( $x ( $high ) ) . '"><title>' . padChartAttr ( $desc [$about] ) . '</title></path>';

      if ( count ( $steps ) <= 100 )
        foreach ( $steps as list ( $v, $at ) )
          $svg .= '<circle class="pc-hit" cx="' . padChartXY ( $x ( $v ) ) . '" cy="' . padChartXY ( $y ( $at / $n ) ) . '" r="6"><title>'
                . padChartAttr ( ( count ( $sets ) > 1 ? "$name: " : '' ) . 'at or below ' . padChartNumber ( $v ) . ": $at of $n, "
                                 . padChartNumber ( round ( $at / $n * 100, 1 ) ) . '%' ) . '</title></circle>';

      $i++;

    }

    return "$svg</svg>";

  }

?>
