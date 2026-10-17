<?php

  // Many measures of many rows - parallel coordinates: an upright axis per measure, a line
  // per row through its value on each.
  //
  //   {chart 'parallel', data='cars', value='mpg, cylinders, horsepower, weight', color='origin'}
  //
  // value= lists the measures, two or more - else every numeric field of the first row -
  // each an axis of its own scaled from round numbers below its lowest value to past its
  // highest, named on top, with a few ticks. A row is a line across them; a row without a
  // number for every measure is left out. color= colours the lines by a field, eight groups
  // with a legend and the rest grey; without it every line is the series colour, lighter
  // the more there are. label= names a row in its tooltip.

  function padChartParallel ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $fields = padChartFields ( padTagParm ( 'value' ) ) ?: $numbers;
    $group  = (string) padTagParm ( 'color' );
    $used   = array_merge ( $fields, [ $group ] );
    $label  = padChartPick ( 'label', $names, $used );

    if ( count ( $fields ) < 2 ) {
      if ( $padCheckSyntax )
        padError ( 'a parallel coordinates chart needs two measures or more in value=, it has ' . count ( $fields ) );
      return '';
    }

    $lines  = [];
    $groups = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $values = [];

      foreach ( $fields as $field ) {
        $v = $row [$field] ?? NULL;
        if ( ! padChartFinite ( $v ) )
          continue 2;
        $values [] = $v + 0;
      }

      $g = padChartText ( $row, $group );

      if ( $group !== '' and ! isset ( $groups [$g] ) )
        $groups [$g] = count ( $groups );

      $name     = padChartText ( $row, $label );
      $lines [] = [ $name !== '' ? $name : "Row $index", $values, $g ];

    }

    if ( ! $lines )
      return '';

    $desc = [];

    foreach ( $lines as list ( $name, $values, $g ) ) {
      $parts = [];
      foreach ( $fields as $i => $field )
        $parts [] = "$field " . padChartNumber ( $values [$i] );
      $desc [] = $name . ( $g !== '' ? " ($g)" : '' ) . ': ' . implode ( ', ', $parts );
    }

    $title = (string) padTagParm ( 'title', implode ( ', ', array_map ( 'ucfirst', $fields ) ) . ( $group !== '' ? " by $group" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'parallel', [ $fields, $lines ], $desc, $title, $width, $height );

    $legendH = 0;

    if ( count ( $groups ) > 1 ) {
      $keys = array_keys ( $groups );
      if ( count ( $keys ) > 8 )
        $keys = array_merge ( array_slice ( $keys, 0, 8 ), [ 'Other' ] );
      list ( $legend, $legendH ) = padChartLegend ( $keys, 12, 6, $width - 24 );
      $svg .= $legend;
      $legendH += 6;
    }

    // An axis per measure: its round scale and the widest tick label beside it.

    $axes = [];

    foreach ( $fields as $i => $field ) {
      $column = array_column ( array_column ( $lines, 1 ), $i );
      $ticks  = padChartTicks ( min ( $column ), max ( $column ), $height < 240 ? 3 : 4 );
      $tickW  = 0;
      foreach ( $ticks as $tick )
        $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) ) );
      $axes [] = [ $ticks, $tickW ];
    }

    $n      = count ( $fields );
    $left   = max ( $axes [0] [1] + 10, padChartWidth ( $fields [0] ) / 2 + 6, 12 );
    $right  = max ( 12, padChartWidth ( $fields [$n - 1] ) / 2 + 6 );
    $top    = $legendH + 30;
    $bottom = 12;
    $plotH  = max ( 10, $height - $top - $bottom );
    $step   = ( $width - $left - $right ) / ( $n - 1 );
    $x      = fn ( $i ) => $left + $i * $step;
    $y      = function ( $i, $v ) use ( $axes, $top, $plotH ) {
      $low  = $axes [$i] [0] [0];
      $high = end ( $axes [$i] [0] );
      return $top + ( $high - $v ) / ( $high - $low ) * $plotH;
    };

    // The lines first, so the axes and their numbers stay readable on top.

    $alone = $group === '' ? padChartXY ( max ( 0.25, min ( 0.8, 6 / sqrt ( count ( $lines ) ) / 3 ) ) ) : '0.65';

    foreach ( $lines as $j => list ( $name, $values, $g ) ) {

      $d = '';
      foreach ( $values as $i => $v )
        $d .= ( $i ? 'L' : 'M' ) . padChartXY ( $x ( $i ) ) . ',' . padChartXY ( $y ( $i, $v ) );

      $slot  = ( $group === '' ) ? 0 : $groups [$g];
      $class = ( $slot < 8 ) ? 'pc-l' . ( $slot + 1 ) : 'pc-edge';

      $svg .= '<path class="pc-mark ' . $class . '" stroke-opacity="' . $alone . '" style="stroke-width:1.5" d="' . $d . '">'
            . '<title>' . padChartAttr ( $desc [$j] ) . '</title></path>';

    }

    foreach ( $fields as $i => $field ) {

      list ( $ticks ) = $axes [$i];

      $svg .= '<line class="pc-axis" x1="' . padChartXY ( $x ( $i ) ) . '" x2="' . padChartXY ( $x ( $i ) ) . '" y1="' . padChartXY ( $top ) . '" y2="' . padChartXY ( $top + $plotH ) . '"/>'
            . '<text x="' . padChartXY ( $x ( $i ) ) . '" y="' . padChartXY ( $top - 12 ) . '" text-anchor="middle" style="font-weight:600">'
            . padChartAttr ( padChartFit ( ucfirst ( $field ), $i == 0 || $i == $n - 1 ? $step : $step - 6 ) ) . '</text>';

      foreach ( $ticks as $tick )
        $svg .= '<line class="pc-axis" x1="' . padChartXY ( $x ( $i ) - 3 ) . '" x2="' . padChartXY ( $x ( $i ) ) . '" y1="' . padChartXY ( $y ( $i, $tick ) ) . '" y2="' . padChartXY ( $y ( $i, $tick ) ) . '"/>'
              . '<text class="pc-ring" style="paint-order:stroke;stroke-width:3px" x="' . padChartXY ( $x ( $i ) - 5 ) . '" y="' . padChartXY ( $y ( $i, $tick ) + 3.5 ) . '" text-anchor="end">'
              . padChartNumber ( $tick ) . '</text>';

    }

    return "$svg</svg>";

  }

?>
