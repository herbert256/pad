<?php

  // {printable:foldcard data='guests'}...{/printable:foldcard} - a folded card, a greeting
  // card or an invitation, printed on one side of a sheet and folded twice, a quarter fold:
  // the top half folded back, then the sheet folded in two from left to right, A4 making a card
  // of A6, 105 by 148.5 mm. The content between the tags is the card: its <header> the front,
  // its <aside> the inside left, its <footer> the back and the rest the message on the inside
  // right - the front and the back on the lower half of the sheet, the inside upside down on
  // the upper half, so that folded each reads the right way up. paper= a4, a3 or letter (a card
  // of 4.25 by 5.5 inches), landscape for a card on its side; padding= (8 mm, kept from the
  // folds and the edges, where a printer does not print), size= (12 pt) and align= (center)
  // the text. The screen shows the folds and the name of every panel; on paper and in the PDF
  // they are left out - folds prints the folds as light lines, which end up on the edges of the
  // card. A card for every row of the data.
  //
  //   {printable:foldcard data='guests'}
  //     <header><h1>You are invited</h1></header>
  //     <h2>Dear {$name},</h2><p>Come to our party on Saturday 5 December.</p>
  //     <footer>Printed with PAD</footer>
  //   {/printable:foldcard}

  list ( $padFoldName, $padFoldPaper ) = padPrintablePaper ( [ 'a4' => PAD_PRINTABLE_PAPERS ['a4'], 'a3' => PAD_PRINTABLE_PAPERS ['a3'],
                                                               'letter' => PAD_PRINTABLE_PAPERS ['letter'] ], 'a4' );

  $padFoldBox   = padPrintableBox ( [ 'pad' => 8, 'size' => 12, 'align' => 'center', 'valign' => 'middle' ] );
  $padFoldLines = padPrintableFlag ( 'folds' );

  return padPrintablePlan ( [

    'name'      => [ 'folded card', 'folded cards' ],
    'paper'     => $padFoldPaper,
    'paperName' => $padFoldName,
    'parts'     => [ 'header', 'aside', 'footer' ],

    'item' => function ( $cell, $parts ) use ( $padFoldPaper, $padFoldBox, $padFoldLines ) {

      list ( $w, $h ) = $padFoldPaper;

      $x    = round ( $w / 2, 3 );
      $y    = round ( $h / 2, 3 );
      $fold = $padFoldLines ? [ 'stroke' => '#c9c8c2', 'width' => 0.3, 'exact' => TRUE ] : [ 'stroke' => '#9cc0e4', 'width' => 0.6, 'dash' => TRUE, 'screen' => TRUE ];
      $name = [ 'pad' => 2, 'size' => 7, 'color' => '#7d9fc2', 'screen' => TRUE ];

      // The panels: [ x, y, the part, its name, turned ] - the upper half turned half round.

      $panels = [ [ $x, $y, 'header', 'Front',        FALSE ],
                  [ 0,  $y, 'footer', 'Back',         FALSE ],
                  [ $x, 0,  'aside',  'Inside left',  TRUE  ],
                  [ 0,  0,  '',       'Inside right', TRUE  ] ];

      $draw = [ [ 'line', 0, $y, $w, $y, $fold ], [ 'line', $x, 0, $x, $h, $fold ] ];

      foreach ( $panels as list ( $px, $py, $part, $label, $turn ) ) {

        $draw [] = [ 'box', $px, $py, $x, $y, $label, $name + [ 'valign' => 'bottom', 'turn' => $turn ? 180 : 0 ] ];

        if ( ( $parts [$part] ?? '' ) !== '' )
          $draw [] = [ 'box', $px, $py, $x, $y, $parts [$part], [ 'turn' => $turn ? 180 : 0 ] + $padFoldBox ];

      }

      return $draw;

    },

  ] );

?>
