<?php

  // {printable:graphpaper copies=5/} - sheets of graph paper to draw, sketch and calculate on:
  // a grid of squares (the default), of dots or of lines over the whole sheet, in as many
  // whole squares as fit inside margin= (10 mm, or 'top left') and centred on the paper.
  // spacing= the step in mm, 5 (8 for lines), from 1 to 50; major= every that many lines a
  // heavier one - or a bigger dot - counted from the top left corner: millimetre paper is
  // spacing=1, major=10; color= the grid (#8db4dc, a light blue); paper= a4, a3, a5, a6,
  // letter or legal, landscape. The lines are drawn exactly, on paper as in the PDF: printed at
  // its actual size, not fitted to the page, 5 mm is 5 mm. The content between the tags is a
  // line over the grid - a name and a date to fill in - with size= (10 pt) and align=, the grid
  // moved down for it; a sheet for every row of the data, copies= times.
  //
  //   {printable:graphpaper grid='dots', copies=10}Name ______________ Date ________{/printable:graphpaper}
  //   {printable:graphpaper spacing=1, major=10, color='#e0915a'/}

  $padGraphGrid = padPrintableChoice ( 'grid', 'squares', [ 'squares', 'dots', 'lines' ] );

  list ( $padGraphName, $padGraphPaper ) = padPrintablePaper ( PAD_PRINTABLE_PAPERS, 'a4' );

  $padGraphStep   = padPrintableNumber ( 'spacing', $padGraphGrid == 'lines' ? 8 : 5, 1, 50 );
  $padGraphMajor  = padPrintableInt ( 'major', 0, 0, 100 );
  $padGraphColor  = padPrintableColor ( 'color', '#8db4dc' );
  $padGraphMargin = padPrintablePair ( 'margin', [ 10, 10 ], 0, 60 );
  $padGraphBox    = padPrintableBox ( [ 'pad' => 0, 'size' => 10, 'align' => 'left', 'valign' => 'bottom' ] );

  // The squares that fit inside the margins, across and down - with and without the line on
  // top, which takes 12 mm of the room: none is an error under the strict check.

  $padGraphFit = function ( $head ) use ( $padGraphPaper, $padGraphMargin, $padGraphStep ) {

    $wide = $padGraphPaper [0] - 2 * $padGraphMargin [1];
    $tall = $padGraphPaper [1] - 2 * $padGraphMargin [0] - $head;

    return [ max ( 0, (int) floor ( $wide / $padGraphStep + 0.0001 ) ), max ( 0, (int) floor ( $tall / $padGraphStep + 0.0001 ) ), $tall ];

  };

  list ( $padGraphAcross, $padGraphDown ) = $padGraphFit ( 0 );

  if ( ( $padGraphAcross < 1 or $padGraphDown < 1 ) and $GLOBALS ['padCheckSyntax'] )
    padError ( 'the ' . padPrintableWhat () . ' has no room for a square of ' . padPrintableMm ( $padGraphStep ) . ' mm inside its margin of '
               . padPrintableMm ( $padGraphMargin [0] ) . ' by ' . padPrintableMm ( $padGraphMargin [1] ) . " mm on $padGraphName, "
               . padPrintableMm ( $padGraphPaper [0] ) . ' by ' . padPrintableMm ( $padGraphPaper [1] ) . ' mm' );

  return padPrintablePlan ( [

    'name'      => [ 'page of graph paper', 'pages of graph paper' ],
    'paper'     => $padGraphPaper,
    'paperName' => $padGraphName,
    'exact'     => TRUE,

    'item' => function ( $cell, $parts ) use ( $padGraphGrid, $padGraphPaper, $padGraphStep, $padGraphMajor, $padGraphColor, $padGraphMargin, $padGraphBox, $padGraphFit ) {

      $text = $parts [''] ?? '';
      $head = ( $text !== '' ) ? 12 : 0;

      list ( $across, $down, $tall ) = $padGraphFit ( $head );

      $step = $padGraphStep;
      $wide = $across * $step;
      $high = $down * $step;
      $x0   = round ( ( $padGraphPaper [0] - $wide ) / 2, 3 );
      $y0   = round ( $padGraphMargin [0] + $head + ( $tall - $high ) / 2, 3 );
      $at   = fn ( $n ) => round ( $n * $step, 3 );
      $big  = fn ( $n ) => $padGraphMajor and $n % $padGraphMajor == 0;
      $line = fn ( $n ) => [ 'stroke' => $padGraphColor, 'width' => $big ( $n ) ? 0.75 : 0.3 ];
      $draw = [];

      if ( $text !== '' )
        $draw [] = [ 'box', $x0, round ( $y0 - $head + 1, 3 ), $wide, 9, $text, $padGraphBox ];

      if ( $across < 1 or $down < 1 )
        return $draw;

      if ( $padGraphGrid == 'squares' )
        for ( $i = 0; $i <= $across; $i++ )
          $draw [] = [ 'line', $x0 + $at ( $i ), $y0, $x0 + $at ( $i ), round ( $y0 + $high, 3 ), $line ( $i ) ];

      // A row of dots for every line across - the dots of a major line and a major column
      // drawn bigger over it, a row of its own of dots major= squares apart.

      for ( $j = 0; $j <= $down; $j++ ) {

        $y = round ( $y0 + $at ( $j ), 3 );

        if ( $padGraphGrid != 'dots' )
          $draw [] = [ 'line', $x0, $y, round ( $x0 + $wide, 3 ), $y, $line ( $j ) ];
        else {
          $draw [] = [ 'line', $x0, $y, round ( $x0 + $wide, 3 ), $y, [ 'stroke' => $padGraphColor, 'width' => 1.7, 'dots' => $step ] ];
          if ( $big ( $j ) )
            $draw [] = [ 'line', $x0, $y, round ( $x0 + $at ( $across - $across % $padGraphMajor ), 3 ), $y,
                         [ 'stroke' => $padGraphColor, 'width' => 3, 'dots' => $at ( $padGraphMajor ) ] ];
        }

      }

      return $draw;

    },

  ] );

?>
