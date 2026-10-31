<?php

  // nim - a few heaps of matches; each turn takes as many as you like, at least one, from one
  // heap, and who takes the last one wins - or, with misere, loses. The computer plays the
  // winning reply whenever there is one, so the first to move can win only by playing
  // perfectly. heaps= the sizes, '3, 4, 5' unless given (one to seven heaps of one to
  // fifteen); opens='computer' lets it begin.

  $padNimHeaps = [];

  foreach ( explode ( ',', (string) padParmText ( padTagParm ( 'heaps', '3, 4, 5' ) ) ) as $padNimHeap )
    if ( preg_match ( '/^\s*(\d+)\s*$/', $padNimHeap, $padNimMatch ) and $padNimMatch [1] >= 1 and $padNimMatch [1] <= 15 )
      $padNimHeaps [] = (int) $padNimMatch [1];
    elseif ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padGameWhat () . " has a heap '" . padMakeSafe ( trim ( $padNimHeap ), 10 ) . "' - heaps are whole numbers from 1 to 15" );

  if ( ( ! $padNimHeaps or count ( $padNimHeaps ) > 7 ) and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padGameWhat () . " needs one to seven heaps - heaps='3, 4, 5'" );

  return padGame ( 'nim', 'Nim', [
    'heaps'  => array_slice ( $padNimHeaps ?: [ 3, 4, 5 ], 0, 7 ),
    'misere' => padGameFlag ( 'misere' ),
    'opens'  => padGameChoice ( 'opens', 'you', [ 'you', 'computer' ] )
  ] );

?>
