<?php

  // memory - pairs of cards face down: turn two over, and a pair stays open; turn over the
  // fewest you can until every pair is found. pairs= how many, 8 unless given (2 to 18); the
  // faces are the symbols between the tags or of the list given - one a line, or 'a, b' -
  // else a set built in.

  $padMemoryFaces = array_values ( array_unique ( padGameList () ) );

  foreach ( $padMemoryFaces as $padMemoryFace )
    if ( mb_strlen ( $padMemoryFace ) > 12 and $GLOBALS ['padCheckSyntax'] )
      padError ( "the memory card '" . padMakeSafe ( $padMemoryFace, 20 ) . "' is too long - a face is a symbol or a short word, at most 12 characters" );

  $padMemoryPairs = padGameInt ( 'pairs', $padMemoryFaces ? min ( 18, max ( 2, count ( $padMemoryFaces ) ) ) : 8, 2, 18 );

  if ( $padMemoryFaces and count ( $padMemoryFaces ) < $padMemoryPairs and $GLOBALS ['padCheckSyntax'] )
    padError ( "the memory game has " . count ( $padMemoryFaces ) . " faces for $padMemoryPairs pairs - give a face for every pair" );

  return padGame ( 'memory', 'Memory', [ 'pairs' => $padMemoryPairs, 'faces' => $padMemoryFaces ] );

?>
