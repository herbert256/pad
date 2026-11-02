<?php

  // harmonograph - the Victorian drawing machine: two pendulums swing a pen from side to
  // side and two more swing it up and down, each a little slower than the last as it dies
  // away, and the pen draws the figures their frequencies make - lissajous knots, roses,
  // spirals that wind into the middle. Change the frequencies with the sliders, detune one a
  // hair for the slow drift that makes the figure, or let Random pick. frequencies= the four
  // pendulums - x, x, y, y - '2, 3, 3, 2.01' unless given (0.5 to 10 each); damping= how fast
  // they die away (3, 0 to 20); rotary turns the paper in a circle under the pen as well.
  //
  //   {toy:harmonograph frequencies='3, 2, 2.005, 3', damping=2/}

  return padToy ( 'harmonograph', 'Harmonograph', [
    'frequencies' => padToyNumbers ( 'frequencies', [ 2, 3, 3, 2.01 ], 0.5, 10, 4, 4 ),
    'damping'     => padToyInt ( 'damping', 3, 0, 20 ),
    'rotary'      => padToyFlag ( 'rotary' )
  ] );

?>
