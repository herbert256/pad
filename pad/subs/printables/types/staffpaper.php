<?php

  // {printable:staffpaper copies=4/} - music paper, manuscript paper to write music on: empty
  // staves over the width of the sheet, no clef, no bar, as many as fit with room between them
  // to write in - twelve on A4. rastral= the size of a staff, the height of its five lines
  // from the lowest to the highest, as engravers number them (Elaine Gould, Behind Bars, Faber
  // Music 2011, p. 483): 0 9.2 mm, 1 7.9, 2 7.4, 3 7.0 (the default, the size of a part or a
  // piano score), 4 6.5, 5 6.0, 6 5.5, 7 4.8, 8 3.7; lines= 5 (1 to 6 - 6 for guitar
  // tablature, 1 for percussion), its lines a space, a quarter of that height, apart; staves=
  // how many, spread evenly from the top margin to the bottom one; grand pairs them for the
  // piano, the two joined by a line at their start, the room between pairs wider than in one;
  // margin= (15 mm, or 'top left'), color= (#3a3a37), paper= a4, a3, a5, letter or legal,
  // landscape. The lines are drawn exactly, on paper as in the PDF. The content between the
  // tags is a line over the staves - a title, a name - with size= (10 pt) and align=.
  //
  //   {printable:staffpaper copies=4/}
  //   {printable:staffpaper grand, rastral=2}<b>Sonatina</b> - for piano{/printable:staffpaper}
  //   {printable:staffpaper lines=6, rastral=1, staves=8/}

  list ( $padStaffName, $padStaffPaper ) = padPrintablePaper ( PAD_PRINTABLE_PAPERS, 'a4' );

  $padStaffHigh   = [ 9.2, 7.9, 7.4, 7.0, 6.5, 6.0, 5.5, 4.8, 3.7 ] [ padPrintableInt ( 'rastral', 3, 0, 8 ) ];
  $padStaffLines  = padPrintableInt ( 'lines', 5, 1, 6 );
  $padStaffGrand  = padPrintableFlag ( 'grand' );
  $padStaffMargin = padPrintablePair ( 'margin', [ 15, 15 ], 0, 60 );
  $padStaffColor  = padPrintableColor ( 'color', '#3a3a37' );
  $padStaffBox    = padPrintableBox ( [ 'pad' => 0, 'size' => 10, 'align' => 'left', 'valign' => 'bottom' ] );

  // A staff is its lines a space apart - a quarter of the rastral height, the height of a
  // staff of five; a grand staff two of them, 1.6 staff heights between. Between one and the
  // next at least 2.2 staff heights, room for the notes above and below it.

  $padStaffSpace  = $padStaffHigh / 4;
  $padStaffOne    = ( $padStaffLines - 1 ) * $padStaffSpace;
  $padStaffInner  = $padStaffGrand ? 1.6 * $padStaffHigh : 0;
  $padStaffSystem = $padStaffGrand ? 2 * $padStaffOne + $padStaffInner : $padStaffOne;
  $padStaffGap    = 2.2 * $padStaffHigh;
  $padStaffRoom   = fn ( $head ) => $padStaffPaper [1] - 2 * $padStaffMargin [0] - $head;
  $padStaffFit    = fn ( $head ) => (int) floor ( ( $padStaffRoom ( $head ) - $padStaffSystem ) / ( $padStaffSystem + $padStaffGap ) + 0.0001 ) + 1;

  $padStaffMost   = max ( 0, $padStaffFit ( 0 ) );
  $padStaffCount  = padPrintableInt ( 'staves', min ( max ( 1, $padStaffMost ), 99 ), 1, 99 );

  if ( ( $padStaffSystem > $padStaffRoom ( 0 ) or $padStaffPaper [0] - 2 * $padStaffMargin [1] < 10
         or $padStaffCount * $padStaffSystem > $padStaffRoom ( 0 ) ) and $GLOBALS ['padCheckSyntax'] )
    padError ( 'the ' . padPrintableWhat () . " has no room for $padStaffCount " . ( $padStaffGrand ? 'grand ' : '' ) . ( $padStaffCount == 1 ? 'staff' : 'staves' )
               . ' of ' . padPrintableMm ( $padStaffHigh ) . ' mm inside its margin of ' . padPrintableMm ( $padStaffMargin [0] ) . ' by '
               . padPrintableMm ( $padStaffMargin [1] ) . " mm on $padStaffName, " . padPrintableMm ( $padStaffPaper [0] ) . ' by '
               . padPrintableMm ( $padStaffPaper [1] ) . ' mm - ' . $padStaffMost . ' fit with room to write between them' );

  return padPrintablePlan ( [

    'name'      => [ 'page of staff paper', 'pages of staff paper' ],
    'paper'     => $padStaffPaper,
    'paperName' => $padStaffName,
    'exact'     => TRUE,

    'item' => function ( $cell, $parts ) use ( $padStaffPaper, $padStaffMargin, $padStaffSpace, $padStaffLines, $padStaffOne, $padStaffGrand,
                                               $padStaffInner, $padStaffSystem, $padStaffCount, $padStaffColor, $padStaffBox, $padStaffRoom ) {

      $text  = $parts [''] ?? '';
      $head  = ( $text !== '' ) ? 12 : 0;
      $x0    = $padStaffMargin [1];
      $x1    = round ( $padStaffPaper [0] - $padStaffMargin [1], 3 );
      $room  = $padStaffRoom ( $head );
      $top   = $padStaffMargin [0] + $head;
      $step  = ( $padStaffCount > 1 ) ? ( $room - $padStaffSystem ) / ( $padStaffCount - 1 ) : 0;
      $line  = [ 'stroke' => $padStaffColor, 'width' => 0.5 ];
      $draw  = [];

      if ( $text !== '' )
        $draw [] = [ 'box', $x0, round ( $padStaffMargin [0], 3 ), round ( $x1 - $x0, 3 ), 9, $text, $padStaffBox ];

      // The staves spread from the top of the room to its bottom - a staff alone at the top.

      for ( $s = 0; $s < $padStaffCount; $s++ ) {

        $y = $top + $s * max ( $step, $padStaffSystem );

        if ( $y + $padStaffSystem > $padStaffPaper [1] - $padStaffMargin [0] + 0.01 )
          break;

        foreach ( $padStaffGrand ? [ $y, $y + $padStaffOne + $padStaffInner ] : [ $y ] as $staff )
          for ( $l = 0; $l < $padStaffLines; $l++ )
            $draw [] = [ 'line', $x0, round ( $staff + $l * $padStaffSpace, 3 ), $x1, round ( $staff + $l * $padStaffSpace, 3 ), $line ];

        if ( $padStaffGrand )
          $draw [] = [ 'line', $x0, round ( $y, 3 ), $x0, round ( $y + $padStaffSystem, 3 ), [ 'stroke' => $padStaffColor, 'width' => 0.9 ] ];

      }

      return $draw;

    },

  ] );

?>
