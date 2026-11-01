<?php

  // percolation - water finding its way through a porous grid, site percolation: every site
  // of a square grid is open with the same chance, and water poured on the top row runs into
  // every open site joined to it, side by side - or corner to corner as well with eight
  // neighbours - never across the outer edges. Below a certain chance the open sites form
  // islands and the water stops; above it one cluster spans the grid from top to bottom, and
  // the change is sudden. Every site keeps its own random number, so the probability slider
  // opens and closes the same sites each time and the clusters grow and merge as it moves.
  // The experiment opens the sites of an empty grid one at a time in a random order until
  // the water gets through, notes the share open, and does it again: the share that
  // percolates by each chance is drawn as a curve beside the grid, steeper the larger the
  // grid, and the mean of the trials estimates the threshold of this finite grid - for an
  // endless grid of four neighbours it is near 0.5927 (Newman and Ziff), of eight 0.4073.
  // Click a site, or move with the arrow keys and press Enter, to open or close it.
  // probability= the chance a site is open (0.59, 0 to 1); size= the sites across and down
  // (64, 10 to 200); neighbours= 4 or 8; mode= what Play does - flow, the water seeping down
  // a step at a time (unless given), or experiment, the trials; colour= flow, the water in
  // the accent, or clusters, each one a colour, the spanning one the accent - in the flow;
  // speed= how fast (5, 1 to 10).
  //
  //   {percolation probability=0.6, size=100/}
  //   {percolation mode='experiment', neighbours=8, seed=3/}

  // The neighbourhood: the four sites beside, or the eight round about.

  $padPercNeighbours = padTagParm ( 'neighbours', 4 );

  if ( is_string ( $padPercNeighbours ) )
    $padPercNeighbours = trim ( padParmText ( $padPercNeighbours ) );

  if ( ! in_array ( (string) $padPercNeighbours, [ '4', '8' ], TRUE ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has no neighbours '" . padToyShow ( $padPercNeighbours ) . "' - 4, the sites beside, or 8, the corners too" );
    $padPercNeighbours = 4;
  }

  return padToy ( 'percolation', 'Percolation', [
    'probability' => padToyNumber ( 'probability', 0.59, 0, 1 ),
    'size'        => padToyInt ( 'size', 64, 10, 200 ),
    'neighbours'  => (int) $padPercNeighbours,
    'mode'        => padToyChoice ( 'mode', 'flow', [ 'flow', 'experiment' ] ),
    'colour'      => padToyChoice ( 'colour', 'flow', [ 'flow', 'clusters' ] ),
    'speed'       => padToyInt ( 'speed', 5, 1, 10 )
  ] );

?>
