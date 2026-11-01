<?php

  // {printable:placecards data='guests'}...{/printable:placecards} - place cards for a table,
  // folded into a tent: the content between the tags - a name in an <h2> - stands on both
  // halves of the card, the upper one upside down, so that folded along the dashed line it
  // reads from both sides of the table. sheet= a4-2x2 (four cards of 105 by 148.5 mm, folded
  // to 105 by 74, the default), a4-1x3 (three long ones, folded to 210 by 49.5) or letter-2x2,
  // the grid filling the paper, cut along its grey lines; padding= (6 mm), size= (12 pt) and
  // align= (center) the card's text.
  //
  //   {printable:placecards data='guests'}<h2>{$name}</h2>{$table}{/printable:placecards}

  $padPlaceSheets = [
    'a4-2x2'     => [ 'a4',     2, 2, 105,    148.5, 105,    148.5 ],
    'a4-1x3'     => [ 'a4',     1, 3, 210,    99,    210,    99    ],
    'letter-2x2' => [ 'letter', 2, 2, 107.95, 139.7, 107.95, 139.7 ],
  ];

  $padPlaceGrid = padPrintableGrid ( $padPlaceSheets, 'a4-2x2' );
  $padPlaceBox  = padPrintableBox ( [ 'pad' => 6, 'size' => 12, 'align' => 'center', 'valign' => 'middle' ] );

  // The lines to cut along, between the cards and round them where the paper has a margin.

  $padPlaceCuts = [];

  for ( $c = 0; $c <= $padPlaceGrid ['across']; $c++ ) {
    $x = round ( $padPlaceGrid ['left'] + $c * $padPlaceGrid ['hpitch'], 3 );
    if ( $x > 0.5 and $x < $padPlaceGrid ['paper'] [0] - 0.5 )
      $padPlaceCuts [] = [ 'line', $x, 0, $x, $padPlaceGrid ['paper'] [1], [ 'stroke' => '#bdbcb6', 'width' => 0.3 ] ];
  }

  for ( $r = 0; $r <= $padPlaceGrid ['down']; $r++ ) {
    $y = round ( $padPlaceGrid ['top'] + $r * $padPlaceGrid ['vpitch'], 3 );
    if ( $y > 0.5 and $y < $padPlaceGrid ['paper'] [1] - 0.5 )
      $padPlaceCuts [] = [ 'line', 0, $y, $padPlaceGrid ['paper'] [0], $y, [ 'stroke' => '#bdbcb6', 'width' => 0.3 ] ];
  }

  return padPrintablePlan ( [

    'name'  => [ 'place card', 'place cards' ],
    'grid'  => $padPlaceGrid,

    'sheet' => fn ( $sheet ) => $padPlaceCuts,

    'item'  => function ( $cell, $parts ) use ( $padPlaceBox ) {

      list ( $x, $y, $w, $h ) = $cell;

      $half = round ( $h / 2, 3 );

      if ( $parts === NULL )
        return [ padPrintableEdge ( $cell ) ];

      return [
        [ 'line', $x + 4, $y + $half, $x + $w - 4, $y + $half, [ 'stroke' => '#9a9993', 'width' => 0.4, 'dash' => TRUE ] ],
        [ 'box', $x, $y, $w, $half, $parts [''], [ 'turn' => 180 ] + $padPlaceBox ],
        [ 'box', $x, $y + $half, $w, $half, $parts [''], $padPlaceBox ] ];

    },

  ] );

?>
