<?php

  // Fixture for the walk case: an application tag that hands its rows over in batches - a
  // list of two values a pass - and asks for another pass with $padWalk [$pad] = 'next'
  // until the third pass, which answers that there is no more.

  $GLOBALS ['batchesPass'] = ( $padWalk [$pad] == 'next' ) ? $GLOBALS ['batchesPass'] + 1 : 1;

  if ( $GLOBALS ['batchesPass'] > 2 ) {
    $padWalk [$pad] = '';
    return NULL;
  }

  $padWalk [$pad] = 'next';

  return ( $GLOBALS ['batchesPass'] == 1 ) ? [ 'a', 'b' ] : [ 'c', 'd' ];

?>
