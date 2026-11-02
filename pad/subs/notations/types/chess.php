<?php

  // {notation:chess 'fen', flip, highlight='e2, e4', arrow='e2-e4, g8-f6', size=360, title='name'/} -
  // a chess position drawn as a board, the pieces shapes of PAD's own. The text is the FEN
  // (Forsyth-Edwards Notation); only its placement is needed, the other fields - side to move,
  // castling, en passant, the two counters - are checked when written. flip turns the board so
  // Black plays up, highlight= marks squares and arrow= draws arrows - the last move, a plan, a
  // threat. size= is the width and height in pixels (default 360), title= the accessible name;
  // the description lists every piece.
  //
  // Once a built-in tag of its own, now a notation: {notation:chess 'fen'/} reaches it bare, as before,
  // and it is {notation:chess 'fen'/} and {notation:chess 'fen'/} as well.

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padChessPosition = padChessFen ( $padNotationText );

  if ( is_string ( $padChessPosition ) ) {
    if ( $padCheckSyntax )
      padError ( "the chess position '" . padMakeSafe ( $padNotationText, 100 ) . "' is no FEN - $padChessPosition" );
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

  // In a <pad-notation>, as every notation is: the page's tidy pass leaves it as it is, where
  // it took every attribute off the bare svg and nested its rects and paths in one another.

  return padNotationWrap ( padChess ( $padChessPosition, (bool) padTagParm ( 'flip', FALSE ), $padChessMarks, $padChessArrows,
                                      max ( 80, (int) padTagParm ( 'size', 360 ) ), padParmText ( padTagParm ( 'title' ) ) ) );

?>
