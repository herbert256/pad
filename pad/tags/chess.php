<?php

  // {chess 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1'} - a chess position
  // as inline SVG, lib/chess.php. The first parameter is the position in FEN; only its
  // placement is needed, the other fields are checked when written. flip turns the board
  // so Black plays up, highlight='e2, e4' marks squares and arrow='e2-e4, g8-f6' draws
  // arrows - the last move, a plan, a threat. size= is the width and height in pixels
  // (default 360), title= the accessible name; the description lists every piece.

  $padChessPosition = padChessFen ( $padParm );

  if ( is_string ( $padChessPosition ) ) {
    if ( $padCheckSyntax )
      padError ( "the chess position '" . padMakeSafe ( $padParm, 100 ) . "' is no FEN - $padChessPosition" );
    return '';
  }

  // The marked squares and the arrows, each square checked by name.

  $padChessMarks  = [];
  $padChessArrows = [];
  $padChessWrong  = [];

  foreach ( padExplode ( (string) padTagParm ( 'highlight' ), ',' ) as $padChessName )
    if ( ( $padChessSquare = padChessSquare ( $padChessName ) ) !== NULL )
      $padChessMarks [] = $padChessSquare;
    else
      $padChessWrong [] = $padChessName;

  foreach ( padExplode ( (string) padTagParm ( 'arrow' ), ',' ) as $padChessName ) {
    $padChessEnds = explode ( '-', $padChessName );
    $padChessFrom = padChessSquare ( $padChessEnds [0] );
    $padChessTo   = padChessSquare ( $padChessEnds [1] ?? '' );
    if ( count ( $padChessEnds ) == 2 and $padChessFrom !== NULL and $padChessTo !== NULL and $padChessFrom != $padChessTo )
      $padChessArrows [] = [ $padChessFrom, $padChessTo ];
    else
      $padChessWrong [] = $padChessName;
  }

  if ( $padChessWrong and $padCheckSyntax )
    padError ( "the chess board has no square or arrow '" . padMakeSafe ( implode ( ', ', $padChessWrong ), 60 ) . "' - write squares as e4, arrows as e2-e4" );

  return padChess ( $padChessPosition, (bool) padTagParm ( 'flip', FALSE ), $padChessMarks, $padChessArrows,
                    max ( 80, (int) padTagParm ( 'size', 360 ) ), (string) padTagParm ( 'title' ) );

?>
