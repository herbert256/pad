<?php

  // nonogram - a picture to uncover: the numbers beside each row and over each column are the
  // runs of filled squares in it, in order. Click a square to fill it, click it again to mark
  // it empty (or switch to marking), until the picture is found. The picture is written
  // between the tags, a line per row, # for a filled square and . for an empty one - else
  // one of a few built in, the same for the same seed=.
  //
  //   {game:nonogram}
  //     .##.##.
  //     #######
  //     .#####.
  //     ..###..
  //     ...#...
  //   {/game:nonogram}

  $padNonoRows = padGameList ();

  if ( $padNonoRows ) {

    $padNonoWidth = strlen ( $padNonoRows [0] );

    foreach ( $padNonoRows as $padNonoRow )
      if ( ! preg_match ( '/^[#.]+$/D', $padNonoRow ) or strlen ( $padNonoRow ) != $padNonoWidth ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "the nonogram picture has a row '" . padMakeSafe ( $padNonoRow, 30 ) . "' - every row is as long as the first, # for a filled square and . for an empty one" );
        $padNonoRows = [];
        break;
      }

    if ( $padNonoRows and ( count ( $padNonoRows ) > 25 or $padNonoWidth > 25 or ! str_contains ( implode ( '', $padNonoRows ), '#' ) ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "the nonogram picture is " . count ( $padNonoRows ) . " by $padNonoWidth - at most 25 by 25, with one filled square or more" );
      $padNonoRows = [];
    }

  }

  return padGame ( 'nonogram', 'Nonogram', [ 'picture' => $padNonoRows ] );

?>
