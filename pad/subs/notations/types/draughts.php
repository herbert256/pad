<?php

  // draughts - a draughts position from the FEN of PDN, drawn as a board: international
  // draughts on 10 by 10 squares, or with board=8 the 8 by 8 board of English draughts and
  // checkers. The FEN is the side to move, then each colour with its squares - W:W31,32,K33:
  // B1,2 - a K before a king's square, a range for a row of them, 31-35. White plays up from the
  // bottom. flip turns the board, numbers writes the number in every playing square,
  // highlight='32, 28' marks squares, size= is the width in pixels (360 unless given), title=
  // the accessible name; the description lists every piece.
  //
  //   {draughts 'W:W31-50:B1-20'/}
  //   {draughts 'B:W21-32:B1-12', board=8, numbers/}

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padDraughtsBoard    = (int) padNotationChoice ( 'board', '10', [ '10', '8' ] );
  $padDraughtsPosition = padDraughtsFen ( $padNotationText, $padDraughtsBoard );

  if ( is_string ( $padDraughtsPosition ) ) {
    if ( $padCheckSyntax )
      padError ( "the draughts position '" . padMakeSafe ( $padNotationText, 100 ) . "' is no FEN - $padDraughtsPosition" );
    return '';
  }

  $padDraughtsMarks = [];

  foreach ( padExplode ( (string) padNotationOption ( 'highlight' ), ',' ) as $padDraughtsOne )
    if ( $padDraughtsOne === '' )
      continue;
    elseif ( ctype_digit ( $padDraughtsOne ) and (int) $padDraughtsOne >= 1 and (int) $padDraughtsOne <= $padDraughtsBoard * $padDraughtsBoard / 2 )
      $padDraughtsMarks [] = (int) $padDraughtsOne;
    elseif ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has no square '" . padNotationShow ( $padDraughtsOne ) . "' to highlight - squares are 1 to " . ( $padDraughtsBoard * $padDraughtsBoard / 2 ) );

  $padDraughtsTitle = padNotationOption ( 'title' );

  if ( $padDraughtsTitle === '' )
    $padDraughtsTitle = 'Draughts position, ' . ( $padDraughtsPosition [0] == 'W' ? 'White' : 'Black' ) . ' to move';

  $padDraughtsPixels  = padNotationInt ( 'size', 360, 80, 2000 );
  $padDraughtsFlip    = (bool) padTagParm ( 'flip', FALSE );
  $padDraughtsNumbers = (bool) padTagParm ( 'numbers', FALSE );

  return padNotationWrap ( padNotationStyle ( 'draughts', PAD_DRAUGHTS_COLOURS )
       . padDraughtsSvg ( $padDraughtsPosition, $padDraughtsBoard, $padDraughtsFlip, $padDraughtsNumbers, $padDraughtsMarks, $padDraughtsPixels,
                          padNotationId ( 'draughts', [ $padDraughtsPosition, $padDraughtsBoard, $padDraughtsFlip, $padDraughtsNumbers, $padDraughtsMarks, $padDraughtsPixels, $padDraughtsTitle ] ),
                          $padDraughtsTitle, padDraughtsDescribe ( $padDraughtsPosition, $padDraughtsMarks ) ) );

?>
