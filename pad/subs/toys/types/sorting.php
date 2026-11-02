<?php

  // sorting - sorting algorithms at work on a row of bars: every comparison lights two bars
  // up, every swap or write moves one, and the counts of both run along - bubble sort's
  // thousands against quicksort's few hundred. Pick an algorithm, the order the bars start
  // in and the speed, or race them all at once on the same bars; sound plays each bar it
  // touches as a note. algorithm= bubble, cocktail, insertion, selection, gnome, shell, comb,
  // quick, merge, heap or radix (quick unless given); bars= how many (60, 8 to 300); order=
  // random, reversed, nearly (sorted but for a few) or few (of a few heights); speed= the
  // steps a second (120, 1 to 5000); race sorts with every algorithm side by side.
  //
  //   {toy:sorting algorithm='heap', bars=100/}
  //   {toy:sorting race, bars=40/}

  $padSortAlgorithms = [ 'bubble', 'cocktail', 'insertion', 'selection', 'gnome', 'shell', 'comb', 'quick', 'merge', 'heap', 'radix' ];

  return padToy ( 'sorting', 'Sorting algorithms', [
    'algorithms' => $padSortAlgorithms,
    'algorithm'  => padToyChoice ( 'algorithm', 'quick', $padSortAlgorithms ),
    'bars'       => padToyInt ( 'bars', 60, 8, 300 ),
    'order'      => padToyChoice ( 'order', 'random', [ 'random', 'reversed', 'nearly', 'few' ] ),
    'speed'      => padToyInt ( 'speed', 120, 1, 5000 ),
    'race'       => padToyFlag ( 'race' ),
    'sound'      => padToyFlag ( 'sound' )
  ] );

?>
