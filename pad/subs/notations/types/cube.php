<?php

  // cube - a sequence of moves of a twisty cube in the notation of the World Cube Association -
  // U D L R F B a face a quarter turn clockwise as seen from it, ' the other way, 2 a half turn,
  // Rw or r two layers, 3Rw three, 2R the second layer alone, M E S the middle slice, x y z the
  // whole cube, ( ) a group a number after it repeats - applied to a solved cube and drawn: the
  // six faces unfolded as a net, or view='cube' three of them seen from above front right. case
  // draws the cube the moves solve instead, the moves undone from solved, as an algorithm's
  // case is shown; layers= is 2 to 7 (3 unless given), size= the width in pixels (240), title=
  // the accessible name. A screen reader hears every face row by row, or that the cube is solved.
  //
  //   {notation:cube "R U R' U' R' F R2 U' R' U' R U R' F'"/}
  //   {notation:cube 'M2 E2 S2', view='cube'/}

  if ( trim ( $padNotationText ) === '' )
    return '';

  if ( strlen ( $padNotationText ) > PAD_CUBE_LONGEST ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has moves of " . strlen ( $padNotationText ) . " bytes - the moves are at most " . PAD_CUBE_LONGEST );
    return '';
  }

  $padCubeLayers = padNotationInt ( 'layers', 3, 2, 7 );
  $padCubeMoves  = padCubeMoves ( $padNotationText, $padCubeLayers );

  if ( is_string ( $padCubeMoves ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has $padCubeMoves" );
    return '';
  }

  $padCubeCase = (bool) padTagParm ( 'case', FALSE );

  if ( $padCubeCase )
    $padCubeMoves = padCubeInverse ( $padCubeMoves );

  $padCubeStickers = padCubeSolved ( $padCubeLayers );

  foreach ( $padCubeMoves as $padCubeMove )
    $padCubeStickers = padCubeTurn ( $padCubeStickers, $padCubeMove, $padCubeLayers );

  $padCubeFaces = padCubeFaces ( $padCubeStickers, $padCubeLayers );
  $padCubeView  = padNotationChoice ( 'view', 'net', [ 'net', 'cube' ] );
  $padCubeSize  = padNotationInt ( 'size', 240, 60, 1000 );
  $padCubeTitle = padNotationOption ( 'title' );
  $padCubeText  = trim ( preg_replace ( '/\s+/u', ' ', $padNotationText ) );

  if ( $padCubeTitle === '' )
    $padCubeTitle = ( $padCubeCase ? 'The case ' : 'A cube after ' ) . padMakeSafe ( $padCubeText, 120 ) . ( $padCubeCase ? ' solves' : '' );

  return padNotationWrap ( padNotationStyle ( 'cube', PAD_CUBE_COLOURS )
       . padCubeSvg ( $padCubeFaces, $padCubeLayers, $padCubeView, $padCubeSize,
                      padNotationId ( 'cube', [ $padCubeFaces, $padCubeView, $padCubeSize, $padCubeTitle ] ), $padCubeTitle,
                      ( $padCubeView == 'cube' ? 'Seen from above, the front and the right face. ' : '' ) . padCubeWords ( $padCubeFaces ) ) );

?>
