<?php

  // go - a go position from a game record in SGF, drawn as a board: SZ[9], SZ[13] or SZ[19]
  // (19 unless given) with its star points and coordinates, the stones set up by AB and AW,
  // then the moves ;B[dd];W[pp] played out, their captures taken off the board, the last move
  // ringed. The first variation of every branch is the game; the outer brackets may be left
  // out. numbers writes each stone's move number on it instead, move=N shows the position
  // after move N, plain leaves the coordinates out, size= is the width in pixels (360 unless
  // given), title= the accessible name - else GN[], the game's name. The markup of a diagram -
  // LB[dd:A] labels, TR CR SQ MA marks - is drawn as well.
  //
  //   {notation:go '(;SZ[9]AB[cc][gc]AW[cg][gg];B[ee];W[ec])'/}
  //   {notation:go numbers}(;SZ[13];B[dj];W[jd];B[jj];W[dd]){/notation:go}

  $padGoText = trim ( $padNotationText );

  if ( $padGoText === '' )
    return '';

  if ( $padGoText [0] != '(' )
    $padGoText = "($padGoText)";

  $padGoNodes = padGoParse ( $padGoText );

  if ( is_string ( $padGoNodes ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has a game record that is no SGF - $padGoNodes" );
    return '';
  }

  $padGoSize = trim ( $padGoNodes [0] ['SZ'] [0] ?? '19' );

  if ( ! ctype_digit ( $padGoSize ) or (int) $padGoSize < 2 or (int) $padGoSize > 25 ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has a board SZ[" . padNotationShow ( $padGoSize ) . "] - a square board of 2 to 25 lines, SZ[9], SZ[13], SZ[19]" );
    return '';
  }

  $padGoSize   = (int) $padGoSize;
  $padGoPlayed = padGoPlay ( $padGoNodes, $padGoSize, padNotationInt ( 'move', 0, 0, 9999 ) );

  if ( $padGoPlayed [5] and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . ' ' . $padGoPlayed [5] [0] );

  $padGoTitle = padNotationOption ( 'title' );

  if ( $padGoTitle === '' )
    $padGoTitle = trim ( $padGoNodes [0] ['GN'] [0] ?? '' ) ?: "Go position, $padGoSize by $padGoSize";

  $padGoPixels = padNotationInt ( 'size', 360, 80, 2000 );
  $padGoNumbers = (bool) padTagParm ( 'numbers', FALSE );
  $padGoPlain   = (bool) padTagParm ( 'plain', FALSE );

  return padNotationWrap ( padNotationStyle ( 'go', PAD_GO_COLOURS )
       . padGoSvg ( $padGoPlayed, $padGoSize, $padGoNumbers, ! $padGoPlain, $padGoPixels,
                    padNotationId ( 'go', [ $padGoText, $padGoPixels, $padGoNumbers, $padGoPlain, $padGoTitle ] ),
                    $padGoTitle, padGoDescribe ( $padGoPlayed, $padGoSize ) ) );

?>
