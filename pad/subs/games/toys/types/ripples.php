<?php

  // ripples - a ripple tank: waves spread over water, bounce off walls and run through each
  // other, crest on crest and trough on trough, into the stripes of interference. Click or
  // drag to touch the water; with walls ticked the pointer builds walls instead. setup= what
  // the tank starts with - two (two sources beating in step, unless given), slits (a straight
  // wave through two slits in a wall) or pond (raindrops); frequency= how fast the sources
  // beat (4, 1 to 10).
  //
  //   {toy:ripples setup='slits', frequency=6/}

  return padToy ( 'ripples', 'Ripple tank', [
    'setup'     => padToyChoice ( 'setup', 'two', [ 'two', 'slits', 'pond' ] ),
    'frequency' => padToyInt ( 'frequency', 4, 1, 10 )
  ] );

?>
