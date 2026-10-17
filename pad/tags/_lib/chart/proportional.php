<?php

  // Sizes compared by area - a circle per row, or a square with square, whose area grows
  // with its value.
  //
  //   {chart 'proportional', data='emissions', label='country', value='mt'}
  //   {chart 'proportional', data='oceans', label='ocean', value='area', square}
  //
  // value= names the number - else the first numeric field - and label= the row, both
  // written under its shape; a row without a number above 0 is left out. The shapes stand
  // side by side on a common baseline in the order of the rows, centred, and wrap into
  // more lines when the width runs out - the largest shape as large as the room allows.
  // An area is hard to read exactly, so the value is always written beside it.

  function padChartProportional ( $rows, $width, $height ) {

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $field  = padChartPick ( 'value', $numbers, $used );
    $label  = padChartPick ( 'label', $names, $used );
    $square = padTagParm ( 'square', FALSE );
    $square = ( $square !== FALSE and $square !== '' and $square !== 0 and $square !== '0' );
    $items  = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {
      $row = padChartRow ( $row );
      $index++;
      $v   = $row [$field] ?? NULL;
      if ( ! padChartFinite ( $v ) or $v <= 0 )
        continue;
      $name     = padChartText ( $row, $label );
      $items [] = [ $name !== '' ? $name : "Row $index", $v + 0 ];
    }

    if ( ! $items )
      return '';

    $desc = [];
    foreach ( $items as list ( $name, $v ) )
      $desc [] = "$name: " . padChartNumber ( $v );

    $title = (string) padTagParm ( 'title', ucfirst ( $field !== '' ? $field : 'value' ) . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'proportional', [ $items, $square ], $desc, $title, $width, $height );

    // The size of the largest shape: the largest whose lines all fit in the height. A
    // shape's cell is as wide as the shape or its texts, whichever is wider; a line is as
    // high as its tallest shape and two lines of text.

    $max    = max ( array_column ( $items, 1 ) );
    $gap    = 14;
    $textH  = 32;
    $layout = function ( $big ) use ( $items, $max, $gap, $textH, $width ) {
      $lines = [];
      $line  = [];
      $used  = 0;
      foreach ( $items as $i => list ( $name, $v ) ) {
        $size = $big * sqrt ( $v / $max );
        $cell = max ( $size, min ( padChartWidth ( $name ), 140 ), padChartWidth ( padChartNumber ( $v ) ) ) + $gap;
        if ( $line and $used + $cell > $width - 16 ) {
          $lines [] = [ $line, $used ];
          $line     = [];
          $used     = 0;
        }
        $line [] = [ $i, $size, $cell ];
        $used   += $cell;
      }
      $lines [] = [ $line, $used ];
      $tall     = 0;
      foreach ( $lines as list ( $line ) )
        $tall += max ( array_column ( $line, 1 ) ) + $textH + 10;
      return [ $lines, $tall ];
    };

    $low  = 4;
    $high = max ( 4, min ( $height - $textH - 20, $width - 16 - $gap ) );

    for ( $round = 0; $round < 30; $round++ ) {
      $mid = ( $low + $high ) / 2;
      list ( , $tall ) = $layout ( $mid );
      if ( $tall <= $height - 12 )
        $low = $mid;
      else
        $high = $mid;
    }

    list ( $lines, $tall ) = $layout ( $low );

    $top = ( $height - $tall ) / 2 + 4;

    foreach ( $lines as list ( $line, $used ) ) {

      $base = $top + max ( array_column ( $line, 1 ) );
      $x    = ( $width - $used ) / 2;

      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $x ) . '" x2="' . padChartXY ( $x + $used - $gap ) . '" y1="' . padChartXY ( $base ) . '" y2="' . padChartXY ( $base ) . '"/>';

      foreach ( $line as list ( $i, $size, $cell ) ) {

        list ( $name, $v ) = $items [$i];

        $c   = $x + ( $cell - $gap ) / 2;
        $tip = '<title>' . padChartAttr ( $desc [$i] ) . '</title>';

        if ( $square )
          $svg .= '<rect class="pc-mark pc-c1" x="' . padChartXY ( $c - $size / 2 ) . '" y="' . padChartXY ( $base - $size ) . '" width="' . padChartXY ( max ( 0.5, $size ) ) . '" height="' . padChartXY ( max ( 0.5, $size ) ) . "\" rx=\"2\">$tip</rect>";
        else
          $svg .= '<circle class="pc-mark pc-c1" cx="' . padChartXY ( $c ) . '" cy="' . padChartXY ( $base - $size / 2 ) . '" r="' . padChartXY ( max ( 0.5, $size / 2 ) ) . "\">$tip</circle>";

        $svg .= '<text x="' . padChartXY ( $c ) . '" y="' . padChartXY ( $base + 15 ) . '" text-anchor="middle" style="font-weight:600">' . padChartAttr ( padChartNumber ( $v ) ) . '</text>'
              . '<text x="' . padChartXY ( $c ) . '" y="' . padChartXY ( $base + 29 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $name, $cell - 4 ) ) . '</text>';

        $x += $cell;

      }

      $top = $base + $textH + 10;

    }

    return "$svg</svg>";

  }

?>
