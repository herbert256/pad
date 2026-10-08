<?php

  // The parts the chart kinds share - lib/chart.php draws bar, line and sparkline, the
  // files beside this one the other kinds:
  //
  //   series.php     bar and line with several value fields, stacked bars, hbar
  //   pie.php        pie and donut
  //   scatter.php    scatter and bubble
  //   heatmap.php    heatmap
  //   sankey.php     sankey
  //   network.php    network
  //   hierarchy.php  treemap and sunburst
  //   gauge.php      gauge
  //   radar.php      radar
  //   waterfall.php  waterfall
  //   distribution.php  histogram and boxplot
  //   calendar.php   calendar
  //   gantt.php      gantt
  //   area.php       area and stream
  //   multiples.php  multiples
  //   candlestick.php  candlestick
  //   dual.php       dual
  //   spiral.php     spiral
  //   radial.php     rose and radialbar
  //   funnel.php     funnel and pyramid
  //   waffle.php     waffle
  //   marimekko.php  marimekko
  //   parliament.php parliament
  //   venn.php       venn
  //   density.php    density and violin
  //   bullet.php     bullet
  //   rings.php      rings
  //   parallel.php   parallel
  //   proportional.php  proportional
  //   chord.php      chord
  //   arc.php        arc
  //   pack.php       pack
  //   orgchart.php   orgchart
  //
  // A field option names a field of the rows; left out, the kind takes the first field of
  // the first row that fits - a numeric one for a number, another one for a name.

  // value='sales, costs' - the field names of a list option, in their order.

  function padChartFields ( $list ) {

    $fields = [];

    foreach ( explode ( ',', (string) $list ) as $field )
      if ( ( $field = trim ( $field ) ) !== '' )
        $fields [] = $field;

    return $fields;

  }

  // A row as an array of its fields: an object's properties, a value alone as 'value'.

  function padChartRow ( $row ) {

    if ( is_object ( $row ) )
      $row = padToArray ( $row );

    return is_array ( $row ) ? $row : [ 'value' => $row ];

  }

  // The names of the first row's fields: the numeric ones and the other scalar ones, in
  // their order - the fields a kind falls back on when an option names none.

  function padChartGuess ( $rows ) {

    foreach ( (array) $rows as $row ) {

      $row     = padChartRow ( $row );
      $numbers = $names = [];

      foreach ( $row as $key => $field )
        if ( is_numeric ( $field ) )
          $numbers [] = (string) $key;
        elseif ( is_scalar ( $field ) )
          $names [] = (string) $key;

      return [ $numbers, $names ];

    }

    return [ [], [] ];

  }

  // The field an option names, else the first unused one of the guessed fields.

  function padChartPick ( $option, $guessed, &$used ) {

    $field = (string) padTagParm ( $option );

    if ( $field === '' )
      foreach ( $guessed as $name )
        if ( ! in_array ( $name, $used, TRUE ) ) {
          $field = $name;
          break;
        }

    if ( $field !== '' )
      $used [] = $field;

    return $field;

  }

  // A field of a row as text: a scalar, else ''.

  function padChartText ( $row, $field ) {

    $value = ( $field !== '' ) ? ( $row [$field] ?? '' ) : '';

    return is_scalar ( $value ) ? (string) $value : '';

  }

  // The rows as a table of several series: the labels and, per value field, its numbers
  // in the order of the labels. A row is left out when one of its values is no number the
  // axis can take. With no field or one, padChartPoints decides, as for one series.

  function padChartTable ( $rows, $label, $fields ) {

    if ( count ( $fields ) <= 1 ) {
      $points = padChartPoints ( $rows, $label, $fields [0] ?? '' );
      return [ array_column ( $points, 0 ), [ ucfirst ( $fields [0] ?? 'value' ) => array_column ( $points, 1 ) ] ];
    }

    $labels = [];
    $series = array_fill_keys ( array_map ( 'ucfirst', $fields ), [] );
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $index++;
      $row    = padChartRow ( $row );
      $values = [];

      foreach ( $fields as $field ) {
        $value = $row [$field] ?? NULL;
        if ( ! padChartFinite ( $value ) )
          continue 2;
        $values [] = $value + 0;
      }

      $text      = padChartText ( $row, $label );
      $labels [] = ( $label !== '' and $text !== '' ) ? $text : (string) $index;

      foreach ( array_keys ( $series ) as $i => $name )
        $series [$name] [] = $values [$i];

    }

    return [ $labels, $series ];

  }

  // The class of the n-th categorical slot, counted from 0: the eight slots in their fixed
  // order, and the grey of 'Other' past them - a ninth colour is never made up.

  function padChartSlot ( $index ) {

    return ( $index < 8 ) ? 'pc-c' . ( $index + 1 ) : 'pc-co';

  }

  // The width a text takes at the chart's 11px, about.

  function padChartWidth ( $text ) {

    return mb_strlen ( (string) $text ) * 6.5;

  }

  // A text cut to the width it may take, an ellipsis where it was cut.

  function padChartFit ( $text, $width ) {

    $text = (string) $text;
    $max  = (int) floor ( $width / 6.5 );

    if ( mb_strlen ( $text ) <= $max )
      return $text;

    return ( $max < 2 ) ? '' : mb_substr ( $text, 0, $max - 1 ) . '…';

  }

  // A legend: a swatch and a name per series, in rows that wrap at $width. Answers the
  // SVG and the height it takes.

  function padChartLegend ( $names, $x, $y, $width ) {

    $svg  = '';
    $left = $x;
    $top  = $y;

    foreach ( array_values ( $names ) as $i => $name ) {

      $w = padChartWidth ( $name ) + 26;

      if ( $left > $x and $left + $w > $x + $width ) {
        $left = $x;
        $top += 16;
      }

      $svg .= '<rect class="' . padChartSlot ( $i ) . '" x="' . padChartXY ( $left ) . '" y="' . padChartXY ( $top ) . '" width="10" height="10" rx="2"/>'
            . '<text x="' . padChartXY ( $left + 14 ) . '" y="' . padChartXY ( $top + 9 ) . '">' . padChartAttr ( $name ) . '</text>';

      $left += $w;

    }

    return [ "<g class=\"pc-key\">$svg</g>", $top - $y + 16 ];

  }

  // A bar from $base to $end along the value axis, between $from and $to across it, its
  // data end rounded by 4 and square at the base. Vertical: the value axis is y.

  function padChartBarPath ( $from, $to, $base, $end, $vertical = TRUE ) {

    $r = min ( 4, ( $to - $from ) / 2, abs ( $base - $end ) );

    if ( $vertical ) {
      $dir = ( $end <= $base ) ? 1 : -1;
      return 'M' . padChartXY ( $from ) . ',' . padChartXY ( $base )
           . 'V' . padChartXY ( $end + $dir * $r )
           . 'Q' . padChartXY ( $from ) . ',' . padChartXY ( $end ) . ' ' . padChartXY ( $from + $r ) . ',' . padChartXY ( $end )
           . 'H' . padChartXY ( $to - $r )
           . 'Q' . padChartXY ( $to ) . ',' . padChartXY ( $end ) . ' ' . padChartXY ( $to ) . ',' . padChartXY ( $end + $dir * $r )
           . 'V' . padChartXY ( $base ) . 'Z';
    }

    $dir = ( $end >= $base ) ? -1 : 1;

    return 'M' . padChartXY ( $base ) . ',' . padChartXY ( $from )
         . 'H' . padChartXY ( $end + $dir * $r )
         . 'Q' . padChartXY ( $end ) . ',' . padChartXY ( $from ) . ' ' . padChartXY ( $end ) . ',' . padChartXY ( $from + $r )
         . 'V' . padChartXY ( $to - $r )
         . 'Q' . padChartXY ( $end ) . ',' . padChartXY ( $to ) . ' ' . padChartXY ( $end + $dir * $r ) . ',' . padChartXY ( $to )
         . 'H' . padChartXY ( $base ) . 'Z';

  }

  // A ring segment around ($cx, $cy) from radius $inner to $outer, from angle $a0 to $a1
  // clockwise, 0 at twelve o'clock. An inner radius of 0 is a pie wedge; a segment of the
  // whole circle is drawn as two halves, as one arc cannot start where it ends.

  function padChartArc ( $cx, $cy, $inner, $outer, $a0, $a1 ) {

    $at = fn ( $a, $r ) => padChartXY ( $cx + $r * sin ( $a ) ) . ',' . padChartXY ( $cy - $r * cos ( $a ) );

    $o = padChartXY ( $outer );
    $i = padChartXY ( $inner );

    if ( $a1 - $a0 >= 2 * M_PI - 1e-9 ) {
      $d = 'M' . $at ( 0, $outer ) . "A$o,$o 0 1 1 " . $at ( M_PI, $outer ) . "A$o,$o 0 1 1 " . $at ( 0, $outer ) . 'Z';
      if ( $inner > 0 )
        $d .= 'M' . $at ( 0, $inner ) . "A$i,$i 0 1 0 " . $at ( M_PI, $inner ) . "A$i,$i 0 1 0 " . $at ( 0, $inner ) . 'Z';
      return $d;
    }

    $large = ( $a1 - $a0 > M_PI ) ? 1 : 0;

    if ( $inner <= 0 )
      return 'M' . padChartXY ( $cx ) . ',' . padChartXY ( $cy ) . 'L' . $at ( $a0, $outer ) . "A$o,$o 0 $large 1 " . $at ( $a1, $outer ) . 'Z';

    return 'M' . $at ( $a0, $outer ) . "A$o,$o 0 $large 1 " . $at ( $a1, $outer )
         . 'L' . $at ( $a1, $inner ) . "A$i,$i 0 $large 0 " . $at ( $a0, $inner ) . 'Z';

  }

?>
