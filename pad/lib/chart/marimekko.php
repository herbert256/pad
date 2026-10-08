<?php

  // Two ways of parting one whole at once: the marimekko, or mosaic chart.
  //
  //   {chart 'marimekko', data='market', label='region', value='alpha, beta, gamma, other'}
  //
  // A column per row, named by label= - else the first field that is no number - and as
  // WIDE as its total of the value= fields, two or more - else every numeric field of the
  // first row. Inside its column each field stands as its share of that column, stacked
  // to 100% from the bottom in the order of value=, a colour each with a legend above;
  // the percentage is written in every part that has room. The axis on the left runs from
  // 0 to 100%, the names and totals - and their share of all - stand below the columns.
  // A row with a field that is no finite number of zero or more, or a total of zero, is
  // left out.

  function padChartMarimekko ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $fields = padChartFields ( padTagParm ( 'value' ) ) ?: $numbers;
    $used   = $fields;
    $label  = padChartPick ( 'label', $names, $used );

    if ( count ( $fields ) < 2 ) {
      if ( $padCheckSyntax )
        padError ( 'a marimekko chart needs two value fields or more, it has ' . count ( $fields ) );
      return '';
    }

    if ( count ( $fields ) > 8 ) {
      if ( $padCheckSyntax )
        padError ( 'the marimekko chart has ' . count ( $fields ) . ' value fields - eight at most, each its own colour' );
      $fields = array_slice ( $fields, 0, 8 );
    }

    $columns = [];
    $index   = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $values = [];

      foreach ( $fields as $field ) {
        $v = $row [$field] ?? NULL;
        if ( ! padChartFinite ( $v ) or $v < 0 )
          continue 2;
        $values [] = $v + 0;
      }

      if ( array_sum ( $values ) <= 0 )
        continue;

      $name       = padChartText ( $row, $label );
      $columns [] = [ $name !== '' ? $name : (string) $index, $values, array_sum ( $values ) ];

    }

    $grand = array_sum ( array_column ( $columns, 2 ) );

    if ( ! $columns or ! is_finite ( $grand ) )
      return '';

    $pct  = fn ( $v, $of ) => padChartNumber ( round ( $v / $of * 100, 1 ) ) . '%';
    $desc = [];

    foreach ( $columns as list ( $name, $values, $total ) ) {
      $parts = [];
      foreach ( $fields as $i => $field )
        $parts [] = "$field " . padChartNumber ( $values [$i] ) . ' (' . $pct ( $values [$i], $total ) . ')';
      $desc [] = "$name, " . padChartNumber ( $total ) . ' (' . $pct ( $total, $grand ) . ' of all): ' . implode ( ', ', $parts );
    }

    $title = (string) padTagParm ( 'title', implode ( ', ', array_map ( 'ucfirst', $fields ) ) . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'marimekko', [ $columns, $fields ], $desc, $title, $width, $height );

    list ( $legend, $legendH ) = padChartLegend ( array_map ( 'ucfirst', $fields ), 10, 6, $width - 20 );
    $svg .= $legend;

    $left   = 10 + padChartWidth ( '100%' ) + 8;
    $right  = $width - 10;
    $top    = $legendH + 14;
    $bottom = $height - 36;
    $plotW  = $right - $left;
    $plotH  = $bottom - $top;

    // The axis: 0 to 100% on the left, in quarters.

    foreach ( [ 0, 25, 50, 75, 100 ] as $tick ) {
      $y    = $bottom - $tick / 100 * $plotH;
      $svg .= '<line class="pc-axis" x1="' . padChartXY ( $left - 4 ) . '" x2="' . padChartXY ( $left ) . '" y1="' . padChartXY ( $y ) . '" y2="' . padChartXY ( $y ) . '"/>'
            . '<text x="' . padChartXY ( $left - 7 ) . '" y="' . padChartXY ( $y + 4 ) . "\" text-anchor=\"end\">$tick%</text>";
    }

    // The columns side by side, each as wide as its total; their parts from the bottom up,
    // a 2px line of the surface between them.

    $x = $left;

    foreach ( $columns as list ( $name, $values, $total ) ) {

      $w = $total / $grand * $plotW;
      $y = $bottom;

      foreach ( $values as $i => $v ) {

        $h  = $v / $total * $plotH;
        $y -= $h;

        if ( $v <= 0 )
          continue;

        $text = $pct ( $v, $total );

        $svg .= '<rect class="pc-mark pc-gap ' . padChartSlot ( $i ) . '" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h ) . '">'
              . '<title>' . padChartAttr ( "$name · {$fields [$i]}: " . padChartNumber ( $v ) . " ($text of $name)" ) . '</title></rect>';

        if ( padChartWidth ( $text ) + 8 <= $w and $h >= 16 )
          $svg .= '<text class="pc-in" x="' . padChartXY ( $x + $w / 2 ) . '" y="' . padChartXY ( $y + $h / 2 + 4 ) . '" text-anchor="middle">' . $text . '</text>';

      }

      $svg .= '<text x="' . padChartXY ( $x + $w / 2 ) . '" y="' . padChartXY ( $bottom + 15 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $name, $w - 4 ) ) . '</text>'
            . '<text x="' . padChartXY ( $x + $w / 2 ) . '" y="' . padChartXY ( $bottom + 29 ) . '" text-anchor="middle" opacity="0.75">'
            . padChartAttr ( padChartFit ( padChartNumber ( $total ) . ' · ' . $pct ( $total, $grand ), $w - 4 ) ) . '</text>';

      $x += $w;

    }

    return "$svg</svg>";

  }

?>
