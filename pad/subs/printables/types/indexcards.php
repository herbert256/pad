<?php

  // {printable:indexcards data='words'}...{/printable:indexcards} - index cards, ruled as the
  // ones in a card box are - a red line under the heading, blue lines below it - as many to
  // the sheet as fit, with crop marks to cut along: the content between the tags is one card,
  // its heading in an <h3>. card= 5x3 (5 by 3 inches, the default), 6x4, a6 or a7; paper= a4
  // or letter, turned the way more cards fit unless landscape says so; plain leaves out the
  // ruling, skip= and copies= as for labels; padding= (4 mm), size= (10 pt) and align= the
  // card's text.
  //
  //   {printable:indexcards data='words'}
  //     <h3>{$word}</h3>{$meaning}
  //   {/printable:indexcards}

  $padIndexCards = [ '5x3' => [ 127, 76.2 ], '6x4' => [ 152.4, 101.6 ], 'a6' => [ 148, 105 ], 'a7' => [ 105, 74 ] ];

  $padIndexCard  = padPrintableChoice ( 'card', '5x3', array_keys ( $padIndexCards ) );
  $padIndexName  = padPrintableChoice ( 'paper', 'a4', [ 'a4', 'letter' ] );
  $padIndexPaper = PAD_PRINTABLE_PAPERS [$padIndexName];

  list ( $padIndexW, $padIndexH ) = $padIndexCards [$padIndexCard];

  // Upright or on its side: the way more cards fit, upright when the two are equal - or on
  // its side when landscape asks.

  $padIndexFit  = fn ( $paper ) => intdiv ( (int) ( $paper [0] * 1000 ), (int) ( $padIndexW * 1000 ) ) * intdiv ( (int) ( $paper [1] * 1000 ), (int) ( $padIndexH * 1000 ) );
  $padIndexSide = [ $padIndexPaper [1], $padIndexPaper [0] ];

  if ( padPrintableFlag ( 'landscape' ) or $padIndexFit ( $padIndexSide ) > $padIndexFit ( $padIndexPaper ) )
    $padIndexPaper = $padIndexSide;

  $padIndexAcross = max ( 1, intdiv ( (int) ( $padIndexPaper [0] * 1000 ), (int) ( $padIndexW * 1000 ) ) );
  $padIndexDown   = max ( 1, intdiv ( (int) ( $padIndexPaper [1] * 1000 ), (int) ( $padIndexH * 1000 ) ) );

  $padIndexGrid = [ 'paper' => $padIndexPaper, 'across' => $padIndexAcross, 'down' => $padIndexDown,
                    'width' => $padIndexW, 'height' => $padIndexH, 'hpitch' => $padIndexW, 'vpitch' => $padIndexH,
                    'left'  => round ( ( $padIndexPaper [0] - $padIndexAcross * $padIndexW ) / 2, 3 ),
                    'top'   => round ( ( $padIndexPaper [1] - $padIndexDown * $padIndexH ) / 2, 3 ) ];

  $padIndexCells = [];

  for ( $r = 0; $r < $padIndexDown; $r++ )
    for ( $c = 0; $c < $padIndexAcross; $c++ )
      $padIndexCells [] = [ round ( $padIndexGrid ['left'] + $c * $padIndexW, 3 ), round ( $padIndexGrid ['top'] + $r * $padIndexH, 3 ), $padIndexW, $padIndexH ];

  $padIndexBox   = padPrintableBox ( [ 'pad' => 4, 'size' => 10, 'align' => 'left' ] );
  $padIndexPlain = padPrintableFlag ( 'plain' );
  $padIndexSkip  = padPrintableInt ( 'skip', 0, 0, count ( $padIndexCells ) - 1 );

  return padPrintablePlan ( [

    'name'      => [ 'index card', 'index cards' ],
    'paper'     => $padIndexPaper,
    'paperName' => ( $padIndexName == 'letter' ? 'Letter' : 'A4' ),
    'cells'     => $padIndexCells,
    'skip'      => $padIndexSkip,

    'sheet' => fn ( $sheet ) => padPrintableCrop ( $padIndexGrid ),

    'item'  => function ( $cell, $parts ) use ( $padIndexBox, $padIndexPlain ) {

      list ( $x, $y, $w, $h ) = $cell;

      $draw = [ padPrintableEdge ( $cell ) ];

      if ( $parts === NULL )
        return $draw;

      // The ruling: a red line 13 mm down, under the heading, and blue ones a quarter of an
      // inch apart below it.

      if ( ! $padIndexPlain ) {
        $draw [] = [ 'line', $x + 3, $y + 13, $x + $w - 3, $y + 13, [ 'stroke' => '#e07a7a', 'width' => 0.6 ] ];
        for ( $line = $y + 13 + 6.35; $line < $y + $h - 3; $line += 6.35 )
          $draw [] = [ 'line', $x + 3, round ( $line, 3 ), $x + $w - 3, round ( $line, 3 ), [ 'stroke' => '#a9c9ea', 'width' => 0.35 ] ];
      }

      $draw [] = [ 'box', $x, $y, $w, $h, $parts [''], $padIndexBox ];

      return $draw;

    },

  ] );

?>
