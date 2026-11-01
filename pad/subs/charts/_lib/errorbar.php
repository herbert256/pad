<?php

  // Estimates with their uncertainty: a point per row and an upright whisker from its low to
  // its high bound.
  //
  //   {chart 'errorbar', data='trial', label='dose', value='mean', low='lower', high='upper', interval='95% CI'}
  //   {chart 'errorbar', data='rides', label='line', value='median', interval='min-max'}
  //
  // The rows hold numbers already computed - an estimate and the two ends of its interval;
  // nothing is computed here, no mean, no deviation, no confidence level: the chart draws
  // what it is given and says only what it is told. label= names the category along the
  // bottom - else the first field that is no number; value= the estimate - else the first
  // numeric field that is no bound; low= and high= the bounds - else the fields of those
  // names, as candlestick has them. interval= names what the whisker is - '95% CI', 'min-max',
  // '± 1 SD' - in the legend, the title and the <desc>; never made up: without it the legend
  // names the two bound fields, Low to high. One vertical scale of round numbers takes in
  // every point and every whisker drawn, without a zero of its own; the line at zero is
  // drawn stronger when zero lies inside. A row without both bounds draws its point alone.
  // A low above its high is a mistake of the data the strict check names by its row; without
  // the check that row's whisker is left out.

  function padChartErrorbar ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows, TRUE );

    $low   = (string) padTagParm ( 'low',  'low'  );
    $high  = (string) padTagParm ( 'high', 'high' );
    $used  = [ $low, $high ];
    $label = padChartPick ( 'label', $names,   $used );
    $value = padChartPick ( 'value', $numbers, $used );

    if ( $value === '' ) {
      if ( $padCheckSyntax and $rows )
        padError ( "the errorbar chart needs a numeric field for its points - value='field'" );
      return '';
    }

    $interval = padTagParm ( 'interval', '' );

    if ( ! is_scalar ( $interval ) or is_bool ( $interval ) ) {
      if ( $padCheckSyntax )
        padError ( "the errorbar chart's interval= is a text naming its whiskers - interval='95% CI'" );
      $interval = '';
    }

    $interval = trim ( (string) $interval );

    // A bound field that no row has is a misspelt option - or one left out where the rows
    // name their bounds otherwise - where a row without a value in it is a bound not known.

    if ( $padCheckSyntax and $rows )
      foreach ( [ 'low' => $low, 'high' => $high ] as $option => $field )
        if ( ! padChartFieldSomewhere ( $rows, $field ) )
          padError ( "the errorbar chart has no bound $option='" . padMakeSafe ( $field, 30 ) . "' in its rows - name its field with $option=" );

    // Per row: its name, the estimate and the two bounds - NULL for none, or for both when
    // one is missing or the low lies above the high.

    $points = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $v   = $row [$value] ?? NULL;

      if ( ! padChartFinite ( $v ) )
        continue;

      $name = padChartText ( $row, $label );
      $name = ( $name !== '' ) ? $name : (string) $index;
      $lo   = $row [$low]  ?? NULL;
      $hi   = $row [$high] ?? NULL;
      $lo   = padChartFinite ( $lo ) ? $lo + 0 : NULL;
      $hi   = padChartFinite ( $hi ) ? $hi + 0 : NULL;

      if ( $lo === NULL or $hi === NULL )
        $lo = $hi = NULL;
      elseif ( $lo > $hi ) {
        if ( $padCheckSyntax )
          padError ( "the errorbar chart's row $index, '" . padMakeSafe ( $name, 30 ) . "', has its low " . padChartNumber ( $lo )
                   . " above its high " . padChartNumber ( $hi ) . " - low='" . padMakeSafe ( $low, 30 ) . "', high='" . padMakeSafe ( $high, 30 ) . "'" );
        $lo = $hi = NULL;
      }

      $points [] = [ $name, $v + 0, $lo, $hi ];

    }

    if ( ! $points ) {
      if ( $padCheckSyntax and $rows )
        padError ( "the errorbar chart has no row with a number in value='" . padMakeSafe ( $value, 30 ) . "'" );
      return '';
    }

    $what  = ucfirst ( $value );
    $span  = ( $interval !== '' ) ? $interval : ucfirst ( "$low to $high" );
    $tip   = fn ( $point ) => "{$point [0]}: " . padChartNumber ( $point [1] )
                            . ( ( $point [2] === NULL ) ? ', no interval'
                              : ( ( $interval !== '' ) ? ", $interval " . padChartNumber ( $point [2] ) . ' – ' . padChartNumber ( $point [3] )
                                                       : ", $low " . padChartNumber ( $point [2] ) . ", $high " . padChartNumber ( $point [3] ) ) );
    $desc  = array_map ( $tip, $points );
    $title = (string) padTagParm ( 'title', $what . ( $interval !== '' ? " with $interval" : '' ) . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'errorbar', [ $points, $interval ], $desc, $title, $width, $height );

    // The scale takes in every point and every whisker drawn - no zero: an estimate is read
    // against its neighbours and its own interval.

    $values = [];
    foreach ( $points as list ( , $v, $lo, $hi ) )
      array_push ( $values, ...( ( $lo === NULL ) ? [ $v ] : [ $v, $lo, $hi ] ) );

    $ticks     = padChartTicks ( min ( $values ), max ( $values ), $height < 200 ? 3 : 5 );
    $scaleLow  = $ticks [0];
    $scaleHigh = end ( $ticks );

    if ( $scaleHigh == $scaleLow )
      $scaleHigh = $scaleLow + 1;

    $tickW = 0;
    foreach ( $ticks as $tick )
      $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) ) );

    $left  = max ( 24, (int) ceil ( $tickW ) + 10 );
    $right = 10;
    $plotW = max ( 1, $width - $left - $right );

    // The legend: a dot named by the estimate, a whisker by what the interval is.

    list ( $legend, $legendH ) = padChartErrorbarKey ( $what, $span, $left, 6, $plotW );

    $svg   .= $legend;
    $top    = 10 + $legendH;
    $bottom = 24;
    $plotH  = max ( 1, $height - $top - $bottom );
    $n      = count ( $points );
    $band   = $plotW / $n;
    $cap    = max ( 2, min ( 7, $band * 0.2 ) );
    $r      = max ( 2.5, min ( 5, $band * 0.25 ) );
    $mid    = fn ( $i ) => $left + ( $i + 0.5 ) * $band;
    $y      = fn ( $v ) => $top + ( $scaleHigh - $v ) / ( $scaleHigh - $scaleLow ) * $plotH;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( $tick ) ) . '" y2="' . padChartXY ( $y ( $tick ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $y ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    if ( $scaleLow < 0 and $scaleHigh > 0 )
      $svg .= '<line class="pc-axis" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( 0 ) ) . '" y2="' . padChartXY ( $y ( 0 ) ) . '"/>';

    // The categories under the plot, shortened to their band, thinned out when a band holds
    // less than about ten characters.

    $labelW = 0;
    foreach ( $points as $point )
      $labelW = max ( $labelW, padChartWidth ( $point [0] ) + 8 );

    $every = max ( 1, (int) ceil ( min ( $labelW, 73 ) / max ( 1, $band ) ) );

    foreach ( $points as $i => $point )
      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $mid ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $point [0], $band * $every - 8 ) ) . '</text>';

    // A row: the whisker from low to high with a cap at each end, the ringed point over it,
    // the whole row the tooltip of both.

    foreach ( $points as $i => $point ) {

      list ( , $v, $lo, $hi ) = $point;

      $x    = padChartXY ( $mid ( $i ) );
      $svg .= '<g class="pc-mark"><title>' . padChartAttr ( $desc [$i] ) . '</title>';

      if ( $lo !== NULL ) {
        $a    = padChartXY ( $mid ( $i ) - $cap );
        $b    = padChartXY ( $mid ( $i ) + $cap );
        $yLo  = padChartXY ( $y ( $lo ) );
        $yHi  = padChartXY ( $y ( $hi ) );
        $svg .= "<line class=\"pc-l1\" x1=\"$x\" x2=\"$x\" y1=\"$yHi\" y2=\"$yLo\"/>"
              . "<line class=\"pc-l1\" x1=\"$a\" x2=\"$b\" y1=\"$yHi\" y2=\"$yHi\"/>"
              . "<line class=\"pc-l1\" x1=\"$a\" x2=\"$b\" y1=\"$yLo\" y2=\"$yLo\"/>";
      }

      $svg .= "<circle class=\"pc-c1 pc-ring\" cx=\"$x\" cy=\"" . padChartXY ( $y ( $v ) ) . '" r="' . padChartXY ( $r ) . '"/></g>';

    }

    return "$svg</svg>";

  }

  // The legend of an errorbar chart: a dot and the estimate's name, a whisker and the
  // interval's - the second under the first when the two do not fit side by side. Answers
  // the SVG and the height it takes.

  function padChartErrorbarKey ( $what, $span, $x, $y, $width ) {

    $second = $x + padChartWidth ( $what ) + 26;
    $top    = $y;

    if ( $second + padChartWidth ( $span ) + 14 > $x + $width ) {
      $second = $x;
      $top   += 16;
    }

    $shown = padChartFit ( $span, $x + $width - $second - 14 );
    $whole = ( $shown !== $span ) ? '<title>' . padChartAttr ( $span ) . '</title>' : '';
    $first = padChartFit ( $what, $width - 14 );
    $line  = fn ( $x1, $x2, $y1, $y2 ) => '<line class="pc-l1" x1="' . padChartXY ( $x1 ) . '" x2="' . padChartXY ( $x2 ) . '" y1="' . padChartXY ( $y1 ) . '" y2="' . padChartXY ( $y2 ) . '"/>';

    $svg = '<circle class="pc-c1" cx="' . padChartXY ( $x + 5 ) . '" cy="' . padChartXY ( $y + 5 ) . '" r="4"/>'
         . '<text x="' . padChartXY ( $x + 14 ) . '" y="' . padChartXY ( $y + 9 ) . '">' . padChartAttr ( $first ) . '</text>'
         . $line ( $second + 5, $second + 5, $top, $top + 10 )
         . $line ( $second + 2, $second + 8, $top, $top )
         . $line ( $second + 2, $second + 8, $top + 10, $top + 10 )
         . '<text x="' . padChartXY ( $second + 14 ) . '" y="' . padChartXY ( $top + 9 ) . '">' . $whole . padChartAttr ( $shown ) . '</text>';

    return [ "<g class=\"pc-key\">$svg</g>", $top - $y + 16 ];

  }

?>
